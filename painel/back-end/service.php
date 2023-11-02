<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $phone = $_POST['phone'];

        if ($_POST['country_code'] == "")
        {
            $country_code = $_POST['country_code'];
        } else {
            $country_code = "+55";
        }

        $phone_number = $_POST['phone_number'];

        if ($phone_number !== "")
        {
            // Unir codigo do pais e numero de telefone
            $whatsapp = $country_code . " " . $phone_number;
        }

        $email = $_POST['email'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET phone = :phone, whatsapp = :whatsapp, email = :email WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':whatsapp', $whatsapp);
        $stmt->bindValue(':email', $email);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Informações de atendimento editadas com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "atendimento");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar as informações de atendimento!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "atendimento");
            exit;
        }
    }