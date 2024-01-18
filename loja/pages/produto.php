<?php
    // Nome da tabela para a busca
    $tabela = 'tb_categories';

    $sql = "SELECT * FROM $tabela WHERE id = :id AND shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $product['categories']);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    // Recuperar os resultados
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    .container-images {
        display: flex;
    }

    .product-images {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-right: 10px;
    }

    .thumbnail {
        cursor: pointer;
        margin-bottom: 10px;
    }

    .thumbnail.active img {
        border: 3px solid #000;
        opacity: .5;
    }

    .thumbnail img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 1px solid #c4c4c4;
        border-radius: .3rem;
    }

    .preview {
        width: 500px;
        height: 500px;
        overflow: hidden;
    }

    .preview #preview-image {
        transform-origin: center;
        object-fit: cover;
        height: 100%;
        width: 100%;
    }

    .content-image
    {
        position: relative;
        height: 500px;
    }

    .nav-button
    {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        background: #000;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: .3rem;
    }
    .nav-button.prev
    {
        left: 15px;
    }
    .nav-button.next
    {
        right: 15px;
    }
</style>
<style>
    .info-product .card-discount
    {
        position: absolute;
        width: 100px;
        margin-top: 10px;
        margin-left: 50px;
        font-weight: 600;
        border-radius: .6rem;
        text-align: center;
        background: #000;
        color: #fff;
    }
