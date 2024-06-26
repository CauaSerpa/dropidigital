<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    //Tabela que será solicitada
    $tabela = 'tb_domains';

    if (isset($_POST['name'])) {
        // Criar subdminio do site
        // Pega post name
        $name = $_POST['name'];

        if ($name !== '')
        {
            // Name não preenchida utiliza o post name para criar
            // Transforma o texto em minúsculas
            $texto = strtolower($name);

            // Remove pontos e vírgulas
            $texto = str_replace(['.', ','], '', $texto);

            // Separa o texto em um array de palavras
            $palavras = explode(' ', $texto);

            // Junta as palavras com "-"
            $url = implode('-', $palavras);

            // Verifica se o usuário já existe
            $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain AND domain = :domain";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':subdomain', $url);
            $stmt->bindValue(':domain', 'dropidigital.com.br');
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                echo 'URL já cadastrada!';
            }
        }
    }
    if (isset($_POST['url'])) {
        // Pega o campo url
        $url = $_POST['url'];

        // Verifica se o usuário já existe
        $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain AND domain = :domain";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);
        $stmt->bindValue(':domain', 'dropidigital.com.br');
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo 'URL já cadastrada!';
        }
    }