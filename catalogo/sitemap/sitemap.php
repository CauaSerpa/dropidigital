<?php
// Pesquisar dominio
$shop_id = 2;

// Nome da tabela para a busca
$tabela = 'tb_shop';

$sql = "SELECT * FROM $tabela WHERE id = :id";
// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $shop_id, PDO::PARAM_INT);

$stmt->execute();

// Recuperar os resultados
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

// Define o tipo de conteúdo como XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php
    if ($shop) {
        // Preparar a consulta SQL
        $sql = "
            SELECT 
                GREATEST(
                    IFNULL(MAX(shop.last_modification), '0000-00-00 00:00:00'),
                    IFNULL(MAX(product.last_modification), '0000-00-00 00:00:00'),
                    IFNULL(MAX(categories.last_modification), '0000-00-00 00:00:00'),
                    IFNULL(MAX(articles.last_modification), '0000-00-00 00:00:00')
                ) AS max_modification,
                GREATEST(
                    IFNULL(MAX(shop.date_create), '0000-00-00 00:00:00'),
                    IFNULL(MAX(product.date_create), '0000-00-00 00:00:00'),
                    IFNULL(MAX(categories.date_create), '0000-00-00 00:00:00'),
                    IFNULL(MAX(articles.date_create), '0000-00-00 00:00:00')
                ) AS max_create_item
            FROM 
                tb_shop AS shop
            LEFT JOIN 
                tb_products AS product ON shop.id = product.shop_id
            LEFT JOIN 
                tb_categories AS categories ON shop.id = categories.shop_id
            LEFT JOIN 
                tb_articles AS articles ON shop.id = articles.shop_id
            WHERE
                shop.id = :shop_id
        ";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();

        // Recuperar os resultados
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se os resultados foram encontrados
        if ($result) {
            // Obtém a data de modificação e a data de criação mais recentes
            $last_modification = $result['max_modification'];
            $date_create_item = $result['max_create_item'];

            // Verifica qual das datas é a mais recente
            $recent_date = max($last_modification, $date_create_item);

            // Formata a data no formato desejado (ISO 8601)
            $last_modification = date_format(date_create($recent_date), 'Y-m-d\TH:i:sP');
        }
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>1.00</priority>
        </url>
    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_categories';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status";
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', 1, PDO::PARAM_INT);

        $stmt->execute();

        // Recuperar os resultados
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as $category) {
            // Verifica se o campo last_modification está vazio
            $datetime = !empty($category['last_modification']) ? $category['last_modification'] : $category['date_create'];

            // Formata a data no formato desejado (ISO 8601)
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= $urlCompleta . $category['link']; ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        }

        // Nome da tabela para a busca
        $tabela = 'tb_products';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status";
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', 1, PDO::PARAM_INT);

        $stmt->execute();

        // Recuperar os resultados
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            // Verifica se o campo last_modification está vazio
            $datetime = !empty($product['last_modification']) ? $product['last_modification'] : $product['date_create'];

            // Formata a data no formato desejado (ISO 8601)
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= $urlCompleta . $product['link']; ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        }

        // Nome da tabela para a busca
        $tabela = 'tb_articles';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY last_modification DESC, date_create DESC LIMIT 1";
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', 1, PDO::PARAM_INT);

        $stmt->execute();

        // Recuperar o produto mais recente
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o produto foi encontrado
        if ($article) {
            // Obtém a data de modificação mais recente ou a data de criação do produto mais recente
            $last_modification = $article['last_modification'];
            $date_create = $article['date_create'];

            // Verifica qual das datas é a mais recente
            $recent_date = $last_modification > $date_create ? $last_modification : $date_create;

            // Formata a data no formato desejado (ISO 8601)
            $last_modification = date_format(date_create($recent_date), 'Y-m-d\TH:i:sP');
        }
    ?>
        <url>
            <loc><?= $urlCompleta . "blog/"; ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_articles';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status";
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', 1, PDO::PARAM_INT);

        $stmt->execute();

        // Recuperar os resultados
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($articles as $article) {
            // Verifica se o campo last_modification está vazio
            $datetime = !empty($article['last_modification']) ? $article['last_modification'] : $article['date_create'];

            // Formata a data no formato desejado (ISO 8601)
            $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= $urlCompleta . "blog/" . $article['link']; ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.64</priority>
        </url>
    <?php
        }
    }
    ?>
</urlset>