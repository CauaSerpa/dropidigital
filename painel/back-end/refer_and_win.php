<?php
session_start();
ob_start();
include_once('../../config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Caminho para o diretório pai
$parentDir = dirname(dirname(__DIR__));

require $parentDir . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
$dotenv->load();

// Acessa as variáveis de ambiente
$apiKey = $_ENV['OPENAI_API_KEY'];

// Informacoes para PHPMailer
$smtp_host = $_ENV['SMTP_HOST'];
$smtp_username = $_ENV['SMTP_USERNAME'];
$smtp_password = $_ENV['SMTP_PASSWORD'];
$smtp_secure = $_ENV['SMTP_SECURE'];
$smtp_port = $_ENV['SMTP_PORT'];

require './lib/vendor/autoload.php';

// Crie uma nova instância do PHPMailer
$mail = new PHPMailer(true);

header('Content-Type: application/json');

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Restaurar
if ($_POST['action'] == 'send-link') {
    $tabela = 'tb_users';
    $query = "SELECT * FROM $tabela WHERE id = :id LIMIT 1";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':id', $_POST['user_id']);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];
        $token = $user['token'];
        $social_network = $_POST['social_network'];
        $status = "sending";

        $tabela = 'tb_indication';
        $sql = "INSERT INTO $tabela (user_id, token, social_network, status) VALUES (:user_id, :token, :social_network, :status)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':social_network', $social_network);
        $stmt->bindValue(':status', $status);

        if ($stmt->execute()) {
            echo json_encode(['status' => 200]);
            exit;
        } else {
            http_response_code(500); // Erro interno do servidor
            echo json_encode(['error' => 'Erro ao cadastrar a resposta no banco de dados']);
            exit;
        }
    } else {
        http_response_code(500); // Erro interno do servidor
        echo json_encode(['error' => 'Erro ao decodificar a resposta do banco de dados']);
        exit;
    }
}

// Enviar convite
if ($_POST['action'] == 'send-email') {
    // Verifica se a variável 'emails' está presente
    if (isset($_POST['emails'])) {
        $tabela = 'tb_users';
        $query = "SELECT * FROM $tabela WHERE id = :id LIMIT 1";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $_POST['user_id']);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];
            $name = $user['name'];
            $email = $user['email'];
            $referral_code = $user['referral_code'];
            $status = "sending";

            $link = INCLUDE_PATH . "r/$referral_code";
        }

        $emails = $_POST['emails'];

        // Separa a string em um array de emails
        $guest_emails = array_map('trim', explode(',', $emails));

        if ($guest_emails) {
            foreach ($guest_emails as $guest_email) {
                // Verifica se o email é válido
                if (filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
                    // Send E-mail
                    $mail = new PHPMailer(true);
    
                    try {
                        /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
                        $mail->CharSet = 'UTF-8';
                        $mail->isSMTP();
                        $mail->Host       = $smtp_host;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $smtp_username;
                        $mail->Password   = $smtp_password;
                        $mail->SMTPSecure = $smtp_secure;
                        $mail->Port       = $smtp_port;
    
                        $mail->setFrom($email, 'Você recebeu um convite!');
                        $mail->addReplyTo($email, 'Você recebeu um convite!');
                        $mail->addAddress($guest_email);
    
                        $mail->isHTML(true); //Set email format to HTML
                        $mail->Subject = 'Você recebeu um convite para conhecer a Dropi Digital';
                        $mail->Body    = "Você recebeu um convite de $name<br><br><a href='$link'>$link</a>";
                        $mail->AltBody = "Você recebeu um convite de $name\n\n<a href='$link'>$link</a>";
    
                        $mail->send();
                    } catch (Exception $e) {
                        echo json_encode(['status' => 500, 'error' => 'Erro ao enviar E-mail de convite']);
                        exit;
                    }
    
                    $tabela = 'tb_indication';
                    $sql = "INSERT INTO $tabela (indicator_id, guest_email, status) VALUES (:indicator_id, :guest_email, :status)";
                    $stmt = $conn_pdo->prepare($sql);
    
                    // Substituir os links pelos valores do formulário
                    $stmt->bindParam(':indicator_id', $user_id);
                    $stmt->bindValue(':guest_email', $guest_email);
                    $stmt->bindValue(':status', $status);
    
                    $stmt->execute();
                } else {
                    // Se algum email não for válido, você pode retornar um erro
                    echo json_encode(['status' => 500, 'message' => 'Email inválido: ' . $email]);
                    exit();
                }
            }

            // Retorna uma resposta de sucesso
            $_SESSION['msgcad'] = "Emails enviados com sucesso!</p>";
            echo json_encode(['status' => 200]);
            exit;
        } else {
            // Se algum email não for válido, você pode retornar um erro
            echo json_encode(['status' => 500, 'message' => 'É necessário adicionar um email.']);
            exit();
        }
    } else {
        // Retorna uma resposta de erro se 'emails' não estiver presente
        echo json_encode(['status' => 500, 'message' => 'Nenhum email recebido.']);
        exit;
    }
}

