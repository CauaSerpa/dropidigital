<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Consulta para excluir a código HTML do banco de dados
        $query = "DELETE FROM tb_scripts WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Código HTML deletado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum código HTML encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um código HTML!';
        header("Location: " . INCLUDE_PATH_DASHBOARD . "codigos-html");
    }