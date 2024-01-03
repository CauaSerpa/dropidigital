<?php
        // Nome da tabela para a busca
        $tabela = 'tb_pages';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $id);
        $stmt->execute();

        $countPages = $stmt->rowCount();
?>
<!-- Codigo do site -->
<style>
    #imagePreviews {
        display: flex;
        flex-wrap: wrap;
    }

    .image-preview {
        position: relative;
        margin-right: 10px;
    }

    .image-preview img {
        max-width: 100px;
        height: auto;
        cursor: pointer;
    }

    .remove-image {
        position: absolute;
        top: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        padding: 2px 5px;
        cursor: pointer;
    }

    /* Image */
    #imageInput
    {
        display: none;
    }
    /* .dropzone
    {
        height: 100px;
        background: #c4c4c4;
    } */
    
    /* SEO Preview */
    .seo-preview
    {
        border: 2px dashed #ddd;
    }
</style>
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
        <h2 class="title">Páginas</h2>
        <p class="products-counter"><?php echo $countPages; echo ($countPages == 0 || $countPages == 1) ? ' página' : ' páginas'; ?></p>
    </div>
    <div class="header__actions">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-pagina" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center me-3">
            <i class='bx bx-help-circle me-1' ></i>
            <b>Obtenha ajuda sobre</b>
        </a>
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-pagina" class="button button--flex new text-decoration-none">+ Criar Página</a>
        </div>
    </div>
</div>

<form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/delete_pages.php" method="post" class="table__actions">
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
                <table>
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="checkAll">
                            </th>
                            <th class="small">Nome</th>
                            <th class="small text-nowrap">Data de Criação</th>
                            <th class="small">Status</th>
                            <th class="small">Eventos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Nome da tabela para a busca
                            $tabela = 'tb_pages';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $id);
                            $stmt->execute();

                            // Recuperar os resultados
                            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($resultados as $usuario) {
                                //Formatacao para data
                                $date_create = date("d/m/Y H:i:s", strtotime($usuario['date_create']));

                                //Status
                                if ($usuario['status'] == 1) {
                                    $status = "Ativo";
                                } else {
                                    $status = "Desativado";
                                }

                                echo '
                                    <tbody>
                                        <tr>
                                            <td class="checkbox" scope="row">
                                                <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="' . $usuario['id'] . '" id="defaultCheck2">
                                            </td>
                                            <td class="w-100">' . $usuario['name'] . '</td>
                                            <td>' . $date_create . '</td>
                                            <td>' . $status . '</td>
                                            <td>
                                                <a href="' . INCLUDE_PATH_DASHBOARD . 'editar-pagina?id=' . $usuario['id'] . '" class="btn btn-primary">
                                                    <i class="bx bxs-edit" ></i>
                                                </a>
                                                <a href="' . INCLUDE_PATH_DASHBOARD . 'excluir-pagina?id=' . $usuario['id'] . '" class="btn btn-danger">
                                                    <i class="bx bxs-trash" ></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                ';
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
                    <div class="controller">
                        <span class="analog pag-link active pag-link">1</span>
                    </div>
                </div>
            <?php
                } else {
                    echo '
                            <div class="p-5 text-center">
                                <i class="bx bx-package" style="font-size: 3.5rem;"></i>
                                <p class="fw-semibold mb-4">Você não possui nenhuma página ativa!</p>
                                <a href="' . INCLUDE_PATH_DASHBOARD . 'criar-pagina" class="btn btn-success btn-create-product px-3 py-2 text-decoration-none">+ Criar Página</a>
                            </div>
                        ';
                }
            ?>
            </div>
        </div>
    </div>
</form>

<!-- Link para o TinyMCE CSS -->
<script src="https://cdn.tiny.cloud/1/xiqhvnpyyc1fqurimqcwiz49n6zap8glrv70bar36fbloiko/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- Modal -->
<script>
    const myModal = document.getElementById('myModal')
    const myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', () => {
        myInput.focus()
    })
</script>

<!-- Checkbox -->
<script>
    // Função para mostrar/ocultar a div com base no estado do checkbox
    function toggleDivVisibility(checkboxId, targetDivId) {
        var checkbox = document.getElementById(checkboxId);
        var targetDiv = document.getElementById(targetDivId);

        if (checkbox.checked) {
            // Se o checkbox estiver marcado, remova a classe "d-none" para mostrar a div
            targetDiv.classList.remove("d-none");
        } else {
            // Se o checkbox não estiver marcado, adicione a classe "d-none" para ocultar a div
            targetDiv.classList.add("d-none");
        }
    }

    // Adicione um ouvinte de evento a cada checkbox para chamar a função quando o estado mudar
    var checkbox1 = document.getElementById("modal-1");
    checkbox1.addEventListener("change", function() {
        toggleDivVisibility("modal-1", "targetDiv1");
    });

    var checkbox2 = document.getElementById("modal-2");
    checkbox2.addEventListener("change", function() {
        toggleDivVisibility("modal-2", "targetDiv2");
    });

    // Chame a função para verificar o estado inicial dos checkboxes quando a página é carregada
    document.addEventListener("DOMContentLoaded", function() {
        toggleDivVisibility("modal-1", "targetDiv1");
        toggleDivVisibility("modal-2", "targetDiv2");
    });
</script>