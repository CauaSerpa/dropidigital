<div class="container highlight-center">
    <div class="row px-4">
        <?php
            // Valores possíveis para os checkboxes
            $valoresPossiveis = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];

            // Valores do banco de dados (simulando)
            $valoresDoBanco = $center_highlight_images;

            // Convertendo a string do banco de dados em um array
            $valoresArray = explode(", ", $valoresDoBanco);

            // Array de URLs de imagens correspondentes aos valores
            $imagens = [
                "1" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                "2" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                "3" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                "4" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                "5" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                "6" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                "7" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                "8" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                "9" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                "10" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png"
            ];

            // Função para marcar os checkboxes
            function marcarCheckbox1($valor, $valoresArray, $imagens) {
                if (in_array($valor, $valoresArray)) {
                    echo "<div class='col-sm-4 highlight'>";
                    echo    "<div class='d-flex justify-content-center'>";
                    echo        "<img src='{$imagens[$valor]}' alt='Tarja {$valor}'>";
                    echo    "</div>";
                    echo "</div>";
                }
            }

            // Criando os checkboxes e marcando aqueles cujos valores correspondem aos do banco de dados
            foreach ($valoresPossiveis as $valor) {
                marcarCheckbox1($valor, $valoresArray, $imagens);
            }
        ?>
    </div>
</div>

<?php
    // Função para buscar e dividir as imagens em dois conjuntos
    function dividirImagens($conn_pdo, $shop_id, $location) {
        // Nome da tabela para a busca
        $tabela = 'tb_banner_info';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND location = :location ORDER BY id ASC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindParam(':location', $location);
        $stmt->execute();

        // Recuperar os resultados
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Dividir as imagens em dois conjuntos
        $conjunto1 = array();
        $conjunto2 = array();

        // Variável de controle para alternar entre conjuntos
        $alternarConjunto = true;

        // Loop através dos resultados e dividir as imagens
        foreach ($resultados as $banner) {
            // Consulta SQL para selecionar todas as imagens com base no ID
            $sql = "SELECT * FROM tb_banner_img WHERE banner_id = :banner_id ORDER BY id ASC";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':banner_id', $banner['id']);
            $stmt->execute();

            // Recuperar os resultados
            $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Adicionar as imagens aos conjuntos alternadamente
            foreach ($imagens as $imagem) {
                if ($alternarConjunto) {
                    $conjunto1[] = $imagem;
                } else {
                    $conjunto2[] = $imagem;
                }
                $alternarConjunto = !$alternarConjunto;
            }
        }

        return array($conjunto1, $conjunto2);
    }

    // Exibir as imagens do primeiro conjunto
    echo '<div class="container">';
    echo '    <div class="row g-4 p-4">';
    $conjuntos = dividirImagens($conn_pdo, $shop_id, 'shelf-banner');
    foreach ($conjuntos[0] as $imagem) {
        echo '    <a href="' . $banner['link'] . '" target="' . $banner['target'] . '" class="col-md-6">';
        echo '        <div class="card border-0">';
        echo '            <img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] . '" alt="' . $banner['name'] . '" class="card-img-top shelf-banner" style="height: 245.81px; object-fit: cover;">';
        echo '        </div>';
        echo '    </a>';
    }
    echo '    </div>';
    echo '</div>';
?>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_categories';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindValue(':status', 1);
    $stmt->bindValue(':parent_category', 1);
    $stmt->execute();

    // Recuperar os categories
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($categories) {
?>

<div id="carouselCategorias" class="container carousel slide" data-bs-ride="carousel">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <h4 class="mb-3">Navegue por Departamento</h4>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>
    <div class="carousel-inner">
        <?php
            // Inicialize uma variável de controle e um contador
            $primeiroElemento = true;
            $contador = 0;

            // Loop através dos categories e exibir todas as colunas
            foreach ($categories as $category) {
                // Adicione a classe especial apenas ao primeiro elemento
                $active = $primeiroElemento ? 'active' : '';

                // Se o contador for múltiplo de 4, insira uma nova div carousel-item
                if ($contador % 6 == 0) {
                    echo '<div class="carousel-item ' . $active . '">';
                    echo '<div class="row p-4">';
                }

                echo '<div class="col-sm-2">';
                echo '<div class="card border-0">';
                echo '<a href="' . INCLUDE_PATH_LOJA . $category['link'] . '" class="category-link">';
                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/category/' . $shop_id . '/image/' . $category['image'] . '" class="card-img-top rounded-circle" alt="' . $category['name'] . '">';
                echo '<div class="card-body text-center">';
                echo '<h5 class="card-title">' . $category['name'] . '</h5>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
                echo '</div>';

                // Se o contador for múltiplo de 4, feche a div row e carousel-item
                if ($contador % 6 == 5 || $contador == count($categories) - 1) {
                    echo '</div>';
                    echo '</div>';
                }

                // Marque que o primeiro elemento foi processado
                $primeiroElemento = false;

                // Incrementar o contador
                $contador++;
            }
        ?>
    </div>

    <a class="carousel-control-prev" href="#carouselCategorias" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#carouselCategorias" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
    </a>
</div>

<?php
 
    }

