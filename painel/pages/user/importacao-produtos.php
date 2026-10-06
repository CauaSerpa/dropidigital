<?php
if (!in_array($shop_id, [1, 2, 25, 61])) {
    header('Location: '.INCLUDE_PATH_DASHBOARD);
    exit;
}

$countScripts = 0;
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background: #f5f7fb;
    }

    .sidebar .nav-links li {
        background: #fff;
    }

    .page-title {
        font-weight: 700;
        margin-bottom: .25rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin-bottom: 0;
    }

    .dashboard-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
    }

    .dashboard-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef1f6;
        font-weight: 600;
    }

    .kpi-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
        height: 100%;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }

    .kpi-label {
        color: #6c757d;
        font-size: .95rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        padding: .45rem .8rem;
        font-size: .9rem;
        font-weight: 600;
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-active {
        background: #e9f9ef;
        color: #198754;
    }

    .status-active .status-dot {
        background: #198754;
    }

    .status-paused {
        background: #fff4db;
        color: #b7791f;
    }

    .status-paused .status-dot {
        background: #b7791f;
    }

    .status-error {
        background: #fdecec;
        color: #dc3545;
    }

    .status-error .status-dot {
        background: #dc3545;
    }

    .worker-toggle-btn {
        min-width: 180px;
        font-weight: 600;
    }

    .summary-item {
        padding: .9rem 1rem;
        background: #f8f9fc;
        border: 1px solid #edf0f5;
        border-radius: 14px;
        height: 100%;
    }

    .summary-label {
        font-size: .85rem;
        color: #6c757d;
        margin-bottom: .2rem;
    }

    .summary-value {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }

    .product-thumb {
        width: 54px;
        height: 54px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
    }

    .source-badge {
        font-size: .75rem;
        border-radius: 999px;
        padding: .35rem .65rem;
    }

    .soft-box {
        background: #f8f9fc;
        border: 1px solid #edf0f5;
        border-radius: 14px;
        padding: 1rem;
    }

    .timeline-item {
        position: relative;
        padding-left: 1.25rem;
        margin-bottom: 1rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: .45rem;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #0d6efd;
    }

    .timeline-time {
        color: #6c757d;
        font-size: .85rem;
    }

    .sticky-actions {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f5f7fb;
        padding-top: .25rem;
        padding-bottom: .75rem;
    }

    .progress {
        height: 12px;
        border-radius: 999px;
    }

    .small-muted {
        color: #6c757d;
        font-size: .9rem;
    }

    .table-hover tbody tr:hover {
        background: #fafbfd;
    }
</style>

<?php
    /**
     * =====================================================
     * EXECUÇÃO ATUAL
     * =====================================================
     */

    $stmt = $conn_pdo->query("
        SELECT *
        FROM tb_product_import_jobs
        WHERE status = 'running'
        ORDER BY id ASC
        LIMIT 1
    ");

    $currentJob = $stmt->fetch(PDO::FETCH_ASSOC);

    /**
     * =====================================================
     * DADOS PADRÃO DO DASHBOARD
     * =====================================================
     */

    $jobStats = [
        'processed' => 0,
        'inserted' => 0,
        'updated' => 0,
        'errors' => 0
    ];

    if ($currentJob) {

        /**
         * PRODUTOS PROCESSADOS
         */

        $jobStats['processed'] =
            (int) $currentJob['products_processed'];

        /**
         * ESTATÍSTICAS DOS PRODUTOS
         *
         * Ajuste os valores de status conforme os valores
         * que você estiver gravando em
         * tb_product_import_job_products.
         */

        $stmt = $conn_pdo->prepare("
            SELECT
                SUM(
                    CASE
                        WHEN status = 'inserted'
                        THEN 1
                        ELSE 0
                    END
                ) AS inserted,

                SUM(
                    CASE
                        WHEN status = 'updated'
                        THEN 1
                        ELSE 0
                    END
                ) AS updated,

                SUM(
                    CASE
                        WHEN status = 'error'
                        THEN 1
                        ELSE 0
                    END
                ) AS errors

            FROM tb_product_import_job_products
            WHERE job_id = ?
        ");

        $stmt->execute([
            $currentJob['id']
        ]);

        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stats) {

            $jobStats['inserted'] =
                (int) ($stats['inserted'] ?? 0);

            $jobStats['updated'] =
                (int) ($stats['updated'] ?? 0);

            $jobStats['errors'] =
                (int) ($stats['errors'] ?? 0);
        }
    }

    /**
     * =====================================================
     * PROGRESSO
     * =====================================================
     */

    $progress = 0;

    if ($currentJob) {

        $productLimit =
            (int) $currentJob['product_limit'];

        /**
         * Quando count_only_new está ativo,
         * o progresso considera somente produtos
         * efetivamente cadastrados.
         *
         * Caso contrário, considera todos os
         * produtos processados.
         */
        if ((int) $currentJob['count_only_new'] === 1) {

            $progressCount =
                (int) $currentJob['products_inserted'];

        } else {

            $progressCount =
                (int) $currentJob['products_processed'];
        }

        if ($productLimit > 0) {

            $progress =
                round(
                    ($progressCount / $productLimit) * 100
                );

            $progress =
                min(
                    100,
                    max(0, $progress)
                );
        }
    }

    /**
     * =====================================================
     * TEMPO DECORRIDO
     * =====================================================
     */

    $elapsedTime = '-';

    if (
        $currentJob
        && !empty($currentJob['started_at'])
    ) {

        $startedAt =
            new DateTime(
                $currentJob['started_at']
            );

        $now =
            new DateTime();

        $interval =
            $startedAt->diff($now);

        if ($interval->days > 0) {

            $elapsedTime =
                $interval->days
                . 'd '
                . $interval->h
                . 'h '
                . $interval->i
                . 'min';

        } elseif ($interval->h > 0) {

            $elapsedTime =
                $interval->h
                . 'h '
                . $interval->i
                . 'min';

        } else {

            $elapsedTime =
                $interval->i
                . ' min';
        }
    }

    /**
     * =====================================================
     * ÚLTIMA ATUALIZAÇÃO
     * =====================================================
     */

    $lastActivity = '-';

    if (
        $currentJob
        && !empty($currentJob['last_activity_at'])
    ) {

        $lastActivity =
            date(
                'd/m/Y H:i',
                strtotime(
                    $currentJob['last_activity_at']
                )
            );
    }

    /**
     * =====================================================
     * INÍCIO
     * =====================================================
     */

    $startedAtFormatted = '-';

    if (
        $currentJob
        && !empty($currentJob['started_at'])
    ) {

        $startedAtFormatted =
            date(
                'd/m/Y H:i',
                strtotime(
                    $currentJob['started_at']
                )
            );
    }
?>

<?php
    /**
     * =====================================================
     * ESTADO DOS JOBS / WORKER
     * =====================================================
     */

    $shopId = $_SESSION['shop_id'] ?? 0;

    /**
     * Busca o estado geral dos jobs da loja.
     *
     * running  = existe execução ativa
     * pending  = existem execuções aguardando processamento
     * paused   = existem execuções pausadas
     */
    $stmt = $conn_pdo->prepare("
        SELECT
            SUM(status = 'running') AS running_count,
            SUM(status = 'pending') AS pending_count,
            SUM(status = 'paused') AS paused_count
        FROM tb_product_import_jobs
        WHERE shop_id = :shop_id
    ");

    $stmt->execute([
        ':shop_id' => $shopId
    ]);

    $workerState = $stmt->fetch(PDO::FETCH_ASSOC);

    $runningCount = (int) ($workerState['running_count'] ?? 0);
    $pendingCount = (int) ($workerState['pending_count'] ?? 0);
    $pausedCount = (int) ($workerState['paused_count'] ?? 0);

    /**
     * =====================================================
     * DETERMINA O ESTADO DO WORKER
     * =====================================================
     *
     * Se existir running OU pending, o worker está ativo.
     *
     * Isso é importante porque um job pending será executado
     * pelo próximo ciclo do worker.
     */

    $workerActive =
        $runningCount > 0
        || $pendingCount > 0;

    /**
     * Worker parado somente quando não existe nenhuma
     * execução running/pending e existem jobs pausados.
     */

    $workerPaused =
        !$workerActive
        && $pausedCount > 0;

    /**
     * =====================================================
     * JOB ATUAL
     * =====================================================
     *
     * O job atual deve ser somente o que está realmente
     * executando.
     */

    $stmt = $conn_pdo->prepare("
        SELECT *
        FROM tb_product_import_jobs
        WHERE shop_id = :shop_id
        AND status = 'running'
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute([
        ':shop_id' => $shopId
    ]);

    $currentJob = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Header -->
<div class="sticky-actions">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <h1 class="page-title">Central de Importação de Produtos</h1>
            <p class="page-subtitle">
                Gerencie o worker, acompanhe importações e visualize os produtos encontrados e cadastrados.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Botão Play / Pause do Worker -->
            <form method="POST" action="<?= INCLUDE_PATH_DASHBOARD ?>back-end/import-products.php">

                <?php if ($workerActive): ?>

                    <input
                        type="hidden"
                        name="action"
                        value="pause"
                    >

                    <button
                        type="submit"
                        class="btn btn-warning worker-toggle-btn"
                        name="btnWorkerToggle"
                    >
                        <i class="bi bi-pause-fill me-1"></i>
                        Pausar Worker
                    </button>

                <?php elseif ($workerPaused): ?>

                    <input
                        type="hidden"
                        name="action"
                        value="resume"
                    >

                    <button
                        type="submit"
                        class="btn btn-success worker-toggle-btn"
                        name="btnWorkerToggle"
                    >
                        <i class="bi bi-play-fill me-1"></i>
                        Retomar Worker
                    </button>

                <?php else: ?>

                    <input
                        type="hidden"
                        name="action"
                        value="resume"
                    >

                    <button
                        type="submit"
                        class="btn btn-success worker-toggle-btn"
                        name="btnWorkerToggle"
                    >
                        <i class="bi bi-play-fill me-1"></i>
                        Ativar Worker
                    </button>

                <?php endif; ?>

            </form>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaImportacao">
                <i class="bi bi-plus-circle me-1"></i>
                Nova importação
            </button>
        </div>
    </div>
</div>

<!-- Status bar -->
<div class="card dashboard-card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-center">

            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center gap-2">

                    <?php if ($workerActive): ?>

                        <span
                            class="status-pill status-active"
                            id="workerStatus"
                        >
                            <span class="status-dot"></span>
                            Worker ativo
                        </span>

                    <?php else: ?>

                        <span
                            class="status-pill status-paused"
                            id="workerStatus"
                        >
                            <span class="status-dot"></span>
                            Worker parado
                        </span>

                    <?php endif; ?>

                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-muted">
                    Execução atual
                </div>

                <div class="fw-semibold">

                    <?php if ($currentJob): ?>

                        <?= htmlspecialchars(
                            ucfirst(
                                $currentJob['provider']
                            )
                        ) ?>
                        · Importação de produtos

                    <?php else: ?>

                        Nenhuma execução

                    <?php endif; ?>

                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-muted">
                    Última atualização
                </div>

                <div class="fw-semibold">
                    <?= htmlspecialchars(
                        $lastActivity
                    ) ?>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="small-muted">
                    Tempo decorrido
                </div>

                <div class="fw-semibold">
                    <?= htmlspecialchars(
                        $elapsedTime
                    ) ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
    /**
     * =====================================================
     * KPIs DA IMPORTAÇÃO
     * =====================================================
     */

    $kpis = [
        'keywords_today' => 0,
        'keywords_yesterday' => 0,

        'inserted_today' => 0,
        'inserted_yesterday' => 0,

        'updated_today' => 0,
        'updated_yesterday' => 0,

        'errors_today' => 0,
        'errors_yesterday' => 0
    ];

    $stmt = $conn_pdo->prepare("
        SELECT
            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE()
                    THEN products_inserted
                    ELSE 0
                END
            ), 0) AS inserted_today,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE() - INTERVAL 1 DAY
                    AND created_at < CURDATE()
                    THEN products_inserted
                    ELSE 0
                END
            ), 0) AS inserted_yesterday,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE()
                    THEN products_updated
                    ELSE 0
                END
            ), 0) AS updated_today,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE() - INTERVAL 1 DAY
                    AND created_at < CURDATE()
                    THEN products_updated
                    ELSE 0
                END
            ), 0) AS updated_yesterday,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE()
                    THEN products_errors
                    ELSE 0
                END
            ), 0) AS errors_today,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE() - INTERVAL 1 DAY
                    AND created_at < CURDATE()
                    THEN products_errors
                    ELSE 0
                END
            ), 0) AS errors_yesterday

        FROM tb_product_import_jobs
        WHERE shop_id = :shop_id
    ");

    $stmt->execute([
        ':shop_id' => $shop_id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $kpis = [
            'inserted_today' => (int) $result['inserted_today'],
            'inserted_yesterday' => (int) $result['inserted_yesterday'],

            'updated_today' => (int) $result['updated_today'],
            'updated_yesterday' => (int) $result['updated_yesterday'],

            'errors_today' => (int) $result['errors_today'],
            'errors_yesterday' => (int) $result['errors_yesterday']
        ];
    }

    /**
     * =====================================================
     * KEYWORDS / FONTES DESCOBERTAS
     * =====================================================
     */

    $stmt = $conn_pdo->prepare("
        SELECT
            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE()
                    THEN 1
                    ELSE 0
                END
            ), 0) AS keywords_today,

            COALESCE(SUM(
                CASE
                    WHEN created_at >= CURDATE() - INTERVAL 1 DAY
                    AND created_at < CURDATE()
                    THEN 1
                    ELSE 0
                END
            ), 0) AS keywords_yesterday

        FROM tb_product_import_keywords

        WHERE shop_id = :shop_id
        AND created_at >= CURDATE() - INTERVAL 1 DAY
        AND source NOT IN ('Manual', 'Padrão')
    ");

    $stmt->execute([
        ':shop_id' => $shop_id
    ]);

    $keywordStats = $stmt->fetch(PDO::FETCH_ASSOC);

    $kpis['keywords_today'] = (int) ($keywordStats['keywords_today'] ?? 0);
    $kpis['keywords_yesterday'] = (int) ($keywordStats['keywords_yesterday'] ?? 0);

    /**
     * =====================================================
     * CALCULA VARIAÇÃO
     * =====================================================
     */

    function calcularVariacaoKpi($hoje, $ontem)
    {
        if ($ontem == 0) {
            if ($hoje > 0) {
                return [
                    'percent' => 100,
                    'type' => 'up'
                ];
            }

            return [
                'percent' => 0,
                'type' => 'neutral'
            ];
        }

        $percent = (($hoje - $ontem) / $ontem) * 100;

        return [
            'percent' => round(abs($percent)),
            'type' => $percent > 0
                ? 'up'
                : ($percent < 0 ? 'down' : 'neutral')
        ];
    }

    $keywordsVariation = calcularVariacaoKpi(
        $kpis['keywords_today'],
        $kpis['keywords_yesterday']
    );

    $insertedVariation = calcularVariacaoKpi(
        $kpis['inserted_today'],
        $kpis['inserted_yesterday']
    );

    $updatedVariation = calcularVariacaoKpi(
        $kpis['updated_today'],
        $kpis['updated_yesterday']
    );

    $errorsVariation = calcularVariacaoKpi(
        $kpis['errors_today'],
        $kpis['errors_yesterday']
    );
