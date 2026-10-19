<?php

declare(strict_types=1);

/**
 * Dropi Digital — Criar sitemap no banco para lojas
 *
 * Execução:
 *   php sitemap-pages-worker.php
 *   php sitemap-pages-worker.php --shop-id=25
 */

require_once __DIR__ . '/db.php';

$pdo = connectDB();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

const SITEMAP_LIMIT = 10000;

/**
 * Gera os intervalos de IDs dos produtos publicados
 * e registra cada página em tb_sitemap_product_pages.
 */
function gerarPaginasSitemap(PDO $pdo, int $shopId): void
{
    if ($shopId <= 0) {
        throw new InvalidArgumentException('shop_id inválido.');
    }

    echo "\nLoja {$shopId}: iniciando geração...\n";

    $pdo->beginTransaction();

    try {
        // Remove os registros antigos desta loja.
        $stmtDelete = $pdo->prepare("
            DELETE FROM tb_sitemap_product_pages
            WHERE shop_id = ?
        ");
        $stmtDelete->execute([$shopId]);

        // Busca os produtos em lotes, sem OFFSET.
        $stmtProdutos = $pdo->prepare("
            SELECT id
            FROM tb_products FORCE INDEX (idx_products_shop_status_id)
            WHERE shop_id = ?
              AND status = 1
              AND id > ?
            ORDER BY id ASC
            LIMIT " . SITEMAP_LIMIT
        );

        $stmtInsert = $pdo->prepare("
            INSERT INTO tb_sitemap_product_pages (
                shop_id,
                page,
                start_id,
                end_id,
                products_count,
                generated_at
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $ultimoId = 0;
        $pagina = 0;
        $totalProdutos = 0;

        while (true) {
            $stmtProdutos->execute([$shopId, $ultimoId]);
            $produtos = $stmtProdutos->fetchAll(PDO::FETCH_COLUMN);

            if (empty($produtos)) {
                break;
            }

            $quantidade = count($produtos);
            $startId = (int) $produtos[0];
            $endId = (int) $produtos[$quantidade - 1];

            $pagina++;

            $stmtInsert->execute([
                $shopId,
                $pagina,
                $startId,
                $endId,
                $quantidade
            ]);

            $totalProdutos += $quantidade;
            $ultimoId = $endId;

            echo sprintf(
                "Loja %d | Página %d | IDs %d-%d | %d produtos\n",
                $shopId,
                $pagina,
                $startId,
                $endId,
                $quantidade
            );

            // Libera a memória do lote anterior.
            unset($produtos);
        }

        $pdo->commit();

        echo sprintf(
            "Loja %d concluída: %d páginas, %d produtos publicados.\n",
            $shopId,
            $pagina,
            $totalProdutos
        );

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

try {
    // Permite executar para uma loja específica:
    // php sitemap-pages-worker.php --shop-id=25
    $shopIdArgument = null;

    foreach ($argv ?? [] as $argument) {
        if (preg_match('/^--shop-id=(\d+)$/', $argument, $matches)) {
            $shopIdArgument = (int) $matches[1];
            break;
        }
    }

    if ($shopIdArgument !== null) {
        gerarPaginasSitemap($pdo, $shopIdArgument);
    } else {
        // Processa todas as lojas com produtos publicados.
        $stmtLojas = $pdo->query("
            SELECT DISTINCT shop_id
            FROM tb_products
            WHERE status = 1
            ORDER BY shop_id ASC
        ");

        $lojas = $stmtLojas->fetchAll(PDO::FETCH_COLUMN);

        foreach ($lojas as $shopId) {
            gerarPaginasSitemap($pdo, (int) $shopId);
        }
    }

    echo "\nGeração dos registros concluída.\n";

} catch (Throwable $e) {
    fwrite(STDERR, "ERRO: " . $e->getMessage() . "\n");
    exit(1);
}
