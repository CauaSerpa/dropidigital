<?php
if (!in_array($shop_id, [2, 25, 61])) {
    header('Location: '.INCLUDE_PATH_DASHBOARD);
    exit;
}

// Configurações
$limitePorPagina = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$keyword = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : '';
$categoriaId = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : '';
$tipoBusca = isset($_GET['type']) ? $_GET['type'] : 'mix';

include_once('../config.php');






// Buscar categorias do banco para o select
$sql = "SELECT
            c.id,
            c.aliexpress_category_id AS category_id,
            c.name AS category_name,
            COUNT(p.id) AS total_products
        FROM tb_aliexpress_categories c
        LEFT JOIN tb_aliexpress_products p
            ON p.first_level_category_id = c.aliexpress_category_id
        GROUP BY
            c.id,
            c.aliexpress_category_id,
            c.name
        ORDER BY total_products DESC,
                c.name ASC";

$stmt = $conn_pdo->prepare($sql);
$stmt->execute();
$categoriasAliExpress = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn_pdo->prepare("
    SELECT COUNT(*)
    FROM tb_aliexpress_products
");

$stmt->execute();
$totalProdutosGeral = (int)$stmt->fetchColumn();

array_unshift(
    $categoriasAliExpress,
    [
        'id' => 0,
        'category_id' => '0',
        'category_name' => 'Todos os Produtos',
        'total_products' => $totalProdutosGeral
    ]
);








/**
 * PRODUTOS
 */
$where = [];
$params = [];

/**
 * BUSCA TEXTO
 */
if (!empty($keyword)) {
    $where[] = "(
        p.title LIKE :keyword
        OR p.normalized_title LIKE :keyword
        OR p.keyword_source LIKE :keyword
    )";

    $params[':keyword'] = '%' . $keyword . '%';
}

/**
 * FILTRO CATEGORIA
 */
if (!empty($categoriaId) && $categoriaId != '0') {
    $where[] = "
        p.first_level_category_id = :category_id
    ";

    $params[':category_id'] = $categoriaId;
}

/**
 * WHERE FINAL
 */
$whereSql = '';
if (!empty($where)) {
    $whereSql =
        'WHERE '
        . implode(' AND ', $where);
}

/**
 * TOTAL PRODUTOS
 */
$sqlTotal = "
    SELECT COUNT(*)
    FROM tb_aliexpress_products p
    $whereSql
";

$stmt = $conn_pdo->prepare($sqlTotal);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();

$totalProdutos = (int)$stmt->fetchColumn();

$totalPaginas = max(
    1,
    ceil(
        $totalProdutos
        / $limitePorPagina
    )
);

/**
 * OFFSET
 */
$offset =
    ($paginaAtual - 1)
    * $limitePorPagina;

/**
 * LISTAGEM PRODUTOS
 */

$sql = "
    SELECT
        p.id,
        p.product_id,
        p.title,
        p.product_main_image_url
            AS image_url,
        p.product_url
            AS link,
        p.target_sale_price
            AS price,
        p.evaluate_rate
            AS rating_decimal,
        p.lastest_volume
            AS sales_volume,
        p.commission_rate,
        ROUND(
            REPLACE(
                p.evaluate_rate,
                '%',
                ''
            ) / 20,
            1
        ) AS rating,
        CONCAT(
            ROUND(
                (
                    REPLACE(
                        p.commission_rate,
                        '%',
                        ''
                    )
                    *
                    p.target_sale_price
                ) / 100,
                2
            ),
            ' USD'
        ) AS commission_value
    FROM tb_aliexpress_products p

    $whereSql

    ORDER BY
        p.lastest_volume DESC,
        p.score DESC,
        p.updated_at DESC

    LIMIT :limit
    OFFSET :offset
";

$stmt = $conn_pdo->prepare($sql);

/**
 * PARAMS
 */
foreach ($params as $key => $value) {
    $stmt->bindValue(
        $key,
        $value
    );
}

$stmt->bindValue(
    ':limit',
    $limitePorPagina,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ':offset',
    $offset,
    PDO::PARAM_INT
);