// Reenviar convite
if ($_POST['action'] == 'resend-email') {
    // Verifica se a variável 'emails' está presente
    if (isset($_POST['email'])) {
        $tabela = 'tb_users';
        $query = "SELECT * FROM $tabela WHERE id = :id LIMIT 1";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $_POST['user_id']);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];
            $name = $user['name'];
            $email = $user['email'];
            $referral_code = $user['referral_code'];

            $link = INCLUDE_PATH . "r/$referral_code";
        }

        $guest_email = $_POST['email'];

        if ($guest_email) {
            // Verifica se o email é válido
            if (filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
                // Send E-mail
                $mail = new PHPMailer(true);

                try {
                    /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host       = $smtp_host;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtp_username;
                    $mail->Password   = $smtp_password;
                    $mail->SMTPSecure = $smtp_secure;
                    $mail->Port       = $smtp_port;

                    $mail->setFrom($email, 'Você recebeu um convite!');
                    $mail->addReplyTo($email, 'Você recebeu um convite!');
                    $mail->addAddress($guest_email);

                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = 'Você recebeu um convite para conhecer a Dropi Digital';
                    $mail->Body    = "Você recebeu um convite de $name<br><br><a href='$link'>$link</a>";
                    $mail->AltBody = "Você recebeu um convite de $name\n\n<a href='$link'>$link</a>";

                    $mail->send();

                    // Retorna uma resposta de sucesso
                    echo json_encode(['status' => 200]);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(['status' => 500, 'error' => 'Erro ao enviar E-mail de convite']);
                    exit;
                }
            } else {
                // Se algum email não for válido, você pode retornar um erro
                echo json_encode(['status' => 500, 'message' => 'Email inválido: ' . $email]);
                exit();
            }
        } else {
            // Se algum email não for válido, você pode retornar um erro
            echo json_encode(['status' => 500, 'message' => 'É necessário adicionar um email.']);
            exit();
        }
    } else {
        // Retorna uma resposta de erro se 'emails' não estiver presente
        echo json_encode(['status' => 500, 'message' => 'Nenhum email recebido.']);
        exit;
    }
}

// Remover do historico
if ($_POST['action'] == 'clean-history') {
    $tabela = 'tb_ai_historic';
    $query = "DELETE FROM $tabela WHERE shop_id = :shop_id";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':shop_id', $_POST['shop_id']);
    $stmt->execute();

    // Resto do código que lida com a resposta da API ou do banco de dados
    echo json_encode(['status' => 200]);
    exit;
}

// Verifica se o parâmetro 'keyword' foi enviado
if (!isset($_POST['keyword']) || empty($_POST['keyword'])) {
    http_response_code(400); // Requisição inválida
    echo json_encode(['error' => 'Palavra-chave não especificada']);
    exit;
}

$shop_id = $_POST['shop_id'];
$type = $_POST['type'];
$keyword = $_POST['keyword'];
$plan_id = $_POST['plan'];

if ($plan_id <= 2) {
    $numberKeywords = 10;
} else {
    $numberKeywords = 20;
}

$tabela = 'tb_ai_request';
$query = "SELECT * FROM $tabela WHERE type = :type AND request = :request AND number_keywords = :number_keywords LIMIT 1";
$stmt = $conn_pdo->prepare($query);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':request', $keyword);
$stmt->bindParam(':number_keywords', $numberKeywords);
$stmt->execute();

$request = $stmt->fetch(PDO::FETCH_ASSOC);

