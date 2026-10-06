<?php

// ----------------------------------------------------------
// CONFIGURAÇÕES
// ----------------------------------------------------------
define('SITEMAP_LIMIT', 10000);

// ----------------------------------------------------------
// PÁGINA ATUAL
// ----------------------------------------------------------
$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

// ----------------------------------------------------------
// BUSCA shop_id
// ----------------------------------------------------------
$sql = "
    SELECT shop_id 
    FROM tb_domains 
    WHERE subdomain = :subdomain 
      AND domain = :domain
";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindValue(':subdomain', $subdomain);
$stmt->bindValue(':domain', $domain);
$stmt->execute();

$shop_id = $stmt->fetchColumn();

if (!$shop_id) {
    http_response_code(404);
    exit;
}

// ----------------------------------------------------------
// BUSCA CURSOR DA PÁGINA
// ----------------------------------------------------------
$sqlCursor = "
    SELECT
        start_id,
        end_id,
        products_count
    FROM tb_sitemap_product_pages
    WHERE shop_id = :shop_id
      AND page = :page
    LIMIT 1
";

$stmtCursor = $conn_pdo->prepare($sqlCursor);

$stmtCursor->bindValue(
    ':shop_id',
    $shop_id,
    PDO::PARAM_INT
);

$stmtCursor->bindValue(
    ':page',
    $page,
    PDO::PARAM_INT
);

$stmtCursor->execute();

$cursor = $stmtCursor->fetch(PDO::FETCH_ASSOC);

if (!$cursor) {
    http_response_code(404);
    exit;
}

$startId = (int) $cursor['start_id'];
$endId = (int) $cursor['end_id'];
$productsInPage = (int) $cursor['products_count'];

// ----------------------------------------------------------
// CABEÇALHOS
// ----------------------------------------------------------
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=21600, s-maxage=21600');
header('Pragma: public');

// Limpa qualquer output anterior
if (ob_get_length()) {
    ob_clean();
}

// ----------------------------------------------------------
// URL BASE
// ----------------------------------------------------------
$baseUrl = rtrim(INCLUDE_PATH_LOJA, '/') . '/';

// ----------------------------------------------------------
// XML
// ----------------------------------------------------------
echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<!-- Sitemap gerado por https://www.dropidigital.com.br/ -->
<!-- Total de produtos exibidos neste sitemap: <?= $productsInPage ?> -->
<!-- Página do sitemap: <?= $page ?> -->

<?php

// ----------------------------------------------------------
// BUSCA PRODUTOS POR CURSOR
// ----------------------------------------------------------
$sqlProds = "
    SELECT
        link,
        language,
        last_modification,
        date_create
    FROM tb_products FORCE INDEX (idx_products_shop_status_id)
    WHERE shop_id = :shop_id
      AND status = 1
      AND id >= :start_id
      AND id <= :end_id
    ORDER BY id ASC
    LIMIT :limit
";

$stmtProds = $conn_pdo->prepare($sqlProds);

$stmtProds->bindValue(
    ':shop_id',
    $shop_id,
    PDO::PARAM_INT
);

$stmtProds->bindValue(
    ':start_id',
    $startId,
    PDO::PARAM_INT
);

$stmtProds->bindValue(
    ':end_id',
    $endId,
    PDO::PARAM_INT
);

$stmtProds->bindValue(
    ':limit',
    SITEMAP_LIMIT,
    PDO::PARAM_INT
);

$stmtProds->execute();

while ($prod = $stmtProds->fetch(PDO::FETCH_ASSOC)) {

    $datetime = $prod['last_modification']
        ?: $prod['date_create'];

    if (!$datetime) {
        continue;
    }

    $lastmod = date(
        'Y-m-d\TH:i:sP',
        strtotime($datetime)
    );

    if ($prod['language'] === 'pt') {
        $url = $baseUrl . ltrim($prod['link'], '/');
    } else {
        $url = $baseUrl
            . $prod['language']
            . '/'
            . ltrim($prod['link'], '/');
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