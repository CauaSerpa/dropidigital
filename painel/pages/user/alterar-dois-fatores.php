<?php
    $token = $_GET['token'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Tabela que será solicitada
        $tabela = 'tb_users';

        // Verifica se o usuário já existe
        $sql = "SELECT two_factors FROM $tabela WHERE two_factors_token = :two_factors_token";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':two_factors_token', $token);
        $stmt->execute();

        // Obter o resultado como um array associativo
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Troca o valor do parâmetro :two_factors com base no valor atual do campo two_factors
            $two_factors = ($user['two_factors'] == 1) ? 0 : 1;

            // Adiciona o token a tabela
            $sql = "UPDATE $tabela SET two_factors = :two_factors, two_factors_token = NULL WHERE two_factors_token = :two_factors_token";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':two_factors', $two_factors, PDO::PARAM_INT);

            // Id que sera editado
            $stmt->bindParam(':two_factors_token', $token);

            if ($stmt->execute()) {
                $mensagem = ($two_factors == 1) ? "ativado" : "desativado";
                $_SESSION['msgcad'] = "<p class='green'>Dois fatores $mensagem com sucesso!</p>";

                // Redireciona para a página de configurações e exibe uma mensagem de sucesso
                header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
            } else {
                $mensagem = ($two_factors == 1) ? "ativar" : "desativar";
                $_SESSION['msg'] = "<p class='red'>Erro ao $mensagem os dois fatores!</p>";

                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
    }
    exit;