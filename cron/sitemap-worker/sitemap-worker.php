<?php

declare(strict_types=1);

/**
 * Dropi Digital — Gerador de sitemaps estáticos
 *
 * Arquivo: sitemap-worker.php
 * Dependências: db.php, .env e as tabelas existentes.
 *
 * Execução:
 *   php sitemap-worker.php
 *   php sitemap-worker.php --shop-id=25
 *
 * Saída:
 *   sitemap/static/{shop_id}/sitemap_index.xml
 *   sitemap/static/{shop_id}/sitemap_pages.xml
 *   sitemap/static/{shop_id}/sitemap_products_{n}.xml
 * 
 * CronJob (Terça e Sábado as 4h da manhã)
 * 0 4 * * 3,6 /usr/bin/php /var/www/dropidigital/public/cron/sitemap-worker/sitemap-worker.php --scheduled >> /var/www/dropidigital/public/cron/sitemap-worker/logs/dropidigital-sitemap.log 2>&1
 */

require_once __DIR__ . '/db.php';

$pdo = connectDB();

const SITEMAP_PRODUCT_LIMIT = 30000;
const SITEMAP_GENERATION_INTERVAL_HOURS = 84;

date_default_timezone_set('America/Sao_Paulo');

$staticRoot = dirname(__DIR__, 2) . '/loja/sitemap/static';

/**
 * Escapa texto para uso seguro em XML.
 */
function xmlEscape(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_XML1 | ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Formata uma data para lastmod.
 */
function sitemapLastmod(?string $value): string
{
    if ($value === null || trim($value) === '') {
        return date('c');
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return date('c');
    }

    return date('c', $timestamp);
}

/**
 * Normaliza o domínio de uma loja.
 *
 * Pressupõe que tb_domains possui:
 *   shop_id, domain e subdomain.
 *
 * Ajuste esta função se sua regra de domínio principal
 * for diferente da utilizada abaixo.
 */
function getShopBaseUrl(PDO $pdo, int $shopId): string
{
    $defaultDomain = 'dropidigital.com.br';

    $stmt = $pdo->prepare("
        SELECT domain, subdomain
        FROM tb_domains
        WHERE shop_id = ?
        ORDER BY
            CASE
                WHEN LOWER(TRIM(domain)) <> ? THEN 0
                ELSE 1
            END ASC
        LIMIT 1
    ");

    $stmt->execute([$shopId, $defaultDomain]);

    $domainRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$domainRow) {
        throw new RuntimeException(
            "Nenhum domínio encontrado para a loja {$shopId}."
        );
    }

    $domain = strtolower(trim((string) $domainRow['domain']));
    $subdomain = strtolower(
        trim((string) ($domainRow['subdomain'] ?? ''))
    );

    // Remove protocolo, se houver.
    $domain = preg_replace('#^https?://#i', '', $domain);

    // Remove barras nas extremidades.
    $domain = trim($domain, "/ \t\n\r\0\x0B");

    if (
        $domain === ''
        || str_contains($domain, '/')
        || str_contains($domain, '?')
        || str_contains($domain, '#')
        || !preg_match('/^[a-z0-9.-]+(?::\d+)?$/i', $domain)
    ) {
        throw new RuntimeException(
            "Domínio inválido para a loja {$shopId}: {$domain}"
        );
    }

    // Subdomínio www ou vazio: usa www.dominio.
    // Qualquer outro subdomínio: usa subdominio.dominio.
    if ($subdomain === '' || $subdomain === 'www') {
        $host = 'www.' . $domain;
    } else {
        $host = $subdomain . '.' . $domain;
    }

    if (!preg_match('/^[a-z0-9.-]+(?::\d+)?$/i', $host)) {
        throw new RuntimeException(
            "Host inválido para a loja {$shopId}: {$host}"
        );
    }

    return 'https://' . $host;
}

/**
 * Monta uma URL sem duplicar barras.
 */
function sitemapUrl(string $baseUrl, string $path = ''): string
{
    $baseUrl = rtrim($baseUrl, '/');
    $path = trim($path, '/');

    if ($path === '') {
        return $baseUrl . '/';
    }

    return $baseUrl . '/' . $path;
}

/**
 * Monta a URL pública de um produto.
 *
 * Português: /slug
 * Outros idiomas: /en/slug, /de/slug, /es/slug
 */
function productUrl(
    string $baseUrl,
    string $language,
    string $link
): string {
    $link = trim($link, '/');

    if ($link === '') {
        return sitemapUrl($baseUrl);
    }

    $language = strtolower(trim($language));

    if ($language !== 'pt') {
        $allowedLanguages = ['en', 'de', 'es'];

        if (!in_array($language, $allowedLanguages, true)) {
            $language = 'pt';
        }
    }

    if ($language === 'pt') {
        return sitemapUrl($baseUrl, $link);
    }

    return sitemapUrl($baseUrl, $language . '/' . $link);
}

/**
 * Escreve um arquivo completo.
 */
function writeFileOrFail(string $path, string $content): void
{
    $result = file_put_contents($path, $content, LOCK_EX);

    if ($result === false || $result !== strlen($content)) {
        throw new RuntimeException(
            "Não foi possível gravar o arquivo: {$path}"
        );
    }
}

/**
 * Inicializa o XML de URLs.
 */
function startUrlset(): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset'
        . ' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
        . ' xmlns:xhtml="http://www.w3.org/1999/xhtml"'
        . '>' . "\n";
}

