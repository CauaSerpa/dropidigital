<div class="container">
    <div class="row p-4">
        <div class="col-sm-3">
            <div class="card p-3">
                <h4>Filtro</h4>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="row g-3">
                    <?php
            // Nome da tabela para a busca
            $tabela = 'tb_products';

            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND categories = :categories ORDER BY id ASC";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->bindParam(':categories', $categoria_id);
            $stmt->execute();

            // Recuperar os resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                echo '<div class="col-sm-4">';
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
            }
        ?>
            </div>
        </div>
    </div>
</div>