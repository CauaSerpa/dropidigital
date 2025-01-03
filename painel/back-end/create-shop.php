<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pega o id do usuario que criou a loja
        $user_id = $_SESSION['user_id_for_create_shop'];

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

            // Remove pontos e vírgulas
            $texto = str_replace(['.', ','], '', $texto);

            // Separa o texto em um array de palavras
            $palavras = explode(' ', $texto);

            // Junta as palavras com "-"
            $url = implode('-', $palavras);
        }

        //Tabela que será solicitada
        $tabela = 'tb_domains';
        
        // Verifica se a Url já existe
        $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);
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
        $detailed_segment = $_POST['detailed_segment'];
        
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

        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        $phone = $_POST['phone'];

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (user_id, name, segment, detailed_segment, cpf_cnpj, razao_social, phone) VALUES 
                                    (:user_id, :name, :segment, :detailed_segment, :cpf_cnpj, :razao_social, :phone)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':detailed_segment', $detailed_segment);
        $stmt->bindValue(':cpf_cnpj', $cpf_cnpj);
        $stmt->bindValue(':razao_social', $razao_social);
        $stmt->bindValue(':phone', $phone);
        $stmt->execute();

        // Recebendo id da loja
        $shop_id = $conn_pdo->lastInsertId();

        //Tabela que será solicitada
        $tabela = 'tb_shop_users';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (user_id, shop_id) VALUES (:user_id, :shop_id)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_domains';

        $domain = "dropidigital.com.br";

        // Obtem a data e hora atual
        date_default_timezone_set('America/Sao_Paulo');
        $current_date = date('Y-m-d H:i:s');
    
        // Insere o dominio no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, subdomain, domain, register_date) VALUES 
                                (:shop_id, :subdomain, :domain, :register_date)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':subdomain', $url);
        $stmt->bindValue(':domain', $domain);
        $stmt->bindValue(':register_date', $current_date);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_address';

        // Inserindo informações da fatura no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES 
                                    (:shop_id, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':estado', $estado);
        $stmt->execute();

        // Nome da tabela para a busca
        $tabela = 'tb_users';

        // Consulta SQL para contar os produtos na tabela
        $sql = "SELECT name FROM $tabela WHERE id = :id LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        // Recupere o resultado da consulta
        $name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

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

        //Tabela que será solicitada
        $tabela = 'tb_invoice_info';

        // Passando valores
        $email = $_POST['email'];

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
        $plan_id = 1;
        $value = 0;
        $status = "RECEIVED";
        $cycle = "MONTHLY";
        
        // Obtém a data atual
        $today = new DateTime();

        // Adiciona um mês à data atual para obter a data de vencimento
        $due_date = clone $today;
        $due_date->add(new DateInterval('P1M'));

        // Formata as datas conforme necessário
        $start_date_formatted = $today->format('Y-m-d H:i:s');
        $due_date_formatted = $due_date->format('Y-m-d H:i:s');

        // Criado plano para o cliente no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, plan_id, value, status, start_date, due_date, cycle) VALUES 
                                    (:shop_id, :plan_id, :value, :status, :start_date, :due_date, :cycle)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':plan_id', $plan_id);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':status', $status);
        $stmt->bindParam(':start_date', $start_date_formatted);
        $stmt->bindParam(':due_date', $due_date_formatted);
        $stmt->bindValue(':cycle', $cycle);
        $stmt->execute();

        // Cria a sessao com o id do usuario para login
        $_SESSION['user_id'] = $user_id;

        // Destroi a sessao com o id do usuario para criar a loja
        unset($_SESSION['user_id_for_create_shop']);

        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: ".INCLUDE_PATH_DASHBOARD);
        exit;
    }
?>