$stmt->execute();

$produtosAliExpress = $stmt->fetchAll(PDO::FETCH_ASSOC);









function renderStars($rating) {
    $fullStar = "<i class='bx bxs-star'></i>";
    $halfStar = "<i class='bx bxs-star-half'></i>";
    $emptyStar = "<i class='bx bx-star'></i>";

    $stars = '';
    $fullStars = floor($rating);
    $halfStars = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStars;

    $stars .= str_repeat($fullStar, $fullStars);
    $stars .= str_repeat($halfStar, $halfStars);
    $stars .= str_repeat($emptyStar, $emptyStars);

    return $stars;
}

// Buscar categorias do banco para o select
$sql = "SELECT c.id, c.name AS category_name, p.name AS parent_name
        FROM tb_categories c
        LEFT JOIN tb_categories p ON p.id = c.parent_category AND c.parent_category != 1
        WHERE c.shop_id = :shop_id
        ORDER BY c.id DESC";

$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $id, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [];
foreach ($items as $category) {
    if (!empty($category['parent_name'])) {
        $name = $category['parent_name'] . " > " . $category['category_name'];
    } else {
        $name = $category['category_name'];
    }
    $categories[] = ['id' => $category['id'], 'name' => $name];
}
?>

<style>
    /* Mantenha todos os estilos originais */
    .card-title {
        max-width: 278px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    
    .line {
        height: 80%;
        width: 1px;
        background-color: #dedede;
    }
    
    .active>.page-link, .page-link.active {
        background-color: var(--green-color) !important;
        border-color: var(--green-color) !important;
    }
    
    .pagination-container {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }
    
    .page-link {
        cursor: pointer;
    }
    
    .no-products {
        text-align: center;
        padding: 50px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .card.selected {
        --bs-btn-close-focus-shadow: 0 0 0 0.25rem rgba(1, 200, 155, 0.25);
        --bs-btn-close-focus-opacity: 1;
        border-color: rgb(1, 200, 155);
        outline: 0;
        box-shadow: var(--bs-btn-close-focus-shadow);
        opacity: var(--bs-btn-close-focus-opacity);
    }
    
    .category-buttons {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        max-height: 250px;
        overflow-y: auto;
    }
    
    .category-buttons .btn-category {
        margin: 5px;
        border-radius: 20px;
        padding: 8px 16px;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .category-buttons .btn-category.active {
        background-color: var(--green-color);
        border-color: var(--green-color);
        color: white;
    }
    
    .category-buttons .btn-category:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.9);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    
    .alert-categories-warning {
        margin-bottom: 20px;
    }
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<!-- Modal para cadastro manual -->
<div class="modal fade" id="affiliateModal" tabindex="-1" aria-labelledby="affiliateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="affiliateModalLabel">Cadastrar Produto Manualmente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="affiliateForm">
                    <div class="mb-3">
                        <label for="affiliateLink" class="form-label">Link de Afiliado</label>
                        <input type="url" class="form-control" id="affiliateLink" placeholder="Insira o link de afiliado" required>
                        <input type="hidden" id="productId">
                    </div>
                    <button type="submit" class="btn btn-success">Cadastrar Produto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Produtos AliExpress</h2>
    </div>
</div>

<!-- Categorias - DINÂMICAS (com fallback) -->
<div class="category-buttons">
    <div class="d-flex flex-wrap align-items-center">
        <div class="fw-semibold me-3 mb-2">Categorias:</div>
        <div class="d-flex flex-wrap">
            <?php foreach ($categoriasAliExpress as $cat): ?>
                <?php 
                $catId = is_array($cat) ? ($cat['category_id'] ?? $cat['id'] ?? '0') : '0';
                $catNome = is_array($cat) ? ($cat['category_name'] ?? $cat['name'] ?? 'Sem Nome') : $cat;
                ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['category' => $catId, 'page' => 1])) ?>" 
                   class="btn btn-outline-secondary btn-category mb-2 me-2 <?= $categoriaId == $catId || $catId == 0 && !$categoriaId ? 'active' : '' ?>">
                    <?= htmlspecialchars($catNome) ?>
                    <small>(<?= $cat['total_products'] ?? '0'; ?>)</small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Filtros de busca -->
