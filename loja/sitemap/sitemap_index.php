<?php
// ----------------------------------------------------------
// 1) BUSCA shop_id PELO DOMÍNIO
// ----------------------------------------------------------
$tabela = "tb_domains";
$sql = "SELECT shop_id FROM $tabela WHERE subdomain = :subdomain AND domain = :domain";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':subdomain', $subdomain, PDO::PARAM_STR);
$stmt->bindParam(':domain', $domain, PDO::PARAM_STR);
$stmt->execute();
$shopData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shopData || empty($shopData['shop_id'])) {
    http_response_code(404);
    exit;
}

$shop_id = (int) $shopData['shop_id'];

// ----------------------------------------------------------
// 2) BUSCA DADOS DA SHOP
// ----------------------------------------------------------
$tabela = 'tb_shop';
$sql = "SELECT * FROM $tabela WHERE id = :id";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $shop_id, PDO::PARAM_INT);
$stmt->execute();
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop) {
    http_response_code(404);
    exit;
}

// ----------------------------------------------------------
// 2) CONTA TOTAL DE PRODUTOS
// ----------------------------------------------------------
$sqlCount = "
    SELECT COUNT(*) AS total
    FROM tb_products
    WHERE shop_id = :shop_id
      AND status = 1
";
$stmtCount = $conn_pdo->prepare($sqlCount);
$stmtCount->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmtCount->execute();
$totalProducts = (int) $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

// ----------------------------------------------------------
// 3) CONTA TOTAL DE ARTIGOS
// ----------------------------------------------------------
$sqlCount = "
    SELECT COUNT(*) AS total
    FROM tb_articles
    WHERE shop_id = :shop_id
      AND status = 1
";
$stmtCount = $conn_pdo->prepare($sqlCount);
$stmtCount->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmtCount->execute();
$totalArticles = (int) $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

// ----------------------------------------------------------
// 4) CABEÇALHO XML (SEM ESPAÇOS ANTES)
// ----------------------------------------------------------

// Garante que não exista output antes do XML
if (ob_get_length()) {
    ob_clean();
}

// Cabeçalhos corretos para sitemap
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=86400');
header('Pragma: public');

// Garante URL absoluta
$baseUrl = rtrim(INCLUDE_PATH_LOJA, '/') . '/';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <sitemap>
        <loc><?= htmlspecialchars($baseUrl . 'sitemap_pages.xml'); ?></loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
    </sitemap>

    <?php if ($totalProducts < 10000) { ?>
        <sitemap>
            <loc><?= htmlspecialchars($baseUrl . 'sitemap_products.xml'); ?></loc>
            <lastmod><?= date('Y-m-d'); ?></lastmod>
        </sitemap>
    <?php } else { ?>
        <?php
            $limit = 10000;
            $page  = 1;
            $totalPages = ceil($totalProducts / $limit);
            
            while ($page <= $totalPages) {
        ?>
            <sitemap>
                <loc><?= htmlspecialchars($baseUrl . 'sitemap_products_' . $page . '.xml'); ?></loc>
                <lastmod><?= date('Y-m-d'); ?></lastmod>
            </sitemap>
        <?php
                $page++;
            }
        ?>
    <?php } ?>

    <sitemap>
        <loc><?= htmlspecialchars($baseUrl . 'sitemap_categories.xml'); ?></loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
    </sitemap>

    <?php if ($totalArticles > 0) { ?>
    <sitemap>
        <loc><?= htmlspecialchars($baseUrl . 'sitemap_blog.xml'); ?></loc>
        <lastmod><?= date('Y-m-d'); ?></lastmod>
    </sitemap>
    <?php } ?>

</sitemapindex>