<style>
    .card-img-top {
        height: 220px;
        object-fit: cover;
    }

    .product-card {
        border: 2px solid transparent;
        padding: 10px;
        display: inline-block;
        margin: 10px;
        cursor: pointer;
    }

    .card.selected {
        --bs-btn-close-focus-shadow: 0 0 0 0.25rem rgba(1, 200, 155, 0.25);
        --bs-btn-close-focus-opacity: 1;
        border-color: rgb(1, 200, 155);
        outline: 0;
        box-shadow: var(--bs-btn-close-focus-shadow);
        opacity: var(--bs-btn-close-focus-opacity);
    }
</style>

<!-- Modal Bootstrap -->
<div class="modal fade" id="affiliateModal" tabindex="-1" aria-labelledby="affiliateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="affiliateModalLabel">Inserir link de afiliado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="affiliateForm">
          <div class="mb-3">
            <label for="affiliateLink" class="form-label">Link de Afiliado</label>
            <input type="url" class="form-control" id="affiliateLink" placeholder="Insira o link de afiliado" required>
            <input type="hidden" id="productId"> <!-- ID do produto será inserido dinamicamente -->
          </div>
          <button type="submit" class="btn btn-success">Criar Produto</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Produtos Hotmart</h2>
    </div>
</div>

<!-- Campo de busca e filtros -->
<div class="card mb-3 p-0">
    <div class="card-body px-4 py-3">
        <form method="GET" action="" class="row g-2 align-items-end">

            <!-- Busca -->
            <div class="col-md-4">
                <input type="text" name="search" placeholder="Buscar produtos..." class="form-control" value="<?= $_GET['search'] ?? '' ?>">
            </div>

            <!-- Tipo do produto -->
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="" <?= (!isset($_GET['type']) && empty($_GET['type'])) ? 'selected' : '' ?> disabled>Tipo</option>
                    <option value="digital" <?= ($_GET['type'] ?? '') === 'digital' ? 'selected' : '' ?>>Digital</option>
                    <option value="physical" <?= ($_GET['type'] ?? '') === 'physical' ? 'selected' : '' ?>>Físico</option>
                </select>
            </div>

            <!-- Idioma -->
            <div class="col-md-2">
                <select name="lang" class="form-select">
                    <option value="" <?= (!isset($_GET['lang']) && empty($_GET['lang'])) ? 'selected' : '' ?> disabled>Idioma</option>
                    <option value="pt" <?= ($_GET['lang'] ?? '') === 'pt' ? 'selected' : '' ?>>Português</option>
                    <option value="en" <?= ($_GET['lang'] ?? '') === 'en' ? 'selected' : '' ?>>Inglês</option>
                    <option value="es" <?= ($_GET['lang'] ?? '') === 'es' ? 'selected' : '' ?>>Espanhol</option>
                    <option value="de" <?= ($_GET['lang'] ?? '') === 'de' ? 'selected' : '' ?>>Alemão</option>
                </select>
            </div>

            <!-- Tipo de aprovação -->
            <div class="col-md-2">
                <select name="approval_type" class="form-select">
                    <option value="" <?= (!isset($_GET['approval_type']) && empty($_GET['approval_type'])) ? 'selected' : '' ?> disabled>Aprovação</option>
                    <option value="immediate" <?= ($_GET['approval_type'] ?? '') === 'immediate' ? 'selected' : '' ?>>Imediata</option>
                    <option value="manual" <?= ($_GET['approval_type'] ?? '') === 'manual' ? 'selected' : '' ?>>Manual</option>
                </select>
            </div>

            <!-- Botão -->
            <div class="col-md-2 d-flex flex-column flex-md-row gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    Filtrar
                </button>
                <?php
                    $temFiltro =
                        !empty($_GET['search']) ||
                        !empty($_GET['product_type']) ||
                        !empty($_GET['language']) ||
                        !empty($_GET['approval_type']);

                    if ($temFiltro):
                ?>
                    <a href="?" class="btn btn-outline-secondary">Limpar</a>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_categories';

    $sql = "SELECT id, name FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    // Fetch all retorna um array contendo todas as linhas do conjunto de resultados
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form id="productForm" action="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/auto_create_product_hotmart.php" method="POST">

    <!-- Campo de busca -->
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
                <input type="hidden" name="action" value="auto-create-product-hotmart">

                <!-- Input Hidden para armazenar os valores selecionados -->
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

                <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small" id="btnCad">Cadastrar Produtos</button>
            </div>
        </div>
    </div>

