<?php
        // Nome da tabela para a busca
        $tabela = 'tb_products';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $id);
        $stmt->execute();

        $countProduct = $stmt->rowCount();
?>
<style>
    .card.table
    {
        overflow: hidden;
    }
    .checkbox
    {
        width: 0 !important;
    }
    .form-check-input
    {
        position: relative;
        margin-left: 0;
    }
    .move-icon
    {
        font-size: var(--h3-font-size);
        color: var(--text-color-light);
        vertical-align: middle;
    }
    .sortable tbody tr .glyphicon:hover
    {
        cursor: -webkit-grab;
        cursor: grab;
    }
    .sortable tbody tr .glyphicon:active
    {
        cursor: -webkit-grabbing;
        cursor: grabbing;
    }
    .ui-sortable-handle.ui-sortable-helper
    {
        background-color: #000 !important;
    }
    .table>:not(caption)>*>*
    {
        max-width: 1246px !important;
        padding: 0;
    }
    .table>:not(caption)>*>* th,
    .table>:not(caption)>*>* td
    {
        padding: .75rem;
    }
    table tbody td
    {
        border-top: 1px solid var(--border-color);
    }

    .btn.btn-success
    {
        background: var(--green-color);
        border-color: var(--green-color);
    }

    #searchButton
    {
        height: 30px;
        padding: 0 10px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--card-color);
        background: var(--border-color);
        font-weight: 600;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
    }

    .bullet {
        display: flex;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet.bullet-success {
        background: var(--green-color);
    }
    .bullet.bullet-danger {
        background: red;
    }
</style>

<div class="modal fade" id="import" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/import.php" method="post" enctype="multipart/form-data" id="seu_formulario_id">
                <div class="modal-header px-4 py-3 bg-transparent">
                    <div class="fw-semibold py-2">
                        Importar produtos
                    </div>
                </div>
                <div class="modal-body px-4 py-3">
                    <div>
                        <label for="formFileLg" class="form-label small">Arquivo</label>
                        <input class="form-control" id="formFileLg" type="file" name="arquivo" accept=".xls,.xlsx">
                        <small class="form-text text-muted fw-normal small">Você deve selecionar um arquivo com extensão <span class="fw-semibold">.xlsx</span></small>
                    </div>
                </div>
                <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                <div class="modal-footer fw-semibold px-4">
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="export" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Exportar produtos
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="py-3">
                    <p class="fs-5 fw-semibold">Exportar todos os produtos</p>
                </div>
            </div>
            <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/export.php?id=<?php echo $id; ?>" class="btn btn-success fw-semibold px-4 py-2 small d-flex align-items-center">
                    Baixar planilha
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Produtos</h2>
        <p class="products-counter"><?php echo $countProduct; echo ($countProduct == 0 || $countProduct == 1) ? ' produto' : ' produtos'; ?></p>
    </div>
    <div class="header__actions">
        <label for="import-button" class="import text-black text-decoration-none d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#import" style="cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
            Importar
        </label>
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto" class="button button--flex new text-decoration-none">+ Criar Produto</a>
        </div>
    </div>
</div>

<form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/delete_tables.php" method="post" class="table__actions">
    <div class="card__container grid one tabPanel" style="display: grid;">
        <div class="card__box grid">
            <div class="card table">

        <?php
            if ($stmt->rowCount() > 0) {
        ?>
                <div class="card__title">
                    <div class="title__content grid">
                        <div class="search__container d-flex">
                            <input type="text" name="searchInput" id="searchInput" class="search" placeholder="Pesquisar" title="Pesquisar" value="<?= (!empty($_GET['search'])) ? $_GET['search'] : ""; ?>">
                            <button type="button" id="searchButton" class="btn btn-secondary ms-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"></path></svg>
                            </button>
                        </div>
                        <button type="button" class="filter" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvasExample">
                            Filtrar
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M21 3H5a1 1 0 0 0-1 1v2.59c0 .523.213 1.037.583 1.407L10 13.414V21a1.001 1.001 0 0 0 1.447.895l4-2c.339-.17.553-.516.553-.895v-5.586l5.417-5.417c.37-.37.583-.884.583-1.407V4a1 1 0 0 0-1-1zm-6.707 9.293A.996.996 0 0 0 14 13v5.382l-2 1V13a.996.996 0 0 0-.293-.707L6 6.59V5h14.001l.002 1.583-5.71 5.71z"></path></svg>
                        </button>
                        <button type="submit" class="btn btn-danger delete">
                            Executar Ação
                        </button>
                        <label for="export-button" class="export text-black text-decoration-none" data-bs-toggle="modal" data-bs-target="#export" style="cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
                            Exportar
                        </label>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th class="small">Nome</th>
                            <th class="small">Valor</th>
                            <th class="small">Categoria</th>
                            <th class="small">SKU</th>
                            <th class="small">Status</th>
                            <th class="small">Data de Criação</th>
                            <th class="small">Eventos</th>
                        </tr>
                    </thead>
                    <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_products';

                    // Configuração para paginação
                    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
                    $paginaAtual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                    $inicioConsulta = ($paginaAtual - 1) * $limite;

                    // Preparar a consulta com base na pesquisa (se houver)
                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
                    if (!empty($_GET['search'])) {
                        $searchTerm = '%' . $_GET['search'] . '%';
                        $sql .= " AND (name LIKE :searchTerm OR sku LIKE :searchTerm)"; // Substitua campo1 e campo2 pelos campos que deseja pesquisar
                    }

                    $sql .= " ORDER BY id DESC LIMIT :inicioConsulta, :limite";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $id);
                    if (!empty($_GET['search'])) {
                        $stmt->bindParam(':searchTerm', $searchTerm);
                    }
                    $stmt->bindParam(':inicioConsulta', $inicioConsulta, PDO::PARAM_INT);
                    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                    $stmt->execute();

                    // Recuperar os resultados
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($resultados as $usuario) {
                        //Formatacao preco
                        // $price = str_replace(',', '.', str_replace('.', '', $usuario['price']));
                        $preco = $usuario['price'];

                        // Transforma o número no formato "R$ 149,90"
                        $price = "R$ " . number_format($preco, 2, ",", ".");

                        $price = ($usuario['without_price'] == 1) ? "--" : $price;

                        // SKU
                        $sku = ($usuario['sku'] == "") ? "--" : $usuario['sku'];

                        //Formatacao para data
                        $date_create = date("d/m/Y", strtotime($usuario['date_create']));

                        // Status
                        $isActive = ($usuario['status'] == 1) ? "checked" : "";
                        $status = '<div class="form-check form-switch"><input class="change-status-btn form-check-input" type="checkbox" name="status" role="switch" data-id="' . $usuario['id'] . '" ' . $isActive . '></div>';

                        echo '
                            <tbody>
                                <tr>
                                    <td scope="row">
                                        <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="' . $usuario['id'] . '" id="defaultCheck2">
                                    </td>
                                    <td>
                        ';

                        // Consulta SQL para selecionar todas as colunas com base no ID
                        $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':usuario_id', $usuario['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($imagens) {
                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($imagens as $imagem) {
                                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" alt="Capa do Produto" style="width: 38px; height: 38px; object-fit: cover;">';
                            }
                        } else {
                            echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" alt="Capa do Produto" style="width: 38px; height: 38px; object-fit: cover;">';
                        }
                        
                        echo '
                                        ' . $usuario['name'] . '
                                    </td>
                                    <td>' . $price . '</td>';

                        // Nome da tabela para a busca
                        $tabela = 'tb_product_categories';

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id ORDER BY (main = 1) DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $id);
                        $stmt->bindParam(':product_id', $usuario['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $productsCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Inicia a classe primeiro elemento
                        $primeiroElemento = true;

                        echo "<td style='max-width: 50px;'>";

                        if ($productsCategory) {
                            foreach ($productsCategory as $productCategory) {

                                // Nome da tabela para a busca
                                $tabela = 'tb_categories';

                                $sql = "SELECT (name) FROM $tabela WHERE shop_id = :shop_id AND id = :id ORDER BY id DESC";

                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindParam(':shop_id', $id);
                                $stmt->bindParam(':id', $productCategory['category_id']);
                                $stmt->execute();

                                // Recuperar os resultados
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($categories as $category) {
                                    echo (!$primeiroElemento) ? ", " : "";

                                    echo $category['name'];

                                    $primeiroElemento = false;
                                }
                            }
                        } else {
                            echo "--";
                        }

                        echo "</td>";

                        echo '
                                    <td>' . $sku . '</td>
                                    <td>' . $status . '</td>
                                    <td>' . $date_create . '</td>
                                    <td>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'editar-produto?id=' . $usuario['id'] . '" class="btn btn-primary">
                                            <i class="bx bxs-edit" ></i>
                                        </a>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'excluir-produto?id=' . $usuario['id'] . '" class="btn btn-danger">
                                            <i class="bx bxs-trash" ></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        ';
                    }
                ?>
                </table>
            </div>
            <div class="center">
                <div class="left">
                    <div class="container__button">
                        <div class="limitPageDropdown dropdown button button--flex select">
                            <input type="text" class="text02" value="<?php echo $limite; ?>" readonly>
                            <div class="option">
                                <div class="alterar-produtos-por-pagina <?php echo ($limite == 10) ? "selected" : "" ; ?>" data-value="10">10</div>
                                <div class="alterar-produtos-por-pagina <?php echo ($limite == 20) ? "selected" : "" ; ?>" data-value="20">20</div>
                                <div class="alterar-produtos-por-pagina <?php echo ($limite == 30) ? "selected" : "" ; ?>" data-value="30">30</div>
                                <div class="alterar-produtos-por-pagina <?php echo ($limite == 40) ? "selected" : "" ; ?>" data-value="40">40</div>
                                <div class="alterar-produtos-por-pagina <?php echo ($limite == 50) ? "selected" : "" ; ?>" data-value="50">50</div>
                            </div>
                        </div>
                        <label>Produtos por página</label>
                    </div>
                </div>
                <div class="right grid">
                    <div class="controller">
                        <?php
                            // Nome da tabela para a busca
                            $tabela = 'tb_products';

                            // Lógica para exibição dos links de páginação
                            $sql = "SELECT COUNT(*) as total FROM $tabela WHERE shop_id = :shop_id";
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $id);
                            $stmt->execute();
                            $totalProdutos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            $totalPaginas = ceil($totalProdutos / $limite);

                            $search = isset($_GET['search']) ? "&search=" . $_GET['search'] : "";
                    
                            // Mostra o primeiro link
                            if ($totalPaginas > 1) {
                                echo '<a href="?limite=' . $limite . '&pagina=1' . $search . '" class="analog pag-link ' . ($paginaAtual == 1 ? "active" : "") . '">1</a>';
                            }
                    
                            // Determina o intervalo de páginas a serem exibidas
                            $inicio = max(2, $paginaAtual - 2); // começa no 2 para evitar duplicação do link 1
                            $fim = min($totalPaginas - 1, $paginaAtual + 2); // termina no totalPaginas - 1 para evitar duplicação do link final
                    
                            // Adiciona "..." se necessário
                            if ($inicio > 2) {
                                echo '<span class="pag-link">...</span>';
                            }
                    
                            // Mostra os links do intervalo calculado
                            for ($i = $inicio; $i <= $fim; $i++) {
                                echo '<a href="?limite=' . $limite . '&pagina=' . $i . $search . '" class="analog pag-link ' . ($i == $paginaAtual ? "active" : "") . '">' . $i . '</a>';
                            }
                    
                            // Adiciona "..." se necessário
                            if ($fim < $totalPaginas - 1) {
                                echo '<span class="pag-link">...</span>';
                            }
                    
                            // Mostra o último link
                            if ($totalPaginas > 1) {
                                echo '<a href="?limite=' . $limite . '&pagina=' . $totalPaginas . $search . '" class="analog pag-link ' . ($paginaAtual == $totalPaginas ? "active" : "") . '">' . $totalPaginas . '</a>';
                            }
                        ?>
                    </div>
                </div>
            <?php
                } else {
                    echo '
                            <div class="p-5 text-center">
                                <i class="bx bx-package" style="font-size: 3.5rem;"></i>
                                <p class="fw-semibold mb-4">Você não possui nenhum produto ativo!</p>
                                <a href="' . INCLUDE_PATH_DASHBOARD . 'criar-produto" class="btn btn-success btn-create-product px-3 py-2 text-decoration-none">+ Criar Produto</a>
                            </div>
                        ';
                }
            ?>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Adicionar um ouvinte de evento para a mudança de produtos por página
    $(".alterar-produtos-por-pagina").on("click", function() {
        var novoslimite = parseInt($(this).data("value"));
        var url = window.location.href.split('?')[0];
        window.location.href = url + "?limite=" + novoslimite + "&pagina=1<?= isset($_GET['search']) ? "&search=" . $_GET['search'] : ""; ?>";
    });
