<?php
session_start();
ob_start();
include_once('../../../config.php');

// Caminho do diretório onde os arquivos estão localizados
$directory = '../../pages/user';

// Função para buscar arquivos em um diretório
function listarArquivos($directory) {
    $files = [];
    // Verifica se o diretório existe
    if (is_dir($directory)) {
        $scan = scandir($directory);
        // Remove '.' e '..' da lista de arquivos
        $scan = array_diff($scan, ['.', '..']);
        foreach ($scan as $file) {
            $fullPath = $directory . '/' . $file;
            if (is_file($fullPath)) {
                $files[] = $fullPath;
            }
        }
    }
    return $files;
}

// Lista os arquivos no diretório especificado
$arquivos = listarArquivos($directory);

// Inserir os arquivos no banco de dados
foreach ($arquivos as $arquivo) {
    $infoArquivo = pathinfo($arquivo);
    $nomeArquivo = $infoArquivo['filename'];
    $caminhoArquivo = realpath($arquivo);
    $dataModificacao = date('Y-m-d H:i:s', filemtime($arquivo));

    // Converte o nome do arquivo para um formato mais amigável
    $name = ucwords(str_replace("-", " ", $nomeArquivo));

    // Verificação se o arquivo já existe no banco de dados
    $sqlVerifica = "SELECT * FROM tb_routes WHERE page = :page OR url = :url LIMIT 1";
    $stmtVerifica = $conn_pdo->prepare($sqlVerifica);
    $stmtVerifica->bindParam(':page', $nomeArquivo);
    $stmtVerifica->bindParam(':url', $nomeArquivo);
    $stmtVerifica->execute();

    if ($stmtVerifica->rowCount() == 0) {
        // Se não existe, realiza a inserção
        $sql = "INSERT INTO tb_routes (page, url, name, title) 
                VALUES (:page, :url, :name, :title)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':page', $nomeArquivo);
        $stmt->bindParam(':url', $nomeArquivo);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':title', $name);

        // Executa a inserção
        $stmt->execute();

        // echo "Arquivo '$nomeArquivo' inserido com sucesso no banco de dados!<br><br>";
    } else {
        // Caso já exista, exibe mensagem e não insere
        // echo "Arquivo '$nomeArquivo' já existe no banco de dados!<br>";
    }
}

$_SESSION['msgcad'] = "<p class='green'>Processo finalizado!</p>";
header("Location: ".INCLUDE_PATH_DASHBOARD."paginas");

?>