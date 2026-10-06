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
    // 0 = Ilimitado
    $limitProductsMap = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 900,
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
        $price = str_replace(',', '.', $dados['price'] ?? 0);
        $discount = str_replace(',', '.', $dados['discount'] ?? 0);

        $price = is_numeric($price) ? $price : 0;
        $discount = is_numeric($discount) ? $discount : 0;

        $without_price = 0;
    }

    $dados['product_id'] = (!empty($_POST['product_id'])) ? $_POST['product_id'] : null;

    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        $sql = "INSERT INTO tb_products (shop_id, status, emphasis, language, name, price, without_price, discount, video, description, sku, button_type, redirect_link, iframe, product_mode_related, seo_name, link, seo_description, product_id) VALUES 
                                    (:shop_id, :status, :emphasis, :language, :name, :price, :without_price, :discount, :video, :description, :sku, :button_type, :redirect_link, :iframe, :product_mode_related, :seo_name, :link, :seo_description, :product_id)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['shop_id']);
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
        $stmt->bindParam(':product_id', $dados['product_id']);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $conn_pdo->lastInsertId();
        
        
        
        // Tags
        if (!empty($_POST['tags'])) {
            $sql = "INSERT INTO tb_product_tags (product_id, tag) VALUES (:product_id, :tag)";
            $stmt = $conn_pdo->prepare($sql);

            foreach ($_POST['tags'] as $tag) {
                $tag = trim(strtolower($tag));

                if ($tag == '') continue;

                $stmt->execute([
                    ':product_id' => $ultimo_id,
                    ':tag' => $tag
                ]);
            }
        }



        
        // FAQ
        if (isset($_POST['faq_question'])) {
            echo "Entrou<br><br>";
                
            $sqlFaq = "INSERT INTO tb_product_faqs (product_id, question, answer, position)
                       VALUES (:product_id, :question, :answer, :position)";
            $stmtFaq = $conn_pdo->prepare($sqlFaq);
        
            foreach ($_POST['faq_question'] as $index => $question) {
        
                $answer = $_POST['faq_answer'][$index];
        
                // evitar campos vazios
                if (trim($question) == "" || trim($answer) == "") continue;
        
                $stmtFaq->execute([
                    ':product_id' => $ultimo_id,
                    ':question' => $question,
                    ':answer' => $answer,
                    ':position' => $index,
                ]);
                
                echo "Cadastrou $question<br>";
            }
        }
        
        
        
        
        
        
        // Consulta SQL para obter o domínio da loja
        $sql = "SELECT * FROM tb_domains WHERE shop_id = :shop_id LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->execute();
        $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $urlList = [];
        
        // Gera as URLs completas para cada domínio da loja
        foreach ($domains as $d) {
            // Monta a URL completa (ajuste conforme o slug do produto)
            $fullUrl = "https://" . $d['subdomain'] . "." . $d['domain'] . "/" . $dados['seo_link'];
            $urlList[] = $fullUrl;
        }
        
        // Chave IndexNow (deve estar hospedada no domínio principal ou subdomínio)
        $indexnow_key = "40cd769ebab4d16adaffc7992491894a";
        
        // Endpoint do IndexNow (Bing centralizado)
        $indexnow_endpoint = "https://api.indexnow.org/indexnow";
        
        // Monta o payload
        $payload = json_encode([
            "host" => parse_url($urlList[0], PHP_URL_HOST), // usa o host da primeira URL
            "key" => $indexnow_key,
            "urlList" => $urlList
        ]);
        
        // Envia a requisição
        $ch = curl_init($indexnow_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log para monitoramento
        file_put_contents(
            "../indexnow/indexnow_log.txt",
            date('Y-m-d H:i:s') . " - Produto ID $ultimo_id enviado. HTTP: $httpCode. Resposta: $response. URLs: " . implode(", ", $urlList) . "\n",
            FILE_APPEND
        );
        
        
        
        
        

        // Categorias
        // Recupera o valor do input hidden com o ID das categorias
        $categoriasInputValue = $_POST['categoriasSelecionadas']; // Substitua 'categoriasInput' pelo nome do seu input

        // Certifique-se de que $categoriasInputValue é uma string
        if (is_array($categoriasInputValue)) {
            // Lógica para converter o array em uma string (se aplicável)
            // Isso pode variar dependendo de como os dados estão sendo enviados
            $categoriasInputValue = implode(',', $categoriasInputValue);
        }

        // Separa os IDs das categorias em um array
        $categoriasIds = explode(',', $categoriasInputValue);

        if (!empty($categoriasInputValue)) {
            // Loop para inserir categorias no banco de dados
            foreach ($categoriasIds as $categoriaId) {
                // Certifique-se de validar e escapar os dados para evitar injeção de SQL
                $categoriaId = (int)$categoriaId;
    
                $main = ($dados['inputMainCategory'] == $categoriaId) ? 1 : 0;
    
                // Consulta SQL para inserir a associação entre produto e categoria
                $tabela = "tb_product_categories";
                $sql = "INSERT INTO $tabela (shop_id, product_id, category_id, main) VALUES (:shop_id, :product_id, :category_id, :main)";
                $stmt = $conn_pdo->prepare($sql);
    
                $stmt->bindParam(':shop_id', $dados['shop_id']);
                $stmt->bindParam(':product_id', $ultimo_id);
                $stmt->bindParam(':category_id', $categoriaId);
                $stmt->bindParam(':main', $main);
    
                $stmt->execute();
    
                echo "sucesso";
            }
        }

        // Produtos Relacionados
        // Verifica se o modo de relacionamento foi selecionado como manual
        if ($_POST['selectMode'] === 'manual') {
            // Recupera o valor do input hidden com os IDs dos produtos selecionados
            $produtosInputValue = $_POST['produtosSelecionados']; // Substitua 'produtosSelecionados' pelo nome do seu input

            // Certifique-se de que $produtosInputValue é uma string
            if (is_array($produtosInputValue)) {
                // Converte o array em uma string separada por vírgulas
                $produtosInputValue = implode(',', $produtosInputValue);
            }

            // Separa os IDs dos produtos em um array
            $produtosIds = explode(',', $produtosInputValue);

            // Loop para inserir os produtos relacionados no banco de dados
            foreach ($produtosIds as $produtoIdRelacionado) {
                // Valida e escapa os dados para evitar injeção de SQL
                $produtoIdRelacionado = (int)$produtoIdRelacionado;

                // Consulta SQL para inserir a associação entre o produto atual e os produtos relacionados
                $tabela = "tb_product_related";
                $sql = "INSERT INTO $tabela (shop_id, product_id, related_product_id) VALUES (:shop_id, :product_id, :related_product_id)";
                $stmt = $conn_pdo->prepare($sql);

                $stmt->bindParam(':shop_id', $dados['shop_id']);
                $stmt->bindParam(':product_id', $ultimo_id); // ID do produto atual
                $stmt->bindParam(':related_product_id', $produtoIdRelacionado);

                $stmt->execute();
            }
            echo "Produtos relacionados salvos com sucesso!";
        }

        // Verifique se a URL da imagem foi passada
        $product_img_url = $_POST['product_img'] ?? null;

        if (!empty($product_img_url)) {

            $uploadDir = __DIR__ . "/imagens/$ultimo_id/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Extensões permitidas
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            // Extrai a extensão real da URL
            $urlPath = parse_url($product_img_url, PHP_URL_PATH);
            $imageExtension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            if (!in_array($imageExtension, $allowedExtensions)) {
                $_SESSION['msg'] = "<p class='red'>Formato de imagem não permitido!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                exit;
            }

            $imageName = uniqid('img_') . '.' . $imageExtension;
            $imagePath = $uploadDir . $imageName;

            // ===== Download seguro com cURL =====
            $ch = curl_init($product_img_url);
            $fp = fopen($imagePath, 'wb');

            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0'
            ]);

            $success = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            fclose($fp);

            if ($success && $httpCode === 200 && filesize($imagePath) > 0) {

                $sqlInsertImagem = "
                    INSERT INTO imagens (usuario_id, nome_imagem)
                    VALUES (:usuario_id, :nome_imagem)
                ";

                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindValue(':usuario_id', $ultimo_id, PDO::PARAM_INT);
                $stmtInsertImagem->bindValue(':nome_imagem', $imageName, PDO::PARAM_STR);

                if ($stmtInsertImagem->execute()) {
                    $_SESSION['msgcad'] = "<p class='green'>Imagem do produto cadastrada com sucesso!</p>";
                } else {
                    unlink($imagePath);
                    $_SESSION['msg'] = "<p class='red'>Erro ao salvar a imagem no banco!</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                    exit;
                }

            } else {
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $_SESSION['msg'] = "<p class='red'>Erro ao baixar a imagem!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                exit;
            }
        }

        // Verifique se há arquivos no input de imagens antes de iniciar o processamento
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $totalArquivos = count($_FILES['imagens']['name']);

            // Limite por produto
            if ($totalArquivos > MAX_PRODUCT_IMAGES) {
                $_SESSION['msg'] = "<p class='red'>Máximo de " . MAX_PRODUCT_IMAGES . " imagens por produto.</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                exit;
            }

            for ($i = 0; $i < $totalArquivos; $i++) {

                $tmp = $_FILES['imagens']['tmp_name'][$i];
                $size = $_FILES['imagens']['size'][$i];
                $name = $_FILES['imagens']['name'][$i];

                // Limite de tamanho
                if ($size > MAX_IMAGE_SIZE) {
                    $_SESSION['msg'] = "<p class='red'>Imagem {$name} excede o tamanho máximo permitido.</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                    exit;
                }

                // Validação MIME real
                $mime = mime_content_type($tmp);
                $allowed = ['image/jpeg', 'image/png', 'image/webp'];

                if (!in_array($mime, $allowed)) {
                    $_SESSION['msg'] = "<p class='red'>Formato inválido: {$name}</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                    exit;
                }

                // Nome final
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $finalName = uniqid('prod_') . '.' . $ext;

                // MANTENDO usuario_id = product_id (como você pediu)
                $s3Key = "products/{$ultimo_id}/{$finalName}";

                try {
                    $s3->putObject([
                        'Bucket' => $bucket,
                        'Key'    => $s3Key,
                        'SourceFile' => $tmp,
                        'ContentType' => $mime,
                        'CacheControl' => 'public, max-age=31536000, immutable',
                    ]);
                } catch (Exception $e) {
                    $_SESSION['msg'] = "<p class='red'>Erro ao enviar imagem para o CDN.</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                    exit;
                }

                // Salva SOMENTE o path
                $stmtImg = $conn_pdo->prepare("
                    INSERT INTO imagens (usuario_id, nome_imagem, s3_path)
                    VALUES (:usuario_id, :nome_imagem, :s3_path)
                ");

                $stmtImg->execute([
                    ':usuario_id' => $ultimo_id,
                    ':nome_imagem' => $finalName,
                    ':s3_path' => $s3Key
                ]);
            }
        }

        // Se não houver imagens no input files, exibe uma mensagem ou continue sem processar as imagens
        $_SESSION['msgcad'] = "<p class='green'>Produto cadastrado com sucesso.</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
        exit;
    }