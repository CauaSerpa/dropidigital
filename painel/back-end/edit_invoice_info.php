<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Verifique se a solicitação é do tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $shopType = $_POST['shopType'];
        if ($shopType == "pf")
        {
            $docType = "cpf";
            $docNumber = $_POST['cpf'];
        } else {
            $docType = "cnpj";
            $docNumber = $_POST['cnpj'];
        }

        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        // Tabela que será solicitada
        $tabela = 'tb_invoice_info';

        // Insere a categoria no banco de dados do endereco da loja
        $sql = "UPDATE $tabela SET name = :name, email = :email, phone = :phone, docType = :docType, docNumber = :docNumber, cep = :cep, endereco = :endereco, numero = :numero, complemento = :complemento, bairro = :bairro, cidade = :cidade, estado = :estado WHERE shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':docType', $docType);
        $stmt->bindValue(':docNumber', $docNumber);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':estado', $estado);

        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->execute();

        $_SESSION['msgcad'] = "<p class='green'>Informações da fatura editadas com sucesso!</p>";
        // Redireciona para a página e exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "dados-para-pagamento");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar as informações da fatura!</p>";
        // Redireciona para a página e exibe uma mensagem de erro
        header("Location: " . INCLUDE_PATH_DASHBOARD . "dados-para-pagamento");
    }