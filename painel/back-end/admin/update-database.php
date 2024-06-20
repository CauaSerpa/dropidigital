<?php
include_once('../../../config.php');

try {
    // Inicia a transação
    $conn_pdo->beginTransaction();

    // Seleciona todos os registros da tabela tb_shop
    $sqlSelect = "SELECT id, user_id FROM tb_shop";
    $stmtSelect = $conn_pdo->query($sqlSelect);
    $shops = $stmtSelect->fetchAll();

    // Insere os registros na tabela tb_shop_users
    $sqlInsert = "INSERT INTO tb_shop_users (shop_id, user_id) VALUES (:shop_id, :user_id)";
    $stmtInsert = $conn_pdo->prepare($sqlInsert);

    foreach ($shops as $shop) {
        $stmtInsert->execute([
            ':shop_id' => $shop['id'],
            ':user_id' => $shop['user_id']
        ]);
    }

    // Confirma a transação
    $conn_pdo->commit();
    echo "Dados copiados com sucesso!";
} catch (Exception $e) {
    // Em caso de erro, reverte a transação
    $conn_pdo->rollBack();
    echo "Falha ao copiar os dados: " . $e->getMessage();
}