</form>

<?php
// Nome da tabela dos produtos hotmart
$tabela = 'tb_hotmart_products';

// Definir o número de produtos por página
$produtosPorPagina = 50;

// Verificar a página atual
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Verificar se há uma busca realizada
$search        = $_GET['search']         ?? '';
$productType   = $_GET['type']   ?? '';
$lang      = $_GET['lang']       ?? '';
$approvalType  = $_GET['approval_type']  ?? '';

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "p.title LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($productType)) {
    $where[] = "p.type = :type";
    $params[':type'] = $productType;
}

if (!empty($lang)) {
    $where[] = "p.lang = :lang";
    $params[':lang'] = $lang;
}

if (!empty($approvalType)) {
    $where[] = "p.approval_type = :approval_type";
    $params[':approval_type'] = $approvalType;
}

$whereSQL = '';
if (!empty($where)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

$sql = "
    SELECT p.*,
        CASE 
            WHEN l.id IS NOT NULL THEN 1 
            ELSE 0 
        END AS has_link
    FROM $tabela p
    LEFT JOIN tb_hotmart_links l 
        ON l.product_id = p.id 
        AND l.seller_id = :user_id
    $whereSQL
    ORDER BY has_link DESC, 
             CAST(REPLACE(p.temperature, '°', '') AS UNSIGNED) DESC, 
             p.id ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn_pdo->prepare($sql);

$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':limit', $produtosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

// Executar a consulta
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlTotal = "SELECT COUNT(*) FROM $tabela p $whereSQL";
$totalProdutosStmt = $conn_pdo->prepare($sqlTotal);

foreach ($params as $key => $value) {
    $totalProdutosStmt->bindValue($key, $value);
}

$totalProdutosStmt->execute();
$totalProdutos = $totalProdutosStmt->fetchColumn();

$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

if ($produtos) {
?>
<div class="row g-3" id="hotmartProductsCarousel">
    <?php
    // Loop através dos produtos e exibir as informações
    foreach ($produtos as $produto) {
        // Verificar se o produto já está cadastrado
        $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE (product_id = :short_id OR name = :name) AND shop_id = :shop_id AND related = :related";
        $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
        $verificaStmt->bindValue(':short_id', $produto['id']);
        $verificaStmt->bindValue(':name', $produto['title']);
        $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $verificaStmt->bindValue(':related', 'hotmart');
        $verificaStmt->execute();
        $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

        // NOVA BUSCA: Verifica na tabela tb_hotmart_sellers usando o id do produto e o $_SESSION['user_id']
        $sqlSeller = "SELECT * FROM tb_hotmart_links WHERE product_id = :product_id AND seller_id = :user_id";
        $sellerStmt = $conn_pdo->prepare($sqlSeller);
        // É importante definir se o ID a ser buscado é o 'id' ou 'short_id'; neste exemplo usamos 'id'
        $sellerStmt->bindValue(':product_id', $produto['id']);
        $sellerStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $sellerStmt->execute();
        $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

        // Formatar preço e comissão
        $price = $produto['price'];
        $commission = $produto['commission'];

        $produto['link'] = $produto['product_url'];

        $produto['product_no_img'] = INCLUDE_PATH_DASHBOARD . "back-end/imagens/no-image.jpg";
        $produto['product_img'] = (!empty($produto['image_url'])) ? $produto['image_url'] : $produto['product_no_img'];

    // marca se já está cadastrado
    $isRegistered = $produtoCadastrado ? 1 : 0;
    ?>
        <div class="d-grid col-3">
            <div class="card p-0">
                <div class="product-image">
                    <img src="<?= $produto['product_img']; ?>" class="card-img-top" alt="<?= $produto['title']; ?>">
                </div>
                <div class="card-body">
                    <small class="d-flex align-items-center mb-2 fw-semibold">
                        <?= $produto['temperature']; ?>
                        <i class="bx bxs-hot text-danger"></i>
                    </small>

                    <!-- Exibe alerta se o produto estiver disponível para cadastro automático -->
                    <?php if ($seller) { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-info-emphasis bg-info-subtle border border-info-subtle rounded-1">
                            Cadastro automático ativo!
                        </small>
                    <?php } else { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-light-emphasis bg-light-subtle border border-light-subtle rounded-1">
                            Disponível para cadastro sem link
                        </small>
                    <?php } ?>
                    
                    <!-- Exibir aviso amarelo se o produto já estiver cadastrado -->
                    <?php if ($produtoCadastrado) { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-1">
                            Produto já cadastrado!
                        </small>
                    <?php } ?>
                    
                    <?php if ($produto['affiliation_status'] == 'Necessita aprovação' || $produto['affiliation_status'] == 'Necessita autorização') { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-1">
                            <?= $produto['affiliation_status']; ?>
                        </small>
                    <?php } ?>
                    
                    <?php if ($produto['affiliation_status'] == 'Não necessita aprovação') { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-success-emphasis bg-success-subtle border border-success-subtle rounded-1">
                            <?= $produto['affiliation_status']; ?>
                        </small>
                    <?php } ?>

                    <p class="card-title mb-3"><?= $produto['title']; ?></p>

                    <h5 class="card-text mb-0"><?= $commission; ?></h5>
                    <small class="fw-semibold text-body-secondary"><?= $price; ?></small>
                    <div class="buttons d-flex mt-4">
                        <label 
                            class="input-group-text me-2" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="<?= !$seller ? 'Este produto está disponível para cadastro automático sem o link.' : 'Produto disponível para cadastro automático.'; ?>"
                            aria-label="<?= !$seller ? 'Este produto está disponível para cadastro automático sem o link.' : 'Produto disponível para cadastro automático.'; ?>"
                        >
                            <input
                                  class="form-check-input mt-0 product-checkbox"
                                  type="checkbox"
                                  value="<?= $produto['id']; ?>"
                                  data-registered="<?= $isRegistered; ?>"
                                  data-has-link="<?= $seller ? 1 : 0; ?>"
                                >
                        </label>
                        <a href="<?= $produto['link']; ?>" target="_blank" class="btn btn-success fw-semibold px-4 py-2 small w-100" 
                            onclick="openAffiliateModal('<?= $produto['id']; ?>')">Se Afiliar</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
</div>

<?php
    function buildQuery(array $params, int $page): string {
        return '?' . http_build_query(array_merge($params, ['page' => $page]));
    }

    $queryParams = [
        'search'         => $search ?? '',
        'type'   => $productType ?? '',
        'lang'       => $lang ?? '',
        'approval_type'  => $approvalType ?? '',
    ];

    // Remove parâmetros vazios
    $queryParams = array_filter($queryParams);
?>

<style>
    .line {
        height: 80%;
        width: 1px;
        background-color: #dedede;
    }
    
    .active>.page-link, .page-link.active {
        background-color: var(--green-color) !important;
        border-color: var(--green-color) !important;
    }
</style>

<!-- Paginação -->
<nav aria-label="Page navigation example" class="d-flex align-items-center justify-content-end mb-5">
    
    <form method="get" class="form-inline d-flex align-items-center justify-content-end w-25">
        <label for="goto_page" class="text-end me-3 mb-0">Ir para página:</label>
        <div class="input-group" style="align-items: center; width: max-content;">
            <input
                type="number"
                id="goto_page"
                name="page"
                class="form-control"
                style="width: 70px; max-width: 70px;"
                min="1"
                max="<?= $totalPaginas; ?>"
                value="<?= $paginaAtual; ?>"
            >

            <?php foreach ($queryParams as $key => $value): ?>
                <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>

            <button type="submit" class="btn btn-success ml-2">Ir</button>
        </div>
    </form>
    
    <div class="line ms-3 me-3"></div>
    
    <ul class="pagination justify-content-end align-items-center mb-0">
        <!-- Números das páginas -->
        <?php
        if ($totalPaginas > 1) {
            $intervalo  = 2; // Número de páginas antes e depois da página atual
            $inicio     = max(1, $paginaAtual - $intervalo);
            $fim        = min($totalPaginas, $paginaAtual + $intervalo);

            // Primeira página
            if ($inicio > 1) {
                echo '<li class="page-item"><a class="page-link" href="' . buildQuery($queryParams, 1) . '">1</a></li>';
                if ($inicio > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Páginas antes e depois da atual
            for ($i = $inicio; $i <= $fim; $i++) {
                $url = buildQuery($queryParams, $i);

                if ($i == $paginaAtual) {
                    echo '<li class="page-item active"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
                }
            }

            // Última página
            if ($fim < $totalPaginas) {
                if ($fim < $totalPaginas - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="' . buildQuery($queryParams, $totalPaginas) . '">' . $totalPaginas . '</a></li>';
            }
        }
        ?>
    </ul>
</nav>

<?php
    } else {
        echo "<p>Nenhum produto disponível no momento.</p>";
    }
?>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    function updateUI() {
        const selected = checkboxes.filter(cb => cb.checked).map(cb => cb.value);
        hiddenInput.value = selected.join(',');

        const hasSelected = selected.length > 0;
        cadProducts.classList.toggle('d-none', !hasSelected);
        btnCad.disabled = !hasSelected;
    }

    const selectAll  = document.getElementById('select-all');
    const checkboxes = [...document.querySelectorAll('.product-checkbox')];
    const cadProducts  = document.getElementById('cadProducts');
    const btnCad       = document.getElementById('btnCad');
    const hiddenInput  = document.getElementById('selectedProducts');
    const form         = document.getElementById('productForm');

    // Clique individual em um produto
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            cb.closest('.card')?.classList.toggle('selected', cb.checked);

            // Atualiza estado geral
            updateUI();

            // Atualiza estado do "Selecionar todos"
            const eligible = checkboxes.filter(
                x => x.dataset.registered === '0'
            );

            selectAll.checked =
                eligible.length > 0 &&
                eligible.every(x => x.checked);
        });
    });

    function toggleCard(cb, checked) {
        cb.checked = checked;
        cb.closest('.card')?.classList.toggle('selected', checked);
    }

    selectAll.addEventListener('change', function () {
    if (!this.checked) {
        checkboxes.forEach(cb => toggleCard(cb, false));
        updateUI();
        return;
    }

    // Produtos ainda não cadastrados
    const notRegistered = checkboxes.filter(
        cb => cb.dataset.registered === '0'
    );

    // Prioridade 1 → com link
    const withLink = notRegistered.filter(
        cb => cb.dataset.hasLink === '1'
    );

    // Prioridade 2 → sem link
    const withoutLink = notRegistered.filter(
        cb => cb.dataset.hasLink === '0'
    );

    selectAll.addEventListener('change', () => {
        const hasLink = checkboxes.some(
            cb => cb.dataset.hasLink === '1' && cb.dataset.registered === '0'
        );

        document.querySelector('h5').innerText = hasLink
            ? 'Selecionar produtos com link'
            : 'Selecionar produtos sem link';
    });

    if (withLink.length > 0) {
        // Primeiro ciclo
        withLink.forEach(cb => toggleCard(cb, true));
    } else {
        // Segundo ciclo
        withoutLink.forEach(cb => toggleCard(cb, true));
    }

    updateUI();
    });

    // Valida antes de enviar
    form.addEventListener('submit', function (e) {
        if (hiddenInput.value === '') {
        e.preventDefault();
            alert('Selecione pelo menos um produto para cadastro automático.');
        }
    });

    // Abrir modal e definir o ID do produto
    function openAffiliateModal(productId) {
        // Definir o ID do produto no campo hidden
        document.getElementById('productId').value = productId;
        // Limpar o campo de link de afiliado
        document.getElementById('affiliateLink').value = '';
        // Exibir o modal
        var affiliateModal = new bootstrap.Modal(document.getElementById('affiliateModal'));
        affiliateModal.show();
    }

    // Enviar o formulário com o link de afiliado
    document.getElementById('affiliateForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Evitar o envio padrão do formulário
        
        // Capturar os dados
        var productId = document.getElementById('productId').value;
        var affiliateLink = document.getElementById('affiliateLink').value;

        // Redirecionar o usuário para a página criar-produto com os parâmetros
        var url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + affiliateLink + "&product=hotmart&product_id=" + productId;
        window.location.href = url;
    });
</script>