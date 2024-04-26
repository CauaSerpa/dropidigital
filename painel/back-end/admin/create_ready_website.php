<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $name = $_POST['name'];

        // Criar subdminio do site
        // Pega post url
        $url = $_POST['url'];

        //Tabela que será solicitada
        $tabela = 'tb_domains';
        
        // Verifica se a Url já existe
        $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mensagem de erro
            $_SESSION['msg_url'] = "<p class='danger'>A URL já está sendo utilizado. Escolha outra URL.</p>";
            
            //Link de redirecionamento
            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'criar-loja');

            //Mata o processo
            die();
        }

        if (isset($_POST['status'])) {
            $status = 1;
        } else {
            $status = 0;
        }
        
        if (isset($_POST['emphasis'])) {
            $emphasis = 0;
        } else {
            $emphasis = 0;
        }

        $plan_id = $_POST['plan'];
        $version = $_POST['version'];

        // Checkbox sem preco
        if (isset($_POST["without_price"]))
        {
            $price = 0;
            $discount = 0;
            $without_price = 1;
        } else {
            $price = $dados['price'];
            $discount = $dados['discount'];
            $without_price = 0;
        }

        $video = $_POST['video'];
        $description = $_POST['description'];
        $sku = $_POST['sku'];
        $seo_name = $_POST['seo_name'];
        $link = $_POST['seo_link'];
        $seo_description = $_POST['seo_description'];
        
        $segment = $_POST['segment'];
        $docNumber = $_POST['docNumber'];
        $razaoSocial = $_POST['razaoSocial'];
        $phone = $_POST['phone'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (plan_id, name, segment, cpf_cnpj, razao_social, phone) VALUES 
                                    (:plan_id, :name, :segment, :cpf_cnpj, :razao_social, :phone)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':plan_id', $plan_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':cpf_cnpj', $docNumber);
        $stmt->bindValue(':razao_social', $razaoSocial);
        $stmt->bindValue(':phone', $phone);
        $stmt->execute();

        // Recebendo id da loja
        $shop_id = $conn_pdo->lastInsertId();

        //Tabela que será solicitada
        $tabela = 'tb_ready_sites';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, plan_id, status, emphasis, name, version, price, without_price, discount, video, description, sku, seo_name, link, seo_description) VALUES 
                                    (:shop_id, :plan_id, :status, :emphasis, :name, :version, :price, :without_price, :discount, :video, :description, :sku, :seo_name, :link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindParam(':plan_id', $plan_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':emphasis', $emphasis);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':version', $version);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':without_price', $without_price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':video', $video);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':seo_name', $seo_name);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':seo_description', $seo_description);
        $stmt->execute();

        // Recebendo id da loja
        $ready_site_id = $conn_pdo->lastInsertId();

        //Tabela que será solicitada
        $tabela = 'tb_domains';

        $url = $_POST['url'];
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

        // Categorias
        // Recupera o valor do input hidden com o ID das categorias
        $categoriasInputValue = $_POST['categoriasSelecionadas']; // Substitua 'categoriasInput' pelo nome do seu input

        // Certifique-se de que $categoriasInputValue é uma string
        if (is_array($categoriasInputValue)) {
            // Lógica para converter o array em uma string (se aplicável)
            // Isso pode variar dependendo de como os dados estão sendo enviados
            $categoriasInputValue = implode(',', $categoriasInputValue);
        }

        // Separa os IDs das categorias em um array
        $categoriasIds = explode(',', $categoriasInputValue);

        // Loop para inserir categorias no banco de dados
        foreach ($categoriasIds as $categoriaId) {
            // Certifique-se de validar e escapar os dados para evitar injeção de SQL
            $categoriaId = (int)$categoriaId;

            $main = ($dados['inputMainCategory'] == $categoriaId) ? 1 : 0;

            // Consulta SQL para inserir a associação entre produto e categoria
            $tabela = "tb_site_services";
            $sql = "INSERT INTO $tabela (ready_site_id, service_id, main) VALUES (:ready_site_id, :service_id, :main)";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':ready_site_id', $ready_site_id);
            $stmt->bindParam(':service_id', $categoriaId);
            $stmt->bindParam(':main', $main);

            $stmt->execute();

            echo "sucesso";
        }

        // Imagens
        $total = count($_FILES['imagens']['name']);

        // Loop através de cada arquivo
        for ($i = 0; $i < $total; $i++) {
            // Certifique-se de que a pasta para as imagens exista
            $uploadDir = "ready-website/$ready_site_id/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = $_FILES['imagens']['name'][$i];
            $tmp_name = $_FILES['imagens']['tmp_name'][$i];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sqlInsertImagem = "INSERT INTO tb_ready_site_img (ready_site_id, image) VALUES (:ready_site_id, :image)";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':ready_site_id', $ready_site_id);
                $stmtInsertImagem->bindParam(':image', $fileName);

                if ($stmtInsertImagem->execute()) {
                    echo "Imagem " . $fileName . ", salva com sucesso";
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao salvar imagem do Site Pronto!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "sites-prontos");
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao salvar imagem do Site Pronto!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "sites-prontos");
            }
        }

        // Passa as informações do Site Pronto
        $_SESSION['shop_id'] = $shop_id;

        $_SESSION['msgcad'] = "<p class='green'>Site Pronto criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD);
        exit;
    }
?>