<div class="card mb-3 p-0">
    <div class="card-body row px-4 py-3">
        <form method="get" class="d-flex" id="searchForm">
            <input 
                type="text" 
                name="search" 
                id="searchInput"
                class="form-control me-2" 
                placeholder="Buscar produtos..." 
                value="<?= htmlspecialchars($keyword); ?>"
            >
            <input type="hidden" name="category" id="categoryInput" value="<?= $categoriaId; ?>">
            <input type="hidden" name="limit" value="<?= $limitePorPagina; ?>">
            <input type="hidden" name="type" value="<?= $tipoBusca; ?>">
            <button type="submit" class="btn btn-success">Buscar</button>
        </form>
    </div>
</div>

<?php
    $sql = "
        SELECT 
            c.id,
            c.name AS category_name,
            p.name AS parent_name
        FROM tb_categories c
        LEFT JOIN tb_categories p ON p.id = c.parent_category AND c.parent_category != 1
        WHERE c.shop_id = :shop_id
        ORDER BY c.id DESC
    ";

    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categories = [];

    foreach ($items as $category) {
        if (!empty($category['parent_name'])) {
            $name = $category['parent_name'] . " > " . $category['category_name'];
        } else {
            $name = $category['category_name'];
        }

        $categories[] = [
            'id' => $category['id'],
            'name' => $name
        ];
    }
?>

