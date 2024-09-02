<?php
    session_start();
    ob_start();
    include_once('../config.php');

    if (isset($_SESSION['user_id'])) {
        $_SESSION['msg'] = "<p class='red'>Erro você já possui uma conta na Dropi Digital.</p>";
        header('Location: ' . INCLUDE_PATH_DASHBOARD);
        exit();
    }

    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        $tabela = 'tb_users';
        $query = "SELECT * FROM $tabela WHERE referral_code = :referral_code LIMIT 1";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':referral_code', $token);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['code'] = $token;

            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: ".INCLUDE_PATH_DASHBOARD."assinar?code=".$token);
            exit;
        } else {
            echo json_encode(['status' => 500, 'error' => 'Erro o código de convite é inválido.']);
            exit;
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro nenhum código foi encontrado.</p>";
        header('Location: ' . INCLUDE_PATH_DASHBOARD);
        exit();
    }