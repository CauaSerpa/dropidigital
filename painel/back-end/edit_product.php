<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

    use Aws\S3\S3Client;

    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
    ]);

    $bucket = $_ENV['AWS_BUCKET'];

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT plan_id FROM $tabela WHERE (status = :status OR status = :status1) AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindValue(':status', 'ACTIVE');
    $stmt->bindValue(':status1', 'RECEIVED');
    $stmt->bindParam(':shop_id', $dados['shop_id']);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    $plan_id = (isset($plan['plan_id'])) ? $plan['plan_id'] : 1;

    // Pesquisar plano da Loja
    $tabela = "tb_plans_interval";

    // Consulta SQL para obter o plano da loja
    $sql = "SELECT plan_id FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);
    $stmt->execute();
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pesquisar produtos
    $tabela = "tb_products";

    // Conta o número de produtos ativos
    $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela
                    WHERE shop_id = :shop_id AND status = :status";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $dados['shop_id']);
    $stmt->bindValue(':status', 1);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Define os limites de produtos com base no plano
    $limitProductsMap = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 900,
        // 5 => 5000,
        5 => 0,
    ];

    $limitProducts = $limitProductsMap[$shop['plan_id']] ?? "ilimitado";

    // Define o status com base nos limites de produtos
    // $status = ($limitProducts <= $product['total_produtos']) ? 0 : (isset($_POST['status']) && $_POST['status'] == '1' ? $_POST['status'] : 0);
    $status = ($limitProducts === 0) ? 1
            : (
                ($limitProducts <= $product['total_produtos'])
                ? 0
                : (isset($_POST['status']) && $_POST['status'] === '1' ? 1 : 0)
            );

    if (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') {
        $emphasis = $_POST['emphasis'];
    } else {
        $emphasis = 0;
    }

    if ($_POST['button_type'] == 2) {
        $redirect_link = $dados['redirect_link_whatsapp_standard'];
    } else if ($_POST['button_type'] == 3) {
        $redirect_link = $dados['redirect_link_whatsapp'];
    } else {
        $redirect_link = $dados['redirect_link'];
    }

    // Checkbox sem preco
    if (isset($_POST["without_price"]))
    {
        $price = 0;
        $discount = 0;
        $without_price = 1;
    } else {
        $price = $dados['price'];
        $discount = $dados['discount'];
        $without_price = 0;
    }

    // Define o fuso horário para São Paulo (UTC-3)
    date_default_timezone_set('America/Sao_Paulo');

    // Cria um objeto DateTime com a data e hora atual
    $datetime = new DateTime();
    
    // Formata o objeto DateTime como uma string
    $datetimeFormatted = $datetime->format('Y-m-d H:i:s');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_products';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET status = :status, emphasis = :emphasis, language = :language, name = :name, price = :price, without_price = :without_price, discount = :discount, video = :video, description = :description, sku = :sku, button_type = :button_type, redirect_link = :redirect_link, iframe = :iframe, product_mode_related = :product_mode_related, seo_name = :seo_name, link = :link, seo_description = :seo_description, last_modification = :last_modification WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':emphasis', $emphasis);
        $stmt->bindParam(':language', $dados['language']);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':without_price', $without_price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':video', $dados['video']);
        $stmt->bindParam(':description', $dados['description']);
        $stmt->bindParam(':sku', $dados['sku']);
        $stmt->bindParam(':button_type', $dados['button_type']);
        $stmt->bindParam(':redirect_link', $redirect_link);
        $stmt->bindParam(':iframe', $dados['iframe']);
        $stmt->bindParam(':product_mode_related', $dados['selectMode']);
        $stmt->bindParam(':seo_name', $dados['seo_name']);
        $stmt->bindParam(':link', $dados['seo_link']);
        $stmt->bindParam(':seo_description', $dados['seo_description']);
        $stmt->bindParam(':last_modification', $datetimeFormatted);

        // Id que sera editado
        $stmt->bindParam(':id', $dados['id']);

        $stmt->execute();



        // Tags
        if (!empty($_POST['tags'])) {
            // limpa tudo
            $sqlDelete = "DELETE FROM tb_product_tags WHERE product_id = :product_id";
            $stmtDelete = $conn_pdo->prepare($sqlDelete);
            $stmtDelete->execute([':product_id' => $dados['id']]);

            $sql = "INSERT INTO tb_product_tags (product_id, tag) VALUES (:product_id, :tag)";
            $stmt = $conn_pdo->prepare($sql);

            foreach ($_POST['tags'] as $tag) {
                $tag = trim(strtolower($tag));

                if ($tag == '') continue;

                $stmt->execute([
                    ':product_id' => $dados['id'],
                    ':tag' => $tag
                ]);
            }
        }


        
        // Remove FAQs antigos da categoria
        $sqlDeleteFAQ = "DELETE FROM tb_product_faqs WHERE product_id = :product_id";
        $stmtDel = $conn_pdo->prepare($sqlDeleteFAQ);
        $stmtDel->bindValue(':product_id', $dados['id']);
        $stmtDel->execute();

        if (isset($dados['faq_question'])) {
            $sqlFaq = "INSERT INTO tb_product_faqs (product_id, question, answer, position)
                       VALUES (:product_id, :question, :answer, :position)";
            $stmtFaq = $conn_pdo->prepare($sqlFaq);
        
            foreach ($dados['faq_question'] as $index => $question) {
        
                $answer = $dados['faq_answer'][$index];
        
                // evitar campos vazios
                if (trim($question) == "" || trim($answer) == "") continue;
        
                $stmtFaq->execute([
                    ':product_id' => $dados['id'],
                    ':question' => $question,
                    ':answer' => $answer,
                    ':position' => $index,
                ]);
            }
        }



        // Categorias
        // Recupera o valor do input hidden com o ID das categorias
        $categoriasInputValue = $_POST['categoriasSelecionadas'];

        // Certifique-se de que $categoriasInputValue é uma string
        if (is_array($categoriasInputValue)) {
            // Lógica para converter o array em uma string (se aplicável)
            // Isso pode variar dependendo de como os dados estão sendo enviados
            $categoriasInputValue = implode(',', $categoriasInputValue);
        }

        // Separa os IDs das categorias em um array
        $categoriasIds = explode(',', $categoriasInputValue);

        // Consulta SQL para recuperar as categorias existentes para o produto específico
        $sqlExistingCategories = "SELECT category_id FROM tb_product_categories WHERE shop_id = :shop_id AND product_id = :product_id";
        $stmtExistingCategories = $conn_pdo->prepare($sqlExistingCategories);
        $stmtExistingCategories->bindParam(':shop_id', $dados['shop_id']);
        $stmtExistingCategories->bindParam(':product_id', $dados['id']);
        $stmtExistingCategories->execute();

        // Recupera os IDs das categorias existentes
        $existingCategoryIds = $stmtExistingCategories->fetchAll(PDO::FETCH_COLUMN);

        // Insere as categorias que não estão presentes no banco de dados
        if (!empty($categoriasInputValue)) {
            foreach ($categoriasIds as $categoriaId) {
                // Certifique-se de validar e escapar os dados para evitar injeção de SQL
                $categoriaId = (int)$categoriaId;
    
                if (!in_array($categoriaId, $existingCategoryIds)) {
                    // Categoria não está presente no banco de dados, então insira
                    $main = ($dados['inputMainCategory'] == $categoriaId) ? 1 : 0;
    
                    $tabela = "tb_product_categories";
                    $sqlInsertCategory = "INSERT INTO $tabela (shop_id, product_id, category_id, main) VALUES (:shop_id, :product_id, :category_id, :main)";
                    $stmtInsertCategory = $conn_pdo->prepare($sqlInsertCategory);
                    $stmtInsertCategory->bindParam(':shop_id', $dados['shop_id']);
                    $stmtInsertCategory->bindParam(':product_id', $dados['id']);
                    $stmtInsertCategory->bindParam(':category_id', $categoriaId);
                    $stmtInsertCategory->bindParam(':main', $main);
                    $stmtInsertCategory->execute();
                }
            }
        }

        // Deleta as categorias que não estão mais presentes no input
        if (!empty($existingCategoryIds)) {
            foreach ($existingCategoryIds as $existingCategoryId) {
                if (!in_array($existingCategoryId, $categoriasIds)) {
                    // Categoria não está presente no input, então delete
                    $tabela = "tb_product_categories";
                    $sqlDeleteCategory = "DELETE FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id AND category_id = :category_id";
                    $stmtDeleteCategory = $conn_pdo->prepare($sqlDeleteCategory);
                    $stmtDeleteCategory->bindParam(':shop_id', $dados['shop_id']);
                    $stmtDeleteCategory->bindParam(':product_id', $dados['id']);
                    $stmtDeleteCategory->bindParam(':category_id', $existingCategoryId);
                    $stmtDeleteCategory->execute();
                }
            }
        }

        echo "sucesso";

        // Receber os IDs dos produtos selecionados e removidos do formulário
        $selectMode = $_POST['selectMode']; // "manual" ou "automatic"
        $selectedProducts = isset($_POST['produtos_selecionados']) ? explode(',', $_POST['produtos_selecionados']) : [];
        $removedProducts = isset($_POST['produtos_removidos']) ? explode(',', $_POST['produtos_removidos']) : [];

        // Caso o modo de seleção seja "automatic", remover todos os produtos relacionados
        if ($selectMode === 'automatic') {
            $sqlDeleteAll = "DELETE FROM tb_product_related WHERE product_id = ? AND shop_id = ?";
            $stmtDeleteAll = $conn_pdo->prepare($sqlDeleteAll);
            $stmtDeleteAll->execute([$dados['id'], $dados['shop_id']]);
        } else {
            // Modo "manual": realizar alterações nos produtos selecionados/removidos

            // Deletar produtos removidos
            if (!empty($removedProducts)) {
                $placeholders = implode(',', array_fill(0, count($removedProducts), '?'));
                $sqlDelete = "DELETE FROM tb_product_related 
                            WHERE product_id = ? AND shop_id = ? AND related_product_id IN ($placeholders)";
                $stmtDelete = $conn_pdo->prepare($sqlDelete);
                $stmtDelete->execute(array_merge([$dados['id'], $dados['shop_id']], $removedProducts));
            }

            // Adicionar novos produtos selecionados, verificando duplicação
            if (!empty($selectedProducts)) {
                foreach ($selectedProducts as $relatedProductId) {
                    // Verificar se o produto já está relacionado
                    $sqlCheck = "SELECT COUNT(*) FROM tb_product_related 
                                WHERE product_id = ? AND shop_id = ? AND related_product_id = ?";
                    $stmtCheck = $conn_pdo->prepare($sqlCheck);
                    $stmtCheck->execute([$dados['id'], $dados['shop_id'], $relatedProductId]);
                    $exists = $stmtCheck->fetchColumn();

                    // Se não existir, inserir no banco de dados
                    if (!$exists) {
                        $sqlInsert = "INSERT INTO tb_product_related (product_id, shop_id, related_product_id) 
                                    VALUES (?, ?, ?)";
                        $stmtInsert = $conn_pdo->prepare($sqlInsert);
                        $stmtInsert->execute([$dados['id'], $dados['shop_id'], $relatedProductId]);
                    }
                }
            }
        }

        echo "sucesso";

        // Deletar imagens
        if (!empty($_POST['delete_images'])) {
            $ids = array_map('intval', explode(',', $_POST['delete_images']));

            // Busca imagens
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn_pdo->prepare("
                SELECT id, s3_path
                FROM imagens
                WHERE id IN ($in)
            ");
            $stmt->execute($ids);

            $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($imagens as $img) {
                try {
                    // Remove do S3
                    $s3->deleteObject([
                        'Bucket' => $bucket,
                        'Key'    => $img['s3_path']
                    ]);
                } catch (Exception $e) {
                    // log opcional
                }

                // Remove do banco
                $conn_pdo->prepare("
                    DELETE FROM imagens WHERE id = :id
                ")->execute([
                    ':id' => $img['id']
                ]);
            }
        }

        // Recupere o ID do último registro inserido
        $product_id = (int) $dados['id'];

        $stmt = $conn_pdo->prepare("
            SELECT COUNT(*) 
            FROM imagens 
            WHERE usuario_id = :product_id
        ");
        $stmt->execute([':product_id' => $product_id]);
        $totalExistentes = (int) $stmt->fetchColumn();

        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $novas = count($_FILES['imagens']['name']);

            // Limite total por produto
            if (($totalExistentes + $novas) > MAX_PRODUCT_IMAGES) {
                $_SESSION['msg'] = "<p class='red'>
                    Limite máximo de " . MAX_PRODUCT_IMAGES . " imagens por produto.
                </p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=$product_id");
                exit;
            }

            for ($i = 0; $i < $novas; $i++) {

                $tmp  = $_FILES['imagens']['tmp_name'][$i];
                $size = $_FILES['imagens']['size'][$i];
                $name = $_FILES['imagens']['name'][$i];

                // Tamanho máximo
                if ($size > MAX_IMAGE_SIZE) {
                    $_SESSION['msg'] = "<p class='red'>
                        A imagem {$name} excede o tamanho permitido.
                    </p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=$product_id");
                    exit;
                }

                // MIME real
                $mime = mime_content_type($tmp);
                $allowed = ['image/jpeg', 'image/png', 'image/webp'];

                if (!in_array($mime, $allowed)) {
                    $_SESSION['msg'] = "<p class='red'>
                        Formato inválido: {$name}
                    </p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=$product_id");
                    exit;
                }

                // Nome final
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $finalName = uniqid('prod_') . '.' . $ext;

                $s3Key = "products/{$product_id}/{$finalName}";

                try {
                    $s3->putObject([
                        'Bucket' => $bucket,
                        'Key'    => $s3Key,
                        'SourceFile' => $tmp,
                        'ContentType' => $mime,
                        'CacheControl' => 'public, max-age=31536000, immutable',
                    ]);
                } catch (Exception $e) {
                    $_SESSION['msg'] = "<p class='red'>
                        Erro ao enviar imagem para o CDN.
                    </p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=$product_id");
                    exit;
                }

                // Salva no banco
                $conn_pdo->prepare("
                    INSERT INTO imagens (usuario_id, nome_imagem, s3_path)
                    VALUES (:usuario_id, :nome_imagem, :s3_path)
                ")->execute([
                    ':usuario_id' => $product_id,
                    ':nome_imagem' => $finalName,
                    ':s3_path' => $s3Key
                ]);
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Produto editado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=" . $product_id);
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar o produto!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=" . $product_id);
    }