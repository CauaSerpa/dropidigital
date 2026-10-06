<?php
session_start();
ob_start();
include_once('../../config.php');

// Tabela a ser verificada
$tabela = 'tb_products';

// Verifica se o campo URL foi enviado via POST
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $actualUrl = (isset($_POST['actualUrl'])) ? $_POST['actualUrl'] : null;
    $produto_id = (isset($_POST['produto_id'])) ? $_POST['produto_id'] : null;
    $shop_id = $_POST['shop_id'];

    if ($url == $actualUrl) {
        echo $url;
        exit;
    }

    // Verifica se o URL já existe na tabela
    $sql = "SELECT COUNT(*) as count FROM $tabela WHERE link LIKE :link AND shop_id = :shop_id";
    if (!empty($produto_id)) {
        $sql .= " AND id != :produto_id";
    }
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':link', $url . '%');
    if (!empty($produto_id)) {
        $stmt->bindValue(':produto_id', $produto_id);
    }
    $stmt->bindValue(':shop_id', $shop_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se existir duplicatas, sugere um novo link com sufixo
    if ($result['count'] > 0) {
        $count = $result['count'];
        echo $url . '-' . $count;
    } else {
        echo $url; // URL disponível
    }
}