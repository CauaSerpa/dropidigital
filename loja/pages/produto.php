<?php
    // Nome da tabela para a busca
    $tabela = 'tb_product_categories';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id ORDER BY (main = 1) DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindParam(':product_id', $product['id']);
    $stmt->execute();

    // Recuperar os resultados
    $productCategory = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se encontrou a categoria
    if ($productCategory) {
        // Nova consulta para buscar produtos na mesma categoria
        $sql = "SELECT p.* FROM tb_products p
                INNER JOIN tb_product_categories pc ON p.id = pc.product_id
                WHERE pc.category_id = :category_id AND p.shop_id = :shop_id AND p.status = :status AND p.id != :current_product
                GROUP BY p.id
                ORDER BY p.id ASC
                LIMIT 4";

        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':category_id', $productCategory['category_id']);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindParam(':current_product', $product['id']);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nome da tabela para a busca
        $tabela = 'tb_categories';
    
        $sql = "SELECT name, link FROM $tabela WHERE id = :id";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $productCategory['category_id']);
        $stmt->execute();

        // Recuperar o nome
        $related_categories = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Se não houver resultados na categoria, buscar produtos em destaque
        // Consulta para buscar produtos em destaque
        $sql = "SELECT * FROM tb_products 
                WHERE shop_id = :shop_id AND status = :status AND emphasis = 1 AND id != :current_product
                ORDER BY id ASC
                LIMIT 4";

        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindParam(':current_product', $product['id']);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inicialize uma variável de controle e um contador
    $primeiroElemento = true;
    $contador = 0;
