<?php
    session_start();
    ob_start();
    include('../config.php');

    //Tabela que será solicitada
    $tabela = 'tb_users';

    // Recebe os dados do formulário
    $email = $_POST['email'];

    // Consulta SQL
    $sql = "SELECT id, email, password FROM $tabela WHERE email = :email";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindValue(':email', $email);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            if ($email === $resultado['email'] && password_verify($password, $resultado['password'])) {
                $_SESSION['user_id'] = $resultado['id']; // Você pode definir informações do usuário aqui
                header("Location: " . INCLUDE_PATH_DASHBOARD);
                exit();
            } else {
                $_SESSION['msg'] = "Credenciais inválidas.";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
            }
        }
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "ID não encontrado.";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
    }