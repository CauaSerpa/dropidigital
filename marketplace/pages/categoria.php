<style>
    .listProducts .row
    {
        --bs-gutter-x: 1rem !important;
        --bs-gutter-y: 1rem !important;
    }
    
    .accordion-button:not(.collapsed) {
        color: inherit !important;
        background-color: transparent !important;
    }
</style>

<div class="listProducts container">
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
                        echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
                    } else {
                        echo '<li class="breadcrumb-item small"><a href="' . INCLUDE_PATH_LOJA . '" class="text-decoration-none">' . __('home_page') . '</a></li>';
                        echo '<li class="breadcrumb-item small ms-2"><a href="' . INCLUDE_PATH_LOJA . $parent_category['link'] . '" class="text-decoration-none">' . $parent_category['name'] . '</a></li>';
                    }
                ?>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page"><?php echo $category['name']; ?></li>
            </ol>
        </nav>
        <div class="col-sm-3">
            <div class="card p-3 mb-3">
                <h4><?= __('filter') ?></h4>
            </div>
            
            <!-- Descrição da categoria (opcional) -->
            <?php if (!empty($category['description'])): ?>
                <div class="mb-3">
                    <h5><?= __('description') ?></h5>
                    <p><?= nl2br($category['description']); ?></p>
                </div>
            <?php endif; ?>
            
            <!--Listar FAQ-->
            <?php
                $sqlFaq = "SELECT id, question, answer 
                           FROM tb_category_faqs 
                           WHERE category_id = :category_id 
                           ORDER BY position ASC";

                $stmtFaq = $conn_pdo->prepare($sqlFaq);
                $stmtFaq->bindParam(':category_id', $category['id'], PDO::PARAM_INT);
                $stmtFaq->execute();
                $faqs = $stmtFaq->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div>
                <?php if (!empty($faqs)): ?>
                    <h5 class="mb-3"><?= __('category_faq') ?></h5>
                    
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
                <?php endif; ?>
            </div>
            
        </div>
        <div class="col-sm-9">
            <div class="row g-3">
                <?php
                    // Paginação
                    $limite = 48; // quantidade de produtos por página
                    $paginaAtual = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
                    $offset = ($paginaAtual - 1) * $limite;
                
                    $sqlTotal = "
                        SELECT COUNT(DISTINCT pc.product_id) AS total
                        FROM tb_product_categories pc
                        INNER JOIN tb_products p ON p.id = pc.product_id
                        WHERE pc.shop_id = :shop_id
                          AND pc.category_id = :category_id
                          AND p.status = 1
                    ";
                    
                    $stmtTotal = $conn_pdo->prepare($sqlTotal);
                    $stmtTotal->bindParam(':shop_id', $shop_id);
                    $stmtTotal->bindParam(':category_id', $category['id']);
                    $stmtTotal->execute();
                    
                    $totalRegistros = (int)$stmtTotal->fetchColumn();
                    $totalPaginas = ceil($totalRegistros / $limite);
                
                    $sql = "
                        SELECT p.*
                        FROM tb_product_categories pc
                        INNER JOIN tb_products p ON p.id = pc.product_id
                        WHERE pc.shop_id = :shop_id
                          AND pc.category_id = :category_id
                          AND p.status = 1
                        ORDER BY p.id ASC
                        LIMIT :limite OFFSET :offset
                    ";

                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $shop_id);
                    $stmt->bindParam(':category_id', $category['id']);
                    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                    $stmt->execute();
                    
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
                                echo '<img src="' . CDN_BASE_URL . 'products/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" class="card-img-top" alt="' . $product['name'] . '">';
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
        </div>
        
        <?php if ($totalPaginas > 1): ?>
        <style>
            .line {
                height: 80%;
                width: 1px;
                background-color: #dedede;
            }
            
            .pagination.categories li {
                margin: 0 !important;
            }
            
            .pagination.categories li.active .page-link {
                --bs-btn-color: #fff;
                --bs-btn-border-width: 1px;
                --bs-btn-border-color: #212529;
                --bs-btn-border-radius: .375rem;
                --bs-btn-bg: #212529;
                color: var(--bs-btn-color) !important;
                border: var(--bs-btn-border-width) solid var(--bs-btn-border-color);
                background-color: var(--bs-btn-bg);
            }
            
            .pagination.categories li.active:hover .page-link {
                --bs-btn-hover-color: #fff;
                --bs-btn-hover-bg: #424649;
                --bs-btn-hover-border-color: #373b3e;
                color: var(--bs-btn-hover-color) !important;
                background-color: var(--bs-btn-hover-bg);
                border-color: var(--bs-btn-hover-border-color);
            }
            
            .pagination.categories li .page-link {
                color: #838694 !important;
                line-height: 1.5 !important;
                text-decoration: none !important;
            }
        </style>
        
        <!-- Paginação -->
        <nav aria-label="Page navigation example" class="d-flex align-items-center justify-content-end">
            <form method="get" class="form-inline d-flex align-items-center justify-content-end w-25">
                <label for="goto_page" class="text-end me-3 mb-0">Ir para página:</label>
                <div class="input-group" style="align-items: center; width: max-content;">
                    <input
                        type="number"
                        id="goto_page"
                        name="page"
                        class="form-control"
                        style="padding: .375rem .75rem; width: 70px; max-width: 70px;"
                        min="1"
                        max="<?= $totalPaginas; ?>"
                        value="<?= $paginaAtual; ?>"
                    >
                    <button type="submit" class="btn btn-dark ml-2">Ir</button>
                </div>
            </form>
            
            <div class="line ms-3 me-3"></div>
            
            <ul class="pagination categories justify-content-end align-items-center mb-0">
                <!-- Números das páginas -->
                <?php
                    $intervalo = 2; // Número de páginas antes e depois da página atual
                    $inicio = max(1, $paginaAtual - $intervalo);
                    $fim = min($totalPaginas, $paginaAtual + $intervalo);
        
                    // Primeira página
                    if ($inicio > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '">1</a></li>';
                        if ($inicio > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
        
                    // Páginas antes e depois da atual
                    for ($i = $inicio; $i <= $fim; $i++) {
                        if ($i == $paginaAtual) {
                            echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
                        }
                    }
        
                    // Última página
                    if ($fim < $totalPaginas) {
                        if ($fim < $totalPaginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPaginas . '&search=' . urlencode($search) . '">' . $totalPaginas . '</a></li>';
                    }
                ?>
            </ul>
        </nav>
        <?php endif; ?>
        
    </div>
</div>