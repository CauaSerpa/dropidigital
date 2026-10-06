<?php

session_start(); // Iniciar a sessão

// Limpar o buffer
ob_start();

// Incluir a conexão com BD
include_once('../../config.php');

$shop_id = $_GET['id'];

// QUERY para recuperar os registros do banco de dados
$query_usuarios = "SELECT email FROM tb_newsletter WHERE shop_id = :shop_id ORDER BY email DESC";

// Preparar a QUERY
$result_usuarios = $conn_pdo->prepare($query_usuarios);

// BIND para filtrar pelo id da loja
$result_usuarios->bindParam(':shop_id', $shop_id);

// Executar a QUERY
$result_usuarios->execute();

// Acessa o IF quando encontrar registro no banco de dados
if(($result_usuarios) and ($result_usuarios->rowCount() != 0)){

    // Aceitar csv ou texto 
    header('Content-Type: text/csv; charset=utf-8');

    $date = date('Y-m-d');

    // Nome arquivo
    header('Content-Disposition: attachment; filename=newsletter-' . $date . '.csv');

    // Gravar no buffer
    $resultado = fopen("php://output", 'w');

    // Criar o cabeçalho do Excel - Usar a função mb_convert_encoding para converter carateres especiais
    $cabecalho = ['E-mail'];

    // Escrever o cabeçalho no arquivo
    fputcsv($resultado, $cabecalho, ';');

    // Ler os registros retornado do banco de dados
    while($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)){

        // Escrever o conteúdo no arquivo
        fputcsv($resultado, $row_usuario, ';');

    }

    // Fechar arquivo
    fclose($resultado);
}else{ // Acessa O ELSE quando não encontrar nenhum registro no BD
    $_SESSION['msg'] = "<p class='red'>Nenhum e-mail encontrado!</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD . "newsletter");
}