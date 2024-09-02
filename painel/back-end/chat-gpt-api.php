<?php
include_once('../../config.php');

// Caminho para o diretório pai
$parentDir = dirname(dirname(__DIR__));

require $parentDir . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
$dotenv->load();

// Acessa as variáveis de ambiente
$apiKey = $_ENV['OPENAI_API_KEY'];

header('Content-Type: application/json');

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Restaurar
if ($_POST['action'] == 'restore') {
    $tabela = 'tb_ai_request';
    $query = "SELECT * FROM $tabela WHERE id = :id LIMIT 1";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();

    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $request_id = $request['id'];
        $type = $request['type'];
        $requestKeyword = $request['request'];
        $response = $request['response'];

        // Verifica se a resposta é um JSON válido e decodifica
        $decodedResponse = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $response = $decodedResponse;
        } else {
            http_response_code(500); // Erro interno do servidor
            echo json_encode(['error' => 'Erro ao decodificar a resposta do banco de dados']);
            exit;
        }
    }

    // Monta a resposta para enviar de volta ao JavaScript
    $responseData = [
        'keywords' => $response
    ];

    // Resto do código que lida com a resposta da API ou do banco de dados
    echo json_encode($responseData);
    exit;
}

// Remover do historico
if ($_POST['action'] == 'remove-history') {
    $tabela = 'tb_ai_historic';
    $query = "DELETE FROM $tabela WHERE shop_id = :shop_id AND request_id = :request_id";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':shop_id', $_POST['shop_id']);
    $stmt->bindParam(':request_id', $_POST['id']);
    $stmt->execute();

    // Resto do código que lida com a resposta da API ou do banco de dados
    echo json_encode(['status' => 200]);
    exit;
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