<?php
session_start(); // Iniciar a sessão
ob_start();

include_once('../../config.php');

$arquivo = $_FILES['arquivo'];
$shop_id = $_POST['shop_id'];

$primeira_linha = true;
$linhas_importadas = 0;
$linhas_nao_importadas = 0;
$usuarios_nao_importado = "";

// Conta o número de produtos ativos
$sqlCountProducts = "SELECT COUNT(*) AS total_produtos FROM tb_products WHERE shop_id = :shop_id AND status = :status_ativo";
$stmtCountProducts = $conn_pdo->prepare($sqlCountProducts);
$stmtCountProducts->bindParam(':shop_id', $shop_id);
$stmtCountProducts->bindValue(':status_ativo', 1);
$stmtCountProducts->execute();
$countProducts = $stmtCountProducts->fetch(PDO::FETCH_ASSOC)['total_produtos'];

$limitProductsMap = [
    1 => 10,
    2 => 50,
    3 => 250,
    4 => 750,
];

$limitProducts = $limitProductsMap[$shop_id] ?? PHP_INT_MAX; // Usar PHP_INT_MAX para indicar "ilimitado"

$status = ($limitProducts < $countProducts) ? 0 : 1;

if ($arquivo['type'] == "text/csv") {
    $dados_arquivo = fopen($arquivo['tmp_name'], "r");

    while ($linha = fgetcsv($dados_arquivo, 1000, ";")) {
        if ($primeira_linha) {
            $primeira_linha = false;
            continue;
        }

        array_walk_recursive($linha, 'converter');

        $query_usuario = "INSERT INTO tb_products (shop_id, status, emphasis, name, link, price, discount, description, categories, sku, button_type, redirect_link, seo_name, seo_link, seo_description) VALUES 
                        (:shop_id, :status, :emphasis, :name, :link, :price, :discount, :description, :categories, :sku, :button_type, :redirect_link, :seo_name, :seo_link, :seo_description)";

        $cad_usuario = $conn_pdo->prepare($query_usuario);

        $cad_usuario->bindValue(':shop_id', $shop_id);
        $cad_usuario->bindValue(':status', $status);
        $cad_usuario->bindValue(':emphasis', ($linha[2] ?? "NULL"));
        $cad_usuario->bindValue(':name', ($linha[3] ?? "NULL"));
        $cad_usuario->bindValue(':link', ($linha[4] ?? "NULL"));
        $cad_usuario->bindValue(':price', ($linha[5] ?? "NULL"));
        $cad_usuario->bindValue(':discount', ($linha[6] ?? "NULL"));
        $cad_usuario->bindValue(':description', ($linha[7] ?? "NULL"));
        $cad_usuario->bindValue(':categories', ($linha[8] ?? "NULL"));
        $cad_usuario->bindValue(':sku', ($linha[9] ?? "NULL"));
        $cad_usuario->bindValue(':button_type', ($linha[10] ?? "NULL"));
        $cad_usuario->bindValue(':redirect_link', ($linha[11] ?? "NULL"));
        $cad_usuario->bindValue(':seo_name', ($linha[12] ?? "NULL"));
        $cad_usuario->bindValue(':seo_link', ($linha[13] ?? "NULL"));
        $cad_usuario->bindValue(':seo_description', ($linha[14] ?? "NULL"));

        $cad_usuario->execute();

        if ($cad_usuario->rowCount()) {
            $linhas_importadas++;
        } else {
            $linhas_nao_importadas++;
            $usuarios_nao_importado = $usuarios_nao_importado . ", " . ($linha[0] ?? "NULL");
        }
    }

    if (!empty($usuarios_nao_importado)) {
        $usuarios_nao_importado = "Usuários não importados: $usuarios_nao_importado.";
    }

    $_SESSION['msgcad'] = "<p class='green'>$linhas_importadas linha(s) importa(s), $linhas_nao_importadas linha(s) não importada(s). $usuarios_nao_importado</p>";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
} else {
    $_SESSION['msg'] = "<p class='red'>Necessário enviar arquivo csv!</p>";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
}

function converter(&$dados_arquivo)
{
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}