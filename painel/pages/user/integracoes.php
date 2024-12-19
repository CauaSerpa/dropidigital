<style>
    .card-img-top {
        height: 220px;
        object-fit: cover;
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
        $sqlVerificaProduto = "SELECT COUNT(*) FROM tb_products WHERE product_id = :short_id AND shop_id = :shop_id";
        $verificaStmt = $conn_pdo->prepare($sqlVerificaProduto);
        $verificaStmt->bindValue(':short_id', $produto['short_id']);
        $verificaStmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $verificaStmt->execute();
        $produtoCadastrado = $verificaStmt->fetchColumn() > 0;

        // Formatar preço e comissão
        $price = "R$ " . number_format($produto['price'], 2, ",", ".");
        $commission = "R$ " . number_format($produto['commission'], 2, ",", ".");

        $produto['link'] = "https://dashboard.kiwify.com.br/marketplace?product=" . $produto['short_id'];

        $produto['product_no_img'] = INCLUDE_PATH_DASHBOARD . "back-end/imagens/no-image.jpg";
        $produto['product_img'] = (!empty($produto['product_img'])) ? $produto['product_img'] : $produto['product_no_img'];
    ?>
        <div class="d-grid col-3">
            <div class="card p-0">
                <div class="product-image">
                    <img src="<?= $produto['product_img']; ?>" class="card-img-top" alt="<?= $produto['name']; ?>">
                </div>
                <div class="card-body">
                    <!-- Exibir aviso amarelo se o produto já estiver cadastrado -->
                    <?php if ($produtoCadastrado) { ?>
                        <small class="d-inline-flex mb-2 px-2 py-0 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-1">
                            Produto já cadastrado!
                        </small>
                    <?php } ?>

                    <p class="card-title mb-3"><?= $produto['name']; ?></p>

                    <small class="fw-semibold text-body-secondary">Receba até</small>
                    <h5 class="card-text mb-0"><?= $commission; ?></h5>
                    <small class="fw-semibold text-body-secondary">Preço máximo do produto: <?= $price; ?></small>
                    <div class="buttons d-flex mt-4">
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
                    echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . $search . '">1</a></li>';
                    if ($inicio > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Páginas antes e depois da atual
                for ($i = $inicio; $i <= $fim; $i++) {
                    if ($i == $paginaAtual) {
                        echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '&search=' . $search . '">' . $i . '</a></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&search=' . $search . '">' . $i . '</a></li>';
                    }
                }

                // Última página
                if ($fim < $totalPaginas) {
                    if ($fim < $totalPaginas - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPaginas . '&search=' . $search . '">' . $totalPaginas . '</a></li>';
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
        var url = "<?= INCLUDE_PATH_DASHBOARD; ?>criar-produto?link=" + affiliateLink + "&product=kiwify&product_id=" + productId;
        window.location.href = url;
    });
</script>