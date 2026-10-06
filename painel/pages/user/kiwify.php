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
        <h2 class="title">Produtos Kiwify</h2>
    </div>
</div>

<!-- Campo de busca -->
<div class="card mb-3 p-0">
    <div class="card-body row px-4 py-3">
        <div class="search-bar">
            <form method="GET" action="" class="d-flex">
                <input type="text" name="search" placeholder="Buscar produtos..." class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="btn btn-secondary ms-2">Buscar</button>
            </form>
        </div>
    </div>
</div>

<form id="productForm" action="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/auto_create_product.php" method="POST">

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
                <input type="hidden" name="action" value="auto-create-product-kiwify">

                <!-- Input Hidden para armazenar os valores selecionados -->
                <input type="hidden" name="selectedProducts" id="selectedProducts">

                <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small" id="btnCad">Cadastrar Produtos</button>
            </div>
        </div>
    </div>

</form>

<?php
// Nome da tabela dos produtos Kiwify
$tabela = 'tb_kiwify_products';

// Definir o número de produtos por página
$produtosPorPagina = 50;

// Verificar a página atual
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Verificar se há uma busca realizada
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modificar a consulta SQL para buscar produtos pelo nome e limitar a quantidade de resultados
if ($search) {
    $sql = "SELECT * FROM $tabela WHERE name LIKE :search ORDER BY id ASC LIMIT :limit OFFSET :offset";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%');
} else {
    $sql = "SELECT * FROM $tabela ORDER BY id ASC LIMIT :limit OFFSET :offset";
    $stmt = $conn_pdo->prepare($sql);
}

// Limitar e deslocar os resultados para paginação
$stmt->bindValue(':limit', $produtosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Recuperar os produtos
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular o número total de produtos (para paginação)
$sqlTotal = "SELECT COUNT(*) FROM $tabela";
$totalProdutosStmt = $conn_pdo->prepare($sqlTotal);
$totalProdutosStmt->execute();
$totalProdutos = $totalProdutosStmt->fetchColumn();

// Calcular o número total de páginas
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

if ($produtos) {
?>
<div class="row g-3" id="kiwifyProductsCarousel">
    <?php
    // Loop através dos produtos e exibir as informações
    foreach ($produtos as $produto) {
        // Verificar se o produto já está cadastrado
        $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE (product_id = :short_id OR name = :name) AND shop_id = :shop_id";
        $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
        $verificaStmt->bindValue(':short_id', $produto['short_id']);
        $verificaStmt->bindValue(':name', $produto['name']);
        $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $verificaStmt->execute();
        $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

        // NOVA BUSCA: Verifica na tabela tb_kiwify_sellers usando o id do produto e o $_SESSION['user_id']
        $sqlSeller = "SELECT * FROM tb_kiwify_sellers WHERE product_id = :product_id AND seller_id = :user_id";
        $sellerStmt = $conn_pdo->prepare($sqlSeller);
        // É importante definir se o ID a ser buscado é o 'id' ou 'short_id'; neste exemplo usamos 'id'
        $sellerStmt->bindValue(':product_id', $produto['short_id']);
        $sellerStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $sellerStmt->execute();
        $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);

        // Formatar preço e comissão
        $price = "R$ " . number_format($produto['price'], 2, ",", ".");
        $commission = "R$ " . number_format($produto['commission'], 2, ",", ".");

        $produto['link'] = "https://dashboard.kiwify.com.br/marketplace?product=" . $produto['short_id'];

        $produto['product_no_img'] = INCLUDE_PATH_DASHBOARD . "back-end/imagens/no-image.jpg";
        $produto['product_img'] = (!empty($produto['product_img'])) ? $produto['product_img'] : $produto['product_no_img'];

    // marca se já está cadastrado
    $isRegistered = $produtoCadastrado ? 1 : 0;
    ?>
        <div class="d-grid col-3">
            <div class="card p-0">
                <div class="product-image">
                    <img src="<?= $produto['product_img']; ?>" class="card-img-top" alt="<?= $produto['name']; ?>">
                </div>
                <div class="card-body">
                    <!-- Exibe alerta se o produto estiver disponível para cadastro automático -->
                    <?php if ($seller) { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-info-emphasis bg-info-subtle border border-info-subtle rounded-1">
                            Cadastro automático ativo!
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

                    <p class="card-title mb-3"><?= $produto['name']; ?></p>

                    <small class="fw-semibold text-body-secondary">Receba até</small>
                    <h5 class="card-text mb-0"><?= $commission; ?></h5>
                    <small class="fw-semibold text-body-secondary">Preço máximo do produto: <?= $price; ?></small>
                    <div class="buttons d-flex mt-4">
                        <label 
                            class="input-group-text me-2" 
                            data-toggle="tooltip" 
                            data-placement="top" 
                            title="<?= !$seller ? 'Este produto ainda não está disponível para cadastro automático.' : 'Produto disponível para cadastro automático.'; ?>"
                            aria-label="<?= !$seller ? 'Este produto ainda não está disponível para cadastro automático.' : 'Produto disponível para cadastro automático.'; ?>"
                        >
                            <input
                                  class="form-check-input mt-0 product-checkbox"
                                  type="checkbox"
                                  value="<?= $produto['id']; ?>"
                                  <?= !$seller ? 'disabled' : ''; ?>
                                  data-registered="<?= $isRegistered; ?>"
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
<nav aria-label="Page navigation example" class="d-flex align-items-center justify-content-end">
    
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
            <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-success ml-2">Ir</button>
        </div>
    </form>
    
    <div class="line ms-3 me-3"></div>
    
    <ul class="pagination justify-content-end align-items-center mb-0">
        <!-- Números das páginas -->
        <?php
        if ($totalPaginas > 1) {
            $intervalo = 2; // Número de páginas antes e depois da página atual
            $inicio     = max(1, $paginaAtual - $intervalo);
            $fim        = min($totalPaginas, $paginaAtual + $intervalo);

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
  const selectAll    = document.getElementById('select-all');
  const checkboxes   = document.querySelectorAll('.product-checkbox');
  const cadProducts  = document.getElementById('cadProducts');
  const btnCad       = document.getElementById('btnCad');
  const hiddenInput  = document.getElementById('selectedProducts');
  const form         = document.getElementById('productForm');

  function updateUI() {
    const selectedValues = [...checkboxes]
      .filter(cb => cb.checked)
      .map(cb => cb.value)
      .join(',');

    hiddenInput.value = selectedValues;
    const atLeastOne = selectedValues.length > 0;
    cadProducts.classList.toggle('d-none', !atLeastOne);
    btnCad.disabled = !atLeastOne;
  }

  // Selecionar Todos: só marca checkboxes habilitados e não-registrados
  selectAll.addEventListener('change', function () {
    checkboxes.forEach(cb => {
      const already = cb.dataset.registered === '1';
      if (!cb.disabled && !already) {
        cb.checked = this.checked;
        cb.closest('.card').classList.toggle('selected', this.checked);
      }
    });
    updateUI();
  });

  // Checkboxes individuais
  checkboxes.forEach(cb => {
    cb.addEventListener('change', function () {
      this.closest('.card').classList.toggle('selected', this.checked);

      // Atualiza “Selecionar Todos”: só considera os habilitados e não-registrados
      const eligible = [...checkboxes].filter(x => !x.disabled && x.dataset.registered === '0');
      selectAll.checked = eligible.length > 0 && eligible.every(x => x.checked);

      updateUI();
    });
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
        var url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + affiliateLink + "&product=kiwify&product_id=" + productId;
        window.location.href = url;
    });
</script>