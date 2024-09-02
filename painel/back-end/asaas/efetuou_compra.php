<?php
    function efetuouCompra() {
        session_start();
        ob_start();
        include('config.php');

        $user_id = $_SESSION['user_id'];

        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Verifica se o usuário já existe
        $sql = "SELECT email, referral_code_used FROM $tabela WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $user_id);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            //Tabela que será solicitada
            $tabela = 'tb_users';
    
            // Verifica se o usuário já existe
            $sql = "SELECT id FROM $tabela WHERE referral_code = :referral_code";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':referral_code', $user['referral_code_used']);
            $stmt->execute();
            
            $indicator = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($indicator) {
                //Tabela que será solicitada
                $tabela = 'tb_indication';

                // Verifica se o usuário já existe
                $sql = "SELECT number_purchases FROM $tabela WHERE indicator_id = :indicator_id AND guest_id = :guest_id AND guest_email = :guest_email";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':indicator_id', $indicator['id']);
                $stmt->bindValue(':guest_id', $user_id);
                $stmt->bindValue(':guest_email', $user['email']);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $total_number_purchases = (int)$result['number_purchases'];
                $number_purchases = $total_number_purchases + 1;

                // Obtém a data atual no formato desejado
                $date_bought_something = date('Y-m-d H:i:s');

                //Tabela que será solicitada
                $tabela = 'tb_indication';

                // Adiciona o referral_code a tabela
                $sql = "UPDATE $tabela SET status = :status, number_purchases = :number_purchases, date_bought_something = :date_bought_something WHERE indicator_id = :indicator_id AND guest_id = :guest_id AND guest_email = :guest_email";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':status', 'bought-something');
                $stmt->bindValue(':number_purchases', $number_purchases);
                $stmt->bindValue(':date_bought_something', $date_bought_something);

                $stmt->bindValue(':indicator_id', $indicator['id']);
                $stmt->bindValue(':guest_id', $user_id);
                $stmt->bindValue(':guest_email', $user['email']);

                $stmt->execute();

                

                // Obtém a data atual no formato desejado
	            $dueDate = date('Y-m-d', strtotime('+3 month'));

                //Tabela que será solicitada
                $tabela = 'tb_rewards';

                // Adiciona o referral_code a tabela
                $sql = "INSERT INTO $tabela (indicator_id, due_date) VALUES (:indicator_id, :due_date)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':indicator_id', $indicator['id']);
                $stmt->bindValue(':due_date', $dueDate);

                $stmt->execute();
            }
        }
    }