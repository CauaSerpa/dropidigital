<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    date_default_timezone_set('America/Sao_Paulo');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Dados
        if (!isset($_POST['step'])) {
            $_SESSION['msg'] = "<p class='red'>Por favor concorde com nossos termos!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        }
        
        if (isset($_POST['setSubdomain'])) {
            $subdomain = $_POST['subdomain'];
        } else {
            $subdomain = "www";
        }
        
        $domain = $_POST['domain'];

        // Obtem a data e hora atual
        $current_date = date('Y-m-d H:i:s');

        if ($subdomain !== "" || $domain !== "") {
            //Tabela que será solicitada
            $tabela = 'tb_domains';
    
            // Insere o dominio no banco de dados
            $sql = "INSERT INTO $tabela (shop_id, subdomain, domain, register_date) VALUES 
                                    (:shop_id, :subdomain, :domain, :register_date)";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->bindValue(':subdomain', $subdomain);
            $stmt->bindValue(':domain', $domain);
            $stmt->bindValue(':register_date', $current_date);
    
            if ($stmt->execute()) {
                $_SESSION['msgcad'] = "<p class='green'>Domínio cadastrado com sucesso!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar o domínio!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
                exit;
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>Por favor preencha todos os campos!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        }
    }