<?php
// ----------------------------------------------------------
// CABEÇALHO XML
// ----------------------------------------------------------

// Garante que não exista output antes do XML
if (ob_get_length()) {
    ob_clean();
}

// Cabeçalhos corretos para sitemap
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=21600, s-maxage=21600');
header('Pragma: public');

echo '<?xml version="1.0" encoding="UTF-8"?>';

// Base URL absoluta
$baseUrl = rtrim(INCLUDE_PATH_LOJA, '/') . '/';

$shop_id = 2;

if (!$shop_id) {
    http_response_code(404);
    exit;
}
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php
// ----------------------------------------------------------
// 2) GERAÇÃO DE CATEGORIAS
// ----------------------------------------------------------
$sql = "
    SELECT link, last_modification, date_create
    FROM tb_categories
    WHERE shop_id = :shop_id
      AND status = 1
";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmt->execute();

while ($cat = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $datetime = $cat['last_modification'] ?: $cat['date_create'];
    if (!$datetime || empty($cat['link'])) {
        continue;
    }

    $lastmod = date('Y-m-d\TH:i:sP', strtotime($datetime));
    $url     = $baseUrl . "c/" . ltrim($cat['link'], '/');
    ?>
    <url>
        <loc><?= htmlspecialchars($url); ?></loc>
        <lastmod><?= $lastmod; ?></lastmod>
        <priority>0.70</priority>
    </url>
    <?php
}
?>

</urlset>