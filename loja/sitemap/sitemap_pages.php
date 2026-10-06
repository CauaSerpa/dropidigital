<?php
// -----------------------------------------
// CONFIGURAÇÕES INICIAIS
// -----------------------------------------

// Garante que não exista output antes do XML
if (ob_get_length()) {
    ob_clean();
}

// Cabeçalhos corretos para sitemap
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=21600');

// URL base da loja (garanta que seja absoluta)
$baseUrl = rtrim(INCLUDE_PATH_LOJA, '/') . '/';

// ----------------------------------------------------------
// 1) BUSCA shop_id PELO DOMÍNIO (igual ao seu código original)
// ----------------------------------------------------------
$sql = "SELECT shop_id FROM tb_domains WHERE subdomain = :subdomain AND domain = :domain";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':subdomain', $subdomain, PDO::PARAM_STR);
$stmt->bindParam(':domain', $domain, PDO::PARAM_STR);
$stmt->execute();
$shop_id = $stmt->fetch(PDO::FETCH_ASSOC)['shop_id'] ?? null;

if (!$shop_id) {
    http_response_code(404);
    exit;
}

// -----------------------------------------
// BUSCA PÁGINAS DINÂMICAS (CMS)
// -----------------------------------------
$sql = "SELECT link, last_modification FROM tb_pages WHERE status = 1 AND shop_id = ?";
$stmt = $conn_pdo->prepare($sql);
$stmt->execute([$shop_id]);
$paginasDinamicas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------------------
// INÍCIO DO XML
// -----------------------------------------
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">

<?php
    // ----------------------------------------------------------
    // 6) GERAÇÃO DAS ENTRADAS DE PRODUTOS, AGRUPADAS POR IDIOMA
    // ----------------------------------------------------------
    // 6.1) Primeiro, pega todos os idiomas disponíveis para esta shop_id
    $sqlLangs = "
        SELECT DISTINCT language
        FROM tb_products
        WHERE shop_id = :shop_id
          AND status = 1
        ORDER BY 
            CASE 
                WHEN language = 'pt' THEN 1
                WHEN language = 'en' THEN 2
                WHEN language = 'de' THEN 3
                WHEN language = 'es' THEN 4
                ELSE 5
            END
    ";
    $stmtLangs = $conn_pdo->prepare($sqlLangs);
    $stmtLangs->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtLangs->execute();
    $langs = $stmtLangs->fetchAll(PDO::FETCH_COLUMN, 0);

    $hreflangMap = [
        'pt' => 'pt-BR',
        'en' => 'en',
        'es' => 'es',
        'de' => 'de'
    ];
?>
    <url>
        <loc><?= htmlspecialchars($baseUrl); ?></loc>

        <?php foreach ($langs as $lang): ?>
            <xhtml:link rel="alternate" hreflang="<?= $hreflangMap[$lang] ?>" href="<?= htmlspecialchars($lang === 'pt' ? $baseUrl : $baseUrl . $lang . '/') ?>" />
        <?php endforeach; ?>

        <xhtml:link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($baseUrl); ?>" />

        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <?php foreach ($paginasDinamicas as $pagina): ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . "atendimento/" . $pagina['link']); ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($pagina['last_modification'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>

</urlset>