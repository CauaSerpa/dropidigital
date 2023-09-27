<head>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
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
</style>
<?php
        // Nome da tabela para a busca
        $tabela = 'tb_categories';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $id);
        $stmt->execute();

        $countCategories = $stmt->rowCount();
?>
<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Categorias</h2>
                <p class="products-counter"><?php echo $countCategories; echo ($countCategories == 0 || $countCategories == 1) ? ' categoria' : ' categorias'; ?></p>
            </div>
        </div>
    </div>
    <div class="header__actions">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="export text-black text-decoration-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
            Importar
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
        </a>
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-categoria" class="button button--flex new text-decoration-none">+ Criar Categoria</a>
        </div>
    </div>
</div>

<form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/delete_categories.php" method="post" class="table__actions">
    <div class="card__container grid one tabPanel" style="display: grid;">
        <div class="card__box grid">
            <div class="card table">
            <?php
                if ($stmt->rowCount() > 0) {
            ?>
                <div class="card__title">
                    <div class="title__content grid">
                        <div class="search__container">
                            <input type="text" name="searchUsers" id="searchUsers" class="search" placeholder="Pesquisar" title="Pesquisar">
                        </div>
                        <button type="button" class="filter" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvasExample">
                            Filtrar
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M21 3H5a1 1 0 0 0-1 1v2.59c0 .523.213 1.037.583 1.407L10 13.414V21a1.001 1.001 0 0 0 1.447.895l4-2c.339-.17.553-.516.553-.895v-5.586l5.417-5.417c.37-.37.583-.884.583-1.407V4a1 1 0 0 0-1-1zm-6.707 9.293A.996.996 0 0 0 14 13v5.382l-2 1V13a.996.996 0 0 0-.293-.707L6 6.59V5h14.001l.002 1.583-5.71 5.71z"></path></svg>
                        </button>
                        <button type="submit" class="btn btn-danger delete">
                            Executar Ação
                        </button>
                    </div>
                </div>
                <table class="table sortable">
                    <thead>
                        <tr>
                            <th class="checkbox">
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th class="small">Nome</th>
                            <th class="small">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_categories';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $id);
                            $stmt->execute();

                            // Recuperar os resultados
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                // Loop através dos resultados e exibir todas as colunas
                                foreach ($resultados as $usuario) {
                                    echo "<tr data-id='" . $usuario['id'] . "' class='align-middle'>";
                                        echo '<td class="checkbox" scope="row">
                                                <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="' . $usuario['id'] . '" id="defaultCheck2">
                                                <span class="move-icon glyphicon glyphicon-move ml-2">
                                                    <i class="bx bx-grid-horizontal"></i>
                                                </span>
                                            </td>';
                                        echo "<td style='width:100%;'>" . $usuario['name'] . "</td>";
                                        echo '<td>
                                                <a href="' . INCLUDE_PATH_DASHBOARD . 'editar-categoria?id=' . $usuario['id'] . '" class="btn btn-primary">
                                                    <i class="bx bx-show-alt" ></i>
                                                </a>
                                                <a href="' . INCLUDE_PATH_DASHBOARD . 'excluir-categoria?id=' . $usuario['id'] . '" class="btn btn-danger">
                                                    <i class="bx bxs-trash" ></i>
                                                </a>
                                            </td>';
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="center">
                <div class="left">
                    <div class="container__button">
                        <div class="limitPageDropdown dropdown button button--flex select">
                            <input type="text" class="text02" placeholder="10" readonly="">
                            <div class="option">
                                <div onclick="show('10')">10</div>
                                <div onclick="show('20')">20</div>
                                <div onclick="show('30')">30</div>
                                <div onclick="show('40')">40</div>
                                <div onclick="show('50')">50</div>
                            </div>
                        </div>
                        <label>Produtos por página</label>
                    </div>
                </div>
                <div class="right grid">
                    <div class="controller"><span class="analog pag-link active pag-link">1</span></div>            
                </div>
            </div>
            <?php
                    } else {
                        echo '
                                <div class="p-5 text-center">
                                    <i class="bx bx-image" style="font-size: 3.5rem;"></i>
                                    <p class="fw-semibold mb-4">Você não possui nenhuma categoria ativa!</p>
                                    <a href="' . INCLUDE_PATH_DASHBOARD . 'criar-categoria" class="btn btn-success btn-create-product px-3 py-2 text-decoration-none">+ Criar Categorias</a>
                                </div>
                            ';
                    }
                ?>
        </div>
    </div>
</form>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Ajax -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

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

<script>
    $(document).ready(function() {
        $(".sortable tbody").sortable({
            handle: ".move-icon",
            helper: fixWidthHelper,
            axis: "y", // Restringe a movimentação apenas no eixo vertical
            tolerance: "pointer" // Melhora a precisão do toque em dispositivos móveis
        });

        function fixWidthHelper(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        }

        $(".sortable tbody").on("sortupdate", function(event, ui) {
            var order = $(this).sortable("toArray", { attribute: "data-id" });
            $.ajax({
                url: "update_order.php",
                method: "POST",
                data: { order: order },
                success: function(response) {
                    console.log("Ordem atualizada no servidor.");
                }
            });
        });
    });
</script>