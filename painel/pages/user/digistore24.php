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
            <label for="productName" class="form-label">Nome do Produto para pesquisa</label>
            <div class="input-group">
              <input type="text" class="form-control" id="productName" disabled readonly>
              <button type="button" class="btn btn-outline-secondary" id="copyProductName">Copiar</button>
            </div>
            <small class="text-muted">Por favor, copie o produto e pesquise no site da digistore24.</small>
          </div>
          
          <a href="https://www.digistore24-app.com/app/pt/vendor/account/marketplace/all" 
            target="_blank" 
            class="btn btn-primary">Pesquisar Produto</a>
          <hr>
          <div class="mb-3">
            <label for="affiliateLink" class="form-label">Link de Afiliado</label>
            <input type="url" class="form-control" id="affiliateLink" placeholder="Insira o link de afiliado" required>
            <input type="hidden" id="productId">
          </div>
          <button type="submit" class="btn btn-success">Criar Produto</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Produtos Digistore24</h2>
    </div>
</div>

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

<form id="productForm" action="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/auto_create_product_digistore.php" method="POST">

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
                <input type="hidden" name="action" value="auto-create-product-digistore">

                <!-- Input Hidden para armazenar os valores selecionados -->
                <input type="hidden" name="selectedProducts" id="selectedProducts">

                <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small" id="btnCad">Cadastrar Produtos</button>
            </div>
        </div>
    </div>

</form>

<?php
// Nome da tabela dos produtos Digistore24
$tabela = 'tb_digistore24_products';

// // Definir o número de produtos por página
// $produtosPorPagina = 50;
$validPerPage = [10, 50, 100, 500, 1000];
$produtosPorPagina = isset($_GET['perPage']) && in_array((int)$_GET['perPage'], [10,50,100,500,1000])
    ? (int)$_GET['perPage']
    : 50;

// Verificar a página atual
$paginaAtual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($paginaAtual - 1) * $produtosPorPagina;

// Verificar se há uma busca realizada e filtrar por tipo
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modificar a consulta SQL para buscar produtos pelo nome e tipo
$whereClause = "1=1"; // Filtro inicial sem condições
if ($search) {
    $whereClause .= " AND description LIKE :search";
}

