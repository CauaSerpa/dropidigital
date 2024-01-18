<?php
    $token = $_GET['token'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Adiciona o token a tabela
        $sql = "UPDATE $tabela SET active_email = :active_email, two_factors = :two_factors, token = NULL WHERE token = :token";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':active_email', 1);
        $stmt->bindValue(':two_factors', 1);

        // Id que sera editado
        $stmt->bindValue(':token', $token);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>E-mail ativado com sucesso!</p>";

            // Redireciona para a página de configuracoes e exibe uma mensagem de sucesso
            header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes");
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao ativar o e-mail!</p>";

            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro: É necessário um token!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
    }
    exit;