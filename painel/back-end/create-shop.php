<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Pega o id do usuario que criou a loja
        $user_id = $_POST['user_id'];

        // Recebe os dados do formulário
        $name = $_POST['name'];

        // Criar subdminio do site
        // Pega post url
        $url = $_POST['url'];

        if ($url == '')
        {
            // Url não preenchida utiliza o post name para criar
            // Transforma o texto em minúsculas
            $texto = strtolower($name);

            // Separa o texto em um array de palavras
            $palavras = explode(' ', $texto);

            // Junta as palavras com "-"
            $url = implode('-', $palavras);
        }

        // Verifica se a Url já existe
        $sql = "SELECT id FROM $tabela WHERE url = :url";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':url', $url);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mostra formulario de url
            $_SESSION['input_url'] = "show";

            // Mensagem de erro
            $_SESSION['msg_url'] = "<p class='danger'>A URL já está sendo utilizado. Escolha outra URL.</p>";
            
            //Link de redirecionamento
            $redirect_url = INCLUDE_PATH_DASHBOARD . 'criar-loja';
            header('Location: ' . $redirect_url);

            //Mata o processo
            die();
        }
        
        $segment = $_POST['segment'];
        
        $person = $_POST['person'];
        // Verifica se e pessoa fisica ou pessoa juridica
        if ($person == 'pj')
        {
            $cpf_cnpj = $_POST['cnpj'];
            $razao_social = $_POST['razao_social'];
        } else {
            $cpf_cnpj = $_POST['cpf'];
        }

        $razao_social = $_POST['razao_social'];
        $phone = $_POST['phone'];

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (user_id, name, url, segment, cpf_cnpj, razao_social, phone) VALUES 
                                    (:user_id, :name, :url, :segment, :cpf_cnpj, :razao_social, :phone)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':cpf_cnpj', $cpf_cnpj);
        $stmt->bindValue(':razao_social', $razao_social);
        $stmt->bindValue(':phone', $phone);
        $stmt->execute();

        // Recebendo id da loja
        $shop_id = $conn_pdo->lastInsertId();

        //Tabela que será solicitada
        $tabela = 'tb_invoice_info';

        // Passando valores
        $email = $_POST['email'];

        if ($person == 'pj')
        {
            $name = $razao_social;
            $docType = "cnpj";
            $docNumber = $cpf_cnpj;
        } else {
            $name = $name;
            $docType = "cpf";
            $docNumber = $cpf_cnpj;
        }

        // Inserindo informações da fatura no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, name, email, phone, docType, docNumber, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES 
                                    (:shop_id, :name, :email, :phone, :docType, :docNumber, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
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
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_subscriptions';
        
        // Passando valores
        $subscription_id = 1;
        $value = 0;
        $status = "RECIVED";
        $cycle = "MONTHLY";
        
        // Obtém a data atual e adiciona um mês
        $start_date = new DateTime();
        $date_clone = clone $start_date;
        $due_date = $date_clone->add(new DateInterval('P1M'));

        // Criado plano para o cliente no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, subscription_id, value, status, start_date, due_date, cycle) VALUES 
                                    (:shop_id, :subscription_id, :value, :status, :start_date, :due_date, :cycle)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':subscription_id', $subscription_id);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':status', $status);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindValue(':cycle', $cycle);
        $stmt->execute();

        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: ".INCLUDE_PATH_DASHBOARD);
        exit;
    }
?>