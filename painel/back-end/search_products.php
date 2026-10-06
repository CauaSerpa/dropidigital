<?php

require_once('../../config.php');

header('Content-Type: application/json');


$search = $_POST['search'] ?? '';
$shop_id = $_POST['shop_id'] ?? 0;


if(strlen($search) < 3){
    echo json_encode([]);
    exit;
}


$sql = "
SELECT 
    id,
    name

FROM tb_products

WHERE shop_id = ?
AND name LIKE ?

ORDER BY name ASC

LIMIT 20
";


$stmt = $conn_pdo->prepare($sql);

$stmt->execute([
    $shop_id,
    "%".$search."%"
]);


$products = [];


while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $products[] = [
        "id" => $row['id'],
        "text" => $row['name']
    ];

}


echo json_encode($products);