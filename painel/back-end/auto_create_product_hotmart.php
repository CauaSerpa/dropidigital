<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    function generateRandomCode($length = 7) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        return $randomString;
    }
    
    function generateFullCode() {
        $randomCode = generateRandomCode(7);
        $afid = generateRandomCode(8);
        return "{$randomCode}?afid={$afid}";
    }

    function gerarCodigoAleatorio($tamanho = 10) {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $codigo = '';
    
        for ($i = 0; $i < $tamanho; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
    
        return $codigo;
    }

    // ===========================
    // GERAR LINKS
    // ===========================
    function getStopWords($lang = 'pt') {

        $stopWords = [

            'pt' => [
                'de','da','do','das','dos','para','com','sem','em','no','na','nos','nas',
                'e','ou','a','o','as','os','por','como','mais','menos','sobre'
            ],

            'en' => [
                'the','and','or','for','with','without','in','on','at','to','of'
            ],

            'de' => [
                'der','die','das','und','mit','ohne','fur','von','zu'
            ]

        ];

        return $stopWords[$lang] ?? $stopWords['pt'];
    }

    function removerStopWords($texto, $lang = 'pt') {

        $stopWords = getStopWords($lang);

        $palavras = explode(' ', $texto);

        $palavrasFiltradas = array_filter($palavras, function($palavra) use ($stopWords) {
            return !in_array($palavra, $stopWords)
                && strlen($palavra) > 2; // remove palavras muito curtas
        });

        return implode(' ', $palavrasFiltradas);
    }
    
    function gerarSlug($nome, $lang = 'pt', $limiteCaracteres = 65, $maxPalavras = 6) {

        $nome = strip_tags($nome);
        $nome = html_entity_decode($nome, ENT_QUOTES, 'UTF-8');
        $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
        $nome = strtolower($nome);
        $nome = preg_replace('/[^a-z0-9\s-]/', '', $nome);
        $nome = preg_replace('/\s+/', ' ', $nome);

        $nome = removerStopWords($nome, $lang);

        $palavras = explode(' ', trim($nome));

        // Remove duplicadas mantendo ordem
        $palavras = array_values(array_unique($palavras));

        $slug = '';
        $contadorPalavras = 0;

        foreach ($palavras as $palavra) {

            if ($contadorPalavras >= $maxPalavras) {
                break;
            }

            $teste = $slug ? $slug . '-' . $palavra : $palavra;

            if (strlen($teste) > $limiteCaracteres) {
                break;
            }

            $slug = $teste;
            $contadorPalavras++;
        }

        // fallback caso fique vazio
        if (empty($slug)) {
            $slug = substr(md5($nome . time()), 0, 8);
        }

        return trim($slug, '-');
    }
    
    function gerarLinkUnico($conn_pdo, $nomeProduto, $lang = 'pt') {

        $linkBase = gerarSlug($nomeProduto, $lang);
        $novoLink = $linkBase;
        $contador = 1;

        while (true) {

            $sql = "SELECT 1 FROM tb_products WHERE link = :link LIMIT 1";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':link', $novoLink);
            $stmt->execute();

            if (!$stmt->fetch()) {
                break;
            }

            // garante que não ultrapasse 70 caracteres com o sufixo
            $sufixo = '-' . $contador;
            $novoLink = substr($linkBase, 0, 65 - strlen($sufixo)) . $sufixo;

            $contador++;
        }

        return $novoLink;
    }

    // ===========================
    // REMOVER LINKS DA DESCRIÇÃO DO SEO
    // ===========================
    function limparSeoDescription($texto, $limite = 155) {
        if (empty($texto)) {
            return '';
        }

        // Remove HTML
        $texto = strip_tags($texto);

        // Remove URLs
        $texto = preg_replace('/https?:\/\/\S+/i', '', $texto);
        $texto = preg_replace('/www\.\S+/i', '', $texto);

        // Remove múltiplos espaços
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = trim($texto);

        // Se menor que limite, retorna normal
        if (mb_strlen($texto, 'UTF-8') <= $limite) {
            return $texto;
        }

        // Corta no limite
        $corte = mb_substr($texto, 0, $limite, 'UTF-8');

        // Volta até o último espaço para evitar cortar palavra
        $ultimoEspaco = mb_strrpos($corte, ' ', 0, 'UTF-8');

        if ($ultimoEspaco !== false) {
            $corte = mb_substr($corte, 0, $ultimoEspaco, 'UTF-8');
        }

        return $corte . '...';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedProducts']) && $_POST['action'] == 'auto-create-product-hotmart') {
        $shop_id = $_POST['shop_id'];
        $selectedProducts = explode(',', $_POST['selectedProducts']);

        foreach ($selectedProducts as $productId) {

            // Nome da tabela para a busca
            $tabela = 'tb_subscriptions';

            // Consulta SQL para contar os produtos na tabela
            $sql = "SELECT plan_id FROM $tabela WHERE (status = :status OR status = :status1) AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";
            $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
            $stmt->bindValue(':status', 'ACTIVE');
            $stmt->bindValue(':status1', 'RECEIVED');
            $stmt->bindParam(':shop_id', $shop_id);
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
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->bindValue(':status', 1);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Define os limites de produtos com base no plano
            $limitProductsMap = [
                1 => 10,
                2 => 50,
                3 => 250,
                4 => 900,
                5 => 0,
            ];

            $limitProducts = $limitProductsMap[$shop['plan_id']] ?? "ilimitado";

            // // Define o status com base nos limites de produtos
            // $status = ($limitProducts <= $product['total_produtos']) ? 0 : 1;
            
            

            // Define o status com base nos limites de produtos
            // $status = ($limitProducts <= $product['total_produtos']) ? 0 : (isset($_POST['status']) && $_POST['status'] == '1' ? $_POST['status'] : 0);
            $status = ($limitProducts === 0) ? 1
                    : (($limitProducts <= $product['total_produtos'])
                    ? 0
                    : 1);










            $productId = trim($productId);

            // Buscar os detalhes do produto no banco de dados
            $sql = "SELECT p.*, s.link_afiliado 
                    FROM tb_hotmart_products p
                    LEFT JOIN tb_hotmart_links s 
                        ON p.id = s.product_id 
                        AND s.seller_id = :seller_id
                    WHERE p.id = :produto_id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':produto_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':seller_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // echo "Usuario {$_SESSION['user_id']}<br>";
            // print_r($product);
            // exit;

            if ($product) {
                $name = $product['title'];
                $product_img_url = $product['image_url'];
                $description = $product['descricao'] ?? null;
                $seo_name = mb_substr(strip_tags($name), 0, 60, 'UTF-8');
                $seo_description = limparSeoDescription($description, 155);

                $emphasis = 1;
                $language = $product['lang'] ?? 'pt';
                $button_type = '1';
                $redirect_link = $product['link_afiliado'] ?? null;
                $iframe = $product['link_afiliado'] ?? null;
                $product_mode_related = 'automatic';

                $sku = gerarCodigoAleatorio();
                $link = gerarLinkUnico($conn_pdo, $product['title'], $language);
                
                $product_id = $product['id'];
                $related = 'hotmart';


                // Extrair apenas o preço do texto
                $rawPrice = trim(mb_strtolower($product['price'] ?? ''));

                $price = 0;
                $without_price = false;

                // Casos explícitos sem preço
                $invalidTexts = [
                    'ver detalhes',
                    'ver preço',
                    'consultar',
                    'sob consulta',
                    ''
                ];

                // Moedas que você NÃO quer aceitar
                $blockedCurrencies = [
                    'ars', 'cop', 'clp', 'mxn', 'pen', 'uyu', 'crc'
                ];

                // Se detectar moeda bloqueada → ignora preço
                foreach ($blockedCurrencies as $currencyCode) {
                    if (strpos($rawPrice, $currencyCode) !== false) {
                        $price = 0;
                        $without_price = true;
                        break;
                    }
                }

                if (!$without_price) {
                    if (in_array($rawPrice, $invalidTexts, true)) {
                        $price = 0;
                        $without_price = true;
                    } else {
                        // Detecta moeda suportada
                        $currency = null;

                        if (preg_match('/r\$/i', $rawPrice)) {
                            $currency = 'BRL';
                        } elseif (preg_match('/usd/i', $rawPrice)) {
                            $currency = 'USD';
                        } elseif (preg_match('/eur|€/i', $rawPrice)) {
                            $currency = 'EUR';
                        }

                        // Se moeda não for suportada → ignora preço
                        if (!$currency) {
                            $price = 0;
                            $without_price = true;
                        } else {
                            // Extrai apenas números, ponto e vírgula
                            $numeric = preg_replace('/[^0-9.,]/', '', $rawPrice);

                            if ($numeric === '') {
                                $price = 0;
                                $without_price = true;
                            } else {

                                if (substr_count($numeric, ',') === 1 && substr_count($numeric, '.') >= 1) {
                                    $numeric = str_replace('.', '', $numeric);
                                    $numeric = str_replace(',', '.', $numeric);
                                } elseif (substr_count($numeric, ',') === 1) {
                                    $numeric = str_replace(',', '.', $numeric);
                                }

                                $price = (float) $numeric;

                                if ($price <= 0) {
                                    $price = 0;
                                    $without_price = true;
                                }
                            }
                        }
                    }
                }


                // Inserir os produtos selecionados na tabela
                $sqlInsert = "INSERT INTO tb_products 
                    (shop_id, status, emphasis, language, name, price, description, sku, button_type, redirect_link, iframe, product_mode_related, seo_name, link, seo_description, product_id, related) 
                    VALUES 
                    (:shop_id, :status, :emphasis, :language, :name, :price, :description, :sku, :button_type, :redirect_link, :iframe, :product_mode_related, :seo_name, :link, :seo_description, :product_id, :related)";

                $stmtInsert = $conn_pdo->prepare($sqlInsert);
                $stmtInsert->bindParam(':shop_id', $shop_id);
                $stmtInsert->bindValue(':status', $status);
                $stmtInsert->bindValue(':emphasis', $emphasis);
                $stmtInsert->bindParam(':language', $language);
                $stmtInsert->bindParam(':name', $name);
                $stmtInsert->bindParam(':price', $price);
                $stmtInsert->bindParam(':description', $description);
                $stmtInsert->bindParam(':sku', $sku);
                $stmtInsert->bindParam(':button_type', $button_type);
                $stmtInsert->bindParam(':redirect_link', $redirect_link);
                $stmtInsert->bindParam(':iframe', $iframe);
                $stmtInsert->bindParam(':product_mode_related', $product_mode_related);
                $stmtInsert->bindParam(':seo_name', $seo_name);
                $stmtInsert->bindParam(':link', $link);
                $stmtInsert->bindParam(':seo_description', $seo_description);
                $stmtInsert->bindParam(':product_id', $product_id);
                $stmtInsert->bindParam(':related', $related);

                if ($stmtInsert->execute()) {

                    // Recupere o ID do último registro inserido
                    $ultimo_id = $conn_pdo->lastInsertId();

                
        
        
        
                    $selectedCategory = $_POST['category'];

                    if (!empty($selectedCategory) && !empty($shop_id) && !empty($ultimo_id)) {

                        $tabela = "tb_product_categories";
                    
                        $main = 1;
                    
                        // Insere a única associação
                        $sql = "INSERT INTO $tabela (shop_id, product_id, category_id, main) VALUES (:shop_id, :product_id, :category_id, :main)";
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
                        $stmt->bindParam(':product_id', $ultimo_id, PDO::PARAM_INT);
                        $stmt->bindParam(':category_id', $selectedCategory, PDO::PARAM_INT);
                        $stmt->bindParam(':main', $main, PDO::PARAM_INT);
                    
                        if ($stmt->execute()) {
                            // sucesso opcional
                            echo "categoria associada com sucesso";
                        } else {
                            // log / tratamento de erro mínimo
                            echo "erro ao associar categoria";
                        }
                    }
        
        
        
        
                    // Consulta SQL para obter o domínio da loja
                    $sql = "SELECT * FROM tb_domains WHERE shop_id = :shop_id LIMIT 1";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $_SESSION['shop_id']);
                    $stmt->execute();
                    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $urlList = [];
                    
                    // Gera as URLs completas para cada domínio da loja
                    foreach ($domains as $d) {
                        // Monta a URL completa (ajuste conforme o slug do produto)
                        $fullUrl = "https://" . $d['subdomain'] . "." . $d['domain'] . "/" . $link;
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
                    
                    
                    
                    
                    
    

                    if (!empty($product_img_url)) {
                        try {

                            // Conta imagens atuais do produto
                            $stmt = $conn_pdo->prepare("
                                SELECT COUNT(*) FROM imagens WHERE usuario_id = :id
                            ");
                            $stmt->execute([':id' => $ultimo_id]);
                            $totalExistentes = (int) $stmt->fetchColumn();

                            if ($totalExistentes >= MAX_PRODUCT_IMAGES) {
                                throw new Exception('Limite máximo de imagens atingido');
                            }

                            // Download seguro via cURL
                            $ch = curl_init($product_img_url);
                            curl_setopt_array($ch, [
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_TIMEOUT => 20,
                                CURLOPT_SSL_VERIFYPEER => false,
                            ]);

                            $imageData = curl_exec($ch);
                            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $mime      = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                            curl_close($ch);

                            if ($httpCode !== 200 || !$imageData) {
                                throw new Exception('Erro ao baixar imagem');
                            }

                            // Valida tamanho
                            $size = strlen($imageData);
                            if ($size > MAX_IMAGE_SIZE) {
                                throw new Exception('Imagem maior que o permitido');
                            }

                            // Valida MIME
                            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                            if (!in_array($mime, $allowed)) {
                                throw new Exception('Formato de imagem inválido');
                            }

                            // Extensão
                            $extMap = [
                                'image/jpeg' => 'jpg',
                                'image/png'  => 'png',
                                'image/webp' => 'webp',
                            ];
                            $ext = $extMap[$mime];

                            // Nome final
                            $fileName = uniqid('prod_') . '.' . $ext;
                            $s3Key = "products/{$ultimo_id}/{$fileName}";

                            $s3->putObject([
                                'Bucket' => $bucket,
                                'Key'    => $s3Key,
                                'Body'   => $imageData,
                                'ContentType' => $mime,
                                'CacheControl' => 'public, max-age=31536000, immutable',
                            ]);

                            // Salva no banco
                            $stmt = $conn_pdo->prepare("
                                INSERT INTO imagens (usuario_id, nome_imagem, s3_path)
                                VALUES (:usuario_id, :nome_imagem, :s3_path)
                            ");
                            $stmt->execute([
                                ':usuario_id' => $ultimo_id,
                                ':nome_imagem' => $fileName,
                                ':s3_path' => $s3Key
                            ]);

                        } catch (Exception $e) {
                            // Loga erro mas NÃO interrompe o fluxo
                            file_put_contents(
                                '../logs/image_errors.log',
                                date('Y-m-d H:i:s') .
                                " | Produto {$ultimo_id} | {$e->getMessage()} | URL: {$product_img_url}\n",
                                FILE_APPEND
                            );
                        }
                    }

                } else {
                    echo "Erro ao cadastrar o produto ID {$product['id']}<br>";
                }
            } else {
                echo "Produto ID {$productId} não encontrado.<br>";
            }
        }

        // Se não houver imagens no input files, exibe uma mensagem ou continue sem processar as imagens
        $_SESSION['msgcad'] = "<p class='green'>Produtos cadastrados com sucesso.</p>";
        echo "<p class='grenn'>Produtos cadastrados com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "hotmart");
        exit;
    } else {
        echo "Nenhum produto selecionado.";
        $_SESSION['msg'] = "<p class='red'>Nenhum produto selecionado.</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "hotmart");
        exit;
    }