<?php
    include("config.php");

    $tabela = 'tb_shop';

    $stmt = $conn->prepare("UPDATE $tabela SET plan_id = :plan_id WHERE id = :id");

    // Bind dos parâmetros
    $stmt->bindValue(':plan_id', $_POST['plan_id']);

    $stmt->bindValue(':id', $_POST['shop_id']);

    // Executando o update
    $stmt->execute();