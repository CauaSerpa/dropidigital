<?php
// Conecte-se ao banco de dados aqui
require('../../../config.php');

// Saber qual e o subdominio para pegar o id do shop que o email esta sendo deletado
$dominioCompleto = $_SERVER['HTTP_HOST'];

// Divide o domínio completo em partes usando o ponto como separador
$partesDoDominio = explode('.', $dominioCompleto);

// O subdomínio estará na primeira parte do array (índice 0)
$subdominio = $partesDoDominio[0];

// Verifique se há www como subdomínio padrão e o remova, se presente
if ($subdominio === 'www') {
    $subdominio = '';
}

if (isset($_GET['email'])) {
    // Tabela que sera feita a exclusao
    $tabela = "tb_newsletter";

    // E-mail que sera deletado
    $shop_id = 1;

    // E-mail que sera deletado
    $email = $_GET['email'];

    $query = "SELECT email FROM $tabela WHERE shop_id = :shop_id AND email = :email";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Consulta para excluir a categoria do banco de dados
        $query = "DELETE FROM $tabela WHERE shop_id = :shop_id AND email = :email";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':shop_id', $shop_id);

        if ($stmt->execute()) {
            echo "Seu e-mail foi removido de nossa newsletter com sucesso!";
            exit;
        }
    } else {
        echo "Nenhum e-mail encontrado para remoção.";
        exit;
    }
} else {
    // Caso o parâmetro 'email' não seja fornecido na URL, você pode exibir uma página de erro ou redirecionar para a página inicial.
    header("Location: " . INCLUDE_PATH . "loja/");
    exit();
}
?>