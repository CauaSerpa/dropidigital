<?php
    // Conecte-se ao banco de dados aqui
    require('../../config.php');

    // Função PHP para carregar mais produtos
    function carregarMaisProdutos($shop_id, $offset, $limit) {
        global $conn_pdo;

        $tabela = 'tb_products';
        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis ORDER BY id ASC LIMIT :limit OFFSET :offset";

        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindValue(':emphasis', 1);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['carregarMais'])) {
        $url = $_POST['url'];
        $shop_id = $_POST['shop_id'];
        $offset = $_POST['offset'];
        $limit = 100;

        $produtos = carregarMaisProdutos($shop_id, $offset, $limit);

        foreach ($produtos as $product) {
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
            $price = "R$ " . number_format($preco, 2, ",", ".");

            // Formatação preço com desconto
            $desconto = $product['discount'];
            $discount = "R$ " . number_format($desconto, 2, ",", ".");

            // Calcula a porcentagem de desconto
            if ($product['price'] != 0) {
                $porcentagemDesconto = (($product['price'] - $product['discount']) / $product['price']) * 100;
            } else {
                $porcentagemDesconto = 0;
            }

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
            $link = $url . $product['link'];

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
    }
?>
