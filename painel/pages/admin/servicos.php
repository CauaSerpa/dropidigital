
<?php
        // Nome da tabela para a busca
        $tabela = 'tb_services';

        $sql = "SELECT * FROM $tabela ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->execute();

        $countServices = $stmt->rowCount();
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
</style>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Serviços</h2>
        <p class="shop-counter"><?php echo $countServices; echo ($countServices == 0 || $countServices == 1) ? ' serviço' : ' serviços'; ?></p>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-servico" class="button button--flex new text-decoration-none">+ Criar Serviços</a>
        </div>
    </div>
</div>

<form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/delete_shop.php" method="post" class="table__actions">
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
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th class="small">Nome do Serviço</th>
                            <th class="small">Descrição</th>
                            <th class="small">Preço</th>
                            <th class="small">Data de Criação</th>
                            <th class="small">Eventos</th>
                        </tr>
                    </thead>
                    <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_services';

                    // Configuração para paginação
                    $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
                    $paginaAtual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                    $inicioConsulta = ($paginaAtual - 1) * $limite;

                    // Preparar a consulta com base na pesquisa (se houver)
                    $sql = "SELECT * FROM $tabela";
                    if (!empty($_GET['search'])) {
                        $searchTerm = '%' . $_GET['search'] . '%';
                        $sql .= " WHERE (name LIKE :searchTerm)"; // Substitua campo1 e campo2 pelos campos que deseja pesquisar
                    }

                    $sql .= " ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    if (!empty($_GET['search'])) {
                        $stmt->bindParam(':searchTerm', $searchTerm);
                    }
                    $stmt->execute();

                    // Recuperar os resultados
                    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Conta o numero de Serviços exibidos  
                    $countServices = $stmt->rowCount();

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($services as $service) {
                        //Formatacao preco
                        // $price = str_replace(',', '.', str_replace('.', '', $service['price']));
                        $preco = $service['price'];

                        // Transforma o número no formato "R$ 149,90"
                        $price = "R$ " . number_format($preco, 2, ",", ".");

                        $price = ($service['without_price'] == 1) ? "--" : $price;

                        echo '
                            <tbody>
                                <tr>
                                    <td scope="row">
                                        <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="' . $service['id'] . '" id="defaultCheck2">
                                    </td>
                                    <td title="' . $service['name'] . '">
                        ';

                        if (!empty($service['card_image'])) {
                            echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/admin/service/' . $service['id'] . '/card-image/' . $service['card_image'] . '" alt="Capa do Serviço" style="width: 38px; height: 38px; object-fit: cover;">';
                        } else {
                            echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg" alt="Capa do Serviço" style="width: 38px; height: 38px; object-fit: cover;">';
                        }
                        
                        echo '
                                        ' . $service['name'] . '
                                    </td>
                                    <td title="' . strip_tags($service['description']) . '">' . strip_tags($service['description']) . '</td>
                                    <td title="' . $price . '">' . $price . '</td>
                                    <td title="' . date("d/m/Y", strtotime($service['date_create'])) . '">' . date("d/m/Y", strtotime($service['date_create'])) . '</td>
                                    <td>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'editar-servico?id=' . $service['id'] . '" class="btn btn-primary">
                                            <i class="bx bxs-edit" ></i>
                                        </a>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'excluir-servico?id=' . $service['id'] . '" class="btn btn-danger">
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
                                <div class="alterar-itens-por-pagina <?php echo ($limite == 10) ? "selected" : "" ; ?>" data-value="10">10</div>
                                <div class="alterar-itens-por-pagina <?php echo ($limite == 20) ? "selected" : "" ; ?>" data-value="20">20</div>
                                <div class="alterar-itens-por-pagina <?php echo ($limite == 30) ? "selected" : "" ; ?>" data-value="30">30</div>
                                <div class="alterar-itens-por-pagina <?php echo ($limite == 40) ? "selected" : "" ; ?>" data-value="40">40</div>
                                <div class="alterar-itens-por-pagina <?php echo ($limite == 50) ? "selected" : "" ; ?>" data-value="50">50</div>
                            </div>
                        </div>
                        <label>Serviços por página</label>
                    </div>
                </div>
                <div class="right grid">
                    <div class="controller">
                        <?php
                            $numServices = ceil($countServices / $limite);
                            // Garante que numServices seja pelo menos 1
                            $numServices = max(1, $numServices);

                            $search = isset($_GET['search']) ? "&search=" . $_GET['search'] : "";

                            for ($i = 1; $i <= $numServices; $i++) {
                                $classeAtiva = ($i == $paginaAtual) ? "active" : "";
                                echo '<a href="?limite=' . $limite . '&pagina=' . $i . $search . '" class="analog pag-link ' . $classeAtiva . '">' . $i . '</a>';
                            }
                        ?>
                    </div>
                </div>
            <?php
                } else {
                    echo '
                            <div class="p-5 text-center">
                                <i class="bx bx-wrench" style="font-size: 3.5rem;"></i>
                                <p class="fw-semibold mb-4">Não existe nenhum serviço cadastrado!</p>
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
    // Adicionar um ouvinte de evento para a mudança de itens por página
    $(".alterar-itens-por-pagina").on("click", function() {
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