</script>

<script>
    $(document).ready(function() {
        $('#checkAll').on('click', function() {
            $('.itemCheckbox').prop('checked', $(this).prop('checked'));
        });
            
        $('.itemCheckbox').on('click', function() {
            $('#checkAll').prop('indeterminate', true);

            if ($('.itemCheckbox:checked').length === $('.itemCheckbox').length) {
                $('#checkAll').prop('indeterminate', false);
                $('#checkAll').prop('checked', true);
            } else if ($('.itemCheckbox:checked').length === 0) {
                $('#checkAll').prop('indeterminate', false);
                $('#checkAll').prop('checked', false);
            }
        });
    });
</script>

<!-- Ativar produto -->
<script>
    $(document).ready(function() {
        $(".change-status-btn").click(function() {
            var productId = $(this).data("id");
            var shopId = <?php echo $shop_id; ?>;

            // Armazenar a referência do elemento em uma variável para uso dentro do AJAX
            var button = $(this);

            $.ajax({
                url: "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/change_product_status.php",
                method: "POST",
                data: {
                    product_id: productId,
                    shop_id: shopId
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        if (response.action === "activated") {
                            // Alternar entre os ícones de like e atualizar o contador de likes
                            button.prop('checked', true);
                        } else if (response.action === "disabled") {
                            // Alternar entre os ícones de like e atualizar o contador de likes
                            button.prop('checked', false);
                        }
                    } else {
                        // Alternar entre os ícones de like e atualizar o contador de likes
                        button.prop('checked', false);
                        alert("Erro ao alterar o status do produto. " + response.message);
                    }
                },
                error: function() {
                    alert("Erro na solicitação AJAX.");
                }
            });
        });
    });