</style>
<div class="container">
    <div class="row p-4">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_categories';

                    $sql = "SELECT name, link FROM $tabela WHERE id = :id";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':id', $category['parent_category']);
                    $stmt->execute();

                    // Recuperar o nome
                    $parent_category = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($category['parent_category'] == 1)
                    {
                        echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">Página inicial</a></li>';
                        echo '<li class="breadcrumb-item small ms-2" aria-current="page"><a href="' . INCLUDE_PATH_LOJA . $category['link'] . '" class="text-decoration-none">' . $category['name'] . '</a></li>';
                    } else {
                        echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">Página inicial</a></li>';
                        echo '<li class="breadcrumb-item small ms-2"><a href="' . INCLUDE_PATH_LOJA . $parent_category['link'] . '" class="text-decoration-none">' . $parent_category['name'] . '</a></li>';
                        echo '<li class="breadcrumb-item small ms-2" aria-current="page"><a href="' . INCLUDE_PATH_LOJA . $category['link'] . '" class="text-decoration-none">' . $category['name'] . '</a></li>';
                    }
                ?>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page"><?php echo $product['name']; ?></li>
            </ol>
        </nav>

        <div class="col-md-6 container-images">
            <div class="product-images">





                <?php
                    // Consulta SQL para selecionar todas as colunas com base no ID
                    $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':usuario_id', $product['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $index = 1; // Inicializa o contador de índice

                    if ($imagens) {
                        foreach ($imagens as $imagem) {
                            // Verifica se é o primeiro elemento
                            $class = ($index === 1) ? 'thumbnail active' : 'thumbnail';
    
                            echo '<div class="' . $class . '" data-index="' . $index . '" onclick="showImage(`' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $product['id'] . '/' . $imagem['nome_imagem'] . '`, this)">';
                            echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $product['id'] . '/' . $imagem['nome_imagem'] . '" alt="' . $imagem['nome_imagem'] . '">';
                            echo '</div>';
    
                            // Incrementa o contador de índice
                            $index++;
                        }
                    } else {
                        // Verifica se é o primeiro elemento
                        $class = ($index === 1) ? 'thumbnail active' : 'thumbnail';
    
                        echo '<div class="' . $class . '" data-index="' . $index . '" onclick="showImage(`' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg`, this)">';
                        echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg">';
                        echo '</div>';

                        // Incrementa o contador de índice
                        $index++;
                    }
                ?>



                <!-- <div class="thumbnail active" data-index="1" onclick="showImage('<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/30/foto_teste.png', this)">
                    <img src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/30/foto_teste.png" alt="Imagem 1">
                </div> -->










            </div>

            <div class="content-image">
                <div class="preview" id="container">
                    <?php
                        // Consulta SQL para selecionar todas as colunas com base no ID
                        $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':usuario_id', $product['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $imagem = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($imagem) {
                            echo '<img id="preview-image" src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $product['id'] . '/' . $imagem['nome_imagem'] . '" alt="' . $imagem['nome_imagem'] . '">';
                        } else {
                            echo '<img id="preview-image" src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" alt="Imagem de visualização">';
                        }
                    ?>
                </div>
                <button class="nav-button prev" onclick="previousImage()"><i class='bx bx-chevron-left'></i></button>
                <button class="nav-button next" onclick="nextImage()"><i class='bx bx-chevron-right'></i></button>
            </div>
        </div>
        <div class="col-md-6 info-product">
            <small><bold>SKU: </bold> <?php echo $product['sku']; ?></small>
            <h3><?php echo $product['name']; ?></h3>
            <?php
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
                

                echo '<small class="fw-semibold text-body-secondary text-decoration-line-through me-2 ' . $activeDiscount . '">' . $discount . '</small>';
                
                echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';

                echo '<h4 class="card-text mb-3">' . $priceAfterDiscount . '</h4>';
            ?>
            
            <a href="<?php echo $product['redirect_link']; ?>" target="_blank" class="btn btn-dark d-inline-flex align-items-center px-3">
                <?php
                    if ($product['button_type'] == 1) {
                        echo "Comprar";
                    } elseif ($product['button_type'] == 2) {
                        echo "<i class='bx bxl-whatsapp me-1'></i>";
                        echo "Chamar no WhatsApp";
                    } elseif ($product['button_type'] == 3) {
                        echo "Saiba mais";
                    } else {
                        echo "<i class='bx bx-calendar me-1'></i>";
                        echo "Agenda";
                    }
                ?>
            </a>
        </div>
    </div>
    <div class="card p-4 m-4">
        <?php echo $product['description']; ?>

        <div class="d-flex justify-content-center">
            <?php
                // Função para extrair o código do vídeo do URL do YouTube
                function getYoutubeEmbedCode($url) {
                    // Verifica se o URL é um link válido do YouTube
                    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoCode = $matches[1];

                        // Gera o código de incorporação
                        $embedCode = '<iframe width="937" height="527" src="https://www.youtube.com/embed/' . $videoCode . '" frameborder="0" allowfullscreen class="mt-4"></iframe>';

                        return $embedCode;
                    }
                }

                // Exemplo de uso:
                $youtubeURL = $product['video'];
                $embedCode = getYoutubeEmbedCode($youtubeURL);

                if ($embedCode !== 'URL do YouTube inválido.') {
                    echo $embedCode;
                }
            ?>
        </div>
    </div>

    
    <div id="carouselProdutos" class="container carousel slide" data-bs-ride="carousel">
        <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
            <div class="d-flex justify-content-center">
                <h4 class="mb-3 me-3">Produtos relacionados</h4>
            </div>
            <div style="width: 100px; height: 5px; background: #000;"></div>
        </div>

        <div class="carousel-inner">
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_products';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND categories = :categories AND id != :current_product ORDER BY id ASC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindParam(':categories', $product['categories']);
                $stmt->bindParam(':current_product', $product['id']);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                if ($stmt->rowCount() <= 0) {
                    echo '<script>document.getElementById("carouselProdutos").classList.add("d-none");</script>';
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
</div>
<script>
    const container = document.getElementById("container");
    const img = document.getElementById("preview-image");

    container.addEventListener("mousemove", (event) => {
        const rect = container.getBoundingClientRect();
        const x = event.clientX - rect.left; // Posição X em relação à imagem
        const y = event.clientY - rect.top;  // Posição Y em relação à imagem

        img.style.transformOrigin = `${x}px ${y}px`;
        img.style.transform = "scale(2)";
    });

    container.addEventListener("mouseleave", () => {
        img.style.transformOrigin = "center";
        img.style.transform = "scale(1)";
    });
</script>
<script>
    let currentImageIndex = 1;

    function showImage(imageUrl, clickedThumbnail) {
        const previewImage = document.getElementById("preview-image");

        const thumbnails = document.querySelectorAll(".thumbnail");
        thumbnails.forEach(thumbnail => thumbnail.classList.remove("active"));

        clickedThumbnail.classList.add("active");

        previewImage.src = imageUrl;
        currentImageIndex = parseInt(clickedThumbnail.getAttribute("data-index"));
    }

    function previousImage() {
        const thumbnails = document.querySelectorAll(".thumbnail");
        const previousIndex = (currentImageIndex - 2 + thumbnails.length) % thumbnails.length + 1;
        const previousThumbnail = document.querySelector(`.thumbnail[data-index="${previousIndex}"]`);
        showImage(previousThumbnail.querySelector("img").src, previousThumbnail);
    }

    function nextImage() {
        const thumbnails = document.querySelectorAll(".thumbnail");
        const nextIndex = currentImageIndex % thumbnails.length + 1;
        const nextThumbnail = document.querySelector(`.thumbnail[data-index="${nextIndex}"]`);
        showImage(nextThumbnail.querySelector("img").src, nextThumbnail);
    }
</script>