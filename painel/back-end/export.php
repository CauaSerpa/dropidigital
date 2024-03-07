<?php

session_start();
ob_start();
include_once('../../config.php');

// Carregar a biblioteca PhpSpreadsheet
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$shop_id = $_GET['id'];

$query_usuarios = "SELECT id, status, emphasis, name, link, price, discount, description, sku, button_type, redirect_link, seo_name, seo_link, seo_description FROM tb_products WHERE shop_id = :shop_id ORDER BY id DESC";

$result_usuarios = $conn_pdo->prepare($query_usuarios);
$result_usuarios->bindParam(':shop_id', $shop_id);
$result_usuarios->execute();

if ($result_usuarios and $result_usuarios->rowCount() != 0) {
    // Criar uma nova planilha
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Adicionar cabeçalho
    $cabecalho = ['id', 'Status', 'Destaque', 'Nome', 'Link', 'Preço', 'Desconto', 'Descrição', 'Categorias', 'SKU', 'Tipo do Botão', 'Link de Redirecionamento', 'Nome no SEO', 'Link no SEO', 'Descrição no SEO'];
    $sheet->fromArray($cabecalho, null, 'A1');

    // Adicionar os dados do banco de dados à planilha
    $row = 2;
    while ($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)) {
        $sheet->fromArray($row_usuario, null, 'A' . $row);
        $row++;
    }

    // Criar o nome do arquivo
    $date = date('Y-m-d');
    $filename = "produtos-" . $date . ".xlsx";

    // Configurar o tipo de resposta
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Salvar a planilha no formato XLSX
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
} else {
    $_SESSION['msg'] = "<p class='red'>Nenhum produto encontrado!</p>";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
}