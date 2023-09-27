<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Consulta para excluir a categoria do banco de dados
        $query = "DELETE FROM tb_categories WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Categoria deletada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhuma categoria encontrada para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar uma categoria!';
        header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
    }