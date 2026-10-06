<?php
// include e bootstrap
session_start();
ob_start();
include_once('../../config.php');    // conexão em $conn_pdo
date_default_timezone_set('America/Sao_Paulo');

try {
    // 1) Carrega todas as lojas e o e‑mail de contato
    $sqlShops = "
        SELECT 
            s.id        AS shop_id,
            s.name      AS shop_name,
            u.email     AS contact_email
        FROM tb_shop AS s
        LEFT JOIN tb_users AS u
          ON u.id = s.user_id
    ";
    $stmtShops = $conn_pdo->prepare($sqlShops);
    $stmtShops->execute();
    $shops = $stmtShops->fetchAll(PDO::FETCH_ASSOC);

    // 2) Para cada loja, cria a página de Política de Privacidade
    $sqlInsert = "
        INSERT INTO tb_pages
            (shop_id, status, name, link, content, seo_name, seo_link, seo_description)
        VALUES
            (:shop_id, 1, :name, :link, :content, :seo_name, :seo_link, :seo_description)
    ";
    $stmtInsert = $conn_pdo->prepare($sqlInsert);

    foreach ($shops as $shop) {
        $shopId    = (int) $shop['shop_id'];
        $shopName  = $shop['shop_name'];
        $email     = $shop['contact_email'] ?: "contato@{$shop['shop_name']}.com.br";

        // dados SEO e URL
        $defaultName    = 'Política de Privacidade';
        $defaultLink    = 'politica-privacidade';
        $defaultSeoName = "Política de Privacidade - {$shopName}";
        $defaultSeoLink = 'politica-privacidade';
        $defaultSeoDesc = "Seja bem-vindo à nossa Política de Privacidade de {$shopName}. "
                        . "Aqui explicamos como coletamos, armazenamos e utilizamos dados dos visitantes e clientes.";

        // gera o conteúdo HTML (com data atual e dados da loja)
        $today = date('d/m/Y');
        $defaultContent = "
            <section class='privacy-policy'>
                <header>
                    <h1>Política de Privacidade</h1>
                    <p>Última atualização: {$today}</p>
                </header>
                <article>
                    <h2>1. Sobre {$shopName}</h2>
                    <p>Esta loja trabalha majoritariamente com infoprodutos e produtos de afiliados, sem processo de checkout próprio.</p>
                </article>
                <article>
                    <h2>2. Dados que Coletamos</h2>
                    <ul>
                    <li><strong>Informações fornecidas diretamente:</strong> e-mail, nome e dados de contato quando você se inscreve em nossas listas.</li>
                    <li><strong>Dados de navegação:</strong> IP, navegador, tempo de acesso e páginas visitadas.</li>
                    <li><strong>Cookies:</strong> para personalizar conteúdo e analisar tráfego.</li>
                    </ul>
                </article>
                <article>
                    <h2>3. Finalidade do Tratamento</h2>
                    <p>Utilizamos seus dados para enviar conteúdo relevante, recomendações de afiliados e melhorar sua experiência em {$shopName}.</p>
                </article>
                <!-- … repita as seções conforme sua política … -->
                <article>
                    <h2>9. Contato</h2>
                    <p>Para exercer seus direitos ou esclarecer dúvidas, envie um e-mail para 
                    <a href='mailto:{$email}'>{$email}</a>.
                    </p>
                </article>
                <footer>
                    <p>Se tiver dúvidas sobre esta política, entre em contato em 
                    <a href='mailto:{$email}'>{$email}</a>.
                    </p>
                </footer>
            </section>
        ";

        // executa o INSERT
        $stmtInsert->bindValue(':shop_id',         $shopId,         PDO::PARAM_INT);
        $stmtInsert->bindValue(':name',            $defaultName,    PDO::PARAM_STR);
        $stmtInsert->bindValue(':link',            $defaultLink,    PDO::PARAM_STR);
        $stmtInsert->bindValue(':content',         $defaultContent, PDO::PARAM_STR);
        $stmtInsert->bindValue(':seo_name',        $defaultSeoName, PDO::PARAM_STR);
        $stmtInsert->bindValue(':seo_link',        $defaultSeoLink, PDO::PARAM_STR);
        $stmtInsert->bindValue(':seo_description', $defaultSeoDesc, PDO::PARAM_STR);
        $stmtInsert->execute();
    }

    echo "Páginas de Política de Privacidade geradas para todas as lojas com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao gerar páginas: " . $e->getMessage();
    // opcional: error_log($e->getMessage());
}
?>