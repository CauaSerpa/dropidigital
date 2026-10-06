<?php
include('config.php');

// // 1️⃣ Pegar todos os produtos do shop_id 25
// $stmt = $conn_pdo->prepare("SELECT id, link FROM tb_products WHERE shop_id = 25");
// $stmt->execute();
// $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // 2️⃣ Agrupar por slug base
// $groups = [];

// foreach ($products as $product) {
//     // Pega a parte do link antes do último '-' se existir um número
//     if (preg_match('/^(.*)-\d+$/', $product['link'], $matches)) {
//         $slug_base = $matches[1];
//     } else {
//         $slug_base = $product['link'];
//     }

//     // Agrupa por slug base
//     $groups[$slug_base][] = $product;
// }

// $countDeleted = 0;

// // 3️⃣ Deletar duplicatas deixando 1
// foreach ($groups as $slug_base => $items) {
//     if (count($items) > 1) {
//         // Ordena pelo id para manter o menor
//         usort($items, function($a, $b) {
//             return $a['id'] - $b['id'];
//         });

//         // Mantém o primeiro, remove os outros
//         $keep = array_shift($items); // Este vamos manter
//         $ids_to_delete = array_column($items, 'id');

//         if (!empty($ids_to_delete)) {
//             $in = implode(',', $ids_to_delete);
//             $sql = "DELETE FROM tb_products WHERE id IN ($in)";
//             $conn_pdo->exec($sql);
//             echo "Slug base '$slug_base': deletados IDs " . implode(', ', $ids_to_delete) . "\n";

//             $countDeleted++;
//         }
//     }
// }

// echo "Duplicatas removidas com sucesso! Total: $countDeleted\n"; // Total: 1276

// 1️⃣ Pegar todos os produtos do shop_id 25
$stmt = $conn_pdo->prepare("SELECT id, name FROM tb_products WHERE shop_id = 25");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2️⃣ Agrupar produtos pelo nome (case insensitive)
$groups = [];

foreach ($products as $product) {
    $name_key = mb_strtolower(trim($product['name'])); // ignora maiúsculas/minúsculas
    $groups[$name_key][] = $product;
}

$countDeleted = 0;

// 3️⃣ Deletar duplicatas deixando 1
foreach ($groups as $name_key => $items) {
    if (count($items) > 1) {
        // Ordena pelo id para manter o menor
        usort($items, function($a, $b) {
            return $a['id'] - $b['id'];
        });

        // Mantém o primeiro, remove os outros
        $keep = array_shift($items);
        $ids_to_delete = array_column($items, 'id');

        if (!empty($ids_to_delete)) {
            $in = implode(',', $ids_to_delete);
            $sql = "DELETE FROM tb_products WHERE id IN ($in)";
            // Para teste, comente a linha abaixo
            $conn_pdo->exec($sql);
            
            $countDeleted++;
            echo "Produto '{$keep['name']}': deletados IDs " . implode(', ', $ids_to_delete) . "\n";
        }
    }
}

echo "Duplicatas por nome processadas com sucesso! Total: $countDeleted\n"; // Total: 1524