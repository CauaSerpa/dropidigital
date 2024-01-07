
<?php
        // Nome da tabela para a busca
        $tabela = 'tb_shop';

        $sql = "SELECT * FROM $tabela ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->execute();

        $countShop = $stmt->rowCount();
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
                        <input class="form-control" id="formFileLg" type="file" name="arquivo" accept="text/csv">
                        <small class="form-text text-muted fw-normal small">Você deve selecionar um arquivo com extensão <span class="fw-semibold">.csv</span></small>
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
        <h2 class="title">Lojas</h2>
        <p class="shop-counter"><?php echo $countShop; echo ($countShop == 0 || $countShop == 1) ? ' loja' : ' lojas'; ?></p>
    </div>
    <div class="header__actions">
        <label for="import-button" class="import text-black text-decoration-none d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#import" style="cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
            Importar
        </label>
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
                            <th class="small">Responsável</th>
                            <th class="small">Nome da Loja</th>
                            <th class="small">Número do Documento</th>
                            <th class="small">E-mail</th>
                            <th class="small">Plano Atual</th>
                            <th class="small">Data de Criação</th>
                            <th class="small">Eventos</th>
                        </tr>
                    </thead>
                    <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_users';

                    $sql = "SELECT * FROM $tabela WHERE permissions = :permissions ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':permissions', 0);
                    $stmt->execute();

                    // Recuperar os resultados
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($users as $user) {
                        // Nome da tabela para a busca
                        $tabela = 'tb_shop';

                        $sql = "SELECT * FROM $tabela WHERE user_id = :user_id ORDER BY id DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':user_id', $user['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Loop através dos resultados e exibir todas as colunas
                        foreach ($shops as $shop) {
                            echo '
                                <tbody>
                                    <tr>
                                        <td scope="row">
                                            <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="' . $shop['id'] . '" id="defaultCheck2">
                                        </td>
                                        <td>' . $user['name'] . '</td>
                                        <td>' . $shop['name'] . '</td>
                                        <td>' . $shop['cpf_cnpj'] . '</td>
                                        <td>' . $user['email'] . '</td>
                            ';

                                    // Nome da tabela para a busca
                                    $tabelaInterval = 'tb_plans_interval';
                                    $tabelaPlans = 'tb_plans';

                                    $sql = "SELECT p.name
                                            FROM $tabelaInterval i
                                            JOIN $tabelaPlans p ON i.plan_id = p.id
                                            WHERE i.id = :id
                                            ORDER BY i.id DESC";

                                    // Preparar e executar a consulta
                                    $stmt = $conn_pdo->prepare($sql);
                                    $stmt->bindParam(':id', $shop['plan_id']);
                                    $stmt->execute();

                                    // Recuperar os resultados
                                    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                                    if ($plan) {
                                        echo "<td>";
                                        echo $plan['name'];
                                        echo "</td>";
                                    }
                                        
                            echo '
                                        <td>' . date("d/m/Y", strtotime($user['date_create'])) . '</td>
                                        <td>
                                            <a href="' . INCLUDE_PATH_DASHBOARD . 'ver-loja?id=' . $shop['id'] . '" class="btn btn-secondary">
                                                <i class="bx bx-show" ></i>
                                            </a>
                                            <a href="' . INCLUDE_PATH_DASHBOARD . 'excluir-loja?id=' . $shop['id'] . '" class="btn btn-danger">
                                                <i class="bx bxs-trash" ></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            ';
                        }
                    }
                ?>
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
                        <label>Lojas por página</label>
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
                                <p class="fw-semibold mb-4">Não existe nenhuma loja cadastrada!</p>
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