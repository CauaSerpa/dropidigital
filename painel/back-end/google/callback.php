<?php

session_start();

require_once dirname(__DIR__, 3) . '/config.php';

require_once __DIR__ . '/App/GoogleOAuth.php';
require_once __DIR__ . '/App/GoogleCrypto.php';
require_once __DIR__ . '/App/GoogleAnalyticsAdmin.php';
require_once __DIR__ . '/App/GoogleAnalyticsRepository.php';
require_once __DIR__ . '/App/GoogleAnalyticsAuth.php';
require_once __DIR__ . '/App/GoogleAnalyticsData.php';
require_once __DIR__ . '/App/GoogleAnalyticsSync.php';


if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Usuário não autenticado.');
}


/*
 * ============================================================
 * POST
 *
 * Usuário selecionou propriedade / Data Stream
 * ============================================================
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty(
            $_SESSION['google_analytics_options']
        ) ||

        empty(
            $_SESSION['google_analytics_token']
        ) ||

        empty(
            $_SESSION['google_oauth_shop_id']
        )
    ) {
        http_response_code(400);

        exit(
            'Sessão de seleção do Google Analytics expirada.'
        );
    }

    if (
        empty(
            $_POST['selection_token']
        ) ||

        empty(
            $_SESSION[
                'google_analytics_selection_token'
            ]
        ) ||

        !hash_equals(
            $_SESSION[
                'google_analytics_selection_token'
            ],

            $_POST['selection_token']
        )
    ) {
        http_response_code(400);

        exit(
            'Seleção inválida ou expirada.'
        );
    }

    $optionIndex =
        filter_input(
            INPUT_POST,
            'option',
            FILTER_VALIDATE_INT
        );

    if (
        $optionIndex === false ||
        $optionIndex === null
    ) {
        http_response_code(400);

        exit(
            'Selecione uma propriedade e um Data Stream.'
        );
    }

    $options =
        $_SESSION[
            'google_analytics_options'
        ];

    if (!isset($options[$optionIndex])) {
        http_response_code(400);

        exit(
            'A opção selecionada não é válida.'
        );
    }

    $selected =
        $options[$optionIndex];

    $shopId =
        (int) $_SESSION[
            'google_oauth_shop_id'
        ];

    if ($shopId <= 0) {
        http_response_code(400);

        exit(
            'Loja inválida.'
        );
    }

    $token =
        $_SESSION[
            'google_analytics_token'
        ];

    if (
        empty(
            $token['access_token']
        )
    ) {
        http_response_code(400);

        exit(
            'Token do Google Analytics inválido.'
        );
    }

    try {

        /*
         * ====================================================
         * 1. Salvar conexão
         * ====================================================
         */

        $tokenExpiresAt = null;

        if (!empty($token['expires_in'])) {

            $tokenExpiresAt =
                date(
                    'Y-m-d H:i:s',

                    time()
                    + (int) $token['expires_in']
                );
        }

        $repository =
            new GoogleAnalyticsRepository(
                $conn_pdo
            );

        $repository->saveConnection(
            $shopId,

            [
                'access_token' =>
                    $token['access_token'],

                'refresh_token' =>
                    $token['refresh_token']
                    ?? null,

                'token_expires_at' =>
                    $tokenExpiresAt,

                'property_id' =>
                    $selected['property_id'],

                'property_name' =>
                    $selected['property_name'],

                'property_account' =>
                    $selected['account_name'],

                'measurement_id' =>
                    $selected['measurement_id'],

                'stream_name' =>
                    $selected['stream_name'],
            ]
        );


        /*
         * ====================================================
         * 2. Primeiro sync imediato
         * ====================================================
         */

        $syncResult = null;

        $syncError = null;

        try {

            $sync =
                new GoogleAnalyticsSync(
                    $conn_pdo
                );

            $syncResult =
                $sync->syncShop(
                    $shopId,
                    '30daysAgo',
                    'yesterday'
                );

        } catch (Throwable $syncException) {

            /*
             * A conexão continua válida mesmo se o primeiro
             * sync falhar.
             */
            $syncError =
                $syncException->getMessage();
        }


        /*
         * ====================================================
         * 3. Limpar sessão temporária
         * ====================================================
         */

        unset(
            $_SESSION['google_oauth_state'],
            $_SESSION['google_oauth_shop_id'],
            $_SESSION['google_analytics_token'],
            $_SESSION['google_analytics_options'],
            $_SESSION['google_analytics_selection_token']
        );


        /*
         * ====================================================
         * 4. Resultado
         * ====================================================
         */

        ?>

        <!DOCTYPE html>
        <html lang="pt-BR">

        <head>

            <meta charset="UTF-8">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1.0"
            >

            <title>
                Google Analytics conectado
            </title>

            <style>

                body {
                    margin: 0;
                    padding: 40px 20px;
                    background: #f8f9fa;
                    font-family: Arial, sans-serif;
                }

                .container {
                    max-width: 650px;
                    margin: 60px auto;
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 35px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
                }

                .success {
                    width: 52px;
                    height: 52px;
                    border-radius: 50%;
                    background: #e8f7ee;
                    color: #198754;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 26px;
                    margin-bottom: 20px;
                }

                .warning {
                    width: 52px;
                    height: 52px;
                    border-radius: 50%;
                    background: #fff3cd;
                    color: #856404;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 26px;
                    margin-bottom: 20px;
                }

                h1 {
                    margin: 0 0 10px;
                    font-size: 24px;
                }

                p {
                    color: #6c757d;
                    line-height: 1.6;
                }

                .info {
                    margin-top: 25px;
                    padding: 18px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }

                .info-row {
                    display: flex;
                    justify-content: space-between;
                    gap: 20px;
                    padding: 8px 0;
                }

                .info-label {
                    color: #6c757d;
                }

                .info-value {
                    font-weight: 600;
                    text-align: right;
                }

                .button {
                    display: inline-block;
                    margin-top: 25px;
                    padding: 11px 18px;
                    border-radius: 6px;
                    background: #0d6efd;
                    color: #fff;
                    text-decoration: none;
                }

                .sync-warning {
                    margin-top: 20px;
                    padding: 15px;
                    border-radius: 8px;
                    background: #fff3cd;
                    color: #664d03;
                    font-size: 14px;
                }

            </style>

        </head>

        <body>

            <div class="container">

                <?php if ($syncError === null): ?>

                    <div class="success">
                        ✓
                    </div>

                    <h1>
                        Google Analytics conectado!
                    </h1>

                    <p>
                        A propriedade selecionada foi vinculada
                        corretamente e os dados dos últimos
                        30 dias foram sincronizados.
                    </p>

                <?php else: ?>

                    <div class="warning">
                        !
                    </div>

                    <h1>
                        Google Analytics conectado
                    </h1>

                    <p>
                        A propriedade foi vinculada corretamente,
                        mas não foi possível realizar a primeira
                        sincronização dos dados.
                    </p>

                    <div class="sync-warning">
                        A sincronização será tentada novamente
                        automaticamente pelo cron.
                    </div>

                <?php endif; ?>


                <div class="info">

                    <div class="info-row">

                        <span class="info-label">
                            Conta
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $selected['account_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            Propriedade
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $selected['property_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            Data Stream
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $selected['stream_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </span>

                    </div>

                    <div class="info-row">

                        <span class="info-label">
                            Measurement ID
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $selected['measurement_id'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </span>

                    </div>

                    <?php if ($syncResult): ?>

                        <div class="info-row">

                            <span class="info-label">
                                Dados sincronizados
                            </span>

                            <span class="info-value">
                                <?= number_format(
                                    $syncResult['rows_saved'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </span>

                        </div>

                    <?php endif; ?>

                </div>

                <a
                    href="<?= htmlspecialchars(
                        INCLUDE_PATH_DASHBOARD,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    class="button"
                >
                    Voltar ao painel
                </a>

            </div>

        </body>

        </html>

        <?php

        exit;

    } catch (Throwable $e) {

        http_response_code(500);

        echo '<h1>Erro ao conectar Google Analytics</h1>';

        echo '<pre>';

        echo htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        );

        echo '</pre>';

        exit;
    }
}


/*
 * ============================================================
 * GET
 *
 * Retorno do OAuth do Google
 * ============================================================
 */

if (
    empty(
        $_SESSION['google_oauth_state']
    )
) {
    http_response_code(400);

    exit(
        'Sessão OAuth inválida ou expirada.'
    );
}

if (
    empty($_GET['state']) ||
    !hash_equals(
        $_SESSION['google_oauth_state'],
        $_GET['state']
    )
) {

    unset(
        $_SESSION['google_oauth_state'],
        $_SESSION['google_oauth_shop_id']
    );

    http_response_code(400);

    exit(
        'Falha na validação de segurança do OAuth.'
    );
}

if (empty($_GET['code'])) {

    $error =
        $_GET['error']
        ?? 'unknown';

    unset(
        $_SESSION['google_oauth_state'],
        $_SESSION['google_oauth_shop_id']
    );

    http_response_code(400);

    exit(
        'A autorização do Google não foi concluída. Erro: '
        . htmlspecialchars(
            $error,
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

$shopId =
    (int) (
        $_SESSION['google_oauth_shop_id']
        ?? 0
    );

if ($shopId <= 0) {

    http_response_code(400);

    exit(
        'Loja não encontrada na sessão.'
    );
}

try {

    $googleOAuth =
        new GoogleOAuth();

    $token =
        $googleOAuth->authenticate(
            $_GET['code']
        );

    $googleAdmin =
        new GoogleAnalyticsAdmin(
            $googleOAuth->getClient()
        );


    /*
     * ========================================================
     * 1. Contas
     * ========================================================
     */

    $accounts =
        $googleAdmin->listAccounts();

    if (empty($accounts)) {

        throw new Exception(
            'Nenhuma conta do Google Analytics foi encontrada.'
        );
    }


    /*
     * ========================================================
     * 2. Propriedades
     * ========================================================
     */

    $properties = [];

    foreach ($accounts as $account) {

        $accountName =
            $account['name']
            ?? null;

        if (!$accountName) {
            continue;
        }

        $accountProperties =
            $googleAdmin->listProperties(
                $accountName
            );

        foreach (
            $accountProperties
            as $property
        ) {

            $property['_account'] =
                $account;

            $properties[] =
                $property;
        }
    }

    if (empty($properties)) {

        throw new Exception(
            'Nenhuma propriedade GA4 foi encontrada.'
        );
    }


    /*
     * ========================================================
     * 3. Data Streams
     * ========================================================
     */

    $options = [];

    foreach (
        $properties
        as $property
    ) {

        $propertyName =
            $property['name']
            ?? null;

        if (!$propertyName) {
            continue;
        }

        $propertyStreams =
            $googleAdmin->listDataStreams(
                $propertyName
            );

        foreach (
            $propertyStreams
            as $stream
        ) {

            if (
                ($stream['type'] ?? '')
                !== 'WEB_DATA_STREAM'
            ) {
                continue;
            }

            $measurementId =
                $stream[
                    'webStreamData'
                ]['measurementId']
                ?? null;

            if (!$measurementId) {
                continue;
            }

            $propertyId =
                preg_replace(
                    '/^properties\//',
                    '',
                    $property['name']
                );

            $account =
                $property['_account']
                ?? [];

            $options[] = [

                'property_id' =>
                    $propertyId,

                'property_name' =>
                    $property['displayName']
                    ?? 'Propriedade sem nome',

                'account_name' =>
                    $account['displayName']
                    ?? 'Conta sem nome',

                'stream_name' =>
                    $stream['displayName']
                    ?? 'Data Stream sem nome',

                'measurement_id' =>
                    $measurementId,

                'stream_url' =>
                    $stream[
                        'webStreamData'
                    ]['defaultUri']
                    ?? null,
            ];
        }
    }

    if (empty($options)) {

        throw new Exception(
            'Nenhum Data Stream Web do GA4 foi encontrado.'
        );
    }


    /*
     * ========================================================
     * 4. Guardar descoberta temporariamente na sessão
     * ========================================================
     */

    $_SESSION[
        'google_analytics_token'
    ] = $token;

    $_SESSION[
        'google_analytics_options'
    ] = $options;

    $_SESSION[
        'google_analytics_selection_token'
    ] = bin2hex(
        random_bytes(32)
    );

} catch (Throwable $e) {

    http_response_code(500);

    echo '<h1>Erro ao conectar Google Analytics</h1>';

    echo '<pre>';

    echo htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    );

    echo '</pre>';

    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Selecionar Google Analytics
    </title>

    <style>

        body {
            margin: 0;
            padding: 40px 20px;
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        .header p {
            margin: 0;
            color: #6c757d;
            line-height: 1.6;
        }

        .options {
            display: grid;
            gap: 15px;
        }

        .option {
            position: relative;
        }

        .option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .option label {
            display: block;
            padding: 20px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            cursor: pointer;
            transition: .15s ease;
        }

        .option label:hover {
            border-color: #adb5bd;
        }

        .option input:checked + label {
            border-color: #0d6efd;
            box-shadow:
                0 0 0 2px
                rgba(13, 110, 253, .12);
        }

        .option-title {
            font-weight: 600;
            font-size: 17px;
            margin-bottom: 6px;
        }

        .option-account {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .option-details {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .badge {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 5px;
            background: #f1f3f5;
            color: #495057;
            font-size: 12px;
        }

        .url {
            margin-top: 10px;
            color: #6c757d;
            font-size: 13px;
            word-break: break-all;
        }

        .footer {
            margin-top: 25px;
            padding: 20px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }

        .button {
            width: 100%;
            border: 0;
            border-radius: 6px;
            padding: 12px 18px;
            background: #0d6efd;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .button:hover {
            background: #0b5ed7;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 15px;
            }

            .container {
                margin: 20px auto;
            }

        }

    </style>

</head>

<body>

    <div class="container">

        <div class="header">

            <h1>
                Selecione o Google Analytics
            </h1>

            <p>
                Encontramos mais de uma propriedade ou Data Stream
                disponível na sua conta Google. Selecione qual
                deles pertence a esta loja.
            </p>

        </div>

        <form
            method="POST"
            action=""
        >

            <input
                type="hidden"
                name="selection_token"
                value="<?= htmlspecialchars(
                    $_SESSION[
                        'google_analytics_selection_token'
                    ],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            >

            <div class="options">

                <?php foreach (
                    $options
                    as $index => $option
                ): ?>

                    <div class="option">

                        <input
                            type="radio"
                            name="option"
                            id="option_<?= (int) $index; ?>"
                            value="<?= (int) $index; ?>"
                            <?= $index === 0
                                ? 'checked'
                                : ''; ?>
                        >

                        <label
                            for="option_<?= (int) $index; ?>"
                        >

                            <div class="option-title">

                                <?= htmlspecialchars(
                                    $option['property_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </div>

                            <div class="option-account">

                                Conta:
                                <?= htmlspecialchars(
                                    $option['account_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </div>

                            <div class="option-details">

                                <span class="badge">

                                    Stream:
                                    <?= htmlspecialchars(
                                        $option['stream_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </span>

                                <span class="badge">

                                    <?= htmlspecialchars(
                                        $option['measurement_id'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </span>

                            </div>

                            <?php if (
                                !empty(
                                    $option['stream_url']
                                )
                            ): ?>

                                <div class="url">

                                    <?= htmlspecialchars(
                                        $option['stream_url'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </div>

                            <?php endif; ?>

                        </label>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="footer">

                <button
                    type="submit"
                    class="button"
                >
                    Usar esta propriedade
                </button>

            </div>

        </form>

    </div>

</body>

</html>