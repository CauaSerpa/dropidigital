<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($_POST['status']) && $_POST['status'] == '1') {
        $status = $_POST['status'];
    } else {
        $status = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_scripts';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET status = :status, name = :name, script = :script WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':name', $dados['name']);
        $stmt->bindValue(':script', $dados['script']);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();

        $_SESSION['msgcad'] = "<p class='green'>Código HTML editado com sucesso!</p>";
        // Redireciona para a código HTML de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar o código HTML!</p>";
        // Redireciona para a código HTML de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
    }