?>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_products';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis ORDER BY id ASC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindValue(':status', 1);
    $stmt->bindValue(':emphasis', 1);
    $stmt->execute();

    // Recuperar os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
?>

<div id="carouselProdutos" class="container carousel slide" data-bs-ride="carousel">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <div class="d-flex justify-content-center">
            <h4 class="mb-3 me-3">Super Ofertas</h4>
            <p class="text-black-50">Produtos com preços imperdíveis</p>
        </div>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>

    <div class="carousel-inner">
        <?php
            // Inicialize uma variável de controle e um contador
            $primeiroElemento = true;
            $contador = 0;

            // Loop através dos resultados e exibir todas as colunas
            foreach ($resultados as $product) {
                // Consulta SQL para selecionar todas as colunas com base no ID
                $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':usuario_id', $product['id']);
                $stmt->execute();

                // Recuperar os resultados
                $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Formatação preço
                $preco = $product['price'];

                // Transforma o número no formato "R$ 149,90"
                $price = "R$ " . number_format($preco, 2, ",", ".");

                // Formatação preço com desconto
                $desconto = $product['discount'];

                // Transforma o número no formato "R$ 149,90"
                $discount = "R$ " . number_format($desconto, 2, ",", ".");

                // Calcula a porcentagem de desconto
                $porcentagemDesconto = (($product['price'] - $product['discount']) / $product['price']) * 100;

                // Arredonda o resultado para duas casas decimais
                $porcentagemDesconto = round($porcentagemDesconto, 0);

                if ($product['discount'] == "0.00") {
                    $activeDiscount = "d-none";

                    $priceAfterDiscount = $price;
                } else {
                    $activeDiscount = "";

                    $priceAfterDiscount = $discount;
                    $discount = $price;
                }

                // Link do produto
                $link = INCLUDE_PATH_LOJA . "produto/" . $product['link'];

                // Adicione a classe especial apenas ao primeiro elemento
                $active = $primeiroElemento ? 'active' : '';

                // Se o contador for múltiplo de 4, insira uma nova div carousel-item
                if ($contador % 4 == 0) {
                    echo '<div class="carousel-item ' . $active . '">';
                    echo '<div class="row p-4">';
                }

                echo '<div class="col-sm-3">';
                echo '<a href="' . $link . '" class="product-link">';
                echo '<div class="card">';
                foreach ($imagens as $imagem) {
                echo '<div class="product-image">';
                echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" class="card-img-top" alt="' . $product['name'] . '">';
                echo '</div>';
                }
                echo '<div class="card-body">';
                echo '<p class="card-title mb-0">' . $product['name'] . '</p>';
                echo '<div class="d-flex mb-3">';
                echo '<small class="fw-semibold text-body-secondary text-decoration-line-through me-2 ' . $activeDiscount . '">' . $discount . '</small>';
                echo '<h4 class="card-text">' . $priceAfterDiscount . '</h4>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';

                // Se o contador for múltiplo de 4, feche a div row e carousel-item
                if ($contador % 4 == 3 || $contador == count($resultados) - 1) {
                    echo '</div>';
                    echo '</div>';
                }

                // Marque que o primeiro elemento foi processado
                $primeiroElemento = false;

                // Incrementar o contador
                $contador++;
            }
        ?>
    </div>

    <a class="carousel-control-prev" href="#carouselProdutos" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#carouselProdutos" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
    </a>
</div>

<?php
    }
?>