/**
 * Finaliza o XML de URLs.
 */
function endUrlset(): string
{
    return "</urlset>\n";
}

/**
 * Gera o sitemap de páginas.
 *
 * Inclui:
 * - Página inicial em português.
 * - Página inicial nos idiomas encontrados nos produtos.
 * - Páginas publicadas de atendimento.
 *
 * As páginas de atendimento são incluídas na raiz,
 * seguindo a estrutura informada do sitemap dinâmico.
 */
function generatePagesSitemap(
    PDO $pdo,
    int $shopId,
    string $baseUrl,
    string $filePath
): int {
    $languagesStmt = $pdo->prepare("
        SELECT DISTINCT language
        FROM tb_products
        WHERE shop_id = ?
          AND status = 1
        ORDER BY language
    ");

    $languagesStmt->execute([$shopId]);

    $languages = ['pt'];

    foreach ($languagesStmt->fetchAll(PDO::FETCH_COLUMN) as $language) {
        $language = strtolower((string) $language);

        if (in_array($language, ['pt', 'en', 'de', 'es'], true)) {
            $languages[] = $language;
        }
    }

    $languages = array_values(array_unique($languages));

    $languagePaths = [
        'pt' => '',
        'en' => 'en/',
        'de' => 'de/',
        'es' => 'es/',
    ];

    $languageCodes = [
        'pt' => 'pt-BR',
        'en' => 'en',
        'de' => 'de',
        'es' => 'es',
    ];

    $homeUrls = [];

    foreach ($languages as $language) {
        $path = $languagePaths[$language] ?? '';

        $homeUrls[$language] = sitemapUrl(
            $baseUrl,
            $path
        );
    }

    $xml = startUrlset();
    $count = 0;

    /*
     * Homepages com hreflang.
     */
    foreach ($languages as $language) {
        $loc = $homeUrls[$language];

        $xml .= "  <url>\n";
        $xml .= '    <loc>' . xmlEscape($loc) . "</loc>\n";
        $xml .= '    <lastmod>' . date('c') . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";

        foreach ($languages as $alternateLanguage) {
            $alternateUrl = $homeUrls[$alternateLanguage];
            $hreflang = $languageCodes[$alternateLanguage];

            $xml .= '    <xhtml:link rel="alternate" hreflang="'
                . xmlEscape($hreflang)
                . '" href="'
                . xmlEscape($alternateUrl)
                . "\" />\n";
        }

        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'
            . xmlEscape($homeUrls['pt'])
            . "\" />\n";

        $xml .= "  </url>\n";

        $count++;
    }

    /*
     * Páginas publicadas.
     */
    $pagesStmt = $pdo->prepare("
        SELECT link, last_modification
        FROM tb_pages
        WHERE shop_id = ?
          AND status = 1
        ORDER BY id ASC
    ");

    $pagesStmt->execute([$shopId]);

    while ($page = $pagesStmt->fetch()) {
        $link = trim((string) $page['link'], '/');

        if ($link === '') {
            continue;
        }

        $url = sitemapUrl(
            $baseUrl,
            'atendimento/' . $link
        );

        $lastmod = sitemapLastmod(
            $page['last_modification'] ?? null
        );

        $xml .= "  <url>\n";
        $xml .= '    <loc>' . xmlEscape($url) . "</loc>\n";
        $xml .= '    <lastmod>' . xmlEscape($lastmod) . "</lastmod>\n";
        $xml .= "    <changefreq>monthly</changefreq>\n";
        $xml .= "    <priority>0.60</priority>\n";
        $xml .= "  </url>\n";

        $count++;
    }

    $xml .= endUrlset();

    writeFileOrFail($filePath, $xml);

    return $count;
}

/**
 * Monta os intervalos de produtos elegíveis para o sitemap.
 *
 * Usa paginação por cursor (id > último ID), evitando OFFSET.
 * Cada intervalo contém no máximo 10.000 produtos elegíveis.
 *
 * Para a loja 142, inclui somente produtos que:
 * - tenham ai_status = 'done'; OU
 * - tenham descrição sem tags HTML com mais de 300 caracteres.
 */
function buildProductRanges(
    PDO $pdo,
    int $shopId
): array {
    $ranges = [];
    $lastId = 0;
    $page = 1;

    // Filtro especial da loja 142.
    $eligibilityFilter = '';

    if ($shopId === 142) {
        $eligibilityFilter = "
            AND (
                ai_status = 'done'
                OR CHAR_LENGTH(
                    TRIM(
                        REGEXP_REPLACE(
                            COALESCE(description, ''),
                            '<[^>]*>',
                            ''
                        )
                    )
                ) > 300
            )
        ";
    }

    $sql = "
        SELECT id
        FROM tb_products FORCE INDEX (idx_products_shop_status_id)
        WHERE shop_id = ?
          AND status = 1
          AND id > ?
          {$eligibilityFilter}
        ORDER BY id ASC
        LIMIT " . (int) SITEMAP_PRODUCT_LIMIT;

    $stmt = $pdo->prepare($sql);

    while (true) {
        $stmt->execute([$shopId, $lastId]);

        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$ids) {
            break;
        }

        $ids = array_map('intval', $ids);

        $startId = $ids[0];
        $endId = $ids[count($ids) - 1];

        $ranges[] = [
            'page' => $page,
            'start_id' => $startId,
            'end_id' => $endId,
            'products_count' => count($ids),
        ];

        $lastId = $endId;
        $page++;

        if (count($ids) < SITEMAP_PRODUCT_LIMIT) {
            break;
        }
    }

    return $ranges;
}

/**
 * Mantém tb_sitemap_product_pages sincronizada com os intervalos.
 *
 * Esta função é executada depois que os XMLs foram preparados.
 */
function saveProductRanges(
    PDO $pdo,
    int $shopId,
    array $ranges
): void {
    $pdo->beginTransaction();

    try {
        $delete = $pdo->prepare("
            DELETE FROM tb_sitemap_product_pages
            WHERE shop_id = ?
        ");

        $delete->execute([$shopId]);

        $insert = $pdo->prepare("
            INSERT INTO tb_sitemap_product_pages
                (
                    shop_id,
                    page,
                    start_id,
                    end_id,
                    products_count,
                    generated_at
                )
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        foreach ($ranges as $range) {
            $insert->execute([
                $shopId,
                $range['page'],
                $range['start_id'],
                $range['end_id'],
                $range['products_count'],
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}

/**
 * Gera um sitemap XML de produtos para um intervalo.
 *
 * O filtro especial da loja 142 é preservado:
 * quando ai_status não está concluído, produtos com
 * descrição textual de até 300 caracteres são ignorados.
 *
 * Retorna a quantidade real de URLs incluídas.
 */
function generateProductSitemap(
    PDO $pdo,
    int $shopId,
    string $baseUrl,
    array $range,
    string $filePath
): int {
    $stmt = $pdo->prepare("
        SELECT
            id,
            language,
            link,
            ai_status,
            description,
            last_modification,
            date_create
        FROM tb_products FORCE INDEX (idx_products_shop_status_id)
        WHERE shop_id = ?
          AND status = 1
          AND id BETWEEN ? AND ?
        ORDER BY id ASC
    ");

    $stmt->execute([
        $shopId,
        $range['start_id'],
        $range['end_id'],
    ]);

    $xml = startUrlset();
    $count = 0;

    while ($product = $stmt->fetch()) {
        if ($shopId === 142) {
            $aiStatus = (string) ($product['ai_status'] ?? '');

            $description = trim(
                strip_tags((string) ($product['description'] ?? ''))
            );

            $descriptionLength = function_exists('mb_strlen')
                ? mb_strlen($description, 'UTF-8')
                : strlen($description);

            if (
                $aiStatus !== 'done'
                && $descriptionLength <= 300
            ) {
                continue;
            }
        }

        $link = trim((string) $product['link'], '/');

        if ($link === '') {
            continue;
        }

        $language = (string) ($product['language'] ?? 'pt');

        $url = productUrl(
            $baseUrl,
            $language,
            $link
        );

        $lastmodValue = $product['last_modification']
            ?: $product['date_create'];

        if (!$lastmodValue) {
            continue;
        }

        $xml .= "  <url>\n";
        $xml .= '    <loc>' . xmlEscape($url) . "</loc>\n";
        $xml .= '    <lastmod>'
            . xmlEscape(sitemapLastmod($lastmodValue))
            . "</lastmod>\n";
        $xml .= "    <priority>0.80</priority>\n";
        $xml .= "  </url>\n";

        $count++;
    }

    $xml .= endUrlset();

    writeFileOrFail($filePath, $xml);

    return $count;
}

/**
 * Gera o índice com todos os sitemaps efetivamente criados.
 */
function generateSitemapIndex(
    string $baseUrl,
    string $outputPath,
    array $sitemapFiles
): void {
    $baseUrl = rtrim($baseUrl, '/') . '/';

    $handle = fopen($outputPath, 'wb');

    if ($handle === false) {
        throw new RuntimeException(
            "Não foi possível criar o índice {$outputPath}"
        );
    }

    try {
        fwrite(
            $handle,
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
        );

        $lastmod = date('Y-m-d\TH:i:sP');

        foreach (array_unique($sitemapFiles) as $filename) {
            // Aceita somente nomes de arquivos esperados.
            if (!preg_match(
                '/^sitemap_(?:pages|categories_\d+|blog_\d+|products_\d+)\.xml$/',
                $filename
            )) {
                throw new RuntimeException(
                    "Nome de sitemap inválido no índice: {$filename}"
                );
            }

            $loc = htmlspecialchars(
                $baseUrl . $filename,
                ENT_XML1 | ENT_QUOTES,
                'UTF-8'
            );

            fwrite(
                $handle,
                "  <sitemap>\n"
                . "    <loc>{$loc}</loc>\n"
                . "    <lastmod>{$lastmod}</lastmod>\n"
                . "  </sitemap>\n"
            );
        }

        fwrite($handle, "</sitemapindex>\n");

    } finally {
        fclose($handle);
    }
}


/**
 * Gera sitemaps paginados para categorias ou artigos.
 *
 * Retorna:
 * [
 *     'files' => ['sitemap_categories_1.xml', ...],
 *     'count' => 12345
 * ]
 *
 * Cada arquivo contém no máximo SITEMAP_PRODUCT_LIMIT URLs.
 */
function generateEntitySitemaps(
    PDO $pdo,
    int $shopId,
    string $baseUrl,
    string $stageDir,
    string $type
): array {
    $config = [
        'categories' => [
            'table' => 'tb_categories',
            'prefix' => 'sitemap_categories_',
            'path' => 'c/',
            'priority' => '0.70',
        ],
        'blog' => [
            'table' => 'tb_articles',
            'prefix' => 'sitemap_blog_',
            'path' => 'blog/',
            'priority' => '0.64',
        ],
    ];

    if (!isset($config[$type])) {
        throw new InvalidArgumentException(
            "Tipo de sitemap inválido: {$type}"
        );
    }

    $settings = $config[$type];
    $table = $settings['table'];

    $limit = (int) SITEMAP_PRODUCT_LIMIT;

    if ($limit < 1 || $limit > 50000) {
        throw new RuntimeException(
            'O limite de URLs por sitemap deve estar entre 1 e 50000.'
        );
    }

    $sql = "
        SELECT id, link, last_modification, date_create
        FROM {$table}
        WHERE shop_id = ?
          AND status = 1
          AND id > ?
          AND TRIM(COALESCE(link, '')) <> ''
          AND (
              NULLIF(last_modification, '') IS NOT NULL
              OR NULLIF(date_create, '') IS NOT NULL
          )
        ORDER BY id ASC
        LIMIT {$limit}
    ";

    $stmt = $pdo->prepare($sql);

    $lastId = 0;
    $fileNumber = 0;
    $totalUrls = 0;
    $files = [];

    $baseUrl = rtrim($baseUrl, '/') . '/';

    while (true) {
        $stmt->execute([$shopId, $lastId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            break;
        }

        // O cursor avança mesmo que algum registro seja ignorado.
        $lastId = (int) $rows[count($rows) - 1]['id'];

        $candidateNumber = $fileNumber + 1;

        $filename = $settings['prefix']
            . $candidateNumber
            . '.xml';

        $filepath = $stageDir . '/' . $filename;

        $handle = fopen($filepath, 'wb');

        if ($handle === false) {
            throw new RuntimeException(
                "Não foi possível criar {$filepath}"
            );
        }

        $written = 0;

        try {
            fwrite(
                $handle,
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
                . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            );

            foreach ($rows as $row) {
                $link = trim((string) ($row['link'] ?? ''));

                if ($link === '') {
                    continue;
                }

                $datetime = $row['last_modification']
                    ?: $row['date_create'];

                if (!$datetime) {
                    continue;
                }

                $timestamp = strtotime((string) $datetime);

                if ($timestamp === false) {
                    continue;
                }

                $url = $baseUrl
                    . $settings['path']
                    . ltrim($link, '/');

                $loc = htmlspecialchars(
                    $url,
                    ENT_XML1 | ENT_QUOTES,
                    'UTF-8'
                );

                $lastmod = date(
                    'Y-m-d\TH:i:sP',
                    $timestamp
                );

                $xml = "  <url>\n"
                    . "    <loc>{$loc}</loc>\n"
                    . "    <lastmod>{$lastmod}</lastmod>\n"
                    . "    <priority>{$settings['priority']}</priority>\n"
                    . "  </url>\n";

                if (fwrite($handle, $xml) === false) {
                    throw new RuntimeException(
                        "Erro ao escrever {$filepath}"
                    );
                }

                $written++;
            }

            fwrite($handle, "</urlset>\n");

        } finally {
            fclose($handle);
        }

        if ($written > 0) {
            $files[] = $filename;
            $fileNumber++;
            $totalUrls += $written;

            echo "[OK] Loja {$shopId}: {$filename} — "
                . "{$written} URLs.\n";
        } else {
            // Não publica arquivos vazios.
            @unlink($filepath);
        }

        if (count($rows) < $limit) {
            break;
        }
    }

    return [
        'files' => $files,
        'count' => $totalUrls,
    ];
}

/**
 * Remove arquivos antigos que não fazem mais parte do índice.
 */
function removeStaleProductSitemaps(
    string $shopDir,
    int $currentProductSitemapsCount
): void {
    $files = glob(
        $shopDir . '/sitemap_products_*.xml'
    ) ?: [];

    foreach ($files as $file) {
        if (
            !preg_match(
                '/sitemap_products_(\d+)\.xml$/',
                basename($file),
                $matches
            )
        ) {
            continue;
        }

        $page = (int) $matches[1];

        if ($page > $currentProductSitemapsCount) {
            @unlink($file);
        }
    }
}

/**
 * Atualiza o estado de uma loja.
 */
function updateSitemapState(
    PDO $pdo,
    int $shopId,
    string $status,
    array $data = []
): void {
    $allowedStatuses = [
        'pending',
        'processing',
        'completed',
        'error',
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        throw new InvalidArgumentException(
            "Status inválido: {$status}"
        );
    }

    $pagesCount = (int) ($data['pages_count'] ?? 0);
    $productsCount = (int) ($data['products_count'] ?? 0);
    $productSitemapsCount = (int) (
        $data['product_sitemaps_count'] ?? 0
    );

    $errorMessage = $data['error_message'] ?? null;

    if ($status === 'processing') {
        $stmt = $pdo->prepare("
            INSERT INTO tb_sitemap_static
                (
                    shop_id,
                    status,
                    generation_token,
                    started_at,
                    finished_at,
                    error_message
                )
            VALUES (?, 'processing', UUID(), NOW(), NULL, NULL)
            ON DUPLICATE KEY UPDATE
                status = 'processing',
                generation_token = UUID(),
                started_at = NOW(),
                finished_at = NULL,
                error_message = NULL
        ");

        $stmt->execute([$shopId]);

        return;
    }

    if ($status === 'completed') {
        $stmt = $pdo->prepare("
            INSERT INTO tb_sitemap_static
                (
                    shop_id,
                    status,
                    pages_count,
                    products_count,
                    product_sitemaps_count,
                    generated_at,
                    started_at,
                    finished_at,
                    next_generation_at,
                    error_message
                )
            VALUES (
                ?, 'completed', ?, ?, ?,
                NOW(), NOW(), NOW(),
                DATE_ADD(NOW(), INTERVAL "
                    . SITEMAP_GENERATION_INTERVAL_HOURS
                    . " HOUR),
                NULL
            )
            ON DUPLICATE KEY UPDATE
                status = 'completed',
                pages_count = VALUES(pages_count),
                products_count = VALUES(products_count),
                product_sitemaps_count = VALUES(product_sitemaps_count),
                generated_at = NOW(),
                finished_at = NOW(),
                next_generation_at = DATE_ADD(
                    NOW(),
                    INTERVAL "
                        . SITEMAP_GENERATION_INTERVAL_HOURS
                        . " HOUR
                ),
                error_message = NULL
        ");

        $stmt->execute([
            $shopId,
            $pagesCount,
            $productsCount,
            $productSitemapsCount,
        ]);

        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO tb_sitemap_static
            (
                shop_id,
                status,
                started_at,
                finished_at,
                error_message
            )
        VALUES (?, ?, NOW(), NOW(), ?)
        ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            finished_at = NOW(),
            error_message = VALUES(error_message)
    ");

    $stmt->execute([
        $shopId,
        $status,
        $errorMessage,
    ]);
}

/**
 * Apaga os arquivos de uma pasta temporária.
 */
function removeTemporaryDirectory(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }

    $items = scandir($directory);

    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . '/' . $item;

        if (is_dir($path)) {
            removeTemporaryDirectory($path);
        } else {
            @unlink($path);
        }
    }

    @rmdir($directory);
}

/**
 * Publica os arquivos XML gerados.
 *
 * Publica o índice por último e remove sitemaps antigos
 * que não fazem parte da nova geração.
 */
function publishSitemaps(
    string $stageDir,
    string $shopDir
): void {
    if (!is_dir($shopDir) && !mkdir($shopDir, 0755, true)) {
        throw new RuntimeException(
            "Não foi possível criar {$shopDir}"
        );
    }

    $stagedFiles = glob($stageDir . '/sitemap_*.xml');

    if ($stagedFiles === false) {
        throw new RuntimeException(
            "Não foi possível listar arquivos em {$stageDir}"
        );
    }

    $expectedFiles = [];

    foreach ($stagedFiles as $file) {
        $expectedFiles[] = basename($file);
    }

    if (!in_array('sitemap_index.xml', $expectedFiles, true)) {
        throw new RuntimeException(
            'O sitemap_index.xml não foi gerado.'
        );
    }

    // Publica primeiro todos os sitemaps individuais.
    foreach ($stagedFiles as $source) {
        $filename = basename($source);

        if ($filename === 'sitemap_index.xml') {
            continue;
        }

        $destination = $shopDir . '/' . $filename;

        if (!rename($source, $destination)) {
            throw new RuntimeException(
                "Falha ao publicar {$filename}"
            );
        }
    }

    // Remove arquivos antigos que não pertencem à nova geração.
    $existingFiles = glob($shopDir . '/sitemap_*.xml');

    if ($existingFiles === false) {
        throw new RuntimeException(
            "Não foi possível listar arquivos em {$shopDir}"
        );
    }

    foreach ($existingFiles as $existingFile) {
        $filename = basename($existingFile);

        if (!in_array($filename, $expectedFiles, true)) {
            if (!unlink($existingFile)) {
                throw new RuntimeException(
                    "Não foi possível remover sitemap antigo: {$filename}"
                );
            }
        }
    }

    // O índice é sempre o último arquivo publicado.
    $indexSource = $stageDir . '/sitemap_index.xml';
    $indexDestination = $shopDir . '/sitemap_index.xml';

    if (!rename($indexSource, $indexDestination)) {
        throw new RuntimeException(
            'Falha ao publicar sitemap_index.xml'
        );
    }
}

/**
 * Gera os sitemaps de uma loja.
 */
function generateShopSitemaps(
    PDO $pdo,
    int $shopId,
    string $staticRoot
): void {
    $lockName = 'sitemap_static_shop_' . $shopId;

    $lockStmt = $pdo->prepare(
        'SELECT GET_LOCK(?, 0)'
    );

    $lockStmt->execute([$lockName]);

    if ((int) $lockStmt->fetchColumn() !== 1) {
        echo "[SKIP] Loja {$shopId}: já existe uma geração em execução.\n";
        return;
    }

    $stageDir = null;

    try {
        echo "[START] Gerando sitemaps da loja {$shopId}\n";

        updateSitemapState(
            $pdo,
            $shopId,
            'processing'
        );

        $baseUrl = getShopBaseUrl(
            $pdo,
            $shopId
        );

        if (
            !is_dir($staticRoot)
            && !mkdir($staticRoot, 0755, true)
        ) {
            throw new RuntimeException(
                "Não foi possível criar {$staticRoot}"
            );
        }

        $shopDir = $staticRoot . '/' . $shopId;

        $token = bin2hex(random_bytes(8));

        $stageDir = $staticRoot
            . '/.tmp-'
            . $shopId
            . '-'
            . $token;

        if (!mkdir($stageDir, 0755, true)) {
            throw new RuntimeException(
                "Não foi possível criar o diretório temporário {$stageDir}"
            );
        }

        /*
        * 1. Páginas institucionais.
        */
        $pagesCount = generatePagesSitemap(
            $pdo,
            $shopId,
            $baseUrl,
            $stageDir . '/sitemap_pages.xml'
        );

        $sitemapFiles = [];

        if ($pagesCount > 0) {
            $sitemapFiles[] = 'sitemap_pages.xml';
        } else {
            @unlink($stageDir . '/sitemap_pages.xml');
        }

        echo "[OK] Loja {$shopId}: {$pagesCount} URLs de páginas.\n";

        /*
        * 2. Categorias.
        */
        $categoriesResult = generateEntitySitemaps(
            $pdo,
            $shopId,
            $baseUrl,
            $stageDir,
            'categories'
        );

        $sitemapFiles = array_merge(
            $sitemapFiles,
            $categoriesResult['files']
        );

        echo "[DONE] Loja {$shopId}: "
            . $categoriesResult['count']
            . " URLs de categorias em "
            . count($categoriesResult['files'])
            . " sitemaps.\n";

        /*
        * 3. Artigos do blog.
        */
        $blogResult = generateEntitySitemaps(
            $pdo,
            $shopId,
            $baseUrl,
            $stageDir,
            'blog'
        );

        $sitemapFiles = array_merge(
            $sitemapFiles,
            $blogResult['files']
        );

        echo "[DONE] Loja {$shopId}: "
            . $blogResult['count']
            . " URLs de blog em "
            . count($blogResult['files'])
            . " sitemaps.\n";

        /*
        * 4. Produtos.
        */
        $ranges = buildProductRanges($pdo, $shopId);

        $totalProducts = 0;
        $productSitemapsCount = 0;

        foreach ($ranges as $range) {
            $filename = 'sitemap_products_'
                . $range['page']
                . '.xml';

            $filepath = $stageDir . '/' . $filename;

            $count = generateProductSitemap(
                $pdo,
                $shopId,
                $baseUrl,
                $range,
                $filepath
            );

            if ($count > 0) {
                $totalProducts += $count;
                $productSitemapsCount++;
                $sitemapFiles[] = $filename;
            } else {
                // Evita arquivos de produtos vazios.
                @unlink($filepath);
            }

            echo "[OK] Loja {$shopId}: {$filename} — {$count} URLs.\n";
        }

        /*
        * 5. Índice com todos os arquivos não vazios.
        */
        generateSitemapIndex(
            $baseUrl,
            $stageDir . '/sitemap_index.xml',
            $sitemapFiles
        );

        /*
        * 6. Atualiza os intervalos de produtos.
        */
        saveProductRanges(
            $pdo,
            $shopId,
            $ranges
        );

        /*
        * 7. Publica todos os XMLs e remove os arquivos antigos.
        */
        publishSitemaps(
            $stageDir,
            $shopDir
        );

        /*
        * 8. Marca a geração como concluída.
        */
        updateSitemapState(
            $pdo,
            $shopId,
            'completed',
            [
                'pages_count' => $pagesCount,
                'products_count' => $totalProducts,
                'product_sitemaps_count' => $productSitemapsCount,
            ]
        );

        echo "[DONE] Loja {$shopId}: "
            . "{$pagesCount} páginas, "
            . "{$categoriesResult['count']} categorias, "
            . "{$blogResult['count']} URLs de blog e "
            . "{$totalProducts} produtos.\n";

    } catch (Throwable $e) {
        try {
            updateSitemapState(
                $pdo,
                $shopId,
                'error',
                [
                    'error_message' => mb_substr(
                        $e->getMessage(),
                        0,
                        5000
                    ),
                ]
            );
        } catch (Throwable $stateException) {
            echo "[ERROR] Não foi possível atualizar o estado: "
                . $stateException->getMessage()
                . "\n";
        }

        echo "[ERROR] Loja {$shopId}: "
            . $e->getMessage()
            . "\n";

    } finally {
        if ($stageDir !== null) {
            removeTemporaryDirectory($stageDir);
        }

        try {
            $releaseStmt = $pdo->prepare(
                'SELECT RELEASE_LOCK(?)'
            );

            $releaseStmt->execute([$lockName]);
        } catch (Throwable $e) {
            echo "[WARN] Não foi possível liberar o lock: "
                . $e->getMessage()
                . "\n";
        }
    }
}

/**
 * Entrada do worker.
 */
try {
    $isScheduled = in_array('--scheduled', $argv ?? [], true);

    $shopId = null;

    foreach (($argv ?? []) as $arg) {
        if (preg_match('/^--shop-id=(\d+)$/', $arg, $matches)) {
            $shopId = (int) $matches[1];
            break;
        }
    }

    if ($shopId !== null && $shopId <= 0) {
        fwrite(STDERR, "shop-id inválido.\n");
        exit(1);
    }

    $executionMode = $isScheduled ? 'automática' : 'manual';

    echo sprintf(
        "[%s] Iniciando geração de sitemaps (%s).\n",
        date('Y-m-d H:i:s'),
        $executionMode
    );

    if ($shopId !== null) {
        if ($shopId <= 0) {
            throw new InvalidArgumentException(
                'O shop_id deve ser maior que zero.'
            );
        }

        generateShopSitemaps(
            $pdo,
            $shopId,
            $staticRoot
        );

        exit(0);
    }

    /*
     * Sem --shop-id, seleciona lojas que precisam de geração.
     *
     * Uma loja completed só volta à fila quando next_generation_at
     * vence. Lojas novas, pending e error também entram.
     */
    $stmt = $pdo->query("
        SELECT DISTINCT d.shop_id
        FROM tb_domains d
        LEFT JOIN tb_sitemap_static s
            ON s.shop_id = d.shop_id
        WHERE d.shop_id > 0
          AND (
                s.id IS NULL
                OR s.status IN ('pending', 'error')
                OR (
                    s.status = 'completed'
                    AND (
                        s.next_generation_at IS NULL
                        OR s.next_generation_at <= NOW()
                    )
                )
          )
        ORDER BY d.shop_id ASC
    ");

    $shopIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$shopIds) {
        echo "[INFO] Nenhuma loja precisa de geração neste momento.\n";
        exit(0);
    }

    foreach ($shopIds as $id) {
        generateShopSitemaps(
            $pdo,
            (int) $id,
            $staticRoot
        );
    }

} catch (Throwable $e) {
    fwrite(
        STDERR,
        "[FATAL] " . $e->getMessage() . PHP_EOL
    );

    exit(1);
}