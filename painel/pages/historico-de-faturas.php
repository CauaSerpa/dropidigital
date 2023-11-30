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

    /* Bullet */
    .bullet
    {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet.success
    {
        background: green;
    }
    .bullet.warning
    {
        background: orange;
    }
    .bullet.danger
    {
        background: red;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Dados de fatura</h2>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="http://localhost/dropidigital/app/painel/ajuda/criar-banner" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class="bx bx-help-circle me-1"></i>
                <b>Obtenha ajuda sobre</b>
            </a>
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
                <table>
                    <thead>
                        <tr>
                            <th class="small">ID</th>
                            <th class="small">Vencimento</th>
                            <th class="small">Valor</th>
                            <th class="small">Status</th>
                        </tr>
                    </thead>
                    <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_subscriptions';

                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $id);
                    $stmt->execute();

                    // Recuperar os resultados
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($resultados as $usuario) {
                        //Formatacao preco
                        // $price = str_replace(',', '.', str_replace('.', '', $usuario['price']));
                        $valor = $usuario['value'];

                        // Transforma o número no formato "R$ 149,90"
                        $value = "R$ " . number_format($valor, 2, ",", ".");

                        //Formatacao para data
                        $due_date = date("d/m/Y", strtotime($usuario['due_date']));

                        if ($usuario['status'] == "RECEIVED") {
                            $bullet = "<span class='bullet success me-2'></span>";
                            $status = "Paga";
                        } else if ($usuario['status'] == "ACTIVE" && $usuario['billing_type'] == "CREDIT_CARD") {
                            $bullet = "<span class='bullet success me-2'></span>";
                            $status = "Paga";
                        } else if ($usuario['status'] == "ACTIVE") {
                            $bullet = "<span class='bullet warning me-2'></span>";
                            $status = "Aguardando pagamento";
                        } else if ($usuario['status'] == "INACTIVE") {
                            $bullet = "<span class='bullet danger me-2'></span>";
                            $status = "Cancelada";
                        } else if ($usuario['status'] == "OVERDUE") {
                            $bullet = "<span class='bullet danger me-2'></span>";
                            $status = "Vencida";
                        }

                        echo '
                            <tbody>
                                <tr>
                                    <td>
                                        ' . $usuario['id'] . '
                                    </td>
                                    <td>' . $due_date . '</td>
                                    <td>' . $value . '</td>
                                    <td><div class="d-flex align-items-center">' . $bullet . $status . '</div></td>
                                </tr>
                            </tbody>
                        ';
                    }
                ?>
                </table>
            </div>
            <?php
                } else {
                    echo '
                            <div class="p-5 text-center">
                                <i class="bx bx-receipt" style="font-size: 3.5rem;"></i>
                                <p class="fw-semibold mb-4">Você não possui nenhum plano ativo!</p>
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