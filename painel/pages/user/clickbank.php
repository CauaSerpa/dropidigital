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
        <h2 class="title">Produtos ClickBank</h2>
    </div>
</div>

<?php $search = isset($_GET['search']) ? $_GET['search'] : ''; ?>

<!-- Abas de Produtos -->
<ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <a class="nav-link <?= !isset($_GET['type']) || $_GET['type'] == 'all' ? 'active' : ''; ?>" 
       href="?type=all&search=<?= urlencode($search) ?>">Todos</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link <?= isset($_GET['type']) && $_GET['type'] == 'physical' ? 'active' : ''; ?>" 
       href="?type=physical&search=<?= urlencode($search) ?>">Produtos Físicos</a>
  </li>
  <li class="nav-item" role="presentation">
    <a class="nav-link <?= isset($_GET['type']) && $_GET['type'] == 'digital' ? 'active' : ''; ?>" 
       href="?type=digital&search=<?= urlencode($search) ?>">Produtos Digitais</a>
  </li>
</ul>

<!-- Campo de busca -->
<div class="card mb-3 p-0">
    <div class="card-body row px-4 py-3">
        <div class="search-bar">
            <form method="GET" action="" class="d-flex">
                <input type="hidden" name="type" value="<?= isset($_GET['type']) ? $_GET['type'] : 'physical'; ?>">
                <input type="text" name="search" placeholder="Buscar produtos..." class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="btn btn-secondary ms-2">Buscar</button>
            </form>
        </div>
    </div>
</div>

<?php
// Nome da tabela dos produtos Kiwify
$tabela = 'tb_clickbank_products';

// Definir o número de produtos por página
$produtosPorPagina = 50;

// Verificar a página atual
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Verificar se há uma busca realizada e filtrar por tipo
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'physical';

// Modificar a consulta SQL para buscar produtos pelo nome e tipo
$whereClause = "1=1"; // Filtro inicial sem condições
if ($search) {
    $whereClause .= " AND title LIKE :search";
}
if ($type === 'physical') {
    $whereClause .= " AND type = 'physical'";
} elseif ($type === 'digital') {
    $whereClause .= " AND type = 'digital'";
} elseif ($type === 'all') {
    $whereClause .= " AND type IN ('physical', 'digital')";
}

$sql = "SELECT * FROM $tabela WHERE $whereClause ORDER BY gravity DESC LIMIT :limit OFFSET :offset";
$stmt = $conn_pdo->prepare($sql);

if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':limit', $produtosPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Recuperar os produtos
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular o número total de produtos (para paginação)
$sqlTotal = "SELECT COUNT(*) FROM $tabela WHERE $whereClause";
$totalProdutosStmt = $conn_pdo->prepare($sqlTotal);
if ($search) {
    $totalProdutosStmt->bindValue(':search', '%' . $search . '%');
}
$totalProdutosStmt->execute();
$totalProdutos = $totalProdutosStmt->fetchColumn();

// Calcular o número total de páginas
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

