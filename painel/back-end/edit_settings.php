<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $id = $_POST['id'];
        $shop_id = $_POST['shop_id'];

        // Shop
        $name = $_POST['name'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $segment = $_POST['segment'];
        $detailed_segment = $_POST['detailed_segment'];

        if ($_POST['shopType'] == "pf") {
            $cpfCnpj = $_POST['cpf'];
            $phone = $_POST['phone'];
            $razaoSocial = "";
        } else {
            $razaoSocial = $_POST['razaoSocial'];
            $cpfCnpj = $_POST['cnpj'];
            $phone = $_POST['cellPhone'];
        }

        // User
        $responsible = $_POST['responsible'];

        // Address
        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        if (isset($_POST['activeMaps']) && $_POST['activeMaps'] == '1') {
            $activeMaps = $_POST['activeMaps'];
        } else {
            $activeMaps = 0;
        }

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :name, title = :title, description = :description, cpf_cnpj = :cpf_cnpj, razao_social = :razao_social, phone = :phone, segment = :segment, detailed_segment = :detailed_segment, map = :map WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':cpf_cnpj', $cpfCnpj);
        $stmt->bindValue(':razao_social', $razaoSocial);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':detailed_segment', $detailed_segment);
        $stmt->bindValue(':map', $activeMaps);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        //Executar o stmt
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :responsible WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':responsible', $responsible);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        //Executar o stmt
        $stmt->execute();

        //Tabela que será solicitada
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

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Configurações editadas com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configuracoes");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar as configurações!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configuracoes");
            exit;
        }
    }