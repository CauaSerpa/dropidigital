<?php
include('config.php');

$tabela = "tb_products";

// Consulta ao banco de dados para selecionar os produtos
$sql = "SELECT * FROM $tabela";
$result = $conn_pdo->query($sql);

// Início do documento XML
$xml = new XMLWriter();
$xml->openURI("produtos.xml");
$xml->startDocument('1.0', 'UTF-8');
$xml->setIndent(true);
$xml->startElement('produtos');

// Itera sobre os resultados da consulta
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $xml->startElement('produto');
    $xml->writeElement('id', $row['id']);
    $xml->writeElement('nome', $row['name']);
    $xml->writeElement('preco', $row['price']);
    // Adicione mais campos conforme necessário
    $xml->endElement(); //produto
}

// Fim do documento XML
$xml->endElement(); //produtos
$xml->endDocument();
$xml->flush();

// Fecha a conexão com o banco de dados
$conn_pdo = null;

echo "O arquivo XML dos produtos foi gerado com sucesso!";
?>