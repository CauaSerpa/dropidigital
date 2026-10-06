<?php

/**
 * CONFIGURAÇÕES SHOPEE OPEN PLATFORM
 */

$partnerId = 2043092; // SEU LIVE PARTNER ID

$partnerKey = 'shpk6d616e674b7a694d674a6f756c54734f7342704a47546f51536476487656';


/**
 * VERIFICA SE A SHOPEE RETORNOU OS DADOS
 */

if (
    empty($_GET['code'])
    || empty($_GET['shop_id'])
) {

    die(
        'Erro: code ou shop_id não foram recebidos.'
    );
}


/**
 * DADOS RECEBIDOS DA AUTORIZAÇÃO
 */

$code = $_GET['code'];

$shopId = (int) $_GET['shop_id'];


/**
 * ENDPOINT
 */

$path = '/api/v2/auth/token/get';


/**
 * TIMESTAMP
 */

$timestamp = time();


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
 * ASSINATURA
 */

$sign = hash_hmac(
    'sha256',
    $baseString,
    $partnerKey
);


/**
 * URL DA API
 */

$url =
    'https://partner.shopeemobile.com'
    . $path
    . '?partner_id='
    . urlencode($partnerId)
    . '&timestamp='
    . urlencode($timestamp)
    . '&sign='
    . urlencode($sign);


/**
 * PAYLOAD PARA TROCAR O CODE
 */

$payload = [

    'code' => $code,

    'shop_id' => $shopId,

    'partner_id' => $partnerId

];


/**
 * REQUEST CURL
 */

$ch = curl_init($url);

curl_setopt_array(
    $ch,
    [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS =>
            json_encode($payload),

        CURLOPT_HTTPHEADER => [

            'Content-Type: application/json'

        ],

        CURLOPT_TIMEOUT => 30

    ]
);


$response = curl_exec($ch);


$httpCode =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );


$curlError =
    curl_error($ch);


curl_close($ch);


/**
 * ERRO CURL
 */

if (!empty($curlError)) {

    die(
        'Erro cURL: '
        . htmlspecialchars($curlError)
    );
}


/**
 * CONVERTE RESPOSTA
 */

$responseData =
    json_decode(
        $response,
        true
    );


/**
 * CASO A API RETORNE ERRO
 */

if (
    $httpCode !== 200
    || !is_array($responseData)
) {

    echo '<h2>Erro na API Shopee</h2>';

    echo '<pre>';

    echo htmlspecialchars(
        $response
    );

    echo '</pre>';

    exit;
}


/**
 * VALIDA TOKEN
 */

if (
    empty(
        $responseData['access_token']
    )
) {

    echo '<h2>Não foi possível obter o Access Token</h2>';

    echo '<pre>';

    print_r(
        $responseData
    );

    echo '</pre>';

    exit;
}


/**
 * DADOS IMPORTANTES
 */

$accessToken =
    $responseData['access_token'];

$refreshToken =
    $responseData['refresh_token']
    ?? '';

$expireIn =
    (int)(
        $responseData['expire_in']
        ?? 0
    );

$refreshTokenExpireIn =
    (int)(
        $responseData['refresh_token_expire_in']
        ?? 0
    );


/**
 * CALCULA DATA DE EXPIRAÇÃO
 */

$tokenExpiresAt =
    date(
        'Y-m-d H:i:s',
        time() + $expireIn
    );


$refreshTokenExpiresAt =
    date(
        'Y-m-d H:i:s',
        time() + $refreshTokenExpireIn
    );


/**
 * ARQUIVO DE DESTINO
 */

$file =
    __DIR__
    . '/shopee-tokens.txt';


/**
 * CONTEÚDO A SALVAR
 */

$content =

    "====================================\n"

    . "SHOPEE OPEN PLATFORM TOKENS\n"

    . "====================================\n\n"

    . "SHOP ID:\n"

    . $shopId

    . "\n\n"

    . "ACCESS TOKEN:\n"

    . $accessToken

    . "\n\n"

    . "REFRESH TOKEN:\n"

    . $refreshToken

    . "\n\n"

    . "EXPIRE IN:\n"

    . $expireIn

    . " segundos\n\n"

    . "TOKEN EXPIRES AT:\n"

    . $tokenExpiresAt

    . "\n\n"

    . "REFRESH TOKEN EXPIRE IN:\n"

    . $refreshTokenExpireIn

    . " segundos\n\n"

    . "REFRESH TOKEN EXPIRES AT:\n"

    . $refreshTokenExpiresAt

    . "\n\n"

    . "FULL API RESPONSE:\n"

    . json_encode(
        $responseData,
        JSON_PRETTY_PRINT
        | JSON_UNESCAPED_UNICODE
    )

    . "\n";


/**
 * SALVA ARQUIVO
 */

$result =
    file_put_contents(
        $file,
        $content
    );


if ($result === false) {

    die(
        'Não foi possível salvar o arquivo: '
        . htmlspecialchars($file)
    );
}


/**
 * SUCESSO
 */

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <title>
        Shopee autorizada
    </title>

</head>

<body>

    <h1 style="color:green">

        Loja autorizada com sucesso!

    </h1>


    <p>

        Os dados foram salvos no servidor.

    </p>


    <h3>

        Shop ID

    </h3>

    <pre>

<?= htmlspecialchars($shopId) ?>

    </pre>


    <p>

        Arquivo criado:

        <strong>

            shopee-tokens.txt

        </strong>

    </p>

</body>

</html>

