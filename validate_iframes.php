<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('config.php');
$tabela = "tb_products";

function iframeIsAllowed($url)
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Googlebot/2.1 (+http://www.google.com/bot.html)'
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);
        return ['valid' => false, 'reason' => 'Erro de conexão'];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['valid' => false, 'reason' => 'HTTP ' . $httpCode];
    }

    $headers = strtolower($response);

    // Bloqueios comuns
    if (str_contains($headers, 'x-frame-options: deny')) {
        return ['valid' => false, 'reason' => 'X-Frame-Options DENY'];
    }

    if (str_contains($headers, 'x-frame-options: sameorigin')) {
        return ['valid' => false, 'reason' => 'X-Frame-Options SAMEORIGIN'];
    }

    if (str_contains($headers, 'content-security-policy') &&
        str_contains($headers, 'frame-ancestors')) {
        return ['valid' => false, 'reason' => 'CSP frame-ancestors'];
    }

    return ['valid' => true, 'reason' => 'OK'];
}

// // 🔹 Busca produtos
// $sql = "SELECT id, iframe FROM $tabela WHERE iframe IS NOT NULL AND iframe <> '' AND language = 'es'";
// $stmt = $conn_pdo->query($sql);
// $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// foreach ($products as $product) {

//     $check = iframeIsAllowed($product['iframe']);

//     if (!$check['valid']) {

//         // Remove iframe inválido
//         $update = $conn_pdo->prepare("
//             UPDATE $tabela 
//             SET iframe = NULL 
//             WHERE id = ?
//         ");
//         $update->execute([$product['id']]);

//         echo "❌ Produto {$product['id']} iframe removido ({$check['reason']})\n";

//     } else {
//         echo "✅ Produto {$product['id']} iframe válido\n";
//     }
// }



// 🔹 Busca produtos de provider especifico
$batchSize = 500;
$totalUpdated = 0;

while (true) {

    $stmt = $conn_pdo->prepare("
        SELECT id
        FROM $tabela
        WHERE iframe IS NOT NULL
        AND iframe <> ''
        AND related IN ('aliexpress', 'shopee')
        LIMIT $batchSize
    ");

    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($products)) {
        break;
    }

    $placeholders = implode(
        ',',
        array_fill(0, count($products), '?')
    );

    $update = $conn_pdo->prepare("
        UPDATE $tabela
        SET iframe = NULL
        WHERE id IN ($placeholders)
    ");

    $update->execute($products);

    $updated = $update->rowCount();

    $totalUpdated += $updated;

    echo "Lote processado: {$updated} produtos. ";
    echo "Total: {$totalUpdated}" . PHP_EOL;
}

echo "Concluído. Total de produtos atualizados: {$totalUpdated}" . PHP_EOL;