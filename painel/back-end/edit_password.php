<?php
session_start();
ob_start();
include_once('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Substitua isso pela lógica real para obter o ID do usuário logado
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    // Adicione lógica para verificar a senha atual do usuário no banco de dados
    $tabela = 'tb_users';
    $query = "SELECT password FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($currentPassword, $user['password'])) {
        // A senha atual está correta
        if ($newPassword === $confirmNewPassword) {
            // As novas senhas coincidem
            if (!password_verify($newPassword, $user['password'])) {
                // A nova senha é diferente da senha atual

                // Hash da nova senha
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Atualize a senha no banco de dados
                $tabela = 'tb_users';
                $updateQuery = "UPDATE $tabela SET password = :password WHERE id = :id";
                $updateStmt = $conn_pdo->prepare($updateQuery);
                $updateStmt->bindParam(':password', $hashedPassword);
                $updateStmt->bindParam(':id', $user_id);

                if ($updateStmt->execute()) {
                    $_SESSION['msg'] = "<p class='green'>Senha atualizada com sucesso!</p>";
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao atualizar a senha.</p>";
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>A nova senha não pode ser igual à senha atual.</p>";
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>As novas senhas não coincidem.</p>";
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Senha atual incorreta.</p>";
    }

    // Redireciona para a página de configurações de segurança
    header("Location: " . INCLUDE_PATH_DASHBOARD . "configuracoes/seguranca");
    exit;
}