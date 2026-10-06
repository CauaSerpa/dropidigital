<style>
    .article-product-cta {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        transition: .2s ease;
    }

    .article-product-cta:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,.06);
    }

    .article-product-cta h4 {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .article-ad {
        min-height: 90px;
        overflow: hidden;
    }
</style>

<?php if (defined('ADSENSE_CLIENT') && ADSENSE_CLIENT !== '') : ?>
<script async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= htmlspecialchars(ADSENSE_CLIENT, ENT_QUOTES, 'UTF-8'); ?>"
    crossorigin="anonymous">
</script>
<?php endif; ?>

<div class="article-carousel container" style="margin-top: 186.39px;">
    <?php if (!empty($article['image'])): ?>
    <div class="row px-4">
        <img src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/articles/<?php echo $article['id']; ?>/<?php echo $article['image']; ?>" alt="Imagem do artigo">
    </div>
    <?php endif; ?>
</div>

<div class="container">
    <div class="row p-4">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item small"><a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="text-decoration-none"><?= __('blog') ?></a></li>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page"><?php echo $article['name']; ?></li>
            </ol>
        </nav>
        <div class="col-md-8">
            <?php if (defined('ADSENSE_CLIENT') && ADSENSE_CLIENT !== '' && ADSENSE_ARTICLE_TOP_SLOT !== '') : ?>
                <div class="article-ad mb-4 text-center" aria-label="Publicidade">
                    <ins class="adsbygoogle"
                        style="display:block"
                        data-ad-client="<?php echo htmlspecialchars(ADSENSE_CLIENT, ENT_QUOTES, 'UTF-8'); ?>"
                        data-ad-slot="<?php echo htmlspecialchars(ADSENSE_ARTICLE_TOP_SLOT, ENT_QUOTES, 'UTF-8'); ?>"
                        data-ad-format="auto"
                        data-full-width-responsive="true"></ins>
                    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                </div>
            <?php endif; ?>

            <?php
                //Formatacao para data
                $date_create = date("d/m/Y", strtotime($article['date_create']));
            ?>
            <div class="title mb-3">
                <h2 class="mb-0"><?php echo $article['name']; ?></h2>
                <small><?php echo $date_create; ?></small>
            </div>
            <?php echo $article['content']; ?>

            <?php
                $stmtProducts = $conn_pdo->prepare("
                    SELECT
                        p.id,
                        p.name,
                        p.link,
                        i.nome_imagem,
                        i.usuario_id
                    FROM tb_article_products ap
                    INNER JOIN tb_products p
                        ON p.id = ap.product_id
                    LEFT JOIN imagens i
                        ON i.usuario_id = p.id
                    WHERE ap.article_id = :article_id
                        AND p.shop_id = :shop_id
                    ORDER BY ap.id ASC
                ");

                $stmtProducts->bindValue(
                    ':article_id',
                    (int)$article['id'],
                    PDO::PARAM_INT
                );

                $stmtProducts->bindValue(
                    ':shop_id',
                    (int)$shop_id,
                    PDO::PARAM_INT
                );

                $stmtProducts->execute();

                $productsCTA = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($productsCTA)) :
            ?>

                <div class="article-products-cta mt-5">

                    <?php foreach ($productsCTA as $productCTA) : ?>

                        <?php
                            if (empty($productCTA['nome_imagem'])) {
                                $productCTA['image'] =
                                    INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg';
                            } else {
                                $productCTA['image'] =
                                    CDN_BASE_URL .
                                    'products/' .
                                    $productCTA['usuario_id'] .
                                    '/' .
                                    $productCTA['nome_imagem'];
                            }
                        ?>

                        <div class="article-product-cta mb-4 p-4 rounded border">

                            <div class="row align-items-center">

                                <div class="col-md-3 text-center mb-3 mb-md-0">

                                    <img
                                        src="<?php echo htmlspecialchars($productCTA['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="<?php echo htmlspecialchars($productCTA['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                        class="img-fluid rounded"
                                        style="max-height: 180px; object-fit: contain;"
                                    >

                                </div>

                                <div class="col-md-9">

                                    <span class="badge bg-success mb-2">
                                        Produto relacionado
                                    </span>

                                    <h4 class="mb-2">
                                        <?php echo htmlspecialchars(
                                            $productCTA['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </h4>

                                    <p class="text-muted mb-3">
                                        Gostou do que leu? Veja o produto relacionado a este artigo.
                                    </p>

                                    <a
                                        href="<?php echo INCLUDE_PATH_LOJA . $productCTA['link']; ?>"
                                        class="btn btn-success px-4"
                                    >
                                        Ver produto
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
        <div class="col-md-4">
            <h5><?= __('featured_articles') ?></h5>
            <li class="text-decoration-underline <?php echo ($article['emphasis'] !== 1) ? "d-none" : ""; ?>"><?php echo $article['name']; ?></li>
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_articles';

                $sql = "SELECT id, name, link FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis AND id != :current_article ORDER BY id ASC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
                $stmt->bindValue(':emphasis', 1);
                $stmt->bindParam(':current_article', $article['id']);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $article) {
                    echo "<li><a href='" . INCLUDE_PATH_LOJA . "blog/" . $article['link'] . "'>" . $article['name'] . "</a></li>";
                }
            ?>
        </div>
    </div>
</div>