<!-- Formulário para cadastro em massa -->
<form id="productForm" action="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/auto_create_product_aliexpress.php" method="POST">
    <div class="card mb-2 p-0">
        <div class="card-body row px-4 py-3">
            <label class="d-flex align-items-center">
                <div class="me-3 form-check">
                    <input type="checkbox" class="form-check-input mb-0" id="select-all">
                </div>
                <h5 class="mb-0">Selecionar Todos os Produtos</h5>
            </label>

            <div class="mt-2 d-none" id="cadProducts">
                <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                <input type="hidden" name="action" value="auto-create-product-aliexpress">
                <input type="hidden" name="selectedProducts" id="selectedProducts">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label small">Associar a uma Categoria</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Nenhuma</option>
    
                            <?php foreach ($categories as $c) { ?>
                                <option value="<?= $c['id']; ?>"><?= $c['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small" id="btnCad">Cadastrar Produtos Selecionados</button>
            </div>
        </div>
    </div>
</form>

<!-- Grid de produtos -->
<div class="row g-3" id="aliexpressProductsCarousel">
    <?php if (empty($produtosAliExpress)): ?>
    <div class="col-12">
        <div class="no-products">
            <h4>Nenhum produto encontrado</h4>
            <p class="text-muted">
                <?php if ($tipoBusca == 'hot'): ?>
                    Não foi possível carregar produtos em alta no momento. Tente novamente mais tarde.
                <?php elseif (!empty($categoriaId) && $categoriaId != '0'): ?>
                    Nenhum produto encontrado para esta categoria. Tente outra categoria ou faça uma busca específica.
                <?php else: ?>
                    Nenhum produto encontrado para "<?= htmlspecialchars($keyword); ?>". Tente outra palavra-chave.
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($produtosAliExpress as $index => $produto): ?>
        <?php
            // Verificar se o produto já está cadastrado
            $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE (product_id = :product_id OR name = :name) AND shop_id = :shop_id";
            $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
            $verificaStmt->bindValue(':product_id', $produto['product_id']);
            $verificaStmt->bindValue(':name', $produto['name']);
            $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
            $verificaStmt->execute();
            $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

            // marca se já está cadastrado
            $isRegistered = $produtoCadastrado ? 1 : 0;
        ?>
        <div class="d-grid col-md-3 col-sm-6">
            <div class="card p-0" data-product-id="<?= $produto['id']; ?>">
                <img src="<?= $produto['image_url']; ?>" class="card-img-top" alt="<?= $produto['title']; ?>" style="height: 310px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <!-- Exibir aviso amarelo se o produto já estiver cadastrado -->
                    <?php if ($produtoCadastrado) { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-1">
                            Produto já cadastrado!
                        </small>
                    <?php } ?>

                    <p class="card-title mb-3" title="<?= $produto['title']; ?>"><?= $produto['title']; ?></p>

                    <small class="fw-semibold text-body-secondary">Receba até</small>
                    <h5 class="card-text mb-0"><?= $produto['commission_value']; ?></h5>

                    <small class="fw-semibold text-body-secondary">Preço do produto: <?= $produto['price']; ?> USD</small>

                    <!-- Avaliação em estrelas -->
                    <small class="d-flex align-items-center text-body-secondary mt-3 mb-2" style="height: 21px;">
                        <?php if ($produto['rating'] > 0): ?>
                        <span class="text-warning me-1">
                            <?= renderStars($produto['rating']); ?>
                        </span>
                        <?= $produto['rating']; ?>
                        |
                        <?php endif; ?>
                        <?php if ($produto['sales_volume'] > 0): ?>
                            <?= number_format($produto['sales_volume'], 0, ',', '.'); ?> vendidos
                        <?php endif; ?>
                    </small>

                    <div class="buttons d-flex mt-auto pt-3">
                        <label class="input-group-text me-2" data-toggle="tooltip" data-placement="top" title="Selecionar para cadastro em massa">
                            <input type="checkbox" class="form-check-input mt-0 product-checkbox" value="<?= $produto['id']; ?>" data-registered="<?= $isRegistered; ?>">
                        </label>
                        <a href="#" class="btn btn-success fw-semibold px-4 py-2 small w-100" onclick="openAffiliateModal('<?= $produto['id']; ?>', '<?= addslashes($produto['title']); ?>')">Cadastrar</a>
                        <a href="<?= $produto['link']; ?>" target="_blank" class="btn btn-light ms-2" title="Visualizar no AliExpress">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);">
                                <path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path>
                                <path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Paginação -->
<?php if (!empty($produtosAliExpress) && $totalPaginas > 1): ?>
<div class="pagination-container">
    <nav aria-label="Paginação" class="d-flex align-items-center justify-content-end">
        
        <div class="me-3 text-muted small">
            Página <?= $paginaAtual; ?> de <?= $totalPaginas; ?>
            <?php if ($totalProdutos > 0): ?>
            | Total: <?= number_format($totalProdutos, 0, ',', '.'); ?> produtos
            <?php endif; ?>
        </div>
        
        <div class="line ms-2 me-3"></div>
        
        <form method="get" class="form-inline d-flex align-items-center me-3">
            <label for="goto_page" class="text-end me-2 mb-0 small">Ir para:</label>
            <div class="input-group" style="align-items: center; width: max-content;">
                <input
                    type="number"
                    id="goto_page"
                    name="page"
                    class="form-control form-control-sm"
                    style="width: 60px;"
                    min="1"
                    max="<?= $totalPaginas; ?>"
                    value="<?= $paginaAtual; ?>"
                >
                <input type="hidden" name="search" value="<?= htmlspecialchars($keyword); ?>">
                <input type="hidden" name="type" value="<?= $tipoBusca; ?>">
                <input type="hidden" name="category" value="<?= $categoriaId; ?>">
                <input type="hidden" name="limit" value="<?= $limitePorPagina; ?>">
                <button type="submit" class="btn btn-success btn-sm">Ir</button>
            </div>
        </form>
        
        <div class="line me-3"></div>
        
        <ul class="pagination justify-content-end align-items-center mb-0">
            <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $paginaAtual-1 ?>&search=<?= urlencode($keyword) ?>&type=<?= $tipoBusca ?>&category=<?= $categoriaId ?>&limit=<?= $limitePorPagina ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            
            <?php
            $intervalo = 2;
            $inicio = max(1, $paginaAtual - $intervalo);
            $fim = min($totalPaginas, $paginaAtual + $intervalo);

            if ($inicio > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($keyword) . '&type=' . $tipoBusca . '&category=' . $categoriaId . '&limit=' . $limitePorPagina . '">1</a></li>';
                if ($inicio > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            for ($i = $inicio; $i <= $fim; $i++) {
                $activeClass = $i == $paginaAtual ? 'active' : '';
                echo '<li class="page-item ' . $activeClass . '">';
                echo '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($keyword) . '&type=' . $tipoBusca . '&category=' . $categoriaId . '&limit=' . $limitePorPagina . '">' . $i . '</a>';
                echo '</li>';
            }

            if ($fim < $totalPaginas) {
                if ($fim < $totalPaginas - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPaginas . '&search=' . urlencode($keyword) . '&type=' . $tipoBusca . '&category=' . $categoriaId . '&limit=' . $limitePorPagina . '">' . $totalPaginas . '</a></li>';
            }
            ?>
            
            <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $paginaAtual+1 ?>&search=<?= urlencode($keyword) ?>&type=<?= $tipoBusca ?>&category=<?= $categoriaId ?>&limit=<?= $limitePorPagina ?>" aria-label="Próximo">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<?php endif; ?>

<script>
const selectAll = document.getElementById('select-all');
const checkboxes = document.querySelectorAll('.product-checkbox');
const cadProducts = document.getElementById('cadProducts');
const btnCad = document.getElementById('btnCad');
const hiddenInput = document.getElementById('selectedProducts');
const form = document.getElementById('productForm');
const loadingOverlay = document.getElementById('loadingOverlay');

function showLoading() {
    if (loadingOverlay) loadingOverlay.style.display = 'flex';
}

function hideLoading() {
    if (loadingOverlay) loadingOverlay.style.display = 'none';
}

function updateUI() {
    const selectedValues = [...checkboxes]
        .filter(cb => cb.checked)
        .map(cb => cb.value)
        .join(',');

    hiddenInput.value = selectedValues;
    const atLeastOne = selectedValues.length > 0;
    cadProducts.classList.toggle('d-none', !atLeastOne);
    if (btnCad) btnCad.disabled = !atLeastOne;
}

if (selectAll) {
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => {
            const already = cb.dataset.registered === '1';
            if (!cb.disabled && !already) {
                cb.checked = this.checked;
                const card = cb.closest('.card');
                if (card) {
                    card.classList.toggle('selected', this.checked);
                }
            }
        });
        updateUI();
    });
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
        const card = this.closest('.card');
        if (card) {
            card.classList.toggle('selected', this.checked);
        }

        if (selectAll) {
            const allChecked = [...checkboxes].every(c => c.checked || c.disabled);
            selectAll.checked = allChecked;
        }

        updateUI();
    });
});

