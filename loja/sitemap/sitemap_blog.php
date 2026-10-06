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

// ----------------------------------------------------------
// 1) BUSCA shop_id PELO DOMÍNIO
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
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php
// ----------------------------------------------------------
// 2) GERAÇÃO DOS ARTIGOS DO BLOG
// ----------------------------------------------------------
$sql = "
    SELECT link, last_modification, date_create
    FROM tb_articles
    WHERE shop_id = :shop_id
      AND status = 1
";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmt->execute();

while ($article = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $datetime = $article['last_modification'] ?: $article['date_create'];
    if (!$datetime || empty($article['link'])) {
        continue;
    }

    $lastmod = date('Y-m-d\TH:i:sP', strtotime($datetime));
    $url     = $baseUrl . "blog/" . ltrim($article['link'], '/');
    ?>
    <url>
        <loc><?= htmlspecialchars($url); ?></loc>
        <lastmod><?= $lastmod; ?></lastmod>
        <priority>0.64</priority>
    </url>
    <?php
}
?>

</urlset>