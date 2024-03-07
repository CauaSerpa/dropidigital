<?php
session_start(); // Iniciar a sessão

// Limpar o buffer de saída
ob_start();

// Incluir a conexão com banco de dados
include_once('../../config.php');

// Carregar a biblioteca PhpSpreadsheet
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Receber o arquivo do formulário
$arquivo = $_FILES['arquivo'];
$shop_id = $_POST['shop_id'];

// Variáveis de validação
$primeira_linha = true;
$linhas_importadas = 0;
$linhas_nao_importadas = 0;
$usuarios_nao_importados = [];

if ($arquivo['type'] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
    $spreadsheet = IOFactory::load($arquivo['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    foreach ($sheetData as $linha) {
        if ($primeira_linha) {
            $primeira_linha = false;
            continue;
        }

        array_walk_recursive($linha, 'converter');

        //Tabela que será solicitada
        $tabela = 'tb_products';

        // Verifica se o usuário já existe
        $sql = "SELECT id FROM $tabela WHERE link = :link";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':link', $linha['E']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $linhas_nao_importadas++;
        } else {
            // Link nao encontrado
            $query_usuario = "INSERT INTO tb_products (shop_id, status, emphasis, name, link, price, discount, description, sku, button_type, redirect_link, seo_name, seo_link, seo_description) VALUES 
                            (:shop_id, :status, :emphasis, :name, :link, :price, :discount, :description, :sku, :button_type, :redirect_link, :seo_name, :seo_link, :seo_description)";

            $cad_usuario = $conn_pdo->prepare($query_usuario);

            $cad_usuario->bindValue(':shop_id', $shop_id);
            $cad_usuario->bindValue(':status', ($linha['B'] ?? "NULL"));
            $cad_usuario->bindValue(':emphasis', ($linha['C'] ?? "NULL"));
            $cad_usuario->bindValue(':name', ($linha['D'] ?? "NULL"));
            $cad_usuario->bindValue(':link', ($linha['E'] ?? "NULL"));
            $cad_usuario->bindValue(':price', ($linha['F'] ?? "NULL"));
            $cad_usuario->bindValue(':discount', ($linha['G'] ?? "NULL"));
            $cad_usuario->bindValue(':description', ($linha['H'] ?? "NULL"));
            $cad_usuario->bindValue(':sku', ($linha['J'] ?? "NULL"));
            $cad_usuario->bindValue(':button_type', ($linha['K'] ?? "NULL"));
            $cad_usuario->bindValue(':redirect_link', ($linha['L'] ?? "NULL"));
            $cad_usuario->bindValue(':seo_name', ($linha['M'] ?? "NULL"));
            $cad_usuario->bindValue(':seo_link', ($linha['N'] ?? "NULL"));
            $cad_usuario->bindValue(':seo_description', ($linha['O'] ?? "NULL"));

            $cad_usuario->execute();

            if ($cad_usuario->rowCount()) {
                $linhas_importadas++;
            } else {
                $linhas_nao_importadas++;
                $usuarios_nao_importados[] = $linha['A'] ?? "NULL";
            }
        }
    }

    // Converter o array em uma string
    $usuarios_nao_importado = implode(", ", $usuarios_nao_importados);

    // Mensagem de sucesso
    $_SESSION['msgcad'] = "<p class='green'>$linhas_importadas linha(s) importa(s), $linhas_nao_importadas linha(s) não importada(s). $usuarios_nao_importado</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
} else {
    // Mensagem de erro
    $_SESSION['msg'] = "<p class='red'>Necessário enviar arquivo xlsx!</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
}

function converter(&$dados_arquivo)
{
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}