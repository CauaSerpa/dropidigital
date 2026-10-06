<?php

/**
 * CONFIGURAÇÕES SHOPEE OPEN PLATFORM
 *
 * Use as credenciais LIVE caso seu App já tenha
 * sido aprovado para produção.
 */

$partnerId = 2043092; // SEU LIVE PARTNER ID

$partnerKey = 'shpk6d616e674b7a694d674a6f756c54734f7342704a47546f51536476487656';

/**
 * URL para onde a Shopee retornará após a autorização.
 *
 * O domínio deve estar autorizado/configurado
 * na Shopee Open Platform.
 */
$redirectUrl = 'https://dropidigital.com.br/painel/back-end/shopee-callback.php';

/**
 * TIMESTAMP
 */
$timestamp = time();

/**
 * PATH UTILIZADO PARA A ASSINATURA
 */
$path = '/api/v2/shop/auth_partner';

/**
 * BASE STRING DA ASSINATURA
 *
 * partner_id + path + timestamp
 */
$baseString =
    $partnerId
    . $path
    . $timestamp;

/**
 * GERA ASSINATURA HMAC SHA256
 */
$sign = hash_hmac(
    'sha256',
    $baseString,
    $partnerKey
);

/**
 * URL DE AUTORIZAÇÃO
 */
$authorizationUrl =
    'https://partner.shopeemobile.com'
    . $path
    . '?partner_id='
    . urlencode($partnerId)
    . '&timestamp='
    . urlencode($timestamp)
    . '&sign='
    . urlencode($sign)
    . '&redirect='
    . urlencode($redirectUrl);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title>Autorizar Loja Shopee</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 80px auto;
            padding: 20px;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 15px 25px;
            background: #ee4d2d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
        }

    </style>

</head>

<body>

    <h1>Conectar Loja Shopee</h1>

    <p>
        Clique no botão abaixo para fazer login
        e autorizar sua loja no aplicativo.
    </p>

    <a
        href="<?= htmlspecialchars($authorizationUrl) ?>"
        class="button"
    >
        Autorizar minha loja Shopee
    </a>

</body>

</html>