</script>

<!-- Search -->
<script>
    $(document).ready(function() {
        // Ao clicar no botão de pesquisa
        $('#searchButton').click(function() {
            // Obtenha o valor do campo de entrada de pesquisa
            var searchTerm = $('#searchInput').val();

            // Verifique se o campo de pesquisa não está vazio
            if (searchTerm && searchTerm.trim() !== '') {
                // Atualize a URL do navegador com os parâmetros de pesquisa
                window.location.href = updateQueryStringParameter(window.location.href, 'search', searchTerm);
            } else {
                // Se o campo de pesquisa estiver vazio, remova o parâmetro de pesquisa da URL
                window.location.href = removeQueryStringParameter(window.location.href, 'search');
            }
        });

        // Função para atualizar os parâmetros da string de consulta na URL do navegador
        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            }
            else {
                return uri + separator + key + "=" + value;
            }
        }

        // Função para remover um parâmetro da string de consulta na URL do navegador
        function removeQueryStringParameter(url, parameter) {
            var urlParts = url.split('?');
            if (urlParts.length >= 2) {
                var prefix = encodeURIComponent(parameter) + '=';
                var parts = urlParts[1].split(/[&;]/g);

                // Iterar sobre os parâmetros na string de consulta
                for (var i = parts.length; i-- > 0;) {
                    if (parts[i].lastIndexOf(prefix, 0) !== -1) {
                        parts.splice(i, 1);
                    }
                }

                // Se ainda houver parâmetros, recrie a string de consulta
                if (parts.length > 0) {
                    url = urlParts[0] + '?' + parts.join('&');
                } else {
                    // Se não houver mais parâmetros, remova completamente a string de consulta
                    url = urlParts[0];
                }
            }

            return url;
        }
    });
</script>