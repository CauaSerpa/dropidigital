<style>
    .listProducts .row
    {
        --bs-gutter-x: 1rem !important;
        --bs-gutter-y: 1rem !important;
    }

    .search-text::before,
    .search-text::after
    {
        content: '"';
    }
</style>

<div class="listProducts container">
    <div class="row p-4">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item small"><a href="<?php echo INCLUDE_PATH_LOJA; ?>" class="text-decoration-none">Página inicial</a></li>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page">Busca por "<?php echo @$_GET['q']; ?>"</li>
            </ol>
        </nav>
        <p class="text-black fs-2 mb-3">Busca por <span class="search-text fw-semibold"><?php echo @$_GET['q']; ?></span></p>
        <?php
            // Exibir os resultados, se houver
            if ($search_results) {
        ?>
        <div class="col-sm-3">
            <div class="card p-3">
                <h4>Filtro</h4>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="row g-3">
                <?php
                    foreach ($search_results as $search_result) {
                        // Nome da tabela para a busca
                        $tabela = 'tb_products';

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND id = :id ORDER BY id ASC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop_id);
                        $stmt->bindValue(':status', 1);
                        $stmt->bindParam(':id', $search_result['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Loop através dos resultados e exibir todas as colunas
                        foreach ($products as $product) {
                            // Consulta SQL para selecionar todas as colunas com base no ID
                            $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':usuario_id', $product['id']);
                            $stmt->execute();

                            // Recuperar os resultados
                            $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Formatacao da moeda
                            $currencySymbol = ($product['language'] == 'pt') ? "R$ " : "$ ";

                            // Formatação preço
                            $preco = $product['price'];

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

                            // Link do produto
                            $link = INCLUDE_PATH_LOJA . $product['link'];

                            if ($product['without_price']) {
                                $priceAfterDiscount = "<a href='" . $link . "' class='btn btn-dark small px-3 py-1'>Saiba Mais</a>";
                            }

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
            </div>
        </div>
        <?php
            } else {
                echo '<small class="d-flex mb-3 px-3 py-2" id="noProducts" style="color: #c29100; background: #fff7e0;">Nenhum produto foi encontrado.</small>';
            }
        ?>
    </div>
</div>