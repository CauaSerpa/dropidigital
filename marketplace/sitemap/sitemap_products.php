<?php
// ----------------------------------------------------------
// CONFIGURAÇÕES
// ----------------------------------------------------------
define('SITEMAP_LIMIT', 10000);

// Limpa qualquer output anterior
if (ob_get_length()) {
    ob_clean();
}

// Cabeçalhos corretos
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=21600');
header('Pragma: public');

// XML declaration
echo '<?xml version="1.0" encoding="UTF-8"?>';

// URL base
$baseUrl = rtrim(INCLUDE_PATH_LOJA, '/') . '/';

// Página atual
$page   = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * SITEMAP_LIMIT;

$shop_id = 2;

if (!$shop_id) {
    http_response_code(404);
    exit;
}

// ----------------------------------------------------------
// TOTAL DE PRODUTOS CADASTRADOS
// ----------------------------------------------------------
$sqlCount = "
    SELECT COUNT(*) 
    FROM tb_products
    WHERE shop_id = :shop_id
      AND status = 1
";
$stmtCount = $conn_pdo->prepare($sqlCount);
$stmtCount->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
$stmtCount->execute();
$totalProducts = (int) $stmtCount->fetchColumn();

// ----------------------------------------------------------
// BUSCA PRODUTOS PAGINADOS
// ----------------------------------------------------------
// $sqlProds = "
//     SELECT link, last_modification, date_create
//     FROM tb_products
//     WHERE shop_id = :shop_id
//       AND status = 1
//     ORDER BY id ASC
//     LIMIT :limit OFFSET :offset
// ";

// $stmtProds = $conn_pdo->prepare($sqlProds);
// $stmtProds->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
// $stmtProds->bindValue(':limit', SITEMAP_LIMIT, PDO::PARAM_INT);
// $stmtProds->bindValue(':offset', $offset, PDO::PARAM_INT);
// $stmtProds->execute();

// Contador do sitemap atual
$totalListed = 0;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<!-- Sitemap gerado por https://www.dropidigital.com.br/ -->
<!-- Total de produtos cadastrados: <?= $totalProducts ?> -->
<!-- Total de produtos exibidos neste sitemap: <?= min(SITEMAP_LIMIT, max(0, $totalProducts - $offset)) ?> -->
<!-- Página do sitemap: <?= $page ?> -->

<?php
// ----------------------------------------------------------
// BUSCA PRODUTOS PAGINADOS
// ----------------------------------------------------------
$sqlProds = "
    SELECT link, language, last_modification, date_create
    FROM tb_products
    WHERE shop_id = :shop_id
      AND status = 1
    ORDER BY 
        CASE language
            WHEN 'pt' THEN 1
            WHEN 'en' THEN 2
            WHEN 'de' THEN 3
            WHEN 'es' THEN 4
            ELSE 5
        END,
        id ASC
    LIMIT :limit OFFSET :offset
";

$stmtProds = $conn_pdo->prepare($sqlProds);
$stmtProds->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
$stmtProds->bindValue(':limit', SITEMAP_LIMIT, PDO::PARAM_INT);
$stmtProds->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmtProds->execute();

while ($prod = $stmtProds->fetch(PDO::FETCH_ASSOC)) {

    $datetime = $prod['last_modification'] ?: $prod['date_create'];
    if (!$datetime) continue;

    $lastmod = date('Y-m-d\TH:i:sP', strtotime($datetime));
    if ($prod['language'] == 'pt') {
        $url = $baseUrl . ltrim($prod['link'], '/');
    } else {
        $url = $baseUrl . $prod['language'] . '/' . ltrim($prod['link'], '/');
    }
?>
    <url>
        <loc><?= htmlspecialchars($url, ENT_XML1) ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <priority>0.80</priority>
    </url>
    <?php
}
?>
</urlset>