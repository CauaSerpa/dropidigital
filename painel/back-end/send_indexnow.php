<?php
session_start();
ob_start();
include_once('../../config.php');

// Definir o ID da loja
$shop_id = null;

if (!$shop_id) {
    echo "ID da loja não informado.";
    exit;
}

try {

    // 1. Buscar todos os domínios da loja
    $sql = "SELECT * FROM tb_domains WHERE shop_id = :shop_id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();
    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$domains) {
        echo "Nenhum domínio encontrado para esta loja.";
        exit;
    }

    // 2. Buscar todos os produtos da loja
    $sql = "SELECT id, link FROM tb_products WHERE shop_id = :shop_id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products) {
        echo "Nenhum produto encontrado para esta loja.";
        exit;
    }

    // Lista final de URLs a serem enviadas
    $urlList = [];

    // 3. Montar URLs dos produtos (para cada domínio)
    foreach ($products as $p) {
        foreach ($domains as $d) {
            $fullUrl = "https://" . $d['subdomain'] . "." . $d['domain'] . "/" . $p['seo_link'];
            $urlList[] = $fullUrl;
        }
    }

    // 4. Configurações do IndexNow
    $indexnow_key = "40cd769ebab4d16adaffc7992491894a";
    $indexnow_endpoint = "https://api.indexnow.org/indexnow";

    // 5. Montar payload
    $payload = json_encode([
        "host" => parse_url($urlList[0], PHP_URL_HOST),
        "key" => $indexnow_key,
        "urlList" => $urlList
    ]);

    // 6. Enviar requisição ao IndexNow
    $ch = curl_init($indexnow_endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 7. Log
    file_put_contents(
        "../indexnow/indexnow_log.txt",
        date('Y-m-d H:i:s') . 
        " - Envio em massa de produtos. HTTP: $httpCode. Resposta: $response. Total URLs: " . count($urlList) . "\n",
        FILE_APPEND
    );

    echo "Envio realizado! Total de URLs enviadas: " . count($urlList);

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}