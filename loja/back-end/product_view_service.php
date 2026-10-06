<?php
    function trackProductView($conn_pdo, $shop_id, $product_id) {
        // Ignorar bots
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/bot|crawl|slurp|spider/i', $userAgent)) {
            return;
        }

        // Criar identificador único (cookie)
        if (!isset($_COOKIE['user_id'])) {
            $userId = bin2hex(random_bytes(16));
            setcookie('user_id', $userId, time() + (86400 * 30), "/");
        } else {
            $userId = $_COOKIE['user_id'];
        }

        // Evitar múltiplas contagens (30 min)
        $sql = "SELECT id FROM tb_product_views 
                WHERE product_id = :product_id 
                AND user_id = :user_id 
                AND viewed_at >= NOW() - INTERVAL 30 MINUTE";

        $stmt = $conn_pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id,
            ':user_id' => $userId
        ]);

        if ($stmt->rowCount() > 0) {
            return;
        }

        // Inserir nova visualização
        $sql = "INSERT INTO tb_product_views 
                (product_id, shop_id, user_id, ip, viewed_at) 
                VALUES (:product_id, :shop_id, :user_id, :ip, NOW())";

        $stmt = $conn_pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id,
            ':shop_id' => $shop_id,
            ':user_id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR']
        ]);
    }

    session_start();
    include_once('../../config.php');

    $product_id = $_POST['product_id'] ?? null;
    $shop_id = $_POST['shop_id'] ?? null;

    if (!$product_id || !$shop_id) {
        http_response_code(400);
        exit;
    }

    trackProductView($conn_pdo, $shop_id, $product_id);

    echo json_encode(['status' => 'ok']);