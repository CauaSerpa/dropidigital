<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['shop_id'], [2, 25, 61])) {
        header('Location: ' . INCLUDE_PATH_DASHBOARD);
        exit;
    }

    /*
     * Buscar produtos
     */
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'searchProducts') {
        $search = trim($_GET['search'] ?? '');

        if (mb_strlen($search) < 2) {
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => true,
                'products' => []
            ]);

            exit;
        }

        $shopId = $_SESSION['shop_id'];

        // Domínio da loja
        $stmtDomain = $conn_pdo->prepare("
            SELECT
                subdomain,
                domain
            FROM tb_domains
            WHERE shop_id = :shop_id
            ORDER BY
                (domain = 'dropidigital.com.br') ASC,
                id ASC
            LIMIT 1
        ");

        $stmtDomain->bindValue(':shop_id', $shopId, PDO::PARAM_INT);
        $stmtDomain->execute();

        $domain = $stmtDomain->fetch(PDO::FETCH_ASSOC);

        if (!empty($domain)) {
            $subdomain = (
                !empty($domain['subdomain']) &&
                $domain['subdomain'] !== 'www'
            )
                ? $domain['subdomain'] . '.'
                : '';

            $storeUrl = 'https://www.' . $subdomain . $domain['domain'];
        } else {
            $storeUrl = '';
        }

        // Consultar produtos
        $sql = "SELECT
            p.id,
            p.name,
            p.sku,
            p.link,
            img.nome_imagem AS product_image

        FROM tb_products p

        LEFT JOIN (
            SELECT
                usuario_id,
                MIN(id) AS image_id
            FROM imagens
            GROUP BY usuario_id
        ) first_img
            ON first_img.usuario_id = p.id

        LEFT JOIN imagens img
            ON img.id = first_img.image_id

        WHERE p.shop_id = :shop_id
        AND (
            p.name LIKE :search
            OR p.sku LIKE :search
            OR p.id = :id
        )

        ORDER BY p.name ASC
        LIMIT 10";

        $stmt = $conn_pdo->prepare($sql);

        $searchLike = '%' . $search . '%';

        $stmt->bindValue(':shop_id', $shopId);
        $stmt->bindValue(':search', $searchLike);
        $stmt->bindValue(':id', is_numeric($search) ? (int) $search : 0, PDO::PARAM_INT);

        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $productId = (int) $product['id'];

            if (!empty($product['product_image'])) {
                $product['product_image'] =
                    CDN_BASE_URL .
                    'products/' .
                    $productId .
                    '/' .
                    $product['product_image'];
            } else {
                $product['product_image'] =
                    INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg';
            }

            if (!empty($storeUrl) && !empty($product['link'])) {
                $product['product_url'] = rtrim($storeUrl, '/') . '/' . ltrim($product['link'], '/');
            } else {
                $product['product_url'] = '';
            }
        }

        unset($product);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'products' => $products
        ]);

        exit;
    }

    /*
    * Criar geração de artigos
    */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        header('Content-Type: application/json; charset=utf-8');

        $shopId = (int) $_SESSION['shop_id'];
        $userId = (int) $_SESSION['user_id'];

        $articlesQuantity = (int) ($_POST['articles_quantity'] ?? 0);

        $keywords = trim($_POST['keywords'] ?? '');
        $tone = trim($_POST['tone'] ?? 'informative');
        $articleSize = trim($_POST['article_size'] ?? 'medium');
        $publishMode = trim($_POST['publish_mode'] ?? 'draft');
        $customPrompt = trim($_POST['custom_prompt'] ?? '');

        $generateMeta = isset($_POST['generate_meta']) ? 1 : 0;
        $includeProducts = isset($_POST['include_products']) ? 1 : 0;

        $allowedTones = [
            'professional',
            'informative',
            'commercial',
            'casual'
        ];

        $allowedSizes = [
            'short',
            'medium',
            'long'
        ];

        $allowedPublishModes = [
            'draft',
            'published'
        ];

        if ($articlesQuantity < 1) {
            echo json_encode([
                'success' => false,
                'message' => 'A quantidade de artigos deve ser maior que 1.'
            ]);

            exit;
        }

        if (!in_array($tone, $allowedTones, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tom de conteúdo inválido.'
            ]);

            exit;
        }

        if (!in_array($articleSize, $allowedSizes, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tamanho de artigo inválido.'
            ]);

            exit;
        }

        if (!in_array($publishMode, $allowedPublishModes, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Modo de publicação inválido.'
            ]);

            exit;
        }

        $productIds = $_POST['product_ids'] ?? [];

        if (!is_array($productIds)) {
            $productIds = [];
        }

        $productIds = array_values(
            array_unique(
                array_filter(
                    array_map('intval', $productIds),
                    fn ($id) => $id > 0
                )
            )
        );

        if ($includeProducts && empty($productIds)) {
            echo json_encode([
                'success' => false,
                'message' => 'Selecione pelo menos um produto para utilizar como recomendação.'
            ]);

            exit;
        }

        /*
        * Títulos personalizados
        */
        $titles = [];

        if (
            isset($_FILES['titles']) &&
            $_FILES['titles']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($_FILES['titles']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Não foi possível enviar o arquivo de títulos.'
                ]);

                exit;
            }

            $extension = strtolower(
                pathinfo($_FILES['titles']['name'], PATHINFO_EXTENSION)
            );

            if (!in_array($extension, ['txt', 'csv'], true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'O arquivo de títulos deve estar no formato TXT ou CSV.'
                ]);

                exit;
            }

            $content = file_get_contents($_FILES['titles']['tmp_name']);

            if ($content === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Não foi possível ler o arquivo de títulos.'
                ]);

                exit;
            }

            $lines = preg_split('/\r\n|\r|\n/', $content);

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                if ($extension === 'csv') {
                    $columns = str_getcsv($line);
                    $line = trim($columns[0] ?? '');
                }

                if ($line !== '') {
                    $titles[] = $line;
                }
            }

            if (count($titles) > $articlesQuantity) {
                $titles = array_slice($titles, 0, $articlesQuantity);
            }
        }

        try {
            $conn_pdo->beginTransaction();

            /*
            * Cria a geração
            */
            $stmtJob = $conn_pdo->prepare("
                INSERT INTO tb_seo_article_jobs (
                    shop_id,
                    user_id,
                    articles_quantity,
                    keywords,
                    tone,
                    article_size,
                    generate_meta,
                    include_products,
                    publish_mode,
                    custom_prompt,
                    status
                ) VALUES (
                    :shop_id,
                    :user_id,
                    :articles_quantity,
                    :keywords,
                    :tone,
                    :article_size,
                    :generate_meta,
                    :include_products,
                    :publish_mode,
                    :custom_prompt,
                    'pending'
                )
            ");

            $stmtJob->execute([
                ':shop_id' => $shopId,
                ':user_id' => $userId,
                ':articles_quantity' => $articlesQuantity,
                ':keywords' => $keywords ?: null,
                ':tone' => $tone,
                ':article_size' => $articleSize,
                ':generate_meta' => $generateMeta,
                ':include_products' => $includeProducts,
                ':publish_mode' => $publishMode,
                ':custom_prompt' => $customPrompt ?: null
            ]);

            $jobId = (int) $conn_pdo->lastInsertId();

            /*
            * Valida os produtos e salva as relações
            */
            if ($includeProducts && !empty($productIds)) {
                $placeholders = implode(
                    ',',
                    array_fill(0, count($productIds), '?')
                );

                $stmtProducts = $conn_pdo->prepare("
                    SELECT id
                    FROM tb_products
                    WHERE shop_id = ?
                    AND id IN ($placeholders)
                ");

                $stmtProducts->execute([
                    $shopId,
                    ...$productIds
                ]);

                $validProductIds = $stmtProducts->fetchAll(PDO::FETCH_COLUMN);

                if (count($validProductIds) !== count($productIds)) {
                    throw new Exception(
                        'Um ou mais produtos selecionados não pertencem à loja.'
                    );
                }

                $stmtJobProduct = $conn_pdo->prepare("
                    INSERT INTO tb_seo_article_job_products (
                        job_id,
                        product_id
                    ) VALUES (
                        :job_id,
                        :product_id
                    )
                ");

                foreach ($validProductIds as $productId) {
                    $stmtJobProduct->execute([
                        ':job_id' => $jobId,
                        ':product_id' => (int) $productId
                    ]);
                }
            }

            /*
            * Cria os artigos na fila
            */
            $stmtQueue = $conn_pdo->prepare("
                INSERT INTO tb_seo_article_queue (
                    job_id,
                    title,
                    status
                ) VALUES (
                    :job_id,
                    :title,
                    'pending'
                )
            ");

            for ($i = 0; $i < $articlesQuantity; $i++) {
                $title = $titles[$i] ?? null;

                $stmtQueue->execute([
                    ':job_id' => $jobId,
                    ':title' => $title
                ]);
            }

            $conn_pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Artigos adicionados à fila com sucesso.',
                'job_id' => $jobId,
                'articles_quantity' => $articlesQuantity
            ]);

            exit;

        } catch (Throwable $e) {
            if ($conn_pdo->inTransaction()) {
                $conn_pdo->rollBack();
            }

            echo json_encode([
                'success' => false,
                'message' => 'Não foi possível adicionar os artigos à fila.',
                'error' => $e->getMessage()
            ]);

            exit;
        }
    }