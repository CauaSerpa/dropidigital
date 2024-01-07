<?php
    session_start();
    ob_start();
    include('../../config.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $token = $_POST['token'];

        // Tabela que sera feita a consulta
        $tabela = "tb_users";

        $query_usuario = "SELECT id, password 
                            FROM $tabela 
                            WHERE recup_password = :recup_password  
                            LIMIT 1";
        $result_usuario = $conn_pdo->prepare($query_usuario);
        $result_usuario->bindParam(':recup_password', $token, PDO::PARAM_STR);
        $result_usuario->execute();

        $user = $result_usuario->fetch(PDO::FETCH_ASSOC);

        if ($user)
        {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
            $query_up_usuario = "UPDATE $tabela 
                    SET password = :password,
                    recup_password = NULL
                    WHERE id = :id 
                    LIMIT 1";
            $result_up_usuario = $conn_pdo->prepare($query_up_usuario);
            $result_up_usuario->bindParam(':password', $password, PDO::PARAM_STR);
            $result_up_usuario->bindParam(':id', $user['id'], PDO::PARAM_INT);
    
            if ($result_up_usuario->execute()) {
                $_SESSION['msgcad'] = "Senha atualizada com sucesso!";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
            } else {
                $_SESSION['msg'] = "Erro: Tente novamente!";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "recuperar-senha");
            }
        } else {
            $_SESSION['msg'] = "Erro: Link inválido, solicite novo link para atualizar a senha!";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "recuperar-senha");
        }
    }