$sql = "SELECT * FROM $tabela WHERE $whereClause AND label != '' AND description != '' LIMIT :limit OFFSET :offset";
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
<div class="row">
    <?php
    foreach ($produtos as $produto) {
        // Verificar se o produto já está cadastrado
        $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE product_id = :id OR name = :name AND shop_id = :shop_id";
        $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
        $verificaStmt->bindValue(':id', $produto['productId']);
        $verificaStmt->bindValue(':name', $produto['description']);
        $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $verificaStmt->execute();
        $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

        // Calcular o valor da comissão
        $commissionPercentage = ($produto['commission'] / 100) * $produto['price'];

        // Garantir apenas duas casas decimais
        $commissionPercentage = round($commissionPercentage, 2);

        // Formatar preço e comissão
        $price = "$ " . number_format($produto['price'], 2, ",", ".");
        $commission = "$ " . number_format($commissionPercentage, 2, ",", ".");

        $produto['product_no_img'] = INCLUDE_PATH_DASHBOARD . "back-end/imagens/no-image.jpg";
        $produto['product_img'] = "https://www.digistore24.com/" . $produto['imageUrl'];
        $produto['product_img'] = (!empty($produto['imageUrl'])) ? $produto['product_img'] : $produto['product_no_img'];

    // marca se já está cadastrado
    $isRegistered = $produtoCadastrado ? 1 : 0;

        // Formatar preço e comissão
    ?>
        <div class="col-md-12 mb-3">
            <div class="card product-card p-0">
                <div class="card-body row p-0">
                    <div class="col-md-2 py-5 ps-5">
                        <div class="product-image">
                            <img src="<?= $produto['product_img']; ?>" class="card-img-top rounded-1" alt="Imagem do Produto">
                        </div>
                    </div>
                    <div class="col-md-7 py-5 pe-5">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title mb-3">
                                    <?= strip_tags($produto['label']); ?>
                                </h5>
                            </div>
                            <p>Vendedor: <strong><?= $produto['vendorName']; ?></strong></p>
                        </div>

                        <div class="description">
                            <!-- Descrição do produto com limite de caracteres -->
                            <p class="card-text">
                                <?php 
                                    $descricaoCompleta = strip_tags($produto['description']);
                                    $descricaoLimitada = mb_strimwidth($descricaoCompleta, 0, 300, "...");
                                ?>
                                <span class="short-text"><?= $descricaoLimitada; ?></span>
                                <span class="full-text d-none"><?= $descricaoCompleta; ?></span>
                            </p>
                            
                            <!-- Botão para expandir a descrição -->
                            <a href="#" class="showMore">Mostrar mais</a>
                        </div>
                    </div>
                    <div class="col-md-3 p-5 bg-secondary-subtle border border-1 rounded-end text-center">
                        <p>Renda líquida/venda</p>
                        <h3 class="fw-semibold mb-0"><?php echo $commission; ?></h3>
                        <small class="fw-semibold text-body-secondary">Preço do produto: <?php echo $price; ?></small>
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
                                      data-registered="<?= $isRegistered; ?>"
                                    >
                            </label>
                            <a href="#" 
                                class="btn btn-success fw-semibold px-4 py-2 small promote-product" 
                                data-product-id="<?= $produto['id']; ?>" 
                                data-product-name="<?= strip_tags($produto['label']); ?>" 
                                rel="noreferrer">
                                Promover
                            </a>
                        </div>
                        <p class="small text-muted"><b>OBS:</b> Os valores podem sofrer alterações devido à cotação do dólar.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
</div>

<script>
    $(document).ready(function(){
        $(".showMore").click(function(e){
            e.preventDefault();
            var card = $(this).closest('.description');
            card.find(".short-text").toggleClass("d-none");
            card.find(".full-text").toggleClass("d-none");

            // Alterar o texto do botão
            if ($(this).text() === "Mostrar mais") {
                $(this).text("Mostrar menos");
            } else {
                $(this).text("Mostrar mais");
            }
        });
    });
</script>

<!-- Paginação -->
    <div class="row align-items-center">
        <div class="col-md-6">
            <!-- Novo Select: Itens por página -->
            <form method="GET" action="" id="perPageForm" class="d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type); ?>">
                    <!-- Ao trocar perPage, sempre volta para página 1 -->
                    <input type="hidden" name="page" value="1">
                    <label for="perPageSelect" class="me-2 mb-0">Produtos por página:</label>
                    <select name="perPage" id="perPageSelect" class="form-select" style="width: auto;">
                        <?php foreach ($validPerPage as $opt): ?>
                            <option value="<?= $opt; ?>" <?= $produtosPorPagina === $opt ? 'selected' : ''; ?>>
                                <?= $opt; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button tyle="submit" class="btn btn-success ms-2">Alterar</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-end mb-0">
                    <?php
                    if ($totalPaginas > 1) {
                        $intervalo = 2;
                        $inicio = max(1, $paginaAtual - $intervalo);
                        $fim    = min($totalPaginas, $paginaAtual + $intervalo);
            
                        // Link para a primeira página, se fora do intervalo
                        if ($inicio > 1) {
                            echo '<li class="page-item">
                                    <a class="page-link" href="?page=1'
                                      . '&search=' . urlencode($search)
                                      . '&type=' . urlencode($type)
                                      . '&perPage=' . $produtosPorPagina
                                      . '">1</a>
                                  </li>';
                            if ($inicio > 2) {
                                echo '<li class="page-item disabled">
                                        <span class="page-link">...</span>
                                      </li>';
                            }
                        }
            
                        // Páginas ao redor da atual
                        for ($i = $inicio; $i <= $fim; $i++) {
                            $active = ($i === $paginaAtual) ? ' active' : '';
                            echo '<li class="page-item' . $active . '">
                                    <a class="page-link" href="?page=' . $i
                                      . '&search=' . urlencode($search)
                                      . '&type=' . urlencode($type)
                                      . '&perPage=' . $produtosPorPagina
                                      . '">' . $i . '</a>
                                  </li>';
                        }
            
                        // Link para a última página, se fora do intervalo
                        if ($fim < $totalPaginas) {
                            if ($fim < $totalPaginas - 1) {
                                echo '<li class="page-item disabled">
                                        <span class="page-link">...</span>
                                      </li>';
                            }
                            echo '<li class="page-item">
                                    <a class="page-link" href="?page=' . $totalPaginas
                                      . '&search=' . urlencode($search)
                                      . '&type=' . urlencode($type)
                                      . '&perPage=' . $produtosPorPagina
                                      . '">' . $totalPaginas . '</a>
                                  </li>';
                        }
                    }
                    ?>
                </ul>
            </nav>

        </div>
    </div>
</div>

<?php
    }