?>
<?php
    // // Função para adicionar um registro na tabela tb_visits
    // function addVisitProductToDatabase($conn_pdo, $shop_id, $product_id) {
    //     // Fuso horario Sao Paulo
    //     date_default_timezone_set('America/Sao_Paulo');
    //     // Data atual
    //     $dataAtual = date("Y-m-d");

    //     // Consulta para verificar se já há uma entrada para a data atual
    //     $sql = "SELECT * FROM tb_visits WHERE shop_id = :shop_id AND page = :page AND product_id = :product_id AND data = :data";
    //     $stmt = $conn_pdo->prepare($sql);
    //     $stmt->bindParam(':shop_id', $shop_id);
    //     $stmt->bindValue(':page', 'product');
    //     $stmt->bindValue(':product_id', $product_id);
    //     $stmt->bindParam(':data', $dataAtual);
    //     $stmt->execute();

    //     if ($stmt->rowCount() == 0) {
    //         // Se não houver entrada para a data atual, insira uma nova entrada
    //         $sql = "INSERT INTO tb_visits (shop_id, page, product_id, data, contagem) VALUES (:shop_id, :page, :product_id, :data, 1)";
    //     } else {
    //         // Se houver entrada para a data atual, apenas atualize a contagem
    //         $sql = "UPDATE tb_visits SET contagem = contagem + 1 WHERE shop_id = :shop_id AND page = :page AND product_id = :product_id AND data = :data";
    //     }
    //     $stmt = $conn_pdo->prepare($sql);
    //     $stmt->bindParam(':shop_id', $shop_id);
    //     $stmt->bindValue(':page', 'product');
    //     $stmt->bindValue(':product_id', $product_id);
    //     $stmt->bindParam(':data', $dataAtual);
    //     $stmt->execute();
    // }

    // // Fuso horario Sao Paulo
    // date_default_timezone_set('America/Sao_Paulo');

    // // Verifica se a sessão "access_product_" . $product['id'] existe
    // if (!isset($_SESSION['access_product_' . $product['id']])) {
    //     // Se não existir, cria a sessão e adiciona um registro na tabela tb_visits
    //     $_SESSION['access_product_' . $product['id']] = ["date" => date('Y-m-d H:i')]; // Salva a data, hora e minuto atuais na sessão

    //     addVisitProductToDatabase($conn_pdo, $shop_id, $product['id']);
    // } else {
    //     // Session access
    //     $access = $_SESSION['access_product_' . $product['id']];

    //     // Se a sessão existir, verifica se já passaram 10 minutos desde o último acesso
    //     // Data fornecida
    //     $date = $access['date'];
    //     $currentDate = date('Y-m-d H:i');

    //     // Converte as datas para objetos DateTime
    //     $dateObj = new DateTime($date);
    //     $currentDateObj = new DateTime($currentDate);

    //     // Calcula a diferença entre as duas datas
    //     $interval = $currentDateObj->diff($dateObj);

    //     // Verifica se a diferença é de pelo menos 10 minutos
    //     if ($interval->i >= 10 || $interval->h > 0 || $interval->d > 0 || $interval->m > 0 || $interval->y > 0) {
    //         // Se já passaram 10 minutos, adiciona mais um registro na tabela tb_visits

    //         $access['date'] = date('Y-m-d H:i'); // Atualiza a sessão com a nova data, hora e minuto
    //         $_SESSION['access_product_' . $product['id']] = $access;

    //         addVisitProductToDatabase($conn_pdo, $shop_id, $product['id']);
    //     }
    // }
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
        min-width: 50px;
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
        width: 100%;
        height: auto;
        overflow: hidden;
    }

    .preview #preview-image {
        transform-origin: center;
        object-fit: contain;
        height: 100%;
        width: 100%;
    }
    #preview-image:hover
    {
        cursor: zoom-in;
    }

    .content-image
    {
        position: relative;
        max-height: 500px;
        max-width: 500px;
        display: flex;
        align-items: center;
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

    /* Responsive */
    @media screen and (max-width: 768px) {
        .product-images {
            overflow-y: auto;
            max-height: 230px;
            min-width: 50px;
        }
        .thumbnail
        {
            width: 100%;
        }
        .content-image
        {
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .content-image .preview
        {
            width: 100%;
            height: auto;
        }
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
<div class="container" style="margin-top: <?php echo ($top_highlight_bar == 1) ? "186.39px" : "154.39px"; ?>;">
    <div class="row p-4">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                    if ($related_categories) {    
                        if ($related_categories['parent_category'] == 1)
                        {
                            echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
                            echo '<li class="breadcrumb-item small ms-2" aria-current="page"><a href="' . INCLUDE_PATH_LOJA . "c/" . $related_categories['link'] . '" class="text-decoration-none">' . $related_categories['name'] . '</a></li>';
                        } elseif ($related_categories['parent_category'] !== 1) {
                            echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
                            echo '<li class="breadcrumb-item small ms-2"><a href="' . INCLUDE_PATH_LOJA . $parent_category['link'] . '" class="text-decoration-none">' . $parent_category['name'] . '</a></li>';
                            echo '<li class="breadcrumb-item small ms-2" aria-current="page"><a href="' . INCLUDE_PATH_LOJA . "c/" . $related_categories['link'] . '" class="text-decoration-none">' . $related_categories['name'] . '</a></li>';
                        } else {
                            echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
                        }
                    } else {
                        echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
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
    
                            echo '<div class="' . $class . '" data-index="' . $index . '" onclick="showImage(`' . CDN_BASE_URL . 'products/' . $product['id'] . '/' . $imagem['nome_imagem'] . '`, this)">';
                            echo '<img src="' . CDN_BASE_URL . 'products/' . $product['id'] . '/' . $imagem['nome_imagem'] . '" alt="' . $imagem['nome_imagem'] . '">';
                            echo '</div>';
    
                            // Incrementa o contador de índice
                            $index++;
                        }
                    } else {
                        // Verifica se é o primeiro elemento
                        $class = ($index === 1) ? 'thumbnail active' : 'thumbnail';
    
                        echo '<div class="' . $class . '" data-index="' . $index . '" onclick="showImage(`' . CDN_BASE_URL . 'products/no-image.jpg`, this)">';
                        echo '<img src="' . CDN_BASE_URL . 'products/no-image.jpg">';
                        echo '</div>';

                        // Incrementa o contador de índice
                        $index++;
                    }
                ?>
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
                            echo '<img id="preview-image" src="' . CDN_BASE_URL . 'products/' . $product['id'] . '/' . $imagem['nome_imagem'] . '" alt="' . $imagem['nome_imagem'] . '">';
                        } else {
                            echo '<img id="preview-image" src="' . CDN_BASE_URL . 'products/no-image.jpg" alt="Imagem de visualização">';
                        }
                    ?>
                </div>
                <button class="nav-button prev" onclick="previousImage()"><i class='bx bx-chevron-left'></i></button>
                <button class="nav-button next" onclick="nextImage()"><i class='bx bx-chevron-right'></i></button>
            </div>
        </div>
        <div class="col-md-6 info-product">
            <div class="d-flex align-items-center justify-content-between">
                <small><bold>SKU: </bold> <?php echo $product['sku']; ?></small>
                <div class="share d-flex align-items-center">
                    <i class='bx bxs-share-alt fs-5 me-2' data-toggle="tooltip" data-placement="top" title="<?= __('share') ?>"></i>

                    <ul class="social-medias d-flex">
                        <li class="me-2">
                            <a href="https://www.facebook.com/sharer.php?u=<?php echo INCLUDE_PATH_LOJA . $product['link']; ?>" class="btn btn-dark rounded-circle fs-6" target="_blank" aria-label="Compartilhar no Facebook">
                                <i class='bx bxl-facebook'></i>
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="mailto:?subject=Confira%20este%20produto&body=<?php echo INCLUDE_PATH_LOJA . $product['link']; ?>" class="btn btn-dark rounded-circle fs-6" target="_blank" aria-label="Compartilhar no E-mail">
                                <i class='bx bx-envelope'></i>
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="https://twitter.com/intent/tweet?text=Confira%20este%20produto:%20<?php echo INCLUDE_PATH_LOJA . $product['link']; ?>" class="btn btn-dark rounded-circle fs-6" target="_blank" aria-label="Compartilhar no Twitter">
                                <i class="fa-brands fa-x-twitter" style="font-size: 14px;"></i>
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="https://wa.me/?text=Confira%20este%20produto:%20<?php echo INCLUDE_PATH_LOJA . $product['link']; ?>" class="btn btn-dark rounded-circle fs-6" target="_blank" aria-label="Compartilhar no WhatsApp">
                                <i class='bx bxl-whatsapp'></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <h1 class="mb-0" style="line-height: 1.25; font-size: 32px;"><?php echo $product['name']; ?></h1>
            
            <?php
                // Mapeamento das plataformas
                $plataformas = [
                    'clickbank'   => 'ClickBank',
                    'digistore24' => 'Digistore24',
                    'kiwify'      => 'Kiwify',
                    'hotmart'     => 'Hotmart',
                    'amazon'      => 'Amazon',
                    'aliexpress'  => 'AliExpress',
                    'shopee'      => 'Shopee'
                ];
                
                // Normaliza para minúsculas (caso venha diferente do banco)
                $related = strtolower($product['related']);
                
                // Verifica se existe no array
                if (array_key_exists($related, $plataformas)) {
                    echo "<small>Vendido por <a href='" . $product['redirect_link'] . "' target='_blank'><b>" . $plataformas[$related] . "</b></a></small>";
                    echo "<br>";
                }
            ?>
            
            <?php
                // Formatação preço
                $preco = $product['price'];
                $currencySymbol = ($product['language'] == 'pt') ? "R$ " : "$ ";

                // Transforma o número no formato "R$ 149,90"
                $price = $currencySymbol . number_format($preco, 2, ",", ".");

                // Formatação preço com desconto
                $desconto = $product['discount'];

                // Transforma o número no formato "R$ 149,90"
                $discount = $currencySymbol . number_format($desconto, 2, ",", ".");

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

                if ($product['without_price'] == 1) {
                    $activeDiscount = "d-none";
                    $priceAfterDiscount = "";
                }

                echo '<small class="fw-semibold text-body-secondary text-decoration-line-through me-2 ' . $activeDiscount . '">' . $discount . '</small>';
                
                echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';

                echo '<h4 class="card-text mb-3">' . $priceAfterDiscount . '</h4>';
            ?>
            
            <a href="<?php echo $product['redirect_link']; ?>" target="_blank" class="btn btn-dark d-inline-flex align-items-center fs-5 py-2 px-4 mb-3">
                <?php
                    if ($product['button_type'] == 1) {
                        echo __('buy');
                    } elseif ($product['button_type'] == 2 || $product['button_type'] == 3) {
                        echo "<i class='bx bxl-whatsapp me-1'></i>";
                        echo __('call_whatsApp');
                    } elseif ($product['button_type'] == 4) {
                        echo __('learn_more');
                    } elseif ($product['button_type'] == 5) {
                        echo "<i class='bx bx-calendar me-1'></i>";
                        echo __('agenda');
                    } elseif ($product['button_type'] == 6) {
                        echo __('register');
                    }
                ?>
            </a>

            <div class="row text-center mt-4">
                <!-- Compra Segura -->
                <div class="col-2">
                    <div class="d-flex flex-column align-items-center">
                        <img src="<?= INCLUDE_PATH; ?>assets/images/loja/product_icon_1.jpeg" 
                            style="width:35px;height:35px;object-fit:contain;" alt="Compra Segura">
                        
                        <small class="text-black mt-2" style="font-size: 12px; line-height: normal;">
                            Compra 100% segura
                        </small>
                    </div>
                </div>

                <!-- Enviado pela plataforma -->
                <div class="col-2">
                    <a href="<?php echo $product['redirect_link']; ?>" class="d-flex flex-column align-items-center">
                        <img src="<?= INCLUDE_PATH; ?>assets/images/loja/product_icon_2.jpeg" 
                            style="width:35px;height:35px;object-fit:contain;" alt="Envio">

                        <small class="text-black mt-2" style="font-size: 12px; line-height: normal;">
                            <?php
                            if (array_key_exists($related, $plataformas)) {
                                echo 'Enviado por <a href="' . $product['redirect_link'] . '" target="_blank" class="text-black"><b>' . $plataformas[$related] . '</b></a>';
                            } else {
                                echo 'Envio garantido';
                            }
                            ?>
                        </small>
                    </a>
                </div>

                <!-- Garantia / Reembolso -->
                <div class="col-2">
                    <div class="d-flex flex-column align-items-center">
                        <img src="<?= INCLUDE_PATH; ?>assets/images/loja/product_icon_3.jpeg" 
                            style="width:35px;height:35px;object-fit:contain;" alt="Garantia">

                        <small class="text-black mt-2" style="font-size: 12px; line-height: normal;">
                            Garantia de reembolso
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php if (!empty($product['iframe'])): ?>
    <style>
        .iframe-container {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: auto;
        }

        .iframe-container iframe {
            width: 100%;
            height: 100vh;
            max-height: 1500px;
            border: none;
        }
    </style>

    <div class="iframe-container">
        <iframe src="<?php echo $product['iframe']; ?>" loading="lazy"></iframe>
    </div>
    <?php endif; ?>

    <?php if (!empty(trim($product['description']))): ?>

        <div class="card mt-4">

            <div class="card-body p-4">

                <h2 class="card-title mb-3">
                    Sobre o produto
                </h2>

                <div class="product-description">
                    <?= $product['description']; ?>
                </div>

                <div class="border-top mt-4 pt-4 text-center">

                    <p class="text-body-secondary mb-3">
                        Gostou deste produto? Confira a oferta e veja todos os detalhes.
                    </p>

                    <a href="<?= htmlspecialchars($product['redirect_link']); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn btn-dark d-inline-flex align-items-center px-4 py-2">

                        <?php
                            if ($product['button_type'] == 1) {

                                echo __('buy');

                            } elseif (
                                $product['button_type'] == 2 ||
                                $product['button_type'] == 3
                            ) {

                                echo "<i class='bx bxl-whatsapp me-1'></i>";
                                echo __('call_whatsApp');

                            } elseif ($product['button_type'] == 4) {

                                echo __('learn_more');

                            } elseif ($product['button_type'] == 5) {

                                echo "<i class='bx bx-calendar me-1'></i>";
                                echo __('agenda');

                            } elseif ($product['button_type'] == 6) {

                                echo __('register');
                            }
                        ?>

                        <i class="bx bx-right-arrow-alt ms-2"></i>

                    </a>

                </div>

            </div>

        </div>

    <?php endif; ?>
    
    <!--Listar FAQ-->
    <?php
        $sqlFaq = "SELECT id, question, answer 
                   FROM tb_product_faqs 
                   WHERE product_id = :product_id 
                   ORDER BY position ASC";

        $stmtFaq = $conn_pdo->prepare($sqlFaq);
        $stmtFaq->bindParam(':product_id', $product['id'], PDO::PARAM_INT);
        $stmtFaq->execute();
        $faqs = $stmtFaq->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php if (!empty($faqs)): ?>
        <div class="card p-4 m-4">
            <h5 class="text-center mb-4"><?= __('product_faq') ?></h5>
            
            <div class="accordion" id="accordionFaq">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $faq['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $faq['id']; ?>" aria-expanded="false"
                                aria-controls="collapse<?= $faq['id']; ?>">
                                <?= htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>

                        <div id="collapse<?= $faq['id']; ?>" class="accordion-collapse collapse"
                            aria-labelledby="heading<?= $faq['id']; ?>" data-bs-parent="#accordionFaq">
                            <div class="accordion-body">
                                <?= nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_product_categories';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id ORDER BY (main = 1) DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->execute();

        // Recuperar os resultados
        $productCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verifica se encontrou a categoria
        if ($productCategory) {
            // Nova consulta para buscar produtos na mesma categoria
            $sql = "SELECT p.* FROM tb_products p
                    INNER JOIN tb_product_categories pc ON p.id = pc.product_id
                    WHERE pc.category_id = :category_id AND p.shop_id = :shop_id AND p.status = :status AND p.id != :current_product
                    GROUP BY p.id
                    ORDER BY p.id ASC
                    LIMIT 48";

            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':category_id', $productCategory['category_id']);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->bindValue(':status', 1);
            $stmt->bindParam(':current_product', $product['id']);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Consulta para buscar produtos em destaque
            $sql = "SELECT * FROM tb_products 
                    WHERE shop_id = :shop_id AND status = :status AND id != :current_product
                    ORDER BY emphasis DESC
                    LIMIT 48";

            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->bindValue(':status', 1);
            $stmt->bindParam(':current_product', $product['id']);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Inicialize uma variável de controle e um contador
        $primeiroElemento = true;
        $contador = 0;

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
                <h4 class="mb-3 me-3">
                    <?php
                        if (array_key_exists($related, $plataformas)) {
                            echo __('best_sellers') . $plataformas[$related];
                        } else {
                            echo __('related_products');
                        }
                    ?>
                </h4>
            </div>
            <div style="width: 100px; height: 5px; background: #000;"></div>
        </div>

        <div class="row g-3 p-4">
            <?php
            // Verifica o valor do "product_mode_related"
            $product_mode_related = $product['product_mode_related']; // Substitua pelo valor real da variável

            if ($product_mode_related == "manual") {
                // Consulta na tabela tb_product_related para obter os produtos relacionados manualmente
                $sql_related = "SELECT related_product_id FROM tb_product_related WHERE product_id = :product_id AND shop_id = :shop_id";
                $stmt_related = $conn_pdo->prepare($sql_related);
                $stmt_related->bindParam(':product_id', $product['id']);
                $stmt_related->bindParam(':shop_id', $shop_id);
                $stmt_related->execute();
                $related_products = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

                // Debug: Imprimir os produtos relacionados
                // print_r($related_products);

                foreach ($related_products as $related) {
                    // Consulta para buscar o produto relacionado
                    $sql = "SELECT * FROM tb_products WHERE shop_id = :shop_id AND status = :status AND id = :related_product_id";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $shop_id);
                    $stmt->bindValue(':status', 1); // Status ativo
                    $stmt->bindParam(':related_product_id', $related['related_product_id']);
                    $stmt->execute();
                    $related_product = $stmt->fetch(PDO::FETCH_ASSOC); // Modificado para usar fetch() em vez de fetchAll()

                    // Debug: Imprimir o produto relacionado
                    // print_r($related_product);

                    // Consulta de imagens
                    $sql_images = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";
                    $stmt_images = $conn_pdo->prepare($sql_images);
                    $stmt_images->bindParam(':usuario_id', $related_product['id']);
                    $stmt_images->execute();
                    $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

                    // Formatação de preço
                    $preco = $related_product['price'];
                    $currencySymbol = ($related_product['language'] == 'pt') ? "R$ " : "$ ";
                    $price = $currencySymbol . number_format($preco, 2, ",", ".");
                    $desconto = $related_product['discount'];
                    $discount = $currencySymbol . number_format($desconto, 2, ",", ".");

                    if ($related_product['price'] != 0) {
                        $porcentagemDesconto = (($related_product['price'] - $related_product['discount']) / $related_product['price']) * 100;
                    } else {
                        $porcentagemDesconto = 0;
                    }

                    $porcentagemDesconto = round($porcentagemDesconto, 0);

                    if ($related_product['discount'] == "0.00") {
                        $activeDiscount = "d-none";
                        $priceAfterDiscount = $price;
                    } else {
                        $activeDiscount = "";
                        $priceAfterDiscount = $discount;
                        $discount = $price;
                    }

                    $link = INCLUDE_PATH_LOJA . $related_product['link'];

                    if ($related_product['without_price']) {
                        $priceAfterDiscount = "<a href='" . $link . "' class='btn btn-dark small px-3 py-1'>" . __('learn_more') . "</a>";
                    }

                    // Exibindo o produto relacionado
                    echo '<div class="col-sm-3 numBanner d-grid">';
                    echo '<a href="' . $link . '" class="product-link d-grid">';
                    echo '<div class="card d-grid">';

                    if ($images) {
                        foreach ($images as $image) {
                            echo '<div class="product-image">';
                            echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                            echo '<img src="' . CDN_BASE_URL . 'products/' . $image['usuario_id'] . '/' . $image['nome_imagem'] . '" class="card-img-top" alt="' . $related_product['name'] . '">';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="product-image">';
                        echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                        echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" class="card-img-top" alt="' . $related_product['name'] . '">';
                        echo '</div>';
                    }

                    echo '<div class="card-body">';
                    echo '<p class="card-title mb-0">' . $related_product['name'] . '</p>';
                    echo '<div class="d-flex mb-3">';
                    echo '<small class="fw-semibold text-body-secondary text-decoration-line-through me-2 ' . $activeDiscount . '">' . $discount . '</small>';
                    echo '<h4 class="card-text">' . $priceAfterDiscount . '</h4>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                // Exibe os produtos automaticamente como está
                foreach ($resultados as $related_product) {
                    // A lógica que você já tem para exibir os produtos relacionados automaticamente
                    $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':usuario_id', $related_product['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Formatação preço
                    $preco = $related_product['price'];
                    $currencySymbol = ($related_product['language'] == 'pt') ? "R$ " : "$ ";

                    // Transforma o número no formato "R$ 149,90"
                    $price = $currencySymbol . number_format($preco, 2, ",", ".");

                    // Formatação preço com desconto
                    $desconto = $related_product['discount'];

                    // Transforma o número no formato "R$ 149,90"
                    $discount = $currencySymbol . number_format($desconto, 2, ",", ".");

                    // Calcula a porcentagem de desconto
                    if ($related_product['price'] != 0) {
                        $porcentagemDesconto = (($related_product['price'] - $related_product['discount']) / $related_product['price']) * 100;
                    } else {
                        $porcentagemDesconto = 0;
                    }

                    // Arredonda o resultado para duas casas decimais
                    $porcentagemDesconto = round($porcentagemDesconto, 0);

                    if ($related_product['discount'] == "0.00") {
                        $activeDiscount = "d-none";

                        $priceAfterDiscount = $price;
                    } else {
                        $activeDiscount = "";

                        $priceAfterDiscount = $discount;
                        $discount = $price;
                    }

                    // Link do produto
                    $link = INCLUDE_PATH_LOJA . $related_product['link'];

                    if ($related_product['without_price']) {
                        $priceAfterDiscount = "<a href='" . $link . "' class='btn btn-dark small px-3 py-1'>" . __('learn_more') . "</a>";
                    }

                    echo '<div class="col-sm-3 numBanner d-grid">';
                    echo '<a href="' . $link . '" class="product-link d-grid">';
                    echo '<div class="card d-grid">';

                    if ($imagens) {
                        foreach ($imagens as $imagem) {
                            echo '<div class="product-image">';
                            echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                            echo '<img src="' . CDN_BASE_URL . 'products/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" class="card-img-top" alt="' . $related_product['name'] . '">';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="product-image">';
                        echo '<span class="card-discount small ' . $activeDiscount . '">' . $porcentagemDesconto . '% OFF</span>';
                        echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" class="card-img-top" alt="' . $related_product['name'] . '">';
                        echo '</div>';
                    }

                    echo '<div class="card-body">';
                    echo '<p class="card-title mb-0">' . $related_product['name'] . '</p>';
                    echo '<div class="d-flex mb-3">';
                    echo '<small class="fw-semibold text-body-secondary text-decoration-line-through me-2 ' . $activeDiscount . '">' . $discount . '</small>';
                    echo '<h4 class="card-text">' . $priceAfterDiscount . '</h4>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    <?php
        }
    ?>
</div>







<style>
    .modal-content {
        height: auto !important;
    }
</style>

<!-- Modal Desconto -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content rounded-0 border-0 text-center p-4">
      <div class="modal-body">
        <h4 id="discountTitle" class="fw-bold mb-3">
            🎉 <?= __('modal_discount_title') ?>
        </h4>
        <p id="discountMessage" class="mb-4">
            <?= __('modal_discount_message') ?>
        </p>
        <button id="discountBtn" class="btn btn-dark px-4 py-2 fw-bold">
            <?= __('modal_discount_btn') ?>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('discountModal');
  const discountModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
  const modalKey = "discount_modal_shown";
  const productLink = "<?php echo $product['redirect_link']; ?>";

  if (!sessionStorage.getItem(modalKey)) {
    // Timer aleatório entre 30s (30000 ms) e 60s (60000 ms)
    const delay = Math.floor(Math.random() * (60000 - 30000 + 1)) + 30000;

    setTimeout(() => {
      discountModal.show();
      sessionStorage.setItem(modalKey, true);

      const openLinkAndClose = function () {
        window.open(productLink, "_blank");
        discountModal.hide();
        document.body.removeEventListener("click", clickHandler);
        document.getElementById("discountBtn").removeEventListener("click", openLinkAndClose);
      };

      const clickHandler = function(e) {
        if (!e.target.closest("#discountBtn")) {
          openLinkAndClose();
        }
      };

      // Evento: clique no botão → abre link e fecha modal
      document.getElementById("discountBtn").addEventListener("click", openLinkAndClose);

      // Evento: clique fora do botão → abre link e fecha modal
      document.body.addEventListener("click", clickHandler);

    }, delay);
  }
});
</script>












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

<!-- Title e description do produto -->
<script>
    // Use o jQuery para alterar o título e a descrição
    $(document).ready(function() {
        var productName = "<?php echo $product['seo_name']; ?>";
        var shopName = "<?php echo ($title == "") ? $loja : $title; ?>";

        // Novo título e descrição desejados
        var novoTitulo = productName + " | " + shopName;
        var novaDescricao = "<?php echo $product['seo_description']; ?>";

        // Alterar o título
        $('title').text(novoTitulo);

        // Alterar a descrição
        $('meta[name="description"]').attr('content', novaDescricao);
    });
</script>

<!-- Rastreio de visualização de produto -->
<script>
$(document).ready(function () {
    setTimeout(function () {
        $.ajax({
            url: "./back-end/product_view_service.php",
            type: "POST",
            data: {
                product_id: "<?php echo $product['id']; ?>",
                shop_id: "<?php echo $shop_id; ?>"
            }
        });
    }, 2000); // delay de 2 segundos
});
</script>