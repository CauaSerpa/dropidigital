<?php

session_start();

require_once dirname(__DIR__, 3) . '/config.php';
require_once __DIR__ . '/App/GoogleOAuth.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Usuário não autenticado.');
}

$shopId = isset($_GET['shop_id'])
    ? (int) $_GET['shop_id']
    : 0;

if ($shopId <= 0) {
    http_response_code(400);
    exit('Loja inválida.');
}

$stmt = $conn_pdo->prepare("
    SELECT
        id,
        name
    FROM tb_shop
    WHERE id = :shop_id
      AND user_id = :user_id
    LIMIT 1
");

$stmt->execute([
    ':shop_id' =>
        $shopId,

    ':user_id' =>
        (int) $_SESSION['user_id']
]);

$shop =
    $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop) {
    http_response_code(403);
    exit('Você não possui acesso a esta loja.');
}

$state =
    bin2hex(
        random_bytes(32)
    );

$_SESSION['google_oauth_state'] =
    $state;

$_SESSION['google_oauth_shop_id'] =
    $shopId;

try {

    $googleOAuth =
        new GoogleOAuth();

    $authorizationUrl =
        $googleOAuth->getAuthorizationUrl(
            $state
        );

    header(
        'Location: '
        . $authorizationUrl
    );

    exit;

} catch (Throwable $e) {

    http_response_code(500);

    exit(
        'Não foi possível iniciar a conexão com o Google Analytics.'
    );
}