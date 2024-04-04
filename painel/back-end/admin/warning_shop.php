<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_warning';

        // Edita o produto no banco de dados da loja
        $sql = "INSERT INTO $tabela (shop_id, level, type, title, content) VALUES 
                                    (:shop_id, :level, :type, :title, :content)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->bindParam(':level', $dados['level']);
        $stmt->bindParam(':type', $dados['type']);
        $stmt->bindParam(':title', $dados['title']);
        $stmt->bindParam(':content', $dados['content']);

        $stmt->execute();

        $_SESSION['msgcad'] = "<p class='green'>Aviso criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $dados['shop_id']);
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao criar o aviso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $dados['shop_id']);
    }