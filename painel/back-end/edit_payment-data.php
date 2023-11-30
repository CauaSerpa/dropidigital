<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Verifique se a solicitação é do tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // User
        $name = $_POST['responsible'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $phone = $_POST['phone'];

        // Address
        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        // Tabela que será solicitada
        $tabela = 'tb_users';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :name, email = :email, docNumber = :cpf, phone = :phone WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':id', $shop_id);
        $stmt->execute();

        // Tabela que será solicitada
        $tabela = 'tb_address';

        // Insere a categoria no banco de dados do endereco da loja
        $sql = "UPDATE $tabela SET cep = :cep, endereco = :endereco, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado WHERE shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':estado', $estado);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->execute();

        // Construa a resposta JSON
        $response = array(
            'success' => true,
            'data' => array(
                'name' => $name,
                'email' => $email,
                'cpf' => $cpf,
                'phone' => $phone,
                'endereco' => $endereco,
                'numero' => $numero,
                'complemento' => $complemento,
                'bairro' => $bairro,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep
            )
        );

        // Saída da resposta JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        // Se a solicitação não for do tipo POST, retorne uma resposta de erro
        $response = array('success' => false, 'message' => 'Método de solicitação inválido.');
        echo json_encode($response);
    }
?>