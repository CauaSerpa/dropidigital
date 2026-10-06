<?php
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();
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

    /* Link */
    a.link
    {
        text-decoration: none;
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
        background: rgb(1, 200, 155);
    }
    .bullet.warning
    {
        background: rgb(251, 188, 5);
    }
    .bullet.danger
    {
        background: rgb(229, 15, 56);
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

<style>
    /* Btn */
    .btn.btn-success
    {
        background: var(--green-color) !important;
        border: none !important;
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color) !important;
        border: none !important;
    }

    /* Bullet */
    .bullet
    {
        display: inline-flex;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet.success
    {
        background: rgb(1, 200, 155);
    }
    .bullet.warning
    {
        background: rgb(251, 188, 5);
    }
    .bullet.danger
    {
        background: rgb(229, 15, 56);
    }
</style>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';
    
    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND plan_id = :plan_id ORDER BY id DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->bindParam(':plan_id', $plan_id);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sub['status'] == "RECEIVED") {
        $bullet = "<span class='bullet success me-2'></span>";
        $status = "Paga";
    } else if ($sub['status'] == "ACTIVE" && $sub['billing_type'] == "CREDIT_CARD") {
        $bullet = "<span class='bullet success me-2'></span>";
        $status = "Paga";
    } else if ($sub['status'] == "ACTIVE") {
        $bullet = "<span class='bullet warning me-2'></span>";
        $status = "Aguardando pagamento";
    } else if ($sub['status'] == "OVERDUE") {
        $bullet = "<span class='bullet danger me-2'></span>";
        $status = "Atrasada";
    } else {
        $bullet = "<span class='bullet danger me-2'></span>";
        $status = "Cancelada";
    }

    // Verificação se a consulta retornou algum resultado
    if ($sub) {
        if ($sub['cycle'] == "MONTHLY")
        {
            $cycle = "Mensal";
        } else {
            $cycle = "Anual";
        }

        if ($sub['billing_type'] == "CREDIT_CARD")
        {
            $billing_type = "Cartão de crédito";
        } else {
            $billing_type = "Pix";
        }

        // Formatando datas
        $startDate = new DateTime($sub['start_date']);
        $start_date = $startDate->format("d/m/Y");

        $dueDate = new DateTime($sub['due_date']);
        $due_date = $dueDate->format("d/m/Y");
    }
?>

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Plano Atual</div>
    <div class="card-body row px-4 py-3">
        <div class="card col-md-6 px-4 py-3">
            <ul class="mb-0">
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">ID:</small>
                    <small><?php echo $sub['id']; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Status:</small>
                    <small><?php echo $bullet . $status; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Valor:</small>
                    <small>R$ <?php echo number_format($sub['value'], 2, ',', '.'); ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Forma de Pagamento:</small>
                    <small><?php echo $billing_type; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Ciclo:</small>
                    <small><?php echo $cycle; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">E-mail para NFe:</small>
                    <small><?php echo $email; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Data da Emissão:</small>
                    <small><?php echo $start_date; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Próximo Pagamento:</small>
                    <small><?php echo $due_date; ?></small>
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <h5 class="fs-5 mb-3">Ações</h5>
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h6 class="fs-6 fw-semibold mb-0">Alterar Plano</h6>
                    <small>Clique em alterar plano para alterar o plano da loja</small>
                </div>
                <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#editShopPlan">
                    <a href="#" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small">
                        Alterar Plano
                    </a>
                </button>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h6 class="fs-6 fw-semibold mb-0">Listar NFS-e</h6>
                    <small>Clique em listar para alterar o plano da loja</small>
                </div>
                <div class="d-flex align-items-center">
                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/admin/access_shop.php?id=<?php echo $user['id']; ?>" class="btn btn-success fw-semibold px-4 py-2 small">Alterar Plano</a>
                </div>
            </div>
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
                            <th class="small">Eventos</th>
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
                        } else if ($usuario['status'] == "OVERDUE") {
                            $bullet = "<span class='bullet danger me-2'></span>";
                            $status = "Atrasada";
                        } else {
                            $bullet = "<span class='bullet danger me-2'></span>";
                            $status = "Cancelada";
                        }

                        echo '
                            <tbody>
                                <tr>
                                    <td>' . $usuario['id'] . '</td>
                                    <td>' . $due_date . '</td>
                                    <td>' . $value . '</td>
                                    <td><div class="d-flex align-items-center">' . $bullet . $status . '</div></td>
                                    <td>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'fatura?id=' . $usuario['id'] . '" class="btn btn-secondary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
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