if ($produtos) {
?>

<!-- FORMULÁRIO para cadastro automático -->
<form id="productForm" action="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/auto_create_product_clickbank.php" method="POST">
<div class="card mb-2 p-0">
  <div class="card-body row px-4 py-3">
    <label class="d-flex align-items-center">
      <div class="me-3 form-check">
        <input type="checkbox" class="form-check-input mb-0" id="select-all">
      </div>
      <h5 class="mb-0">Selecionar Todos os Produtos</h5>
    </label>
    <div class="mt-2 d-none" id="cadProducts">
      <input type="hidden" name="shop_id" value="<?php echo $_SESSION['shop_id']; ?>">
      <input type="hidden" name="action" value="auto-create-product-clickbank">
      <input type="hidden" name="selectedProducts" id="selectedProducts">
      <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small" id="btnCad" disabled>
        Cadastrar Produtos
      </button>
    </div>
  </div>
</div>

<div class="row">
    <?php
    foreach ($produtos as $produto) {
        // Verificar se o produto já está cadastrado
        $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE product_id = :id OR name = :name AND shop_id = :shop_id";
        $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
        $verificaStmt->bindValue(':id', $produto['id']);
        $verificaStmt->bindValue(':name', $produto['title']);
        $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $verificaStmt->execute();
        $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

        // Formatar preço e comissão
    ?>
        <div class="col-md-12 mb-3">
            <div class="card product-card p-0">
                <div class="card-body row p-0">
                    <div class="col-md-9 p-5">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title"><?php echo $produto['title']; ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo $produto['category']; ?></h6>
                            </div>
                            <p>Pontuação: <strong><?php echo $produto['gravity']; ?></strong></p>
                        </div>
                        <p class="card-text">
                            <?php echo str_replace(' Read more', '...', $produto['description']); ?>
                        </p>
                    </div>
                    <div class="col-md-3 p-5 bg-secondary-subtle border border-1 rounded-end text-center">
                        <p>Média de $/conversão:</p>
                        <h3 class="fw-semibold mb-3"><?php echo $produto['price']; ?></h3>
                        <div class="buttons d-flex justify-content-center mt-3 mb-3">
                            <label 
                                class="input-group-text me-2" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Produto disponível para cadastro automático."
                                aria-label="Produto disponível para cadastro automático."
                            >
                                <input
                                      class="form-check-input mt-0 product-checkbox"
                                      type="checkbox"
                                      value="<?= $produto['id']; ?>"
                                      data-registered="<?= $produtoCadastrado; ?>"
                                    >
                            </label>
                            <a href="https://accounts.clickbank.com/master/dashboard/affiliate-marketplace?v=0.6305556826474028#/results?includeKeywords=<?php echo str_replace(' ', '+', preg_replace('/[-+!\[\]]/', '', $produto['title'])); ?>&sortField=relevance" 
                                class="btn btn-success fw-semibold px-4 py-2 small promote-product" target="_blank"  
                                onclick="openAffiliateModal('<?= $produto['id']; ?>')" rel="noreferrer">Promover</a>
                        </div>
                        
                        <!-- Exibir aviso amarelo se o produto já estiver cadastrado -->
                        <?php if ($produtoCadastrado) { ?>
                            <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-1">
                                Produto já cadastrado!
                            </small>
                        <?php } ?>
                        
                    </div>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
</div>

<!-- Paginação -->
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-end">
        <!-- Números das páginas -->
        <?php
            // Lógica para a exibição das páginas
            if ($totalPaginas > 1) {
                $intervalo = 2; // Número de páginas antes e depois da página atual
                $inicio = max(1, $paginaAtual - $intervalo);
                $fim = min($totalPaginas, $paginaAtual + $intervalo);

                // Primeira página
                if ($inicio > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1&type=' . $type . '&search=' . $search . '">1</a></li>';
                    if ($inicio > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Páginas antes e depois da atual
                for ($i = $inicio; $i <= $fim; $i++) {
                    if ($i == $paginaAtual) {
                        echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '&type=' . $type . '&search=' . $search . '">' . $i . '</a></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&type=' . $type . '&search=' . $search . '">' . $i . '</a></li>';
                    }
                }

                // Última página
                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPaginas . '&type=' . $type . '&search=' . $search . '">' . $totalPaginas . '</a></li>';
                }
            }
            ?>
    </ul>
</nav>

<?php
    }
?>

<script>
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
        var url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + affiliateLink + "&product=clickbank&product_id=" + productId;
        window.location.href = url;
    });
</script>

<script>
  // Elementos
  const selectAll    = document.getElementById('select-all');
  const checkboxes   = document.querySelectorAll('.product-checkbox');
  const cadProducts  = document.getElementById('cadProducts');
  const btnCad       = document.getElementById('btnCad');
  const hiddenInput  = document.getElementById('selectedProducts');
  const productForm  = document.getElementById('productForm');

  function updateUI() {
    const selected = [...checkboxes]
      .filter(cb => cb.checked)
      .map(cb => cb.value)
      .join(',');
    hiddenInput.value = selected;
    const any = selected.length > 0;
    cadProducts.classList.toggle('d-none', !any);
    btnCad.disabled = !any;
  }

    // "Selecionar Todos": só marca checkboxes habilitados e não-registrados
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
    cb.addEventListener('change', () => {
      updateUI();
      // atualiza o estado do “select all”
      const eligible = [...checkboxes].filter(x => x.dataset.registered === '0');
      selectAll.checked = eligible.length && eligible.every(x => x.checked);
    });
  });

  // Validação antes de enviar
  productForm.addEventListener('submit', e => {
    if (!hiddenInput.value) {
      e.preventDefault();
      alert('Selecione pelo menos um produto para cadastro automático.');
    }
  });

  // Lógica do modal (igual ao seu existente)
  document.querySelectorAll('.promote-product').forEach(button => {
    button.addEventListener('click', e => {
      e.preventDefault();
      document.getElementById('productId').value = button.dataset.productId;
      document.getElementById('affiliateLink').value = '';
      new bootstrap.Modal(document.getElementById('affiliateModal')).show();
    });
  });

  document.getElementById('affiliateForm').addEventListener('submit', e => {
    e.preventDefault();
    const id   = document.getElementById('productId').value;
    const link = document.getElementById('affiliateLink').value;
    window.location.href = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + 
                           encodeURIComponent(link) + 
                           "&product=clickbank&product_id=" + id;
  });
</script>