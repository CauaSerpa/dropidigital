<?php
    echo verificaPermissaoPagina($permissions);
?>
<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $shop_id = filter_input(INPUT_GET, 'shop', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        $tabela = "tb_warning";

        // Consulta para excluir o banner do banco de dados
        $query = "DELETE FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $_SESSION['msg'] = "<p class='green'>Aviso deletado com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $shop_id);
        exit;
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um aviso!';
        header("Location: " . INCLUDE_PATH_ADMIN . "ver-loja?id=" . $shop_id);
        exit;
    }