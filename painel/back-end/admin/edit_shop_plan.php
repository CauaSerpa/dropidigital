<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        $plan_id = $_POST['plan_id'];
        $cycle = $_POST['cycle'];

        if ($cycle == "YEARLY") {
            $plan_id = $plan_id + 1;
        }

        $start_date = $_POST['start_date'];
        $due_date = $_POST['due_date'];

        if (isset($_POST['undefined'])) {
            $due_date = null;
            $undefined = 1;
        } else {
            $undefined = 0;
        }

        // Padroes
        $value = 0;
        $status = "RECEIVED";

        //Tabela que será solicitada
        $tabela = 'tb_subscriptions';

        $sql = "INSERT INTO $tabela (shop_id, plan_id, value, status, start_date, due_date, undefined, cycle) VALUES 
                                    (:shop_id, :plan_id, :value, :status, :start_date, :due_date, :undefined, :cycle)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':plan_id', $plan_id);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':undefined', $undefined);
        $stmt->bindValue(':cycle', $cycle);

        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET plan_id = :plan_id WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':plan_id', $plan_id);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Plano da loja editado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $shop_id);
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar o plano da loja!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $shop_id);
            exit;
        }
    }