?>

<script>
    const selectAll    = document.getElementById('select-all');
    const checkboxes   = document.querySelectorAll('.product-checkbox');
    const cadProducts  = document.getElementById('cadProducts');
    const btnCad       = document.getElementById('btnCad');
    const hiddenInput  = document.getElementById('selectedProducts');
    const productForm  = document.getElementById('productForm');

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
      cb.addEventListener('change', function () {
        this.closest('.card').classList.toggle('selected', this.checked);

        // Atualiza “Selecionar Todos”: só considera os habilitados e não-registrados
        const eligible = [...checkboxes].filter(x => !x.disabled && x.dataset.registered === '0');
        selectAll.checked = eligible.length > 0 && eligible.every(x => x.checked);

        updateUI();
      });
    });

    // Valida antes de enviar o formulário principal de produtos
    productForm.addEventListener('submit', function (e) {
      if (hiddenInput.value === '') {
        e.preventDefault();
        alert('Selecione pelo menos um produto para cadastro automático.');
      }
    });

  document.addEventListener("DOMContentLoaded", function() {
    // Abrir modal ao clicar no botão "Promover"
    document.querySelectorAll('.promote-product').forEach(button => {
      button.addEventListener('click', function(event) {
        event.preventDefault();
        
        let productId = this.getAttribute('data-product-id');
        let productName = this.getAttribute('data-product-name');

        // Definir os valores no modal
        document.getElementById('productId').value = productId;
        document.getElementById('productName').value = productName;

        // Exibir o modal
        let affiliateModal = new bootstrap.Modal(document.getElementById('affiliateModal'));
        affiliateModal.show();
      });
    });

    // 1) Abrir modal ao clicar no botão "Promover"
    document.querySelectorAll('.promote-product').forEach(button => {
      button.addEventListener('click', function(event) {
        event.preventDefault();

        let productId   = this.getAttribute('data-product-id');
        let productName = this.getAttribute('data-product-name');

        // Define os valores nos campos hidden do modal
        document.getElementById('productId').value   = productId;
        document.getElementById('productName').value = productName;

        // Exibe o modal de afiliado
        let affiliateModal = new bootstrap.Modal(document.getElementById('affiliateModal'));
        affiliateModal.show();
      });
    });

    // 2) Copiar nome do produto para a área de transferência
    document.getElementById('copyProductName').addEventListener('click', function() {
      let button = this;
      let input  = document.getElementById('productName');

      navigator.clipboard.writeText(input.value).then(() => {
        button.textContent = "Copiado!";
        setTimeout(() => {
          button.textContent = "Copiar";
        }, 2000);
      }).catch(err => {
        console.error("Erro ao copiar: ", err);
      });
    });

    // 3) Enviar o formulário de afiliado para redirecionar ao criar-produto (Digistore24)
    document.getElementById('affiliateForm').addEventListener('submit', function(event) {
      event.preventDefault();

      let productId     = document.getElementById('productId').value;
      let affiliateLink = document.getElementById('affiliateLink').value;

      // Redireciona para a página de criação do produto, passando link e ID
      let url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" 
                + encodeURIComponent(affiliateLink) 
                + "&product=digistore24&product_id=" 
                + productId;
      window.location.href = url;
    });
  });
</script>