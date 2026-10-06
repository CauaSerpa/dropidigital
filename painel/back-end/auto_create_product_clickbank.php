<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start();
include_once('../../config.php');
date_default_timezone_set('America/Sao_Paulo');

function parseClickbankPrice(string $priceString): float
{
    // 1) Remove tudo que não seja dígito ou ponto decimal
    $onlyNumbersAndDot = preg_replace('/[^\d\.]/', '', $priceString);

    // 2) Se houver mais de um ponto, considera apenas o último como separador decimal
    if (substr_count($onlyNumbersAndDot, '.') > 1) {
        // Ex.: "1.234.567.89" → remove todos os pontos menos o último
        $parts = explode('.', $onlyNumbersAndDot);
        $decimal = array_pop($parts);
        $integer = implode('', $parts);
        $onlyNumbersAndDot = $integer . '.' . $decimal;
    }

    // 3) Converte para float
    return floatval($onlyNumbersAndDot);
}

/**
 * Gera um código aleatório de tamanho variável.
 */
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

function gerarClickbankLinkUnico($conn_pdo, int $length = 24)
{
    $attempts = 0;
    $maxAttempts = 10;
    do {
        // 1) Gera uma string alfanumérica randômica
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $subdomain = '';
        for ($i = 0; $i < $length; $i++) {
            $subdomain .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // 2) Verifica no banco se já existe
        $stmt = $conn_pdo->prepare("
            SELECT COUNT(1)
            FROM tb_products
            WHERE link = :link
        ");
        $fullLink = "https://{$subdomain}.hop.clickbank.net";
        $stmt->bindParam(':link', $fullLink, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetchColumn() == 0) {
            // Não existe: link único encontrado
            return $fullLink;
        }

        $attempts++;
    } while ($attempts < $maxAttempts);

    throw new \Exception("Não foi possível gerar um link único após {$maxAttempts} tentativas");
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

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['selectedProducts']) &&
    ($_POST['action'] ?? '') === 'auto-create-product-clickbank'
) {
    $shop_id = $_SESSION['shop_id'];
    
    $selected = explode(',', $_POST['selectedProducts']);

    // 1) descobrir plano e limite de produtos atuais
    $stmt = $conn_pdo->prepare("
        SELECT s.plan_id
          FROM tb_subscriptions AS sub
          JOIN tb_plans_interval AS s ON sub.plan_id = s.id
         WHERE sub.shop_id = :shop_id
           AND (sub.status IN ('ACTIVE','RECEIVED'))
         ORDER BY sub.id DESC
         LIMIT 1
    ");
    $stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt->execute();
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    $planId = $plan['plan_id'] ?? 1;

    // conta produtos ativos já cadastrados
    $stmt = $conn_pdo->prepare("
        SELECT COUNT(*) AS total
          FROM tb_products
         WHERE shop_id = :shop_id
           AND status = 1
    ");
    $stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmt->execute();
    $totalAtivos = (int) $stmt->fetchColumn();

    $limits = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 900,
        5 => 0 // zero = ilimitado
    ];
    $limit = $limits[$planId] ?? 0;

    foreach ($selected as $prodId) {
        $prodId = trim($prodId);

        // 2) buscar dados no ClickBank
        $stmt = $conn_pdo->prepare("
            SELECT *
              FROM tb_clickbank_products
             WHERE id = :id
        ");
        $stmt->bindValue(':id', $prodId, PDO::PARAM_INT);
        $stmt->execute();
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $prod) {
            continue; // pula se não existir
        }

        // 3) checar se já existe
        $stmt = $conn_pdo->prepare("
            SELECT COUNT(*) 
              FROM tb_products
             WHERE (product_id = :pid OR name = :name)
               AND shop_id = :shop_id
        ");
        $stmt->bindValue(':pid', $prod['id']);
        $stmt->bindValue(':name', $prod['title']);
        $stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            continue; // já cadastrado
        }

        // 4) status: 1 = ativo, 0 = inativo (atingiu limite?)
        $status = ($limit !== 0 && $totalAtivos >= $limit) ? 0 : 1;
        if ($status === 1) {
            $totalAtivos++;
        }
        
        // $link_url = gerarClickbankLinkUnico($conn_pdo);
        $link_url = '';

        // 5) montar campos para inserção
        $name             = $prod['title'];
        $price            = parseClickbankPrice($prod['price']);
        $description      = $prod['description'];
        $sku              = gerarCodigoAleatorio(8);
        $language         = 'en';
        $link             = gerarLinkUnico($conn_pdo, $name, $language);
        $seo_name         = mb_substr(strip_tags($name), 0, 60, 'UTF-8');
        $seo_description  = limparSeoDescription($description, 155);
        $redirect_link    = $link_url; // campo de afiliado no TB
        $iframe           = $link_url; // se usar iframe
        $emphasis         = 0;
        $button_type      = 1;
        $product_mode     = 'automatic';
        $related          = 'clickbank';

        // 6) inserir
        $sql = "
            INSERT INTO tb_products 
                (shop_id, status, emphasis, language, name, price,
                 description, sku, button_type, redirect_link, iframe,
                 product_mode_related, seo_name, link, seo_description,
                 product_id, related)
            VALUES
                (:shop_id, :status, :emphasis, :language, :name, :price,
                 :description, :sku, :button_type, :redirect_link, :iframe,
                 :product_mode, :seo_name, :link, :seo_desc, :prod_id, :related)
        ";
        $stmtIns = $conn_pdo->prepare($sql);
        $stmtIns->bindValue(':shop_id',      $shop_id,       PDO::PARAM_INT);
        $stmtIns->bindValue(':status',       $status,        PDO::PARAM_INT);
        $stmtIns->bindValue(':emphasis',     $emphasis,      PDO::PARAM_INT);
        $stmtIns->bindValue(':language',     $language,      PDO::PARAM_STR);
        $stmtIns->bindValue(':name',         $name,          PDO::PARAM_STR);
        $stmtIns->bindValue(':price',        $price);
        $stmtIns->bindValue(':description',  $description);
        $stmtIns->bindValue(':sku',          $sku,           PDO::PARAM_STR);
        $stmtIns->bindValue(':button_type',  $button_type,   PDO::PARAM_INT);
        $stmtIns->bindValue(':redirect_link',$redirect_link, PDO::PARAM_STR);
        $stmtIns->bindValue(':iframe',       $iframe,        PDO::PARAM_STR);
        $stmtIns->bindValue(':product_mode', $product_mode,  PDO::PARAM_STR);
        $stmtIns->bindValue(':seo_name',     $seo_name,      PDO::PARAM_STR);
        $stmtIns->bindValue(':link',         $link,          PDO::PARAM_STR);
        $stmtIns->bindValue(':seo_desc',     $seo_description, PDO::PARAM_STR);
        $stmtIns->bindValue(':prod_id',      $prod['id'],    PDO::PARAM_STR);
        $stmtIns->bindValue(':related',      $related,       PDO::PARAM_STR);

        if ($stmtIns->execute()) {
            // opcional: salvar imagem, etc.
        }
    }

    $_SESSION['msgcad'] = "<p class='green'>Produtos ClickBank cadastrados com sucesso.</p>";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "clickbank");
    exit;
}

// fallback
$_SESSION['msg'] = "<p class='red'>Nenhum produto selecionado.</p>";
header("Location: " . INCLUDE_PATH_DASHBOARD . "clickbank");
exit;