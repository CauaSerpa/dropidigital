<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Verifica se um arquivo foi enviado
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
        $nome_temporario = $_FILES['arquivo']['tmp_name'];

        // Lê os dados do arquivo CSV
        $handle = fopen($nome_temporario, 'r');
        while (($dados = fgetcsv($handle, 1000, ',')) !== false) {
            // Insere os dados no banco de dados
            $nome = $dados[0];
            $descricao = $dados[1];
            $preco = $dados[2];

            // Escapa os dados para evitar SQL injection
            $nome = $conn_pdo->real_escape_string($nome);
            $descricao = $conn_pdo->real_escape_string($descricao);
            $preco = $conn_pdo->real_escape_string($preco);

            // Insere os dados na tabela
            $query = "INSERT INTO produtos (nome, descricao, preco) VALUES ('$nome', '$descricao', '$preco')";
            $conn_pdo->query($query);
        }

        // Fecha o arquivo
        fclose($handle);

        echo 'Importação concluída com sucesso!';
    } else {
        echo 'Erro ao processar o arquivo.';
    }