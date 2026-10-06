<?php
    session_start();
    ob_start();
    include_once('../../config.php');
    if (isset($_POST['email'])) {
    // Verificar o email no banco de dados
    // Aqui você deve usar a lógica para verificar se o email já existe

    //Tabela que será solicitada
    $tabela = 'tb_users';

    // Pega o campo email
    $email = $_POST['email'];

    // Verifica se o usuário já existe
    $sql = "SELECT id FROM $tabela WHERE email = :email";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo 'Email já cadastrado!';
    }
}