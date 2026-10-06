<?php
    include_once('../../config.php');

    $q = $_GET['q'] ?? '';

    $sql = "SELECT DISTINCT tag as id, tag as text 
            FROM tb_product_tags 
            WHERE tag LIKE :q 
            LIMIT 10";

    $stmt = $conn_pdo->prepare($sql);
    $stmt->execute([':q' => "%$q%"]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));