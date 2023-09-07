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
<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center">
                        <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>produtos" class="fs-5 text-decoration-none text-reset">Produtos</a></li>
                        <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Categorias</li>
                    </ol>
                </nav>
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

<div class="card__container grid one tabPanel" style="display: grid;">
    <div class="card__box grid">
        <div class="card table">
            <div class="card__title">
                <div class="title__content grid">
                    <form action="" class="table__actions">
                        <div class="search__container">
                            <input type="text" name="searchUsers" id="searchUsers" class="search" placeholder="Pesquisar" title="Pesquisar">
                        </div>
                    </form>
                </div>
            </div>
            <table class="table sortable">
                <thead>
                    <tr>
                        <th class="checkbox">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th class="small">Nome</th>
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
                                echo "<tr data-id='" . $usuario['id'] . "'>";
                                    echo "<td class='checkbox' scope='row'>
                                            <input class='form-check-input itemCheckbox' type='checkbox' value='" . $usuario['id'] . "' id='defaultCheck" . $usuario['id'] . "'>
                                            <span class='move-icon glyphicon glyphicon-move ml-2'>
                                                <i class='bx bx-grid-horizontal'></i>
                                            </span>
                                        </td>";
                                    echo "<td>" . $usuario['name'] . "</td>";
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
    </div>
</div>

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