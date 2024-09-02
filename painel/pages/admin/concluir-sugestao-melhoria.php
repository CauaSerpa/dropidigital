
<?php
    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Obtem a data e hora atual
        $current_date = date('Y-m-d H:i:s');

        //Tabela que será solicitada
        $tabela = 'tb_improvement';

        // Insere o dominio no banco de dados
        $sql = "UPDATE $tabela SET finished = :finished, date_finished = :date_finished WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':finished', 1);
        $stmt->bindValue(':date_finished', $current_date);
        
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Sugestão concluída com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "sugestoes-melhorias?id=" . $id);
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao concluir a sugestão!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "sugestoes-melhorias?id=" . $id);
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar uma sugestão!';
        header("Location: " . INCLUDE_PATH_ADMIN . "sugestoes-melhorias");
        exit;
    }