?>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Fontes descobertas hoje</div>
                        <div class="kpi-value mt-2">
                            <?= number_format($kpis['keywords_today'], 0, ',', '.'); ?>
                        </div>

                        <?php if ($keywordsVariation['type'] === 'up'): ?>
                            <div class="small text-success mt-2">
                                <i class="bi bi-arrow-up-right"></i>
                                +<?= $keywordsVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php elseif ($keywordsVariation['type'] === 'down'): ?>
                            <div class="small text-danger mt-2">
                                <i class="bi bi-arrow-down-right"></i>
                                -<?= $keywordsVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php else: ?>
                            <div class="small text-muted mt-2">
                                <i class="bi bi-dash"></i>
                                Igual a ontem
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="kpi-icon bg-primary-subtle text-primary">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Produtos cadastrados hoje</div>
                        <div class="kpi-value mt-2">
                            <?= number_format($kpis['inserted_today'], 0, ',', '.'); ?>
                        </div>

                        <?php if ($insertedVariation['type'] === 'up'): ?>
                            <div class="small text-success mt-2">
                                <i class="bi bi-arrow-up-right"></i>
                                +<?= $insertedVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php elseif ($insertedVariation['type'] === 'down'): ?>
                            <div class="small text-danger mt-2">
                                <i class="bi bi-arrow-down-right"></i>
                                -<?= $insertedVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php else: ?>
                            <div class="small text-muted mt-2">
                                <i class="bi bi-dash"></i>
                                Igual a ontem
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="kpi-icon bg-success-subtle text-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Produtos atualizados hoje</div>
                        <div class="kpi-value mt-2">
                            <?= number_format($kpis['updated_today'], 0, ',', '.'); ?>
                        </div>

                        <div class="small text-muted mt-2">
                            <i class="bi bi-arrow-repeat"></i>
                            Preço, imagem ou dados sincronizados
                        </div>
                    </div>

                    <div class="kpi-icon bg-warning-subtle text-warning">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Erros na importação hoje</div>
                        <div class="kpi-value mt-2">
                            <?= number_format($kpis['errors_today'], 0, ',', '.'); ?>
                        </div>

                        <?php if ($errorsVariation['type'] === 'up'): ?>
                            <div class="small text-danger mt-2">
                                <i class="bi bi-arrow-up-right"></i>
                                +<?= $errorsVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php elseif ($errorsVariation['type'] === 'down'): ?>
                            <div class="small text-success mt-2">
                                <i class="bi bi-arrow-down-right"></i>
                                -<?= $errorsVariation['percent']; ?>% em relação a ontem
                            </div>
                        <?php else: ?>
                            <div class="small text-muted mt-2">
                                <i class="bi bi-dash"></i>
                                Igual a ontem
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="kpi-icon bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main content row -->
<div class="row g-4">

    <!-- Left column -->
    <div class="col-xl-8">

        <!-- Execução atual -->
        <div class="card dashboard-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    Execução atual
                </span>
                <div class="d-flex gap-2">
                    <?php if ($currentJob): ?>
                        <button
                            class="btn btn-sm btn-outline-secondary"
                        >
                            <i class="bi bi-eye me-1"></i>
                            Ver detalhes
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($currentJob): ?>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-label">
                                    Tipo
                                </div>
                                <div class="summary-value">
                                    Importação
                                    <?= htmlspecialchars(
                                        ucfirst(
                                            $currentJob['provider']
                                        )
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-label">
                                    Início
                                </div>
                                <div class="summary-value">
                                    <?= htmlspecialchars(
                                        $startedAtFormatted
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-label">
                                    Tempo decorrido
                                </div>
                                <div class="summary-value">
                                    <?= htmlspecialchars(
                                        $elapsedTime
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-item">
                                <div class="summary-label">
                                    ID da execução
                                </div>
                                <div class="summary-value">
                                    #<?= (int) $currentJob['id'] ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <div class="fw-semibold">
                            Progresso da execução
                        </div>

                        <div class="small-muted">
                            <?php if ((int) $currentJob['count_only_new'] === 1): ?>

                                <?= (int) $jobStats['inserted'] ?>
                                de
                                <?= (int) $currentJob['product_limit'] ?>
                                produtos processados

                            <?php else: ?>

                                <?= (int) $currentJob['products_processed'] ?>
                                de
                                <?= (int) $currentJob['product_limit'] ?>
                                produtos processados

                            <?php endif; ?>

                            ·
                            <?= $progress ?>%

                            <?php if ((int) $currentJob['count_only_new'] === 1): ?>
                                <span class="text-muted">
                                    (Apenas novos produtos)
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="progress mb-4">
                        <div
                            class="progress-bar progress-bar-striped progress-bar-animated"
                            style="width: <?= $progress ?>%;"
                        >
                            <?= $progress ?>%
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="soft-box">
                                <div class="summary-label">
                                    Produtos processados
                                </div>
                                <div class="summary-value">
                                    <?= $jobStats['processed'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="soft-box">
                                <div class="summary-label">
                                    Produtos cadastrados
                                </div>
                                <div class="summary-value text-success">
                                    <?= $jobStats['inserted'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="soft-box">
                                <div class="summary-label">
                                    Atualizados
                                </div>
                                <div class="summary-value text-warning">
                                    <?= $jobStats['updated'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="soft-box">
                                <div class="summary-label">
                                    Erros
                                </div>
                                <div class="summary-value text-danger">
                                    <?= $jobStats['errors'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="text-muted mb-2">
                            <i class="bi bi-hourglass-split fs-2"></i>
                        </div>
                        <div class="fw-semibold">
                            Nenhuma execução em andamento
                        </div>
                        <div class="small-muted">
                            Não há nenhuma fila sendo processada no momento.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Relatório rápido -->
        <!-- <div class="card dashboard-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Relatório rápido</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-primary">Hoje</button>
                    <button class="btn btn-outline-secondary">7 dias</button>
                    <button class="btn btn-outline-secondary">30 dias</button>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="summary-item">
                            <div class="summary-label">Total de execuções</div>
                            <div class="summary-value">12</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="summary-item">
                            <div class="summary-label">Fontes processadas</div>
                            <div class="summary-value">84</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="summary-item">
                            <div class="summary-label">Taxa média de sucesso</div>
                            <div class="summary-value">42%</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="summary-item">
                            <div class="summary-label">Tempo médio por execução</div>
                            <div class="summary-value">17 min</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fonte</th>
                                <th>Tipo</th>
                                <th>Produtos únicos</th>
                                <th>Taxa de sucesso</th>
                                <th>Última execução</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold">smartwatch</td>
                                <td><span class="badge bg-primary-subtle text-primary source-badge">Keyword</span></td>
                                <td>523</td>
                                <td><span class="badge bg-success-subtle text-success">41%</span></td>
                                <td>Hoje às 02:14</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary">Ver detalhes</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">electronics</td>
                                <td><span class="badge bg-info-subtle text-info source-badge">Categoria</span></td>
                                <td>2.430</td>
                                <td><span class="badge bg-success-subtle text-success">55%</span></td>
                                <td>Hoje às 01:58</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary">Ver detalhes</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">headset gamer</td>
                                <td><span class="badge bg-primary-subtle text-primary source-badge">Keyword</span></td>
                                <td>1.120</td>
                                <td><span class="badge bg-warning-subtle text-warning">36%</span></td>
                                <td>Ontem às 23:40</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary">Ver detalhes</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">air fryer</td>
                                <td><span class="badge bg-primary-subtle text-primary source-badge">Keyword</span></td>
                                <td>315</td>
                                <td><span class="badge bg-danger-subtle text-danger">12%</span></td>
                                <td>Ontem às 23:11</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary">Ver detalhes</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->

        <?php
            $stmt = $conn_pdo->prepare("
                SELECT
                    jip.id AS job_product_id,
                    jip.source_product_id,
                    jip.status AS import_status,

                    p.id,
                    p.name,
                    p.price,
                    p.product_id,
                    p.related,
                    p.date_create AS imported_at,

                    (
                        SELECT i.nome_imagem
                        FROM imagens i
                        WHERE i.usuario_id = p.id
                        ORDER BY i.id ASC
                        LIMIT 1
                    ) AS product_image

                FROM tb_product_import_job_products jip

                INNER JOIN tb_products p
                    ON p.id = jip.product_id

                WHERE p.shop_id = ?

                ORDER BY jip.id DESC

                LIMIT 10
            ");

            $stmt->execute([$shop_id]);

            $latestProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <!-- Últimos produtos cadastrados -->
        <div class="card dashboard-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Últimos produtos cadastrados</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary">Ver catálogo</button>
                </div>
            </div>

            <div class="card-body">
                <?php if (!empty($latestProducts)) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Provider</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                    <th>Cadastrado em</th>
                                    <th class="text-end">Ação</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($latestProducts as $product) { ?>
                                    <?php
                                    $provider = strtolower(
                                        $product['related'] ?? ''
                                    );

                                    $providerName = match ($provider) {
                                        'aliexpress' => 'AliExpress',
                                        'shopee'     => 'Shopee',
                                        'amazon'     => 'Amazon',
                                        default      => ucfirst($provider ?: 'Desconhecido'),
                                    };

                                    $providerClass = match ($provider) {
                                        'aliexpress' => 'bg-danger',
                                        'shopee'     => 'bg-warning',
                                        'amazon'     => 'bg-primary',
                                        default      => 'bg-secondary',
                                    };

                                    $status = $product['import_status'] ?? '';

                                    if ($status === 'inserted') {
                                        $statusLabel = 'Cadastrado';
                                        $statusClass = 'bg-success';
                                    } elseif ($status === 'updated') {
                                        $statusLabel = 'Atualizado';
                                        $statusClass = 'bg-primary';
                                    } elseif ($status === 'exists') {
                                        $statusLabel = 'Já existente';
                                        $statusClass = 'bg-warning text-dark';
                                    } else {
                                        $statusLabel = ucfirst($status ?: 'Processado');
                                        $statusClass = 'bg-secondary';
                                    }

                                    $productId = (int) $product['id'];

                                    $image = !empty($product['product_image']) 
                                        ? CDN_BASE_URL . 'products/' . $productId . '/' . $product['product_image']
                                        : INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg';

                                    $sourceProductId =
                                        $product['source_product_id']
                                        ?? $product['product_id']
                                        ?? '';

                                    $price = (float) ($product['price'] ?? 0);

                                    $importedAt =
                                        $product['imported_at'] ?? null;
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($image)) { ?>
                                                    <img
                                                        src="<?= htmlspecialchars($image); ?>"
                                                        class="product-thumb"
                                                        alt="<?= htmlspecialchars($product['name']); ?>"
                                                    >
                                                <?php } else { ?>
                                                    <div class="product-thumb d-flex align-items-center justify-content-center bg-light text-muted">
                                                        <i class="ti ti-photo"></i>
                                                    </div>
                                                <?php } ?>

                                                <div>
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($product['name']); ?>
                                                    </div>

                                                    <div class="small text-muted">
                                                        Product ID:
                                                        <?= htmlspecialchars($sourceProductId); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge <?= $providerClass; ?>">
                                                <?= htmlspecialchars($providerName); ?>
                                            </span>
                                        </td>

                                        <td>
                                            R$
                                            <?= number_format(
                                                $price,
                                                2,
                                                ',',
                                                '.'
                                            ); ?>
                                        </td>

                                        <td>
                                            <span class="badge <?= $statusClass; ?>">
                                                <?= htmlspecialchars($statusLabel); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?php
                                            if ($importedAt) {
                                                echo date(
                                                    'd/m/Y H:i',
                                                    strtotime($importedAt)
                                                );
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="<?= INCLUDE_PATH_DASHBOARD ?>editar-produto?id=<?= $productId ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Ver produto
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-2">
                            <i class="ti ti-package-off fs-1"></i>
                        </div>

                        <div class="fw-semibold">
                            Nenhum produto processado ainda
                        </div>

                        <div class="text-muted small">
                            Os produtos processados por esta importação aparecerão aqui.
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Últimas execuções -->
        <?php
            $sql = "SELECT
                        id,
                        provider,
                        product_limit,
                        products_processed,
                        products_found,
                        products_inserted,
                        products_updated,
                        products_errors,
                        status,
                        started_at,
                        finished_at,
                        created_at
                    FROM tb_product_import_jobs
                    WHERE shop_id = ?
                    ORDER BY id DESC
                    LIMIT 10";

            $stmt = $conn_pdo->prepare($sql);
            $stmt->execute([$shop_id]);

            $product_import_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Últimas execuções</span>
                <button class="btn btn-sm btn-outline-secondary">Ver histórico completo</button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Execução</th>
                                <th>Tipo</th>
                                <th>Provider</th>
                                <th>Início</th>
                                <th>Duração</th>
                                <th>Encontrados</th>
                                <th>Novos</th>
                                <th>Atualizados</th>
                                <th>Erros</th>
                                <th>Status</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($product_import_jobs)): ?>
                                <?php foreach ($product_import_jobs as $job): ?>
                                    <?php
                                        $provider = ucfirst($job['provider']);

                                        switch ($job['status']) {
                                            case 'pending':
                                                $status_text = 'Aguardando';
                                                $status_class = 'bg-warning text-dark';
                                                break;

                                            case 'running':
                                                $status_text = 'Em andamento';
                                                $status_class = 'bg-primary';
                                                break;

                                            case 'completed':
                                                $status_text = 'Concluído';
                                                $status_class = 'bg-success';
                                                break;

                                            case 'stopped':
                                                $status_text = 'Interrompido';
                                                $status_class = 'bg-secondary';
                                                break;

                                            case 'paused':
                                                $status_text = 'Pausado';
                                                $status_class = 'bg-secondary';
                                                break;

                                            case 'error':
                                                $status_text = 'Erro';
                                                $status_class = 'bg-danger';
                                                break;

                                            default:
                                                $status_text = ucfirst($job['status']);
                                                $status_class = 'bg-secondary';
                                                break;
                                        }

                                        $start_time = $job['started_at'] ?? $job['created_at'];

                                        if (!empty($job['started_at'])) {
                                            $start_timestamp = strtotime($job['started_at']);

                                            if (!empty($job['finished_at'])) {
                                                $end_timestamp = strtotime($job['finished_at']);
                                            } else {
                                                $end_timestamp = time();
                                            }

                                            $duration_seconds = max(
                                                0,
                                                $end_timestamp - $start_timestamp
                                            );

                                            /**
                                             * Converte a duração total para:
                                             * horas, minutos e segundos.
                                             */
                                            $duration_hours = floor(
                                                $duration_seconds / 3600
                                            );

                                            $duration_minutes = floor(
                                                ($duration_seconds % 3600) / 60
                                            );

                                            $duration_remaining_seconds =
                                                $duration_seconds % 60;

                                            $duration_parts = [];

                                            if ($duration_hours > 0) {
                                                $duration_parts[] =
                                                    $duration_hours . 'h';
                                            }

                                            if ($duration_minutes > 0) {
                                                $duration_parts[] =
                                                    $duration_minutes . 'min';
                                            }

                                            if ($duration_remaining_seconds > 0 || empty($duration_parts)) {
                                                $duration_parts[] =
                                                    $duration_remaining_seconds . 's';
                                            }

                                            $duration =
                                                implode(
                                                    ' ',
                                                    $duration_parts
                                                );

                                        } else {
                                            $duration = '-';
                                        }

                                        $start_display = '-';

                                        if (!empty($start_time)) {
                                            $start_timestamp = strtotime($start_time);

                                            if (date('Y-m-d', $start_timestamp) === date('Y-m-d')) {
                                                $start_display = 'Hoje ' . date('H:i', $start_timestamp);
                                            } elseif (date('Y-m-d', $start_timestamp) === date('Y-m-d', strtotime('-1 day'))) {
                                                $start_display = 'Ontem ' . date('H:i', $start_timestamp);
                                            } else {
                                                $start_display = date('d/m/Y H:i', $start_timestamp);
                                            }
                                        }
                                    ?>

                                    <tr>
                                        <td class="fw-semibold">#<?= (int) $job['id']; ?></td>
                                        <td>Importação</td>
                                        <td><?= htmlspecialchars($provider); ?></td>
                                        <td><?= htmlspecialchars($start_display); ?></td>
                                        <td><?= htmlspecialchars($duration); ?></td>
                                        <td><?= !empty($job['started_at']) ? number_format((int) $job['products_processed'], 0, ',', '.') : '-'; ?></td>
                                        <td><?= !empty($job['started_at']) ? number_format((int) $job['products_inserted'], 0, ',', '.') : '-'; ?></td>
                                        <td><?= !empty($job['started_at']) ? number_format((int) $job['products_updated'], 0, ',', '.') : '-'; ?></td>
                                        <td><?= !empty($job['started_at']) ? number_format((int) $job['products_errors'], 0, ',', '.') : '-'; ?></td>
                                        <td>
                                            <span class="badge <?= $status_class; ?>">
                                                <?= htmlspecialchars($status_text); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                data-id="<?= (int) $job['id']; ?>"
                                            >
                                                Detalhes
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        Nenhuma importação encontrada.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Right column -->
    <div class="col-xl-4">

        <?php
            /**
             * Fontes de descoberta
             */
            $sql = "SELECT
                        id,
                        keyword,
                        source
                    FROM tb_product_import_keywords
                    WHERE shop_id = :shop_id
                    ORDER BY priority DESC, id DESC
                    LIMIT 5";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->execute();

            $aliexpressKeywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            /**
             * Total de fontes cadastradas para a loja
             */
            $stmt = $conn_pdo->prepare("
                SELECT COUNT(*)
                FROM tb_product_import_keywords
                WHERE shop_id = :shop_id
            ");

            $stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
            $stmt->execute();

            $totalKeywords = (int) $stmt->fetchColumn();
        ?>

        <!-- Fontes de descoberta -->
        <div class="card dashboard-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Fontes de descoberta</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNovaFonte">
                        <i class="bi bi-plus-lg me-1"></i>
                        Nova fonte
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($aliexpressKeywords)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-search fs-1 text-muted"></i>
                        </div>
                        <h5 class="mb-2">
                            Nenhuma fonte cadastrada
                        </h5>
                        <p class="text-muted mb-4">
                            Ainda não existem keywords configuradas para a descoberta de produtos.
                            Você pode cadastrar automaticamente um conjunto de fontes padrão.
                        </p>
                        <form method="POST" action="<?= INCLUDE_PATH_DASHBOARD ?>back-end/import-products.php">
                            <button
                                type="submit"
                                name="btnSeedDefaultKeywords"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-database-add me-1"></i>
                                Cadastrar fontes padrão
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fonte</th>
                                    <th>Status</th>
                                    <th class="text-end">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aliexpressKeywords as $keyword): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($keyword['keyword']) ?>
                                            </div>
                                            <div class="small text-muted">
                                                Keyword · <?= htmlspecialchars($keyword['source']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                Ativa
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                data-id="<?= (int) $keyword['id'] ?>"
                                            >
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="small text-muted">
                            <?= $totalKeywords; ?> fontes cadastradas
                        </div>
                        <button class="btn btn-outline-secondary">
                            Ver todas as fontes
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alertas -->
        <!-- <div class="card dashboard-card mb-4">
            <div class="card-header">
                Alertas e observações
            </div>
            <div class="card-body">
                <div class="timeline-item">
                    <div class="fw-semibold">2 fontes pausadas por baixo desempenho</div>
                    <div class="small-muted">As fontes “air fryer” e “smart tv 32” não trouxeram novos produtos nas últimas execuções.</div>
                </div>

                <div class="timeline-item">
                    <div class="fw-semibold">1 erro recente na Amazon</div>
                    <div class="small-muted">O sistema registrou limite temporário de requisições e reagendou a tentativa.</div>
                </div>

                <div class="timeline-item">
                    <div class="fw-semibold">18 produtos aguardando publicação</div>
                    <div class="small-muted">Há produtos encontrados que ainda não foram publicados na loja.</div>
                </div>
            </div>
        </div> -->

        <!-- Resumo operacional -->
        <!-- <div class="card dashboard-card">
            <div class="card-header">
                Resumo operacional
            </div>
            <div class="card-body">
                <div class="soft-box mb-3">
                    <div class="summary-label">Modo de execução</div>
                    <div class="summary-value">Automático</div>
                    <div class="small-muted mt-1">Janela programada entre 23:00 e 06:00</div>
                </div>

                <div class="soft-box mb-3">
                    <div class="summary-label">Providers ativos</div>
                    <div class="summary-value">Amazon + AliExpress</div>
                    <div class="small-muted mt-1">2 integrações ativas e operacionais</div>
                </div>

                <div class="soft-box mb-3">
                    <div class="summary-label">Heartbeat do worker</div>
                    <div class="summary-value text-success">Online agora</div>
                    <div class="small-muted mt-1">Último heartbeat há 18 segundos</div>
                </div>

                <div class="soft-box">
                    <div class="summary-label">Fila de publicação</div>
                    <div class="summary-value">18 pendentes</div>
                    <div class="small-muted mt-1">7 produtos prontos para publicação automática</div>
                </div>
            </div>
        </div> -->

    </div>
</div>
</div>

<?php
$sql = "
    SELECT 
        c.id,
        c.name AS category_name,
        p.name AS parent_name
    FROM tb_categories c
    LEFT JOIN tb_categories p ON p.id = c.parent_category AND c.parent_category != 1
    WHERE c.shop_id = :shop_id
    ORDER BY c.id DESC
";

$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $id, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [];

foreach ($items as $category) {
    if (!empty($category['parent_name'])) {
        $name = $category['parent_name'] . " > " . $category['category_name'];
    } else {
        $name = $category['category_name'];
    }

    $categories[] = [
        'id' => $category['id'],
        'name' => $name
    ];
}
?>

<!-- Modal: Nova importação -->
<div class="modal fade" id="modalNovaImportacao" tabindex="-1" aria-labelledby="modalNovaImportacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= INCLUDE_PATH_DASHBOARD ?>back-end/import-products.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaImportacaoLabel">Nova importação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <?php if ((int) $totalKeywords === 0) { ?>
                        <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                            <i class="bx bx-error-circle fs-4 me-2"></i>

                            <div>
                                <strong>Nenhuma <b>fonte</b> cadastrada</strong>

                                <div class="mt-1">
                                    Para iniciar uma importação, você precisa
                                    cadastrar pelo menos uma <b>fonte</b>.
                                </div>

                                <div>
                                    Adicione suas <b>fontes</b> e tente novamente.
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="provider" class="form-label">Provider</label>
                            <select class="form-select" name="provider" id="provider">
                                <option value="" disabled selected>Selecione um provider</option>
                                <option value="Amazon">Amazon</option>
                                <option value="AliExpress">AliExpress</option>
                                <option value="Shopee">Shopee</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Associar a uma Categoria</label>

                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">Nenhuma</option>

                                <?php foreach ($categories as $c) { ?>
                                    <option value="<?= $c['id']; ?>">
                                        <?= htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <label class="form-check mt-1">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="save_provider_categories"
                                    value="1"
                                    id="save_provider_categories"
                                >

                                <span class="form-check-label">
                                    Cadastrar categorias do Provider

                                    <span
                                        class="ms-1 text-muted"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Cadastra automaticamente as categorias originais do Provider quando elas ainda não existirem."
                                        style="cursor: help;"
                                    >
                                        <i class="bx bx-info-circle"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label for="product_limit" class="form-label">Limite de produtos</label>
                            <input type="number" name="product_limit" id="product_limit" class="form-control" value="100">

                            <label class="form-check mt-2">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="count_only_new"
                                    value="1"
                                    id="count_only_new"
                                >
                                <span class="form-check-label">
                                    Contar somente produtos novos
                                    <span
                                        class="ms-1 text-muted"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Quando ativado, o limite será aplicado somente aos produtos novos cadastrados. Produtos que já existem não consumirão o limite."
                                        style="cursor: help;"
                                    >
                                        <i class="bx bx-info-circle"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <label for="auto_publish" class="form-label">Publicar automaticamente?</label>
                            <select class="form-select" name="auto_publish" id="auto_publish">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="update_existing" class="form-label">Atualizar já cadastrados?</label>
                            <select class="form-select" name="update_existing" id="update_existing">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="observations" class="form-label">Observações</label>
                            <textarea class="form-control" name="observations" id="observations" rows="4" placeholder="Opcional: descreva o objetivo desta execução."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        name="btnAddImport"
                        class="btn btn-primary"
                        <?= ((int) $totalKeywords === 0) ? 'disabled' : ''; ?>
                    >
                        Iniciar importação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// document.addEventListener('DOMContentLoaded', function () {
//     const categorySelect = document.getElementById('category_id');
//     const providerCheckbox = document.getElementById('save_provider_categories');

//     providerCheckbox.addEventListener('change', function () {
//         if (this.checked) {
//             categorySelect.value = '';
//             categorySelect.disabled = true;
//         } else {
//             categorySelect.disabled = false;
//         }
//     });

//     document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
//         new bootstrap.Tooltip(element);
//     });
// });
document.addEventListener('DOMContentLoaded', function () {
    const providerSelect = document.getElementById('provider');
    const categorySelect = document.getElementById('category_id');
    const providerCheckbox = document.getElementById('save_provider_categories');

    /**
     * Atualiza o estado dos campos de categoria
     */
    function updateCategoryFields() {

        const provider = providerSelect.value;

    }

    /**
     * Quando alterar o provider
     */
    providerSelect.addEventListener('change', function () {
        updateCategoryFields();
    });

    /**
     * Quando marcar/desmarcar:
     * "Cadastrar categorias do Provider"
     */
    providerCheckbox.addEventListener('change', function () {

        if (this.checked) {

            categorySelect.value = '';
            categorySelect.disabled = true;

        } else {

            categorySelect.disabled = false;

        }
    });

    /**
     * Estado inicial
     */
    updateCategoryFields();

    /**
     * Tooltips Bootstrap
     */
    document
        .querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(function (element) {
            new bootstrap.Tooltip(element);
        });
});
</script>

<!-- Modal Nova Fonte -->
<div class="modal fade" id="modalNovaFonte" tabindex="-1" aria-labelledby="modalNovaFonteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form
                method="POST"
                action="<?= INCLUDE_PATH_DASHBOARD ?>back-end/import-products.php"
                id="formNovaFonte"
            >
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaFonteLabel">
                        Nova fonte
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            Keywords
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="inputKeyword"
                                placeholder="Ex.: smartwatch"
                                autocomplete="off"
                            >
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                id="btnAdicionarKeyword"
                            >
                                <i class="bi bi-plus-lg me-1"></i>
                                Adicionar
                            </button>
                        </div>
                        <div class="form-text">
                            Adicione uma ou várias keywords para importar produtos.
                        </div>
                    </div>

                    <!-- Lista de keywords -->
                    <div id="listaKeywords" class="d-flex flex-column gap-2"></div>

                    <!-- Mensagem quando não houver keywords -->
                    <div
                        id="semKeywords"
                        class="text-center text-muted py-3"
                    >
                        <i class="bi bi-tags fs-3 d-block mb-2"></i>
                        Nenhuma keyword adicionada.
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        name="btnAddKeywords"
                        id="btnAddKeywords"
                        disabled
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Cadastrar fontes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputKeyword = document.getElementById('inputKeyword');
    const btnAdicionar = document.getElementById('btnAdicionarKeyword');
    const listaKeywords = document.getElementById('listaKeywords');
    const semKeywords = document.getElementById('semKeywords');
    const btnSalvar = document.getElementById('btnAddKeywords');
    const form = document.getElementById('formNovaFonte');

    let keywords = [];

    function atualizarLista() {
        listaKeywords.innerHTML = '';

        semKeywords.style.display =
            keywords.length === 0
                ? 'block'
                : 'none';

        btnSalvar.disabled =
            keywords.length === 0;

        keywords.forEach(function (keyword, index) {
            const item = document.createElement('div');

            item.className =
                'd-flex align-items-center justify-content-between border rounded px-3 py-2';

            item.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-tag text-primary"></i>

                    <span class="fw-semibold">
                        ${escapeHtml(keyword)}
                    </span>
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger btn-remover-keyword"
                    data-index="${index}"
                >
                    <i class="bi bi-trash"></i>
                </button>
            `;

            listaKeywords.appendChild(item);
        });

        document
            .querySelectorAll('.btn-remover-keyword')
            .forEach(function (button) {

                button.addEventListener('click', function () {

                    const index =
                        parseInt(
                            this.dataset.index,
                            10
                        );

                    keywords.splice(index, 1);

                    atualizarLista();
                });
            });
    }

    function adicionarKeyword() {
        const keyword =
            inputKeyword.value.trim();

        if (!keyword) {
            inputKeyword.focus();
            return;
        }

        /**
         * Evita keywords duplicadas
         */
        const keywordNormalizada =
            keyword.toLowerCase();

        const jaExiste =
            keywords.some(function (item) {
                return item.toLowerCase() === keywordNormalizada;
            });

        if (jaExiste) {
            inputKeyword.value = '';
            inputKeyword.focus();
            return;
        }

        keywords.push(keyword);

        inputKeyword.value = '';

        atualizarLista();

        inputKeyword.focus();
    }

    btnAdicionar.addEventListener(
        'click',
        adicionarKeyword
    );

    inputKeyword.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                adicionarKeyword();
            }
        }
    );

    form.addEventListener(
        'submit',
        function () {
            /**
             * Remove inputs antigos
             */
            form
                .querySelectorAll(
                    'input[name="keywords[]"]'
                )
                .forEach(function (input) {
                    input.remove();
                });

            /**
             * Cria o array que será enviado
             *
             * keywords[]=smartwatch
             * keywords[]=headset
             * keywords[]=iphone
             */
            keywords.forEach(function (keyword) {
                const input =
                    document.createElement('input');

                input.type = 'hidden';

                input.name = 'keywords[]';

                input.value = keyword;

                form.appendChild(input);
            });
        }
    );

    /**
     * Limpa o modal ao fechar
     */
    document
        .getElementById('modalNovaFonte')
        .addEventListener(
            'hidden.bs.modal',
            function () {
                keywords = [];

                inputKeyword.value = '';

                atualizarLista();
            }
        );

    function escapeHtml(text) {
        const div =
            document.createElement('div');

        div.textContent = text;

        return div.innerHTML;
    }

    atualizarLista();
});
</script>