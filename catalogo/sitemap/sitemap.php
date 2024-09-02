<?php
// Aumentar limite de memória e tempo de execução
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300); // 300 segundos = 5 minutos

// Pesquisar domínio
$shop_id = 2;

// Define o tipo de conteúdo como XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php
    // Preparar consultas SQL
    $sqlShop = "SELECT last_modification, date_create FROM tb_shop WHERE id = :shop_id";
    $sqlCategories = "SELECT link, last_modification, date_create FROM tb_categories WHERE shop_id = :shop_id AND status = 1";
    $sqlProducts = "SELECT link, last_modification, date_create FROM tb_products WHERE shop_id = :shop_id AND status = 1";
    $sqlArticlesRecent = "SELECT last_modification, date_create FROM tb_articles WHERE shop_id = :shop_id AND status = 1 ORDER BY last_modification DESC, date_create DESC LIMIT 1";
    $sqlArticles = "SELECT link, last_modification, date_create FROM tb_articles WHERE shop_id = :shop_id AND status = 1";

    // Preparar e executar a consulta para a loja
    $stmt = $conn_pdo->prepare($sqlShop);
    $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt->execute();
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($shop) {
        $last_modification = max($shop['last_modification'], $shop['date_create']);
        $last_modification = date_format(date_create($last_modification), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>1.00</priority>
        </url>
    <?php
        // Consultar e exibir categorias
        $stmt = $conn_pdo->prepare($sqlCategories);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as $category) {
            $datetime = !empty($category['last_modification']) ? $category['last_modification'] : $category['date_create'];
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta . "categoria/" . $category['link']); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        }

        // Consultar e exibir produtos
        $stmt = $conn_pdo->prepare($sqlProducts);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $datetime = !empty($product['last_modification']) ? $product['last_modification'] : $product['date_create'];
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta . $product['link']); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        }

        // Consultar o artigo mais recente
        $stmt = $conn_pdo->prepare($sqlArticlesRecent);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $articleRecent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($articleRecent) {
            $last_modification = max($articleRecent['last_modification'], $articleRecent['date_create']);
            $last_modification = date_format(date_create($last_modification), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta . "blog/"); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        }

        // Consultar e exibir artigos
        $stmt = $conn_pdo->prepare($sqlArticles);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($articles as $article) {
            $datetime = !empty($article['last_modification']) ? $article['last_modification'] : $article['date_create'];
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta . "blog/" . $article['link']); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.64</priority>
        </url>
    <?php
        }
    }
    ?>
</urlset>
