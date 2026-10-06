<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    $device_id = trim($_POST['deviceId']);

    $url = $mpago_url;

    //Cadastrar Cliente

    $email                  = trim($_POST['email']);

    $name                   = trim($_POST['nome']);
    $first_name             = explode(' ',$_POST['nome'])[0];
    $last_name              = explode(' ',$_POST['nome'])[1];
    $phone                  = trim($_POST['phone']);

    // recupear o tipo de documento
    $docType                = trim($_POST['docType']);

    // recupera o numero do documento do Titular
    $docNumber              = trim($_POST['docNumber']);

    $cep                    = trim($_POST['cep']);
    $address                = trim($_POST['address']);
    $number                 = trim($_POST['number']);
    $complement             = trim($_POST['complement']);
    $district               = trim($_POST['district']);
    $city                   = trim($_POST['city']);
    $state                  = trim($_POST['state']);

    // Data atual
    // Configuração do fuso horário
    date_default_timezone_set('America/Sao_Paulo'); // Substitua 'America/Sao_Paulo' pelo fuso horário desejado

    // Obtém a data e hora atual
    $dateTime = new DateTime('now');

    // Formata a data e hora de acordo com o exemplo fornecido
    $date = $dateTime->format('Y-m-d\TH:i:s.vP');

    // Tabela que sera consultado
    $tabela = "tb_invoice_info";

    // Pesquisa pelo email no banco de dados
    $sql = "SELECT customer_id FROM $tabela WHERE email = :email";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer) {
        $customer_id = $customer['id'];
    } else {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . 'v1/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "email": "'.$email.'",
                "first_name": "'.$first_name.'",
                "last_name": "'.$last_name.'",
                "phone": {
                    "area_code": "55",
                    "number": "'.$phone.'"
                },
                "identification": {
                    "type": "'.$docType.'",
                    "number": "'.$docNumber.'"
                },
                "default_address": "Home",
                "address": {
                    "id": "123123",
                    "zip_code": "'.$cep.'",
                    "street_name": "'.$address.'",
                    "street_number": '.$number.'
                },
                "date_registered": "'.$date.'",
                "default_card": "None",
                "description": "Cliente da Dropi Digital"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-meli-session-id: '.$device_id, // Adiciona este cabeçalho
                'Authorization: Bearer '.$access_token
            ),
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
    
        $obj = json_decode($response);

        // var_dump($obj);
    
        $customer_id = $obj->id;

        // Cadastrar customer_id na tabela
        $sql = "INSERT INTO $tabela (customer_id, name, email, phone, docType, docNumber, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES 
                                    (:customer_id, :name, :email, :phone, :docType, :docNumber, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $conn_pdo->prepare($sql);

        $stmt->bindParam(':shop_id', $shop_id);

        $stmt->bindParam(':customer_id', $customer_id);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);

        $stmt->bindParam(':docType', $docType);
        $stmt->bindParam(':docNumber', $docNumber);

        $stmt->bindParam(':cep', $cep);
        $stmt->bindParam(':endereco', $address);
        $stmt->bindParam(':numero', $number);
        $stmt->bindParam(':complemento', $complement);
        $stmt->bindParam(':bairro', $district);
        $stmt->bindParam(':cidade', $city);
        $stmt->bindParam(':estado', $state);

        $stmt->execute();
    }

    echo "Esse é o id do meu cliente: " . $customer_id;
    echo "<br>";

    //Cadastrar Cartao

    // mude para true para ver toda a reposta do mercado pago
    $debug = false; ##########

    // verificar se existe token do cartao
    if(isset($_POST['token'])){
        // recupera o token do cartao
        $token = trim($_POST['token']);

        // VAMOS AGORA SALVAR O CARTÃO PARA O USUARIO CUJO ID ESTÁ ACIMA
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . 'v1/customers/'.$customer_id.'/cards',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "token": "'.$token.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-meli-session-id: '.$device_id, // Adiciona este cabeçalho
                'Authorization: Bearer '.$access_token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($response);

        if($debug){
            echo '<pre>';
            var_dump($obj);
        }else{
            // echo '<script>location.href="../cartoes_do_cliente";</script>';
        }
    }

    echo "Esse é o token do cartao: " . $token;
    echo "<br>";

    // DEFINIMOS AQUI O ID DO CARTAO (cartoes_do_cliente)
    $id_card_token = $token;  ##########

    // DEFINIMOS AQUI AS INFORMACOES DO PLANO (planos_de_assinatura)
    $id_plan             = explode('|',$_POST['plan'])[0];
    $name_plan           = explode('|',$_POST['plan'])[1];
    $value               = explode('|',$_POST['plan'])[2];
    $frequency           = explode('|',$_POST['plan'])[3];

    // mude para true para ver toda a reposta do mercado pago
    $debug = true; ##########

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . 'preapproval',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "auto_recurring": {
                "frequency": '.$frequency.',
                "frequency_type": "months",
                "start_date": "'.$date.'",
                "transaction_amount": '.$value.',
                "currency_id": "BRS"
            },
            "preapproval_plan_id": "'.$id_plan.'",
            "reason": "'.$name_plan.'",
            "payer_email": "'.$email.'",
            "card_token_id": "'.$id_card_token.'",
            "back_url": "https://dropidigital.com.br/painel/planos",
            "status": "authorized"
        }',
        CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-meli-session-id: '.$device_id, // Adiciona este cabeçalho
        'Authorization: Bearer '.$access_token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $obj = json_decode($response);

    echo "Assinatura:";
    if($debug){
        echo '<pre>';
        var_dump($obj);
    }else{
        echo $response;
    }
