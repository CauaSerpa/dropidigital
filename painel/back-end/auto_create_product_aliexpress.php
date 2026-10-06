<?php
session_start();
include_once('../../config.php');

use Aws\S3\S3Client;

// Verifica se o usuário está logado e tem permissão
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['shop_id'], [2, 25, 61])) {
    header('Location: ' . INCLUDE_PATH_DASHBOARD);
    exit;
}

// Recebe os dados do formulário
$shop_id = isset($_POST['shop_id']) ? (int)$_POST['shop_id'] : 0;
$selectedProducts = isset($_POST['selectedProducts']) ? $_POST['selectedProducts'] : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Validação básica
if ($action !== 'auto-create-product-aliexpress' || empty($selectedProducts)) {
    $_SESSION['msg_error'] = 'Nenhum produto selecionado para cadastro.';
    header('Location: ' . INCLUDE_PATH_DASHBOARD . 'produtos-aliexpress');
    exit;
}

// Converte a string de IDs em array
$productIds = explode(',', $selectedProducts);

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

// Função para gerar meta keywords
function gerarMetaKeywords($title, $categoria = '') {
    $keywords = [];
    $palavras = explode(' ', $title);
    $keywords = array_merge($keywords, array_slice($palavras, 0, 5));
    
    if (!empty($categoria)) {
        $keywords[] = $categoria;
    }
    
    $keywords[] = 'aliexpress';
    $keywords[] = 'importado';
    
    return implode(', ', array_unique($keywords));
}

// Função para salvar imagem localmente
// function salvarImagemLocal($urlImagem, $productId, $shop_id) {
//     // Cria diretório se não existir
//     $uploadDir = '../../uploads/produtos/';
//     if (!file_exists($uploadDir)) {
//         mkdir($uploadDir, 0777, true);
//     }
    
//     $uploadDirShop = $uploadDir . 'shop_' . $shop_id . '/';
//     if (!file_exists($uploadDirShop)) {
//         mkdir($uploadDirShop, 0777, true);
//     }
    
//     // Gera nome único para a imagem
//     $ext = pathinfo(parse_url($urlImagem, PHP_URL_PATH), PATHINFO_EXTENSION);
//     if (empty($ext)) {
//         $ext = 'jpg';
//     }
//     $nomeImagem = $productId . '_' . time() . '.' . $ext;
//     $caminhoCompleto = $uploadDirShop . $nomeImagem;
    
//     // Baixa a imagem
//     $ch = curl_init($urlImagem);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//     $imagem = curl_exec($ch);
//     curl_close($ch);
    
//     if ($imagem !== false) {
//         file_put_contents($caminhoCompleto, $imagem);
//         return 'uploads/produtos/shop_' . $shop_id . '/' . $nomeImagem;
//     }
    
//     return $urlImagem; // Retorna a URL original se falhar
// }

function buscarProdutoDetalhes($conn_pdo, $productId) {
    $sql = "SELECT *
            FROM tb_aliexpress_products
            WHERE id = :id
            LIMIT 1";

    $stmt = $conn_pdo->prepare($sql);

    $stmt->execute([
        ':id' => $productId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function prepararDadosProduto($produto, $shop_id) {
    $title = $produto['title'] ?? '';
    $description = $produto['description'] ?? $title;

    $price = str_replace(',', '.', $produto['sale_price'] ?? 0);
    $discount = str_replace(',', '.', $produto['original_price'] ?? 0);
    $price = is_numeric($price) ? $price : 0;
    $discount = is_numeric($discount) && $price !== $discount ? $discount : 0;

    $commissionRate = (float)($produto['commission_rate'] ?? 0);
    $commissionValue = (float)($produto['commission_value'] ?? 0);

    $seo_name = mb_substr(strip_tags($title), 0, 60, 'UTF-8');

    $seo_description = limparSeoDescription(
        !empty($description)
            ? $description
            : $title
    );

    return [
        'shop_id' => $shop_id,
        'name' => $title,
        'description' => $description,
        'price' => $price,
        'discount' => $discount,
        'commission_value' => $commissionValue,
        'commission_percent' => $commissionRate,
        'sku' => $produto['product_id'] ?? '',
        'stock' => 999,
        'status' => 1,
        'seo_name' => $seo_name,
        'seo_description' => $seo_description,
        'images' => $produto['product_main_image_url'] ?? '',
        'redirect_link' => $produto['promotion_link'] ?? '',
        'aliexpress_product_id' => $produto['product_id'] ?? '',
        'rating' => (float)($produto['rating_decimal'] ?? 0),
        'sales_volume' => (int)($produto['sales_volume'] ?? 0),
        'iframe' => '',
        'related' => 'aliexpress',
        'language' => 'en'
    ];
}

// Função para inserir produto no banco
function inserirProduto($conn_pdo, $dados) {
    
    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
    ]);

    $bucket = $_ENV['AWS_BUCKET'];



    // Link
    $link = gerarLinkUnico($conn_pdo, $dados['name'], 'pt');

    $sql = "INSERT INTO tb_products (
        shop_id, name, description, language, price, discount, sku, status, 
        seo_name, link, seo_description, iframe, button_type, 
        redirect_link, product_id, related, rating, sales_volume
    ) VALUES (
        :shop_id, :name, :description, :language, :price, :discount, :sku, :status,
        :seo_name, :link, :seo_description, :iframe, :button_type, 
        :redirect_link, :aliexpress_product_id, :related, :rating, :sales_volume
    )";
    
    $stmt = $conn_pdo->prepare($sql);

    $produto = $stmt->execute([
        ':shop_id' => $dados['shop_id'],
        ':name' => $dados['name'],
        ':description' => $dados['description'],
        ':language' => $dados['language'],
        ':price' => $dados['price'],
        ':discount' => $dados['discount'],
        ':sku' => $dados['sku'],
        ':status' => $dados['status'],
        ':seo_name' => $dados['seo_name'],
        ':link' => $link,
        ':seo_description' => $dados['seo_description'],
        ':iframe' => $dados['redirect_link'],
        ':button_type' => '1',
        ':redirect_link' => $dados['redirect_link'],
        ':aliexpress_product_id' => $dados['aliexpress_product_id'],
        ':related' => $dados['related'],
        ':rating' => $dados['rating'],
        ':sales_volume' => $dados['sales_volume']
    ]);

    if ($produto) {
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

        $product_img_url = $dados['images'];
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

        return true;
    }

}

