<?php
// ----------------------------------------------------------
// 1) BUSCA shop_id PELO DOMÍNIO
// ----------------------------------------------------------
$sql = "
    SELECT shop_id
    FROM tb_domains
    WHERE subdomain = :subdomain
      AND domain = :domain
    LIMIT 1
";

$stmt = $conn_pdo->prepare($sql);
$stmt->bindValue(':subdomain', $subdomain, PDO::PARAM_STR);
$stmt->bindValue(':domain', $domain, PDO::PARAM_STR);
$stmt->execute();

$shop_id = $stmt->fetchColumn();

if (!$shop_id) {
    die('<error>Shop ID não encontrado</error>');
}

$shop_id = (int) $shop_id;

// ----------------------------------------------------------
// 2) BUSCA DADOS DA SHOP
// ----------------------------------------------------------
$sql = "
    SELECT *
    FROM tb_shop
    WHERE id = :id
    LIMIT 1
";

$stmt = $conn_pdo->prepare($sql);
$stmt->bindValue(':id', $shop_id, PDO::PARAM_INT);
$stmt->execute();

$shop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop) {
    die('<error>Dados da loja não encontrados</error>');
}

// ----------------------------------------------------------
// 3) CONTAGEM TOTAL DE PRODUTOS ATIVOS
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
// 4) CABEÇALHO DO XML
// ----------------------------------------------------------
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    <!-- Sitemap gerado por DropiDigital -->
    <!-- Produtos listados: <?= $totalProducts; ?> -->

    <?php
    // ----------------------------------------------------------
    // 5) DATA DE lastmod DA PÁGINA PRINCIPAL DA SHOP
    // ----------------------------------------------------------
    // IMPORTANTE:
    // Não fazemos JOIN entre produtos, categorias e artigos.
    // Cada tabela é consultada separadamente para evitar
    // multiplicação de registros.
    // ----------------------------------------------------------

    $sqlLastmod = "
        SELECT
            GREATEST(
                COALESCE(
                    (SELECT MAX(last_modification)
                     FROM tb_shop
                     WHERE id = :shop_id_shop_mod),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(last_modification)
                     FROM tb_products
                     WHERE shop_id = :shop_id_products_mod),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(last_modification)
                     FROM tb_categories
                     WHERE shop_id = :shop_id_categories_mod),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(last_modification)
                     FROM tb_articles
                     WHERE shop_id = :shop_id_articles_mod),
                    '0000-00-00 00:00:00'
                )
            ) AS max_modification,

            GREATEST(
                COALESCE(
                    (SELECT MAX(date_create)
                     FROM tb_shop
                     WHERE id = :shop_id_shop_create),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(date_create)
                     FROM tb_products
                     WHERE shop_id = :shop_id_products_create),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(date_create)
                     FROM tb_categories
                     WHERE shop_id = :shop_id_categories_create),
                    '0000-00-00 00:00:00'
                ),
                COALESCE(
                    (SELECT MAX(date_create)
                     FROM tb_articles
                     WHERE shop_id = :shop_id_articles_create),
                    '0000-00-00 00:00:00'
                )
            ) AS max_create_item
    ";

    $stmt = $conn_pdo->prepare($sqlLastmod);

    $stmt->bindValue(':shop_id_shop_mod', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_products_mod', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_categories_mod', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_articles_mod', $shop_id, PDO::PARAM_INT);

    $stmt->bindValue(':shop_id_shop_create', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_products_create', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_categories_create', $shop_id, PDO::PARAM_INT);
    $stmt->bindValue(':shop_id_articles_create', $shop_id, PDO::PARAM_INT);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $dates = array_filter([
            $result['max_modification'] ?? null,
            $result['max_create_item'] ?? null
        ]);

        if (!empty($dates)) {
            $last_modification_raw = max($dates);

            if ($last_modification_raw !== '0000-00-00 00:00:00') {
                $dateObject = date_create($last_modification_raw);

                if ($dateObject) {
                    $last_modification = $dateObject->format('Y-m-d\TH:i:sP');
                    ?>
                    <url>
                        <loc><?= htmlspecialchars($urlCompleta, ENT_XML1, 'UTF-8'); ?></loc>
                        <lastmod><?= $last_modification; ?></lastmod>
                        <priority>1.00</priority>
                    </url>
                    <?php
                }
            }
        }
    }

    // ----------------------------------------------------------
    // 6) BUSCA IDIOMAS DISPONÍVEIS
    // ----------------------------------------------------------
    $sqlLangs = "
        SELECT DISTINCT language
        FROM tb_products
        WHERE shop_id = :shop_id
          AND status = 1
          AND language IS NOT NULL
          AND language <> ''
        ORDER BY
            CASE
                WHEN language = 'pt' THEN 1
                WHEN language = 'en' THEN 2
                WHEN language = 'de' THEN 3
                ELSE 4
            END,
            language ASC
    ";

    $stmtLangs = $conn_pdo->prepare($sqlLangs);
    $stmtLangs->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtLangs->execute();

    $langs = $stmtLangs->fetchAll(PDO::FETCH_COLUMN);

    $singleLang = count($langs) === 1;

    // ----------------------------------------------------------
    // 6.1) GERAÇÃO DOS PRODUTOS
    // ----------------------------------------------------------
    foreach ($langs as $lang) {
        $prefix = $singleLang ? '' : ($lang . '/');

        $sqlProds = "
            SELECT
                link,
                last_modification,
                date_create
            FROM tb_products
            WHERE shop_id = :shop_id
              AND status = 1
              AND language = :lang
            ORDER BY id ASC
        ";

        $stmtProds = $conn_pdo->prepare($sqlProds);
        $stmtProds->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmtProds->bindValue(':lang', $lang, PDO::PARAM_STR);
        $stmtProds->execute();

        // Não usamos fetchAll().
        // O resultado é processado registro por registro.
        while ($prod = $stmtProds->fetch(PDO::FETCH_ASSOC)) {
            $datetime = $prod['last_modification'] ?: $prod['date_create'];

            if (empty($datetime) || empty($prod['link'])) {
                continue;
            }

            $dateObject = date_create($datetime);

            if (!$dateObject) {
                continue;
            }

            $last_mod = $dateObject->format('Y-m-d\TH:i:sP');

            $slug = $prod['link'];

            $url = rtrim($urlCompleta, '/') . '/' .
                   $prefix .
                   ltrim($slug, '/');
            ?>
            <url>
                <loc><?= htmlspecialchars($url, ENT_XML1, 'UTF-8'); ?></loc>
                <lastmod><?= $last_mod; ?></lastmod>
                <priority>0.80</priority>
            </url>
            <?php
        }

        // Libera o statement antes de passar para o próximo idioma.
        $stmtProds->closeCursor();
    }

    // ----------------------------------------------------------
    // 7) GERAÇÃO DE CATEGORIAS
    // ----------------------------------------------------------
    $sqlCategories = "
        SELECT
            link,
            last_modification,
            date_create
        FROM tb_categories
        WHERE shop_id = :shop_id
          AND status = 1
        ORDER BY id ASC
    ";

    $stmtCategories = $conn_pdo->prepare($sqlCategories);
    $stmtCategories->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtCategories->execute();

    while ($item = $stmtCategories->fetch(PDO::FETCH_ASSOC)) {
        $datetime = $item['last_modification'] ?: $item['date_create'];

        if (empty($datetime) || empty($item['link'])) {
            continue;
        }

        $dateObject = date_create($datetime);

        if (!$dateObject) {
            continue;
        }

        $last_mod = $dateObject->format('Y-m-d\TH:i:sP');

        $urlItem = rtrim($urlCompleta, '/') . '/' . ltrim($item['link'], '/');
        ?>
        <url>
            <loc><?= htmlspecialchars($urlItem, ENT_XML1, 'UTF-8'); ?></loc>
            <lastmod><?= $last_mod; ?></lastmod>
            <priority>0.80</priority>
        </url>
        <?php
    }

    $stmtCategories->closeCursor();

    // ----------------------------------------------------------
    // 8) GERAÇÃO DE ARTIGOS
    // ----------------------------------------------------------
    $sqlArticles = "
        SELECT
            link,
            last_modification,
            date_create
        FROM tb_articles
        WHERE shop_id = :shop_id
          AND status = 1
        ORDER BY id ASC
    ";

    $stmtArticles = $conn_pdo->prepare($sqlArticles);
    $stmtArticles->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtArticles->execute();

    while ($item = $stmtArticles->fetch(PDO::FETCH_ASSOC)) {
        $datetime = $item['last_modification'] ?: $item['date_create'];

        if (empty($datetime) || empty($item['link'])) {
            continue;
        }

        $dateObject = date_create($datetime);

        if (!$dateObject) {
            continue;
        }

        $last_mod = $dateObject->format('Y-m-d\TH:i:sP');

        $urlItem = rtrim($urlCompleta, '/') . '/' . ltrim($item['link'], '/');
        ?>
        <url>
            <loc><?= htmlspecialchars($urlItem, ENT_XML1, 'UTF-8'); ?></loc>
            <lastmod><?= $last_mod; ?></lastmod>
            <priority>0.80</priority>
        </url>
        <?php
    }

    $stmtArticles->closeCursor();
    ?>

</urlset>
