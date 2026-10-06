# Diagnóstico de Consultas Lentas — MariaDB / Dropi Digital

Este documento serve como guia rápido para identificar consultas SQL
que podem estar causando lentidão, alto consumo de CPU ou travamentos
no MariaDB do Dropi Digital.

## 1. Verificar CPU do MariaDB

```bash
docker stats --no-stream mariadb
```

Se estiver alto, aguarde alguns segundos e execute novamente:

```bash
sleep 5
docker stats --no-stream mariadb
```

Se cair rapidamente, pode ter sido apenas um pico momentâneo.

Se continuar alto, investigar as consultas.

## 2. Verificar consultas em execução

```bash
docker exec -it mariadb mariadb --protocol=socket --skip-ssl \
-uroot -p dropidigital_db -e "SHOW FULL PROCESSLIST;"
```

Procure principalmente por:

* `Time` alto;
* `Sending data`;
* `Creating sort index`;
* `Copying to tmp table`;
* `Copying to tmp table on disk`;
* `Sorting result`;
* `Waiting for table`;
* consultas repetidas simultaneamente.

Não mate uma consulta imediatamente.
Primeiro identifique a causa.

Para matar uma consulta específica:

```sql
KILL ID;
```

Use somente quando houver certeza de que ela está causando o problema.

## 3. Verificar o Slow Query Log

O log está configurado em:

```text
/var/lib/mysql/slow.log
```

Configuração atual:

```text
slow_query_log = ON
long_query_time = 1
min_examined_row_limit = 1000
log_queries_not_using_indexes = OFF
```

Isso significa que o objetivo é registrar consultas que demorem
aproximadamente 1 segundo ou mais e examinem pelo menos 1.000 linhas.

## 4. Ver últimas consultas lentas

Nunca use `wc -l` em um log muito grande.

Use:

```bash
docker exec mariadb tail -n 100 /var/lib/mysql/slow.log
```

Para mais registros:

```bash
docker exec mariadb tail -n 300 /var/lib/mysql/slow.log
```

## 5. Identificar uma consulta problemática

Procure no log por:

```text
# Query_time:
```

Exemplo:

```text
# Query_time: 8.421
# Rows_examined: 850000
SELECT ...
```

Quanto maior o `Query_time` e o `Rows_examined`,
maior a prioridade para investigação.

Também observe consultas iguais aparecendo várias vezes.
Uma consulta de 1 segundo executada centenas de vezes pode ser
mais problemática que uma consulta isolada de vários segundos.

## 6. Executar EXPLAIN

Nunca altere uma consulta imediatamente.

Primeiro execute:

```sql
EXPLAIN
SELECT ...;
```

Exemplo:

```sql
EXPLAIN
SELECT *
FROM tb_products
WHERE shop_id = 25
AND status = 1
ORDER BY id ASC
LIMIT 48;
```

Observe principalmente:

```text
type
key
rows
Extra
```

Sinais de atenção:

```text
type = ALL
key = NULL
rows = muito alto
```

Também observe:

```text
Using filesort
Using temporary
```

Nem sempre esses itens significam problema, mas merecem investigação.

## 7. Verificar os índices existentes

```sql
SHOW INDEX FROM nome_da_tabela;
```

Não crie um índice apenas porque uma consulta parece lenta.

Primeiro confirme pelo `EXPLAIN` que o índice necessário realmente
não existe ou não está sendo utilizado.

## 8. Consultas com OFFSET

Tenha atenção especial a:

```sql
LIMIT 48 OFFSET 100000
```

Quanto maior o OFFSET, mais registros o banco pode precisar percorrer
antes de encontrar os resultados.

Quando houver paginação profunda, considerar paginação por cursor:

```sql
WHERE id > :last_id
ORDER BY id ASC
LIMIT 48
```

Sempre valide a alteração com `EXPLAIN`.

## 9. ORDER BY RAND()

Evite consultas como:

```sql
ORDER BY RAND()
LIMIT 1
```

principalmente em tabelas grandes.

O banco pode precisar processar uma quantidade enorme de registros.

Preferir selecionar candidatos usando índices e fazer a escolha
aleatória na aplicação quando possível.

## 10. Consultas COUNT()

Uma consulta:

```sql
SELECT COUNT(*)
FROM tabela
WHERE shop_id = 25
AND status = 1;
```

pode ser normal mesmo examinando milhares de registros.

Verifique o `EXPLAIN`.

Um bom resultado pode ser:

```text
type = ref
key = indice_apropriado
Extra = Using index
```

Não altere uma consulta somente porque ela aparece no Slow Log.

## 11. Após uma correção

Faça backup do arquivo PHP antes de alterar:

```bash
cp arquivo.php arquivo.php.bak
```

Depois valide novamente:

```sql
EXPLAIN
SELECT ...;
```

E monitore:

```bash
docker stats --no-stream mariadb
```

## 12. Verificar Threads

```bash
docker exec -it mariadb mariadb --protocol=socket --skip-ssl \
-uroot -p dropidigital_db -e "
SHOW GLOBAL STATUS
WHERE Variable_name IN (
    'Threads_connected',
    'Threads_running'
);
"
```

Muitas `Threads_running` simultaneamente podem indicar concorrência
elevada.

## 13. Se o CPU estiver muito alto

Não reinicie MariaDB imediatamente.

Siga esta ordem:

```text
1. docker stats
2. SHOW FULL PROCESSLIST
3. identificar consultas demoradas
4. consultar o Slow Query Log
5. EXPLAIN
6. verificar índices
7. corrigir código/SQL
8. testar novamente
```

Evite reiniciar ou apagar volumes como primeira tentativa.

## 14. Cuidado com o Slow Log

Verifique o tamanho:

```bash
docker exec mariadb ls -lh /var/lib/mysql/slow.log
```

Se estiver crescendo muito, investigue a configuração antes de
simplesmente apagar o arquivo.

Configuração esperada:

```text
slow_query_log = ON
long_query_time = 1
min_examined_row_limit = 1000
log_queries_not_using_indexes = OFF
```

## 15. Limpar o log quando necessário

Antes de limpar, confirme que a configuração está correta.

Para zerar o arquivo sem remover o arquivo utilizado pelo MariaDB:

```bash
docker exec mariadb sh -c \
'truncate -s 0 /var/lib/mysql/slow.log'
```

Depois confirme:

```bash
docker exec mariadb ls -lh /var/lib/mysql/slow.log
df -h /
```

## 16. Regra principal

Não tente otimizar todas as consultas do sistema de uma vez.

Use o processo:

```text
PROBLEMA
   ↓
PROCESSLIST / SLOW LOG
   ↓
CONSULTA REAL
   ↓
EXPLAIN
   ↓
CAUSA
   ↓
CORREÇÃO
   ↓
EXPLAIN NOVAMENTE
   ↓
MONITORAMENTO
```

A prioridade deve ser sempre baseada em evidências reais do servidor.

**Nunca adicionar índices, remover dados, executar OPTIMIZE ou reiniciar
serviços apenas por suspeita.**
