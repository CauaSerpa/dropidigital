<?php
$shop_id = 2;

// Verifica se o shop_id foi encontrado
if (!$shop_id) {
    die('<error>Shop ID não encontrado</error>');
}

// Nome da tabela para a busca
$tabela = 'tb_shop';

$sql = "SELECT * FROM $tabela WHERE id = :id";
// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $shop_id, PDO::PARAM_INT);

$stmt->execute();

// Recuperar os resultados
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se os dados da loja foram encontrados
if (!$shop) {
    die('<error>Dados da loja não encontrados</error>');
}

// Define o tipo de conteúdo como XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <?php
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
    if ($result && !empty($result['max_modification']) && !empty($result['max_create_item'])) {
        // Obtém a data de modificação e a data de criação mais recentes
        $last_modification = $result['max_modification'];
        $date_create_item = $result['max_create_item'];

        // Verifica qual das datas é a mais recente
        $recent_date = max($last_modification, $date_create_item);

        // Formata a data no formato desejado (ISO 8601)
        $last_modification = date_format(date_create($recent_date), 'Y-m-d\TH:i:sP');
    ?>
        <url>
            <loc><?= htmlspecialchars($urlCompleta); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>1.00</priority>
        </url>
    <?php
    }

    // Verifica e processa categorias, produtos e artigos somente se os dados necessários existirem
    $tabelas = ['tb_categories', 'tb_products', 'tb_articles'];
    foreach ($tabelas as $tabela) {
        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', 1, PDO::PARAM_INT);

        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $datetime = $item['last_modification'] ?? $item['date_create'];
            if (!empty($datetime)) {
                $last_modification = date_format(date_create($datetime), 'Y-m-d\TH:i:sP');
                $link = $item['link'] ?? null;
                if (!empty($link)) {
    ?>
        <url>
            <loc><?= $urlCompleta . htmlspecialchars($link); ?></loc>
            <lastmod><?= $last_modification; ?></lastmod>
            <priority>0.80</priority>
        </url>
    <?php
                }
            }
        }
    }
    ?>
</urlset>
