<div class="container highlight-center">
    <div class="row px-4">
        <?php
            // Valores possíveis para os checkboxes
            $valoresPossiveis = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];

            // Valores do banco de dados (simulando)
            $valoresDoBanco = $center_highlight_images;

            // Convertendo a string do banco de dados em um array
            $valoresArray = explode(", ", $valoresDoBanco);

            // Array de URLs de imagens correspondentes aos valores
            $imagens = [
                "1" => INCLUDE_PATH . "loja/tarjas/01.jpg",
                "2" => INCLUDE_PATH . "loja/tarjas/02.jpg",
                "3" => INCLUDE_PATH . "loja/tarjas/03.jpg",
                "4" => INCLUDE_PATH . "loja/tarjas/04.jpg",
                "5" => INCLUDE_PATH . "loja/tarjas/05.jpg",
                "6" => INCLUDE_PATH . "loja/tarjas/06.jpg",
                "7" => INCLUDE_PATH . "loja/tarjas/07.jpg",
                "8" => INCLUDE_PATH . "loja/tarjas/08.jpg",
                "9" => INCLUDE_PATH . "loja/tarjas/09.jpg",
                "10" => INCLUDE_PATH . "loja/tarjas/10.jpg",
                "11" => INCLUDE_PATH . "loja/tarjas/11.jpg",
                "12" => INCLUDE_PATH . "loja/tarjas/12.jpg"
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

<style>
    #categoriesCarousel.owl-carousel .owl-item img
    {
        height: auto;
    }
</style>

<div class="container">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <h4 class="mb-3">Navegue por Departamento</h4>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>
    <div class="owl-carousel p-4" id="categoriesCarousel">
        <?php
            // Loop através dos categories e exibir todas as colunas
            foreach ($categories as $category) {
                echo '<div class="item">';
                echo '<div class="card border-0">';
                echo '<a href="' . INCLUDE_PATH_LOJA . $category['link'] . '" class="category-link">';
                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/category/' . $shop_id . '/image/' . $category['image'] . '" class="card-img-top rounded-circle" alt="' . $category['name'] . '">';
                echo '<div class="card-body text-center">';
                echo '<h5 class="card-title">' . $category['name'] . '</h5>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>
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

<style>
    .listProducts .row
    {
        --bs-gutter-x: 1rem !important;
        --bs-gutter-y: 1rem !important;
    }
</style>

<div class="listProducts container">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <div class="d-flex justify-content-center">
            <h4 class="mb-3 me-3">Super Ofertas</h4>
            <p class="text-black-50">Produtos com preços imperdíveis</p>
        </div>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>

    <div class="row g-3 p-4">
        <?php
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
                if ($product['price'] != 0) {
                    $porcentagemDesconto = (($product['price'] - $product['discount']) / $product['price']) * 100;
                } else {
                    // Lógica para lidar com o caso em que $product['price'] é zero
                    $porcentagemDesconto = 0; // Ou outro valor padrão
                }

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

                if ($product['without_price']) {
                    $priceAfterDiscount = "<a href='" . $link . "' class='btn btn-dark small px-3 py-1'>Saiba Mais</a>";
                }

                // Link do produto
                $link = INCLUDE_PATH_LOJA . "produto/" . $product['link'];

                echo '<div class="col-sm-3 numBanner d-grid">';
                echo '<a href="' . $link . '" class="product-link d-grid">';
                echo '<div class="card">';

                if ($imagens) {
                    foreach ($imagens as $imagem) {
                        echo '<div class="product-image">';
                        echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                        echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" class="card-img-top" alt="' . $product['name'] . '">';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="product-image">';
                    echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                    echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" class="card-img-top" alt="' . $product['name'] . '">';
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
            }
        ?>
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

<style>
    #testimonyCarousel.owl-carousel .owl-item img
    {
        height: auto;
    }

    .testimonyContainer
    {
        position: relative;
    }
    .testimonyContainer .customNavigation .btn
    {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        color: black;
        font-size: 4rem;
    }
    .testimonyContainer .customNavigation .btn.prev
    {
        left: 4px;
    }
    .testimonyContainer .customNavigation .btn.next
    {
        right: 4px;
    }
</style>

<div class="testimonyContainer container">
    <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
        <div class="d-flex justify-content-center">
            <h4 class="mb-3 me-3">Vejam o que dizem nossos clientes</h4>
            <p class="text-black-50">Depoimentos</p>
        </div>
        <div style="width: 100px; height: 5px; background: #000;"></div>
    </div>

    <div class="owl-carousel p-4" id="testimonyCarousel">
        <?php
            // Inicialize uma variável de controle e um contador
            $contador = 0;

            // Loop através dos resultados e exibir todas as colunas
            foreach ($resultados as $product) {
                echo '<div class="item">';
                echo '<div class="card border-0">';
                echo '<div class="card-body d-flex flex-column align-items-center text-center">';
                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/depositions/' . $product['img'] . '" class="rounded-circle mb-2" alt="Produto 1" style="width: 150px;">';
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

                // Incrementar o contador
                $contador++;
            }
        ?>
    </div>
</div>

<?php
    }
?>