if ($request) {
    $request_id = $request['id'];
    $type = $request['type'];
    $requestKeyword = $request['request'];
    $response = $request['response'];


    
    if ($_POST['action'] == 'keywords' || $_POST['action'] == 'products') {

        // Verifica se a resposta é um JSON válido e decodifica
        $decodedResponse = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $keyword = $decodedResponse;
        } else {
            http_response_code(500); // Erro interno do servidor
            echo json_encode(['error' => 'Erro ao decodificar a resposta do banco de dados']);
            exit;
        }


    }
    
    
} else {









if ($_POST['action'] == 'keywords') {

    // Dados a serem enviados para a API do ChatGPT
    $data = [
        'model' => 'gpt-4', // gpt-3.5-turbo
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um assistente que gera palavras-chave de marketing digital.'],
            ['role' => 'user', 'content' => "Por favor, gere $numberKeywords palavras-chave relacionadas a '$keyword' com seus volumes de pesquisa estimados. Me mostre em forma de array com keywords e volume. Me retorne somente o array sem nenhuma mensagem a mais. Por favor retorne os resultados em português do brasil."]
        ]
    ];

    $type = "keywords";


} elseif ($_POST['action'] == 'products') {

    // Dados a serem enviados para a API do ChatGPT
    $data = [
        'model' => 'gpt-4', // gpt-3.5-turbo
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um assistente que cria nome de produtos otmizados para seo.'],
            ['role' => 'user', 'content' => "Por favor, gere $numberKeywords nomes para produtos relacionadas ao segmento '$keyword' com seus volumes de pesquisa estimados. Me mostre em forma de array com keywords e volume. Me retorne somente o array sem nenhuma mensagem a mais. Por favor retorne os resultados em português do brasil."]
        ]
    ];

    $type = "products";

}  elseif ($_POST['action'] == 'description') {

    // Dados a serem enviados para a API do ChatGPT
    $data = [
        'model' => 'gpt-4', // gpt-3.5-turbo
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um assistente que cria descrições de produtos otmizadas para seo.'],
            ['role' => 'user', 'content' => "Por favor, gere uma descrição com um título para o produto '$keyword'. Me retorne somente a descrição e o título sem textos adicionais. Gere a descrição com elementos html e deixe o título maior que o restante do texto para estilizar. Por favor retorne os resultados em português do brasil."]
        ]
    ];

    $type = "description";

} else {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => 'Erro ao enviar requisição para a API']);
    exit;
}
















// Configuração do CURL para enviar a requisição
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Executa a requisição e captura a resposta
$response = curl_exec($ch);
if (curl_errno($ch)) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => 'Erro no CURL: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

// Verifica se houve erro na requisição
if ($response === false) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => 'Erro ao enviar requisição para a API']);
    exit;
}

// Decodifica a resposta JSON
$responseArray = json_decode($response, true);

// Verifica se houve erro na decodificação JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => 'Erro ao decodificar a resposta da API: ' . json_last_error_msg()]);
    exit;
}

// Verifica se há uma resposta válida da API
if (!isset($responseArray['choices'][0]['message']['content'])) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['error' => 'Erro na resposta da API', 'response' => $responseArray]);
    exit;
}

// Extrai e decodifica o texto gerado pela API
$generatedText = $responseArray['choices'][0]['message']['content'];

// Trata o conteúdo como JSON
$keyword = json_decode($generatedText, true);








$request = $_POST['keyword'];

$tabela = 'tb_ai_request';

$stmt = $conn_pdo->prepare("INSERT INTO $tabela (type, request, number_keywords, response) VALUES (:type, :request, :number_keywords, :response)");

// Bind dos parâmetros
$stmt->bindParam(':type', $type, PDO::PARAM_STR);
$stmt->bindParam(':request', $request, PDO::PARAM_STR);
$stmt->bindParam(':number_keywords', $numberKeywords, PDO::PARAM_INT);
$stmt->bindParam(':response', $generatedText, PDO::PARAM_STR);

// Executando o update
$stmt->execute();

$request_id = $conn_pdo->lastInsertId();













}


$tabela = 'tb_ai_historic';

$stmt = $conn_pdo->prepare("INSERT INTO $tabela (shop_id, request_id) VALUES (:shop_id, :request_id)");

// Bind dos parâmetros
$stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
$stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);

// Executando o update
$stmt->execute();









if ($_POST['action'] == 'keywords' || $_POST['action'] == 'products') {

    // Monta a resposta para enviar de volta ao JavaScript
    $responseData = [
        'keywords' => $keyword,
        'moreAvailable' => ($numberKeywords > 50) // Exemplo: Mostra a mensagem 'ver mais' se mais de 50 palavras-chave estão disponíveis
    ];


}  elseif ($_POST['action'] == 'description') {

    if (isset($generatedText)) {
        $response = $generatedText;
    }

    // Monta a resposta para enviar de volta ao JavaScript
    $responseData = [
        'description' => $response
    ];

}

// Resto do código que lida com a resposta da API ou do banco de dados
echo json_encode($responseData);