if (form) {
    form.addEventListener('submit', function (e) {
        if (hiddenInput.value === '') {
            e.preventDefault();
            alert('Selecione pelo menos um produto para cadastro em massa.');
        }
    });
}

function openAffiliateModal(productId, productTitle) {
    document.getElementById('productId').value = productId;
    document.getElementById('affiliateLink').value = '';
    
    const modalTitle = document.querySelector('#affiliateModal .modal-title');
    if (modalTitle && productTitle) {
        modalTitle.textContent = `Cadastrar Produto: ${productTitle.substring(0, 50)}`;
    }
    
    var affiliateModal = new bootstrap.Modal(document.getElementById('affiliateModal'));
    affiliateModal.show();
}

document.getElementById('affiliateForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    var productId = document.getElementById('productId').value;
    var affiliateLink = document.getElementById('affiliateLink').value;

    var url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + encodeURIComponent(affiliateLink) + "&product=aliexpress&product_id=" + productId;
    window.location.href = url;
});

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    
    $('#searchForm').on('submit', function() {
        showLoading();
    });
    
    $('a.page-link').on('click', function(e) {
        if (!$(this).parent().hasClass('disabled')) {
            showLoading();
        }
    });
    
    $('.btn-category').on('click', function() {
        showLoading();
    });
});

window.addEventListener('load', function() {
    hideLoading();
});
</script>