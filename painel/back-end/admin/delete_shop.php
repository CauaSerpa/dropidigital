<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Recebe os dados do formulário
        $id = $_POST['id'];
        $shop_id = $_POST['shop_id'];

        // Consulta SQL
        $sql = "SELECT password FROM $tabela WHERE id = :id";

        // Preparar a consulta
        $stmt = $conn_pdo->prepare($sql);

        // Vincular o valor do parâmetro
        $stmt->bindValue(':id', $id);

        // Executar a consulta
        $stmt->execute();

        // Obter o resultado como um array associativo
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o resultado foi encontrado
        if ($resultado) {
            // Faz login normalmente
            $password = $_POST['password'];

            if (password_verify($password, $resultado['password'])) {
                
                // Credenciais validas, deleta a loja
                $tabela = 'tb_shop';
                $sql = "SELECT * FROM $tabela WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindValue(':id', $shop_id);
                $stmt->execute();
                $shop = $stmt->fetch(PDO::FETCH_ASSOC);

                $tabela = 'tb_shop';
                $sql = "DELETE FROM $tabela WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindValue(':id', $shop['id']);
                $stmt->execute();

                $tabela = 'tb_users';
                $sql = "DELETE FROM $tabela WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindValue(':id', $shop['user_id']);
                $stmt->execute();

                $tabela = 'tb_login';
                $sql = "DELETE FROM $tabela WHERE user_id = :user_id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindValue(':user_id', $shop['user_id']);
                $stmt->execute();

                // Deletar registros
                function deleteRecords($conn_pdo, $shopId, $tableName) {
                    $sql = "DELETE FROM $tableName WHERE shop_id = :shop_id";  // Certifique-se de que a coluna 'shop_id' existe
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':shop_id', $shopId);
                    $stmt->execute();
                }
                
                $shopId = $shop['id'];
                
                // Lista de tabelas a serem excluídas
                $tablesToDelete = array(
                    'tb_products',
                    'tb_pages',
                    'tb_subscriptions',
                    'tb_visits',
                    'tb_scripts',
                    'tb_pages',
                    'tb_newsletter',
                    'tb_invoice_info',
                    'tb_depositions',
                    'tb_categories',
                    'tb_banner_info',
                    // 'tb_banner_img',
                    'tb_articles',
                    'tb_address'
                    // 'imagens'
                );
                
                // Exclui registros de cada tabela
                foreach ($tablesToDelete as $tableName) {
                    deleteRecords($conn_pdo, $shopId, $tableName);
                }

                $_SESSION['msg'] = "<p class='green'>Loja deletada com sucesso!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "lojas");
            } else {
                $_SESSION['msg'] = "<p class='red'>Credenciais inválidas.</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $_POST['shop_id']);
            }
        } else {
            // ID não encontrado ou não existente
            $_SESSION['msg'] = "<p class='red'>ID não encontrado.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $_POST['shop_id']);
        }
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "<p class='red'>Erro ao deletar a loja.</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $_POST['shop_id']);
    }