<?php
    foreach ($conjuntos[1] as $imagem) {
    // Exibir as imagens do segundo conjunto
    echo '<div class="container">';
    echo '    <div class="row g-4 p-4">';
        echo '    <a href="' . $banner['link'] . '" target="' . $banner['target'] . '" class="col-md-6">';
        echo '        <div class="card border-0">';
        echo '            <img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] . '" alt="' . $banner['name'] . '" class="card-img-top shelf-banner" style="height: 245.81px; object-fit: cover;">';
        echo '        </div>';
        echo '    </a>';
    echo '    </div>';
    echo '</div>';
    }
?>

<div class="container">
    <div class="row p-4">
        <div class="col-sm-12">
            <div id="video-display" class="d-flex justify-content-center">
                <div class="d-flex justify-content-center">
                    <?php
                        // Função para extrair o código do vídeo do URL do YouTube
                        function getYoutubeEmbedCode($url) {
                            // Verifica se o URL é um link válido do YouTube
                            if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                $videoCode = $matches[1];

                                // Gera o código de incorporação
                                $embedCode = '<iframe width="1240" height="527" src="https://www.youtube.com/embed/' . $videoCode . '" frameborder="0" allowfullscreen></iframe>';

                                return $embedCode;
                            } else {
                                // URL inválido do YouTube
                                return 'URL do YouTube inválido.';
                            }
                        }

                        // Exemplo de uso:
                        $youtubeURL = $video;
                        $embedCode = getYoutubeEmbedCode($youtubeURL);

                        if ($embedCode !== 'URL do YouTube inválido.') {
                            echo $embedCode;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_depositions';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id ASC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    // Recuperar os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
?>

<div id="carouselDepoimentos" class="container carousel slide" data-bs-ride="carousel">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <div class="d-flex justify-content-center">
            <h4 class="mb-3 me-3">Vejam o que dizem nossos clientes</h4>
            <p class="text-black-50">Depoimentos</p>
        </div>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>

    <div class="carousel-inner">
        <?php
            // Inicialize uma variável de controle e um contador
            $primeiroElemento = true;
            $contador = 0;

            // Loop através dos resultados e exibir todas as colunas
            foreach ($resultados as $product) {
                // Adicione a classe especial apenas ao primeiro elemento
                $active = $primeiroElemento ? 'active' : '';

                // Se o contador for múltiplo de 4, insira uma nova div carousel-item
                if ($contador % 3 == 0) {
                    echo '<div class="carousel-item ' . $active . '">';
                    echo '<div class="row p-4">';
                }

                echo '<div class="col-sm-4">';
                echo '<div class="card border-0">';
                echo '<div class="card-body text-center">';
                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/depositions/' . $product['img'] . '" class="rounded-circle mb-2" alt="Produto 1" style="width: 150px; height: 150px;">';
                echo '<p class="card-title">' . $product['name'] . '</p>';
                echo '<p class="card-text small lh-sm text-black-50 mb-2">"' . $product['testimony'] . '"</p>';
                echo '<div class="rating' . $contador . ' dep-stars text-warning">';
                
                // Código JavaScript para gerar as estrelas de classificação com base na coluna 'rating' do banco de dados
                echo '<script>';
                echo 'const classificacao' . $contador . ' = ' . $product['qualification'] . ';';
                echo 'const ratingContainer' . $contador . ' = document.createElement("div");';
                echo 'ratingContainer' . $contador . '.className = "stars";';
                echo 'for (let i = 0; i < 5; i++) {';
                echo '  const star = document.createElement("i");';
                echo '  if (i < classificacao' . $contador . ') {';
                echo '    star.className = "bx bxs-star";'; // Estrela ativa
                echo '  } else {';
                echo '    star.className = "bx bx-star";'; // Estrela inativa
                echo '  }';
                echo '  ratingContainer' . $contador . '.appendChild(star);';
                echo '}';
                echo 'document.querySelector(".rating' . $contador . '").appendChild(ratingContainer' . $contador . ');';
                echo '</script>';
                
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';

                // Se o contador for múltiplo de 4, feche a div row e carousel-item
                if ($contador % 3 == 2 || $contador == count($resultados) - 1) {
                    echo '</div>';
                    echo '</div>';
                }

                // Marque que o primeiro elemento foi processado
                $primeiroElemento = false;

                // Incrementar o contador
                $contador++;
            }
        ?>
    </div>

    <a class="carousel-control-prev" href="#carouselDepoimentos" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#carouselDepoimentos" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
    </a>
</div>

<?php
    }
?>