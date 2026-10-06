<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Consulta para excluir a página do banco de dados
        $query = "DELETE FROM tb_pages WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Página deletada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhuma página encontrada para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar uma página!';
        header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
    }