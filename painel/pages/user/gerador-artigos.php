<style>
    .product-thumb {
        width: 54px;
        min-width: 54px;
        height: 54px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
    }

    .product-name {
        white-space: nowrap;
        width: max-content;
        max-width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .queue-progress {
        min-width: 120px;
    }

    .queue-status {
        min-width: 110px;
    }

    @media (max-width: 767.98px) {
        .product-name {
            max-width: 220px;
        }

        .queue-progress {
            min-width: 90px;
        }
    }
</style>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">SEO</div>

                <h2 class="page-title">
                    Conteúdo SEO
                </h2>

                <div class="text-secondary mt-1">
                    Gerencie as filas de geração de artigos e acompanhe o processamento dos conteúdos.
                </div>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="bx bx-history me-1"></i>
                        Histórico
                    </a>

                    <button
                        type="button"
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#new-article-generation-modal"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Nova geração
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <?php
            /*
            * =====================================================
            * INDICADORES DA FILA DE ARTIGOS
            * =====================================================
            */

            $shopId = (int) $_SESSION['shop_id'];

            $stmtQueueStats = $conn_pdo->prepare("
                SELECT
                    COUNT(*) AS total_count,

                    SUM(
                        CASE
                            WHEN q.status = 'pending'
                            THEN 1
                            ELSE 0
                        END
                    ) AS pending_count,

                    SUM(
                        CASE
                            WHEN q.status = 'completed'
                            THEN 1
                            ELSE 0
                        END
                    ) AS completed_count,

                    SUM(
                        CASE
                            WHEN q.status = 'failed'
                            THEN 1
                            ELSE 0
                        END
                    ) AS failed_count,

                    SUM(
                        CASE
                            WHEN q.status = 'processing'
                            THEN 1
                            ELSE 0
                        END
                    ) AS processing_count

                FROM tb_seo_article_queue q

                INNER JOIN tb_seo_article_jobs j
                    ON j.id = q.job_id

                WHERE j.shop_id = :shop_id
            ");

            $stmtQueueStats->execute([
                ':shop_id' => $shopId
            ]);

            $queueStats = $stmtQueueStats->fetch(PDO::FETCH_ASSOC);

            $totalQueue = (int) ($queueStats['total_count'] ?? 0);
            $pendingQueue = (int) ($queueStats['pending_count'] ?? 0);
            $completedQueue = (int) ($queueStats['completed_count'] ?? 0);
            $failedQueue = (int) ($queueStats['failed_count'] ?? 0);
            $processingQueue = (int) ($queueStats['processing_count'] ?? 0);

            $pendingPercent = $totalQueue > 0
                ? (int) round(($pendingQueue / $totalQueue) * 100)
                : 0;

            $completedPercent = $totalQueue > 0
                ? (int) round(($completedQueue / $totalQueue) * 100)
                : 0;

            $failedPercent = $totalQueue > 0
                ? (int) round(($failedQueue / $totalQueue) * 100)
                : 0;

            $processingPercent = $totalQueue > 0
                ? (int) round(($processingQueue / $totalQueue) * 100)
                : 0;
        ?>

        <!-- Indicadores -->
        <div class="row row-deck row-cards mb-3">

            <!-- Na fila -->
            <div class="col-sm-6 col-lg-3">
                <div class="card p-0 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-blue-lt text-blue">
                                <i class="bx bx-time-five fs-2"></i>
                            </div>

                            <div class="ms-3">
                                <div class="text-secondary">
                                    Na fila
                                </div>

                                <div class="h2 mb-0">
                                    <?= number_format($pendingQueue, 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="progress progress-sm">
                                <div
                                    class="progress-bar bg-blue"
                                    style="width: <?= $pendingPercent; ?>%"
                                ></div>
                            </div>

                            <div class="text-secondary small mt-2">
                                <?= $pendingPercent; ?>% aguardando processamento
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gerados -->
            <div class="col-sm-6 col-lg-3">
                <div class="card p-0 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-green-lt text-green">
                                <i class="bx bx-check-circle fs-2"></i>
                            </div>

                            <div class="ms-3">
                                <div class="text-secondary">
                                    Gerados
                                </div>

                                <div class="h2 mb-0">
                                    <?= number_format($completedQueue, 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="progress progress-sm">
                                <div
                                    class="progress-bar bg-green"
                                    style="width: <?= $completedPercent; ?>%"
                                ></div>
                            </div>

                            <div class="text-secondary small mt-2">
                                <?= $completedPercent; ?>% concluídos
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Com erro -->
            <div class="col-sm-6 col-lg-3">
                <div class="card p-0 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-red-lt text-red">
                                <i class="bx bx-error-circle fs-2"></i>
                            </div>

                            <div class="ms-3">
                                <div class="text-secondary">
                                    Com erro
                                </div>

                                <div class="h2 mb-0">
                                    <?= number_format($failedQueue, 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="progress progress-sm">
                                <div
                                    class="progress-bar bg-red"
                                    style="width: <?= $failedPercent; ?>%"
                                ></div>
                            </div>

                            <div class="text-secondary small mt-2">
                                <?= $failedPercent; ?>% com falha
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processando -->
            <div class="col-sm-6 col-lg-3">
                <div class="card p-0 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-purple-lt text-purple">
                                <i class="bx bx-loader-alt fs-2"></i>
                            </div>

                            <div class="ms-3">
                                <div class="text-secondary">
                                    Em processamento
                                </div>

                                <div class="h2 mb-0">
                                    <?= number_format($processingQueue, 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="progress progress-sm">
                                <div
                                    class="progress-bar bg-purple"
                                    style="width: <?= $processingPercent; ?>%"
                                ></div>
                            </div>

                            <div class="text-secondary small mt-2">
                                <?= $processingPercent; ?>% sendo processados
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php
            /*
            * =====================================================
            * ÚLTIMAS FILAS DE CRIAÇÃO
            * =====================================================
            */

            $shopId = (int) $_SESSION['shop_id'];

            $stmtJobs = $conn_pdo->prepare("
                SELECT
                    j.id,
                    j.articles_quantity,
                    j.tone,
                    j.article_size,
                    j.generate_meta,
                    j.include_products,
                    j.publish_mode,
                    j.status,
                    j.created_at,

                    COALESCE(jp.products_count, 0) AS products_count,

                    COALESCE(q.total_count, 0) AS total_count,
                    COALESCE(q.pending_count, 0) AS pending_count,
                    COALESCE(q.processing_count, 0) AS processing_count,
                    COALESCE(q.completed_count, 0) AS completed_count,
                    COALESCE(q.failed_count, 0) AS failed_count,
                    COALESCE(q.cancelled_count, 0) AS cancelled_count

                FROM tb_seo_article_jobs j

                LEFT JOIN (
                    SELECT
                        job_id,
                        COUNT(*) AS products_count
                    FROM tb_seo_article_job_products
                    GROUP BY job_id
                ) jp
                    ON jp.job_id = j.id

                LEFT JOIN (
                    SELECT
                        job_id,
                        COUNT(*) AS total_count,

                        SUM(
                            CASE
                                WHEN status = 'pending'
                                THEN 1
                                ELSE 0
                            END
                        ) AS pending_count,

                        SUM(
                            CASE
                                WHEN status = 'processing'
                                THEN 1
                                ELSE 0
                            END
                        ) AS processing_count,

                        SUM(
                            CASE
                                WHEN status = 'completed'
                                THEN 1
                                ELSE 0
                            END
                        ) AS completed_count,

                        SUM(
                            CASE
                                WHEN status = 'failed'
                                THEN 1
                                ELSE 0
                            END
                        ) AS failed_count,

                        SUM(
                            CASE
                                WHEN status = 'cancelled'
                                THEN 1
                                ELSE 0
                            END
                        ) AS cancelled_count

                    FROM tb_seo_article_queue
                    GROUP BY job_id
                ) q
                    ON q.job_id = j.id

                WHERE j.shop_id = :shop_id

                ORDER BY j.id DESC

                LIMIT 10
            ");

            $stmtJobs->execute([
                ':shop_id' => $shopId
            ]);

            $articleJobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <!-- Filas -->
        <div class="card p-0 shadow-sm mb-3">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i class="bx bx-list-ul me-2 text-primary"></i>
                        Últimas filas de criação
                    </h3>

                    <div class="text-secondary small mt-1">
                        Acompanhe as últimas solicitações de geração de artigos e o andamento de cada fila.
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Fila</th>
                            <th>Artigos</th>
                            <th>Produtos</th>
                            <th>Configuração</th>
                            <th>Progresso</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($articleJobs)): ?>

                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty">

                                        <div class="empty-img">
                                            <i
                                                class="bx bx-list-ul text-secondary"
                                                style="font-size: 48px;"
                                            ></i>
                                        </div>

                                        <p class="empty-title">
                                            Nenhuma fila encontrada
                                        </p>

                                        <p class="empty-subtitle text-secondary">
                                            As filas de geração de artigos aparecerão aqui.
                                        </p>

                                    </div>
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($articleJobs as $job): ?>

                                <?php
                                    $total = (int) $job['total_count'];
                                    $completed = (int) $job['completed_count'];
                                    $failed = (int) $job['failed_count'];
                                    $processing = (int) $job['processing_count'];
                                    $pending = (int) $job['pending_count'];
                                    $cancelled = (int) $job['cancelled_count'];

                                    /*
                                    * O total esperado vem da configuração do job.
                                    * Isso evita problemas caso a fila ainda esteja
                                    * sendo criada ou algum registro tenha sido removido.
                                    */
                                    $articlesQuantity = (int) $job['articles_quantity'];

                                    $processed = $completed + $failed + $cancelled;

                                    $progress = $articlesQuantity > 0
                                        ? (int) round(($processed / $articlesQuantity) * 100)
                                        : 0;

                                    $progress = min(100, max(0, $progress));

                                    /*
                                    * Status visual do job
                                    */
                                    if ($cancelled === $articlesQuantity && $articlesQuantity > 0) {
                                        $statusLabel = 'Cancelada';
                                        $statusClass = 'bg-secondary';
                                        $statusIcon = 'bx-x-circle';
                                        $progressClass = 'bg-secondary';

                                    } elseif ($failed === $articlesQuantity && $articlesQuantity > 0) {
                                        $statusLabel = 'Com erro';
                                        $statusClass = 'bg-danger';
                                        $statusIcon = 'bx-error-circle';
                                        $progressClass = 'bg-red';

                                    } elseif ($processing > 0) {
                                        $statusLabel = 'Processando';
                                        $statusClass = 'bg-purple-lt text-purple';
                                        $statusIcon = 'bx-loader-alt';
                                        $progressClass = 'bg-purple';

                                    } elseif ($pending > 0) {
                                        $statusLabel = 'Na fila';
                                        $statusClass = 'bg-primary';
                                        $statusIcon = 'bx-time-five';
                                        $progressClass = 'bg-blue';

                                    } elseif ($completed === $articlesQuantity && $articlesQuantity > 0) {
                                        $statusLabel = 'Concluída';
                                        $statusClass = 'bg-success';
                                        $statusIcon = 'bx-check-circle';
                                        $progressClass = 'bg-green';

                                    } elseif ($failed > 0) {
                                        $statusLabel = 'Parcial';
                                        $statusClass = 'bg-warning';
                                        $statusIcon = 'bx-error-circle';
                                        $progressClass = 'bg-yellow';

                                    } else {
                                        $statusLabel = 'Na fila';
                                        $statusClass = 'bg-primary';
                                        $statusIcon = 'bx-time-five';
                                        $progressClass = 'bg-blue';
                                    }

                                    $productsCount = (int) $job['products_count'];

                                    $articleSizeLabels = [
                                        'short' => 'Curto',
                                        'medium' => 'Médio',
                                        'long' => 'Longo'
                                    ];

                                    $toneLabels = [
                                        'professional' => 'Profissional',
                                        'informative' => 'Informativo',
                                        'commercial' => 'Comercial',
                                        'casual' => 'Casual'
                                    ];

                                    $articleSizeLabel = $articleSizeLabels[$job['article_size']] ?? 'Não informado';
                                    $toneLabel = $toneLabels[$job['tone']] ?? 'Não informado';

                                    /*
                                    * Data
                                    */
                                    $createdAt = !empty($job['created_at'])
                                        ? date('d/m/Y H:i', strtotime($job['created_at']))
                                        : '-';

                                    $createdDate = !empty($job['created_at'])
                                        ? date('d/m/Y', strtotime($job['created_at']))
                                        : '-';

                                    $createdTime = !empty($job['created_at'])
                                        ? date('H:i', strtotime($job['created_at']))
                                        : '-';
                                ?>

                                <tr>

                                    <!-- Fila -->
                                    <td>
                                        <div class="fw-bold">
                                            #<?= (int) $job['id']; ?>
                                        </div>

                                        <div class="text-secondary small">
                                            Geração de artigos
                                        </div>
                                    </td>

                                    <!-- Artigos -->
                                    <td>
                                        <div class="fw-bold">
                                            <?= $articlesQuantity; ?>
                                        </div>

                                        <div class="text-secondary small">
                                            <?= $articlesQuantity === 1 ? 'artigo' : 'artigos'; ?>
                                        </div>
                                    </td>

                                    <!-- Produtos -->
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-primary">
                                                <?= $productsCount; ?>
                                                <?= $productsCount === 1 ? 'produto' : 'produtos'; ?>
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Configuração -->
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">

                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($articleSizeLabel, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>

                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($toneLabel, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>

                                        </div>
                                    </td>

                                    <!-- Progresso -->
                                    <td>
                                        <div class="queue-progress">

                                            <div class="d-flex justify-content-between mb-1">

                                                <span class="text-secondary small">
                                                    <?= $processed; ?> / <?= $articlesQuantity; ?>
                                                </span>

                                                <span class="text-secondary small">
                                                    <?= $progress; ?>%
                                                </span>

                                            </div>

                                            <div class="progress progress-sm">

                                                <div
                                                    class="progress-bar <?= $progressClass; ?>"
                                                    style="width: <?= $progress; ?>%"
                                                ></div>

                                            </div>

                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="queue-status">

                                        <span class="badge <?= $statusClass; ?>">
                                            <i class="bx <?= $statusIcon; ?> me-1"></i>
                                            <?= $statusLabel; ?>
                                        </span>

                                    </td>

                                    <!-- Data -->
                                    <td>
                                        <div class="text-secondary">
                                            <?= $createdDate; ?>
                                        </div>

                                        <div class="text-secondary small">
                                            <?= $createdTime; ?>
                                        </div>
                                    </td>

                                    <!-- Ações -->
                                    <td>

                                        <div class="dropdown">

                                            <button
                                                class="btn btn-icon btn-ghost-secondary"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                            >
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">

                                                <a
                                                    href="#"
                                                    class="dropdown-item"
                                                >
                                                    <i class="bx bx-show me-2"></i>
                                                    Ver detalhes
                                                </a>

                                                <?php if ($pending > 0 || $processing > 0): ?>

                                                    <div class="dropdown-divider"></div>

                                                    <a
                                                        href="#"
                                                        class="dropdown-item text-danger"
                                                    >
                                                        <i class="bx bx-x-circle me-2"></i>
                                                        Cancelar fila
                                                    </a>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between">
                <div class="text-secondary small">
                    Exibindo as últimas filas de criação.
                </div>

                <!-- <a
                    href="#"
                    class="btn btn-sm btn-outline-primary"
                >
                    Ver todas
                    <i class="bx bx-right-arrow-alt ms-1"></i>
                </a> -->
            </div>
        </div>

        <!-- Como funciona -->
        <div class="card p-0 shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bx bx-bulb me-2 text-warning"></i>
                    Como funciona?
                </h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex mb-4 mb-lg-0">
                            <span class="avatar bg-blue-lt text-blue me-3">
                                <i class="bx bx-package"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    1. Selecione os produtos
                                </div>

                                <div class="text-secondary small mt-1">
                                    Escolha os produtos que deseja recomendar aos leitores.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex mb-4 mb-lg-0">
                            <span class="avatar bg-purple-lt text-purple me-3">
                                <i class="bx bx-edit-alt"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    2. Configure os artigos
                                </div>

                                <div class="text-secondary small mt-1">
                                    Defina a quantidade, palavras-chave, tom e outras configurações.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex mb-4 mb-md-0">
                            <span class="avatar bg-yellow-lt text-yellow me-3">
                                <i class="bx bx-loader-alt"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    3. Worker gera os conteúdos
                                </div>

                                <div class="text-secondary small mt-1">
                                    Os artigos são processados automaticamente em segundo plano.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex">
                            <span class="avatar bg-green-lt text-green me-3">
                                <i class="bx bx-cart"></i>
                            </span>

                            <div>
                                <div class="fw-bold">
                                    4. Produtos são recomendados
                                </div>

                                <div class="text-secondary small mt-1">
                                    Os produtos selecionados podem ser apresentados ao final dos artigos.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal: Nova geração -->
<div
    class="modal modal-blur fade"
    id="new-article-generation-modal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form
                id="article-generator-form"
                method="POST"
                enctype="multipart/form-data"
            >
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <i class="bx bx-file-plus me-2 text-primary"></i>
                            Nova geração de artigos
                        </h5>

                        <div class="text-secondary small mt-1">
                            Configure os artigos e selecione os produtos que serão apresentados como recomendações no conteúdo.
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"
                    ></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-lg-8">

                            <!-- Quantidade de artigos -->
                            <div class="mb-4">
                                <label class="form-label required">
                                    Quantidade de artigos
                                </label>

                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bx bx-file"></i>
                                            </span>

                                            <input
                                                type="number"
                                                name="articles_quantity"
                                                id="articles_quantity"
                                                class="form-control"
                                                value="100"
                                                min="1"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-hint mt-2 mt-md-0">
                                            Informe quantos artigos deseja gerar utilizando as configurações definidas abaixo.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produtos -->
                            <div class="mb-4">
                                <label class="form-label required">
                                    Produtos recomendados
                                </label>

                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bx bx-search"></i>
                                        </span>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="product-search"
                                            placeholder="Busque por nome, SKU ou código do produto"
                                            autocomplete="off"
                                        >

                                        <button
                                            class="btn btn-primary"
                                            type="button"
                                            id="product-search-button"
                                        >
                                            <i class="bx bx-search me-1"></i>
                                            Buscar
                                        </button>
                                    </div>

                                    <div
                                        id="product-search-results"
                                        class="list-group position-absolute w-100 shadow-sm d-none"
                                        style="z-index: 1050;"
                                    ></div>
                                </div>

                                <div class="form-hint">
                                    Selecione os produtos que poderão ser utilizados como recomendações nos artigos gerados.
                                </div>
                            </div>

                            <!-- Produtos selecionados -->
                            <div class="card p-0 border mb-4">
                                <div class="card-header bg-light">
                                    <div>
                                        <h3 class="card-title">
                                            <i class="bx bx-package me-2 text-primary"></i>
                                            Produtos selecionados
                                        </h3>

                                        <div class="text-secondary small mt-1">
                                            Os produtos selecionados poderão ser apresentados ao final dos artigos como sugestões de compra.
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>SKU</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>

                                        <tbody id="selected-products">
                                            <tr id="no-selected-products">
                                                <td colspan="3" class="text-center py-5">
                                                    <div class="empty">
                                                        <div class="empty-img">
                                                            <i
                                                                class="bx bx-package text-secondary"
                                                                style="font-size: 48px;"
                                                            ></i>
                                                        </div>

                                                        <p class="empty-title">
                                                            Nenhum produto selecionado
                                                        </p>

                                                        <p class="empty-subtitle text-secondary">
                                                            Selecione os produtos que poderão ser recomendados nos artigos.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-footer">
                                    <span
                                        class="badge bg-primary"
                                        id="selected-count"
                                    >
                                        0 produtos
                                    </span>
                                </div>
                            </div>

                            <!-- Palavras-chave -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Palavras-chave
                                </label>

                                <textarea
                                    name="keywords"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Ex.: melhores notebooks custo-benefício, notebook para estudar, notebook para trabalho"
                                ></textarea>

                                <div class="form-hint">
                                    Opcional. Informe palavras-chave ou temas que poderão ser utilizados como referência na geração dos artigos.
                                </div>
                            </div>

                            <!-- Tom -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Tom do conteúdo
                                </label>

                                <select
                                    name="tone"
                                    class="form-select"
                                >
                                    <option value="professional">
                                        Profissional
                                    </option>

                                    <option
                                        value="informative"
                                        selected
                                    >
                                        Informativo
                                    </option>

                                    <option value="commercial">
                                        Comercial
                                    </option>

                                    <option value="casual">
                                        Casual
                                    </option>
                                </select>

                                <div class="form-hint">
                                    Define a forma como os conteúdos serão escritos.
                                </div>
                            </div>

                            <!-- Configurações avançadas -->
                            <div
                                class="accordion"
                                id="advanced-settings"
                            >
                                <div class="accordion-item">

                                    <h2 class="accordion-header">
                                        <button
                                            class="accordion-button collapsed"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#advanced-content"
                                        >
                                            <i class="bx bx-cog me-2"></i>
                                            Configurações avançadas
                                        </button>
                                    </h2>

                                    <div
                                        id="advanced-content"
                                        class="accordion-collapse collapse"
                                    >
                                        <div class="accordion-body">

                                            <!-- Tamanho -->
                                            <div class="mb-4">
                                                <label class="form-label">
                                                    Tamanho aproximado do artigo
                                                </label>

                                                <select
                                                    name="article_size"
                                                    class="form-select"
                                                >
                                                    <option value="short">
                                                        Curto — 500 a 800 palavras
                                                    </option>

                                                    <option
                                                        value="medium"
                                                        selected
                                                    >
                                                        Médio — 800 a 1.500 palavras
                                                    </option>

                                                    <option value="long">
                                                        Longo — 1.500 a 2.500 palavras
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- SEO -->
                                            <div class="mb-3">
                                                <label class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="generate_meta"
                                                        checked
                                                    >

                                                    <span class="form-check-label">
                                                        Gerar automaticamente Meta Title e Meta Description
                                                    </span>
                                                </label>
                                            </div>

                                            <!-- Produtos -->
                                            <div class="mb-4">
                                                <label class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="include_products"
                                                        checked
                                                    >

                                                    <span class="form-check-label">
                                                        Incluir os produtos selecionados como recomendações de compra
                                                    </span>
                                                </label>

                                                <div class="form-hint ms-4">
                                                    Os produtos poderão ser exibidos ao final do artigo com suas respectivas informações e links.
                                                </div>
                                            </div>

                                            <!-- Publicação -->
                                            <div class="mb-4">
                                                <label class="form-label">
                                                    Publicação
                                                </label>

                                                <select
                                                    name="publish_mode"
                                                    class="form-select"
                                                >
                                                    <option value="draft">
                                                        Salvar como rascunho
                                                    </option>

                                                    <option value="published">
                                                        Publicar automaticamente
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Títulos -->
                                            <div class="mb-4">
                                                <label class="form-label">
                                                    Títulos personalizados
                                                </label>

                                                <input
                                                    type="file"
                                                    name="titles"
                                                    id="titles"
                                                    class="form-control"
                                                    accept=".txt,.csv"
                                                >

                                                <div class="form-hint">
                                                    Opcional. Envie um arquivo <strong>.TXT</strong> ou <strong>.CSV</strong> contendo os títulos dos artigos.
                                                    Utilize um título por linha. Cada título será utilizado para gerar um artigo.
                                                </div>

                                                <div class="mt-2">
                                                    <span class="text-secondary small me-2">
                                                        Arquivos de exemplo:
                                                    </span>

                                                    <a
                                                        href="<?= INCLUDE_PATH; ?>assets/examples/article-titles.txt"
                                                        download
                                                        class="btn btn-sm btn-outline-secondary me-1"
                                                    >
                                                        <i class="bx bx-download me-1"></i>
                                                        Exemplo TXT
                                                    </a>

                                                    <a
                                                        href="<?= INCLUDE_PATH; ?>assets/examples/article-titles.csv"
                                                        download
                                                        class="btn btn-sm btn-outline-secondary"
                                                    >
                                                        <i class="bx bx-download me-1"></i>
                                                        Exemplo CSV
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Prompt -->
                                            <div>
                                                <label class="form-label">
                                                    Prompt personalizado
                                                </label>

                                                <textarea
                                                    name="custom_prompt"
                                                    id="custom_prompt"
                                                    class="form-control"
                                                    rows="5"
                                                    placeholder="Ex.: Crie conteúdos detalhados e informativos, focados em pessoas que estão pesquisando antes de comprar. Compare características relevantes e apresente os produtos selecionados como recomendações ao final do conteúdo."
                                                ></textarea>

                                                <div class="form-hint">
                                                    Opcional. Adicione instruções específicas para orientar a IA na geração dos artigos.
                                                    Essas informações serão utilizadas como complemento às configurações selecionadas.
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Resumo -->
                        <div class="col-lg-4">

                            <div class="card p-0 border shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="bx bx-bar-chart-alt-2 me-2 text-primary"></i>
                                        Resumo da geração
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <div class="d-flex align-items-center mb-4">
                                        <div class="avatar bg-blue-lt text-blue">
                                            <i class="bx bx-package fs-2"></i>
                                        </div>

                                        <div class="ms-3">
                                            <div class="text-secondary">
                                                Produtos selecionados
                                            </div>

                                            <div class="fw-bold fs-2">
                                                <span id="summary-products-count">
                                                    0
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div class="avatar bg-purple-lt text-purple">
                                            <i class="bx bx-file fs-2"></i>
                                        </div>

                                        <div class="ms-3">
                                            <div class="text-secondary">
                                                Artigos para gerar
                                            </div>

                                            <div class="fw-bold fs-2">
                                                <span id="summary-articles-count">
                                                    100
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-4">

                                        <div class="text-secondary mb-1">
                                            Produtos recomendados por artigo
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between">

                                            <span
                                                class="display-6 fw-bold"
                                                id="summary-recommended-products"
                                            >
                                                0
                                            </span>

                                            <span class="badge bg-primary-lt">
                                                produtos
                                            </span>

                                        </div>

                                        <div class="text-secondary small mt-2">
                                            Os produtos selecionados poderão ser exibidos como recomendações de compra nos artigos gerados.
                                        </div>

                                    </div>

                                    <div class="border-top mt-4 pt-4">

                                        <div class="text-secondary mb-2">
                                            Processamento
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-time-five text-primary fs-2 me-2"></i>

                                            <div class="text-secondary small">
                                                Os artigos serão adicionados à fila e processados automaticamente pelo worker.
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div id="selected-product-inputs"></div>

                    <input
                        type="hidden"
                        name="action"
                        value="create"
                    >

                </div>

                <div class="modal-footer">

                    <div class="me-auto text-secondary small">
                        <i class="bx bx-info-circle me-1"></i>

                        <strong id="footer-selected-count">
                            0
                        </strong>

                        produtos selecionados para recomendação
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bx bx-list-plus me-1"></i>
                        Adicionar artigos à fila
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let productSearchTimeout = null;
    const selectedProducts = {};

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function updateArticleQuantitySummary() {
        const quantity = parseInt($('#articles_quantity').val(), 10) || 0;

        $('#summary-articles-count').text(quantity);
    }

    function prepareSelectedProducts() {
        $('#selected-product-inputs').remove();

        const $container = $('<div>', {
            id: 'selected-product-inputs'
        });

        Object.values(selectedProducts).forEach(function (product) {
            $('<input>', {
                type: 'hidden',
                name: 'product_ids[]',
                value: product.id
            }).appendTo($container);
        });

        $('#article-generator-form').append($container);
    }

    function renderSelectedProducts() {
        const $tbody = $('#selected-products');

        $tbody.empty();

        const products = Object.values(selectedProducts);

        $('#selected-count').text(
            `${products.length} ${products.length === 1 ? 'produto' : 'produtos'}`
        );

        $('#footer-selected-count').text(products.length);
        $('#summary-products-count').text(products.length);
        $('#summary-recommended-products').text(products.length);

        if (!products.length) {
            $tbody.html(`
                <tr id="no-selected-products">
                    <td colspan="3" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-img">
                                <i
                                    class="bx bx-package text-secondary"
                                    style="font-size: 48px;"
                                ></i>
                            </div>

                            <p class="empty-title">
                                Nenhum produto selecionado
                            </p>

                            <p class="empty-subtitle text-secondary">
                                Selecione os produtos que poderão ser recomendados nos artigos.
                            </p>
                        </div>
                    </td>
                </tr>
            `);

            return;
        }

        products.forEach(function (product) {

            const image = product.product_image
                ? `
                    <img
                        src="${escapeHtml(product.product_image)}"
                        class="product-thumb"
                        alt="${escapeHtml(product.name)}"
                    >
                `
                : `
                    <span class="product-thumb d-flex align-items-center justify-content-center bg-light text-muted">
                        <i class="bx bx-package"></i>
                    </span>
                `;

            $tbody.append(`
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                ${image}
                            </div>

                            <div class="product-name fw-bold">
                                ${escapeHtml(product.name)}
                            </div>
                        </div>
                    </td>

                    <td class="align-middle">
                        <span class="text-secondary">
                            ${escapeHtml(product.sku || 'Não informado')}
                        </span>
                    </td>

                    <td class="align-middle">
                        ${
                            product.product_url
                                ? `
                                    <a
                                        href="${escapeHtml(product.product_url)}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Visualizar produto na loja"
                                    >
                                        <i class="bx bx-store me-1"></i>
                                        Ver na loja
                                    </a>
                                `
                                : ''
                        }

                        <button
                            type="button"
                            class="btn btn-icon btn-ghost-danger remove-selected-product"
                            data-id="${product.id}"
                            title="Remover produto"
                        >
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function searchProducts() {
        const search = $('#product-search').val().trim();

        clearTimeout(productSearchTimeout);

        if (search.length < 2) {
            $('#product-search-results')
                .addClass('d-none')
                .html('');

            return;
        }

        productSearchTimeout = setTimeout(function () {

            $('#product-search-results')
                .removeClass('d-none')
                .html(`
                    <div class="list-group-item text-center py-3">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Buscando produtos...
                    </div>
                `);

            $.ajax({
                url: '<?= INCLUDE_PATH_DASHBOARD; ?>back-end/article-generator.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    action: 'searchProducts',
                    search: search
                },

                success: function (response) {

                    const $results = $('#product-search-results');

                    if (
                        !response.success ||
                        !response.products ||
                        !response.products.length
                    ) {
                        $results
                            .removeClass('d-none')
                            .html(`
                                <div class="list-group-item text-center py-4">
                                    <i class="bx bx-search-alt-2 text-secondary fs-2"></i>

                                    <div class="fw-bold mt-2">
                                        Nenhum produto encontrado
                                    </div>

                                    <div class="text-secondary small">
                                        Tente buscar por outro nome ou SKU.
                                    </div>
                                </div>
                            `);

                        return;
                    }

                    let html = '';

                    response.products.forEach(function (product) {

                        if (selectedProducts[product.id]) {
                            return;
                        }

                        const image = product.product_image
                            ? `
                                <img
                                    src="${escapeHtml(product.product_image)}"
                                    class="product-thumb"
                                    alt="${escapeHtml(product.name)}"
                                >
                            `
                            : `
                                <span class="product-thumb d-flex align-items-center justify-content-center bg-light text-muted">
                                    <i class="bx bx-package"></i>
                                </span>
                            `;

                        html += `
                            <button
                                type="button"
                                class="list-group-item list-group-item-action product-search-item"
                                data-id="${product.id}"
                                data-name="${escapeHtml(product.name)}"
                                data-sku="${escapeHtml(product.sku || '')}"
                                data-image="${escapeHtml(product.product_image || '')}"
                                data-url="${escapeHtml(product.product_url || '')}"
                            >
                                <div class="d-flex align-items-center">

                                    <div class="me-3">
                                        ${image}
                                    </div>

                                    <div class="flex-fill text-start">

                                        <div class="fw-bold">
                                            ${escapeHtml(product.name)}
                                        </div>

                                        <div class="text-secondary small">
                                            SKU: ${escapeHtml(product.sku || 'Não informado')}
                                        </div>

                                    </div>

                                    <i class="bx bx-plus-circle fs-2 text-primary"></i>

                                </div>
                            </button>
                        `;
                    });

                    if (!html) {
                        html = `
                            <div class="list-group-item text-center py-4">

                                <i class="bx bx-check-circle text-success fs-2"></i>

                                <div class="fw-bold mt-2">
                                    Todos os produtos encontrados já foram selecionados
                                </div>

                            </div>
                        `;
                    }

                    $results
                        .removeClass('d-none')
                        .html(html);
                },

                error: function () {

                    $('#product-search-results')
                        .removeClass('d-none')
                        .html(`
                            <div class="list-group-item text-center py-4 text-danger">

                                <i class="bx bx-error-circle fs-2"></i>

                                <div class="fw-bold mt-2">
                                    Não foi possível realizar a busca
                                </div>

                                <div class="text-secondary small">
                                    Tente novamente em alguns instantes.
                                </div>

                            </div>
                        `);
                }
            });

        }, 300);
    }

    $(document).on('click', '.product-search-item', function () {

        const $item = $(this);

        const product = {
            id: $item.data('id'),
            name: $item.data('name'),
            sku: $item.data('sku'),
            product_image: $item.attr('data-image') || '',
            product_url: $item.attr('data-url') || ''
        };

        if (selectedProducts[product.id]) {
            return;
        }

        selectedProducts[product.id] = product;

        renderSelectedProducts();

        $('#product-search')
            .val('')
            .focus();

        $('#product-search-results')
            .addClass('d-none')
            .html('');
    });

    $(document).on('click', '.remove-selected-product', function () {

        const productId = $(this).data('id');

        delete selectedProducts[productId];

        renderSelectedProducts();
    });

    $('#product-search').on('input', function () {
        searchProducts();
    });

    $('#product-search-button').on('click', function () {
        searchProducts();
    });

    $('#articles_quantity').on('input change', function () {
        updateArticleQuantitySummary();
    });

    $(document).on('click', function (event) {

        if (
            !$(event.target).closest('#product-search').length &&
            !$(event.target).closest('#product-search-results').length
        ) {
            $('#product-search-results')
                .addClass('d-none');
        }
    });

    $('#article-generator-form').on('submit', function (event) {
        event.preventDefault();

        prepareSelectedProducts();

        const form = this;
        const formData = new FormData(form);

        const $button = $(form).find('button[type="submit"]');

        $button.prop('disabled', true);

        $.ajax({
            url: '<?= INCLUDE_PATH_DASHBOARD; ?>back-end/article-generator.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',

            success: function (response) {

                if (!response.success) {
                    alert(
                        response.message ||
                        'Não foi possível adicionar os artigos à fila.'
                    );

                    return;
                }

                alert(
                    `${response.articles_quantity} artigos adicionados à fila com sucesso.`
                );

                window.location.reload();
            },

            error: function () {
                alert(
                    'Não foi possível adicionar os artigos à fila.'
                );
            },

            complete: function () {
                $button.prop('disabled', false);
            }
        });
    });

    $('#new-article-generation-modal').on('shown.bs.modal', function () {
        updateArticleQuantitySummary();

        $('#product-search')
            .trigger('focus');
    });

    $('#new-article-generation-modal').on('hidden.bs.modal', function () {

        $('#product-search-results')
            .addClass('d-none')
            .html('');

        $('#product-search')
            .val('');

        $('#product-search').trigger('blur');
    });

    updateArticleQuantitySummary();
    renderSelectedProducts();
</script>