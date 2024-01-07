<?php
<<<<<<< HEAD

session_start(); // Iniciar a sessão

// Limpar o buffer de saída
ob_start();

// Incluir a conexão com banco de dados
include_once('../../config.php');

// Receber o arquivo do formulário
$arquivo = $_FILES['arquivo'];
//var_dump($arquivo);

$shop_id = $_POST['shop_id'];

// Variáveis de validação
=======
session_start(); // Iniciar a sessão
ob_start();

include_once('../../config.php');

$arquivo = $_FILES['arquivo'];
$shop_id = $_POST['shop_id'];

>>>>>>> 22e4e55354c0bb0c8b99f8ca259d80f5b58aeb3b
$primeira_linha = true;
$linhas_importadas = 0;
$linhas_nao_importadas = 0;
$usuarios_nao_importado = "";

<<<<<<< HEAD
// Verificar se é arquivo csv
if($arquivo['type'] == "text/csv"){

    // Ler os dados do arquivo
    $dados_arquivo = fopen($arquivo['tmp_name'], "r");

    // Percorrer os dados do arquivo
    while($linha = fgetcsv($dados_arquivo, 1000, ";")){

        // Como ignorar a primeira linha do Excel
        if($primeira_linha){
=======
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
>>>>>>> 22e4e55354c0bb0c8b99f8ca259d80f5b58aeb3b
            $primeira_linha = false;
            continue;
        }

<<<<<<< HEAD
        // Usar array_walk_recursive para criar função recursiva com PHP
        array_walk_recursive($linha, 'converter');
        //var_dump($linha);

        // Criar a QUERY para salvar o usuário no banco de dados
        $query_usuario = "INSERT INTO tb_products (shop_id, status, emphasis, name, link, price, discount, description, categories, sku, button_type, redirect_link, seo_name, seo_link, seo_description) VALUES 
                        (:shop_id, :status, :emphasis, :name, :link, :price, :discount, :description, :categories, :sku, :button_type, :redirect_link, :seo_name, :seo_link, :seo_description)";

        // Preparar a QUERY
        $cad_usuario = $conn_pdo->prepare($query_usuario);

        // Substituir os links da QUERY pelos valores
        $cad_usuario->bindValue(':shop_id', $shop_id);
        $cad_usuario->bindValue(':status', ($linha[1] ?? "NULL"));
=======
        array_walk_recursive($linha, 'converter');

        $query_usuario = "INSERT INTO tb_products (shop_id, status, emphasis, name, link, price, discount, description, categories, sku, button_type, redirect_link, seo_name, seo_link, seo_description) VALUES 
                        (:shop_id, :status, :emphasis, :name, :link, :price, :discount, :description, :categories, :sku, :button_type, :redirect_link, :seo_name, :seo_link, :seo_description)";

        $cad_usuario = $conn_pdo->prepare($query_usuario);

        $cad_usuario->bindValue(':shop_id', $shop_id);
        $cad_usuario->bindValue(':status', $status);
>>>>>>> 22e4e55354c0bb0c8b99f8ca259d80f5b58aeb3b
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

<<<<<<< HEAD
        // Executar a QUERY
        $cad_usuario->execute();

        // Verificar se cadastrou corretamente no banco de dados
        if($cad_usuario->rowCount()){
            $linhas_importadas++;
        }else{
=======
        $cad_usuario->execute();

        if ($cad_usuario->rowCount()) {
            $linhas_importadas++;
        } else {
>>>>>>> 22e4e55354c0bb0c8b99f8ca259d80f5b58aeb3b
            $linhas_nao_importadas++;
            $usuarios_nao_importado = $usuarios_nao_importado . ", " . ($linha[0] ?? "NULL");
        }
    }

<<<<<<< HEAD
    // Criar a mensagem com os CPF dos usuários não cadastrados no banco de dados
    if(!empty($usuarios_nao_importado)){
        $usuarios_nao_importado = "Usuários não importados: $usuarios_nao_importado.";
    }

    // Mensagem de sucesso
    $_SESSION['msgcad'] = "<p class='green'>$linhas_importadas linha(s) importa(s), $linhas_nao_importadas linha(s) não importada(s). $usuarios_nao_importado</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
}else{

    // Mensagem de erro
    $_SESSION['msg'] = "<p class='red'>Necessário enviar arquivo csv!</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");

}

// Criar função valor por referência, isto é, quando alter o valor dentro da função, vale para a variável fora da função.
function converter(&$dados_arquivo)
{
    // Converter dados de ISO-8859-1 para UTF-8
=======
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
>>>>>>> 22e4e55354c0bb0c8b99f8ca259d80f5b58aeb3b
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}