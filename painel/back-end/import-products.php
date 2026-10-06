<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['shop_id'], [2, 25, 61])) {
        header('Location: ' . INCLUDE_PATH_DASHBOARD);
        exit;
    }

    /*
     * Cria fila de processamento
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAddImport'])) {
        // Recebe os dados do formulário
        $provider = $_POST['provider'] ?? '';
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $save_provider_categories = !empty($_POST['save_provider_categories']) ? 1 : 0;
        $product_limit = isset($_POST['product_limit']) ? (int) $_POST['product_limit'] : 0;
        $count_only_new = !empty($_POST['count_only_new']) ? 1 : 0;
        $auto_publish = isset($_POST['auto_publish']) ? (int) $_POST['auto_publish'] : 0;
        $update_existing = isset($_POST['update_existing']) ? (int) $_POST['update_existing'] : 0;
        $observations = $_POST['observations'] ?? '';

        // Valida provider
        if ($provider === '') {
            $_SESSION['msg'] = "<p class='red'>Selecione o provider da importação!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "importacao-produtos");
            exit;
        }

        // Valida limite de produtos
        if ($product_limit <= 0) {
            $_SESSION['msg'] = "<p class='red'>Informe um limite de produtos válido!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "importacao-produtos");
            exit;
        }

        // Tabela que será solicitada
        $tabela = 'tb_product_import_jobs';

        // Cria a solicitação de importação
        $sql = "INSERT INTO $tabela (
            shop_id,
            provider,
            category_id,
            save_provider_categories,
            product_limit,
            count_only_new,
            auto_publish,
            update_existing,
            status,
            stop_requested,
            observations
        ) VALUES (
            :shop_id,
            :provider,
            :category_id,
            :save_provider_categories,
            :product_limit,
            :count_only_new,
            :auto_publish,
            :update_existing,
            'pending',
            0,
            :observations
        )";

        $stmt = $conn_pdo->prepare($sql);

        $stmt->bindValue(':shop_id', $_SESSION['shop_id']);
        $stmt->bindValue(':provider', $provider);
        $stmt->bindValue(':category_id', $category_id);
        $stmt->bindValue(':save_provider_categories', $save_provider_categories);
        $stmt->bindValue(':product_limit', $product_limit);
        $stmt->bindValue(':count_only_new', $count_only_new);
        $stmt->bindValue(':auto_publish', $auto_publish);
        $stmt->bindValue(':update_existing', $update_existing);
        $stmt->bindValue(':observations', $observations);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Importação de produtos criada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "importacao-produtos");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao criar a importação de produtos!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "importacao-produtos");
            exit;
        }
    }

    /*
     * Alternar Worker
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnWorkerToggle'])) {
        $shopId = $_SESSION['shop_id'] ?? '';
        $action = $_POST['action'] ?? '';

        if (!in_array($action, ['pause', 'resume'], true)) {
            $_SESSION['msg'] = "<p class='red'>Ação inválida.</p>";
            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
            exit;
        }

        /**
         * =====================================================
         * PAUSAR WORKERS
         * =====================================================
         */

        if ($action === 'pause') {
            try {
                $conn_pdo->beginTransaction();

                /**
                 * -------------------------------------------------
                 * 1. PAUSA O(S) WORKER(S) QUE ESTÁ(ÃO) RODANDO
                 * -------------------------------------------------
                 *
                 * stop_requested = 1 identifica que este job estava
                 * efetivamente executando no momento da pausa.
                 *
                 * O worker deverá detectar essa flag e encerrar
                 * sua execução mantendo o job como paused.
                 */

                $stmt = $conn_pdo->prepare("
                    UPDATE tb_product_import_jobs
                    SET
                        status = 'paused',
                        stop_requested = 1,
                        last_activity_at = NOW()
                    WHERE shop_id = :shop_id
                    AND status = 'running'
                ");

                $stmt->execute([
                    ':shop_id' => $shopId
                ]);

                /**
                 * -------------------------------------------------
                 * 2. PAUSA OS WORKERS QUE ESTÃO PENDENTES
                 * -------------------------------------------------
                 *
                 * Esses jobs não estavam executando.
                 * Portanto, stop_requested permanece 0.
                 */

                $stmt = $conn_pdo->prepare("
                    UPDATE tb_product_import_jobs
                    SET
                        status = 'paused',
                        stop_requested = 0,
                        last_activity_at = NOW()
                    WHERE shop_id = :shop_id
                    AND status = 'pending'
                ");

                $stmt->execute([
                    ':shop_id' => $shopId
                ]);

                $conn_pdo->commit();

                $_SESSION['msg'] =
                    "<p class='green'>Workers pausados com sucesso.</p>";

            } catch (Exception $e) {
                if ($conn_pdo->inTransaction()) {
                    $conn_pdo->rollBack();
                }

                $_SESSION['msg'] =
                    "<p class='red'>Erro ao pausar workers: "
                    . htmlspecialchars($e->getMessage())
                    . "</p>";
            }

            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
            exit;
        }

        /**
         * =====================================================
         * RETOMAR WORKERS
         * =====================================================
         */

        if ($action === 'resume') {
            try {
                $conn_pdo->beginTransaction();

                /**
                 * -------------------------------------------------
                 * 1. VERIFICA SE JÁ EXISTE UM WORKER ATIVO
                 * -------------------------------------------------
                 *
                 * Essa verificação é fundamental.
                 *
                 * Pode acontecer de:
                 *
                 * - o usuário pausar o worker;
                 * - criar uma nova importação;
                 * - essa nova importação ficar como pending;
                 * - outro processo/worker já ter iniciado uma execução;
                 * - e então o usuário clicar em resume.
                 *
                 * Nunca devemos criar um segundo job running.
                 */

                $stmt = $conn_pdo->prepare("
                    SELECT id
                    FROM tb_product_import_jobs
                    WHERE shop_id = :shop_id
                    AND status = 'running'
                    ORDER BY id ASC
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([
                    ':shop_id' => $shopId
                ]);

                $runningJob = $stmt->fetch(PDO::FETCH_ASSOC);

                /**
                 * -------------------------------------------------
                 * 2. CASO JÁ EXISTA UM WORKER RUNNING
                 * -------------------------------------------------
                 *
                 * Não vamos promover nenhum paused para running.
                 *
                 * Todos os paused voltam para pending.
                 *
                 * Isso garante que exista no máximo um worker ativo
                 * para a loja.
                 */

                if ($runningJob) {
                    $stmt = $conn_pdo->prepare("
                        UPDATE tb_product_import_jobs
                        SET
                            status = 'pending',
                            stop_requested = 0,
                            last_activity_at = NOW()
                        WHERE shop_id = :shop_id
                        AND status = 'paused'
                    ");

                    $stmt->execute([
                        ':shop_id' => $shopId
                    ]);

                    $conn_pdo->commit();

                    $_SESSION['msg'] =
                        "<p class='green'>Workers retomados. "
                        . "Já existe uma importação em execução, então as demais foram mantidas como pendentes.</p>";

                    header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
                    exit;
                }

                /**
                 * -------------------------------------------------
                 * 3. NÃO EXISTE WORKER ATIVO
                 * -------------------------------------------------
                 *
                 * Agora podemos procurar o job que estava rodando
                 * antes da pausa.
                 *
                 * Esse é identificado por:
                 *
                 * status = paused
                 * stop_requested = 1
                 */

                $stmt = $conn_pdo->prepare("
                    SELECT id
                    FROM tb_product_import_jobs
                    WHERE shop_id = :shop_id
                    AND status = 'paused'
                    AND stop_requested = 1
                    ORDER BY id ASC
                    LIMIT 1
                    FOR UPDATE
                ");

                $stmt->execute([
                    ':shop_id' => $shopId
                ]);

                $activeJob = $stmt->fetch(PDO::FETCH_ASSOC);

                /**
                 * -------------------------------------------------
                 * 4. RETOMA O WORKER QUE ESTAVA ATIVO
                 * -------------------------------------------------
                 *
                 * Somente este job será marcado como running.
                 */

                if ($activeJob) {
                    $stmt = $conn_pdo->prepare("
                        UPDATE tb_product_import_jobs
                        SET
                            status = 'running',
                            stop_requested = 0,
                            last_activity_at = NOW()
                        WHERE id = :id
                        AND shop_id = :shop_id
                        AND status = 'paused'
                    ");

                    $stmt->execute([
                        ':id' => $activeJob['id'],
                        ':shop_id' => $shopId
                    ]);
                }

                /**
                 * -------------------------------------------------
                 * 5. TODOS OS DEMAIS PAUSED VOLTAM PARA PENDING
                 * -------------------------------------------------
                 *
                 * Isso inclui qualquer job criado antes da pausa
                 * e também garante que somente o job priorizado
                 * permaneça como running.
                 */

                $stmt = $conn_pdo->prepare("
                    UPDATE tb_product_import_jobs
                    SET
                        status = 'pending',
                        stop_requested = 0,
                        last_activity_at = NOW()
                    WHERE shop_id = :shop_id
                    AND status = 'paused'
                    AND (:active_id = 0 OR id <> :active_id)
                ");

                $stmt->execute([
                    ':shop_id' => $shopId,
                    ':active_id' => $activeJob['id'] ?? 0
                ]);

                $conn_pdo->commit();

                /**
                 * -------------------------------------------------
                 * 6. MENSAGEM
                 * -------------------------------------------------
                 */

                if ($activeJob) {
                    $_SESSION['msg'] =
                        "<p class='green'>Workers retomados. "
                        . "A importação que estava ativa anteriormente foi priorizada.</p>";
                } else {
                    $_SESSION['msg'] =
                        "<p class='green'>Workers retomados com sucesso.</p>";
                }

            } catch (Exception $e) {
                if ($conn_pdo->inTransaction()) {
                    $conn_pdo->rollBack();
                }

                $_SESSION['msg'] =
                    "<p class='red'>Erro ao retomar workers: "
                    . htmlspecialchars($e->getMessage())
                    . "</p>";
            }

            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
            exit;
        }
    }

    /*
     * Cria fontes padrões
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSeedDefaultKeywords'])) {
        $shopId = $_SESSION['shop_id'];

        $stmt = $conn_pdo->prepare("
            SELECT COUNT(*)
            FROM tb_product_import_keywords
            WHERE shop_id = ?
        ");
        $stmt->execute([$shopId]);
        $count = (int) $stmt->fetchColumn();

        if ($count > 0) {
            $_SESSION['msg'] = "<p class='red'>As fontes já estão cadastradas.</p>";
            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
            exit;
        }

        /**
         * BASE KEYWORDS
         */
        $baseKeywords = [
            'smartphone',
            'headset',
            'charger',
            'usb cable',
            'smart watch',
            'keyboard',
            'mouse',
            'monitor',
            'webcam',
            'microphone',
            'speaker',
            'earbuds',
            'tablet',
            'drone',
            'camera',
            'tripod',
            'ring light',
            'led lights',
            'fitness equipment',
            'makeup',
            'beauty tools',
            'pet accessories',
            'car accessories',
            'home decor',
            'kitchen accessories',
            'coffee maker',
            'vacuum cleaner',
            'backpack',
            'wallet',
            'shoes',
            't shirt',
            'dress',
            'jacket',
            'hoodie'
        ];

        /**
         * MODIFIERS
         */
        $modifiers = [
            'wireless',
            'bluetooth',
            'gaming',
            'portable',
            'smart',
            'mini',
            'usb',
            'rgb',
            'fast',
            'rechargeable',
            'waterproof',
            'magnetic',
            'professional',
            'digital',
            'automatic',
            'electric',
            'foldable',
            'adjustable',
            'ergonomic',
            'premium'
        ];

        /**
         * TARGETS
         */
        $targets = [
            'for iphone',
            'for samsung',
            'for xiaomi',
            'for pc',
            'for notebook',
            'for ps5',
            'for xbox',
            'for car',
            'for office',
            'for home',
            'for gaming',
            'for travel'
        ];

        /**
         * GERA COMBINAÇÕES
         */
        $keywords = [];

        foreach ($baseKeywords as $base) {
            $keywords[] = $base;

            foreach ($modifiers as $modifier) {
                $keywords[] = $modifier . ' ' . $base;

                foreach ($targets as $target) {
                    $keywords[] =
                        $modifier . ' '
                        . $base . ' '
                        . $target;
                }
            }
        }

        /**
         * REMOVE DUPLICADAS
         */
        $keywords = array_unique($keywords);

        /**
         * INSERT
         */
        $stmt = $conn_pdo->prepare("
            INSERT IGNORE INTO tb_product_import_keywords (
                shop_id,
                keyword,
                source,
                priority,
                status
            ) VALUES (
                ?,
                ?,
                'Padrão',
                10,
                'active'
            )
        ");

        $total = 0;

        foreach ($keywords as $keyword) {
            $stmt->execute([
                $shopId,
                $keyword
            ]);

            if ($stmt->rowCount() > 0) {
                $total++;
            }
        }

        $_SESSION['msgcad'] =
            "<p class='green'>"
            . "Fontes padrão cadastradas com sucesso! "
            . "Total: {$total}"
            . "</p>";

        header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
        exit;
    }

    /*
     * Salvar fontes
     */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnAddKeywords'])) {
        $keywords = $_POST['keywords'] ?? [];
        $shopId = $_SESSION['shop_id'];

        $totalInseridas = 0;

        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);

            if (empty($keyword)) {
                continue;
            }

            $stmt = $conn_pdo->prepare("
                INSERT IGNORE INTO tb_product_import_keywords (
                    shop_id,
                    keyword,
                    source,
                    priority,
                    status
                ) VALUES (
                    ?,
                    ?,
                    'Manual',
                    10,
                    'active'
                )
            ");

            $stmt->execute([
                $shopId,
                $keyword
            ]);

            if ($stmt->rowCount() > 0) {
                $totalInseridas++;
            }
        }

        if ($totalInseridas > 0) {
            $_SESSION['msgcad'] =
                "<p class='green'>"
                . "Fontes cadastradas com sucesso! "
                . "Total: {$totalInseridas}"
                . "</p>";
        } else {
            $_SESSION['msg'] =
                "<p class='red'>"
                . "Nenhuma fonte nova foi cadastrada. "
                . "As keywords informadas já podem existir."
                . "</p>";
        }

        header('Location: ' . INCLUDE_PATH_DASHBOARD . 'importacao-produtos');
        exit;
    }

    exit;