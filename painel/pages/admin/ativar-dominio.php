<?php
    echo verificaPermissaoPagina($permissions);
?>
<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Obtem a data e hora atual
        $current_date = date('Y-m-d H:i:s');

        //Tabela que será solicitada
        $tabela = 'tb_domains';

        // Insere o dominio no banco de dados
        $sql = "UPDATE $tabela SET status = :status, active_date = :active_date WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':status', 1);
        $stmt->bindValue(':active_date', $current_date);

        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Domínio ativo com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "dominios-proprios");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao ativar o domínio!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "dominios-proprios");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um domínio!';
        header("Location: " . INCLUDE_PATH_ADMIN . "dominios-proprios");
        exit;
    }