// Função para verificar se produto já existe
function produtoExiste($conn_pdo, $aliexpressProductId, $shop_id) {
    $sql = "SELECT id FROM tb_products WHERE product_id = :aliexpress_product_id AND related = :related AND shop_id = :shop_id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->execute([
        ':aliexpress_product_id' => $aliexpressProductId,
        ':related' => 'aliexpress',
        ':shop_id' => $shop_id
    ]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Processamento dos produtos
$produtosCadastrados = 0;
$produtosFalha = 0;
$produtosJaExistentes = 0;
$erros = [];

foreach ($productIds as $productId) {
    $productId = trim($productId);
    
    // Verifica se produto já existe
    if (produtoExiste($conn_pdo, $productId, $shop_id)) {
        $produtosJaExistentes++;
        continue;
    }
    
    // Busca detalhes do produto na API
    $produto = buscarProdutoDetalhes($conn_pdo, $productId);

    if ($produto) {
        $dadosProduto = prepararDadosProduto($produto, $shop_id);

        if (inserirProduto($conn_pdo, $dadosProduto)) {
            $produtosCadastrados++;
        } else {
            $produtosFalha++;
            $errorInfo = $conn_pdo->errorInfo();
            $erros[] = "Erro ao cadastrar produto ID: $productId - " . ($errorInfo[2] ?? 'Erro desconhecido');
        }
        
    } else {
        $produtosFalha++;
        $erros[] = "Produto não encontrado no banco. ID: $productId";

        if (count($erros) == 1) {
            error_log("Produto inexistente em tb_aliexpress_products: {$productId}");
        }
    }
    
    // Pequeno delay para não sobrecarregar a API
    usleep(500000); // 0.5 segundos
}

// Mensagem de resultado
if ($produtosCadastrados > 0) {
    $_SESSION['msgcad'] = "<p class='green'>Produtos cadastrados com sucesso! Cadastrados: $produtosCadastrados";
    
    if ($produtosJaExistentes > 0) {
        $_SESSION['msgcad'] .= " | Já existentes: $produtosJaExistentes";
    }
    $_SESSION['msgcad'] .= "</p>";
    
    if ($produtosFalha > 0) {
        $_SESSION['msg'] = "<p class='red'>Falhas: $produtosFalha produtos não foram cadastrados.";
        if (!empty($erros)) {
            $_SESSION['msg'] .= " Erros: " . implode(", ", array_slice($erros, 0, 5));
        }
        $_SESSION['msg'] .= "</p>";
    }
} else {
    if ($produtosJaExistentes > 0 && $produtosFalha == 0) {
        $_SESSION['msg'] = "<p class='red'>Todos os produtos selecionados já estão cadastrados.</p>";
    } else {
        $_SESSION['msg'] = "<p class='red'>Nenhum produto foi cadastrado. Verifique os dados e tente novamente.";
        if (!empty($erros)) {
            $_SESSION['msg'] .= " Erros: " . implode(", ", array_slice($erros, 0, 3));
        }
        $_SESSION['msg'] .= "</p>";
    }
}

// Redireciona de volta
header('Location: ' . INCLUDE_PATH_DASHBOARD . 'aliexpress');
exit;