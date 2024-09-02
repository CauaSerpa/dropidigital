<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    // Função para gerar um UUID
    function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // Selecionar todos os usuários sem referral_code
    $stmt = $conn_pdo->prepare("SELECT id FROM tb_users WHERE referral_code IS NULL OR referral_code = ''");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Atualizar cada usuário com um referral_code único
    foreach ($usuarios as $usuario) {
        $referral_code = generateUUID();
        $updateStmt = $conn_pdo->prepare("UPDATE tb_users SET referral_code = :referral_code WHERE id = :id");
        $updateStmt->bindParam(':referral_code', $referral_code);
        $updateStmt->bindParam(':id', $usuario['id']);
        $updateStmt->execute();
    }

    echo "referral_codes adicionados com sucesso!";