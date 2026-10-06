<?php
include('../../config.php');

// Parâmetros da consulta
$shop_id = 2;  // Shop ID do marketplace
$status = 1;   // Apenas produtos ativos
$emphasis = 1; // Apenas produtos em destaque
$without_price = 0; // Apenas produtos com preco
$limit = 8;  // Limite de produtos a serem exibidos

// Nome da tabela de produtos
$tabela = 'tb_products';

// Consulta SQL com LEFT JOIN para incluir as imagens
$sql = "SELECT p.id, p.name, p.price, p.without_price, p.discount, p.description, i.nome_imagem, i.usuario_id 
        FROM $tabela p
        LEFT JOIN imagens i ON i.usuario_id = p.id
        WHERE p.shop_id = :shop_id 
          AND p.status = :status 
          AND p.emphasis = :emphasis 
          AND p.without_price = :without_price 
        ORDER BY p.id ASC 
        LIMIT :limit";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmt->bindValue(':status', $status, PDO::PARAM_INT);
$stmt->bindValue(':emphasis', $emphasis, PDO::PARAM_INT);
$stmt->bindValue(':without_price', $without_price, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

// Recuperar os resultados
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatar as imagens no retorno
foreach ($result as &$product) {
    // Se não houver imagem, definir um caminho padrão
    if (empty($product['nome_imagem'])) {
        $product['imagem_url'] = INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg';
    } else {
        $product['imagem_url'] = CDN_BASE_URL . 'products/' . $product['usuario_id'] . '/' . $product['nome_imagem'];
    }

    // Remover as colunas desnecessárias do resultado
    unset($product['nome_imagem'], $product['usuario_id']);
}

// Retornar os resultados como JSON
header('Content-Type: application/json'); // Definir o tipo de retorno como JSON
echo json_encode($result, JSON_PRETTY_PRINT); // Formatar o JSON de maneira legível

// Fechar a conexão
$conn_pdo = null;
?>