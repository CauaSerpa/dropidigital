<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_scripts';
        
        //Id da loja
        $shop_id = $_POST['shop_id'];

        if (isset($_POST['status']) && $_POST['status'] == '1') {
            $status = $_POST['status'];
        } else {
            $status = 0;
        }

        // Recebe os dados do formulário
        $name = $_POST['name'];
        $script = $_POST['script'];

        // Insere a categoria no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, status, name, script) VALUES 
                                    (:shop_id, :status, :name, :script)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':script', $script);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Código HTML incluído com sucesso!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao criar ao incluir o código HTML!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
            exit;
        }
    }