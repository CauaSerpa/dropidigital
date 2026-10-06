<style>
    /* Article carousel */
    /* Controls */
    .carousel-control-prev.blog-carousel-control
    {
        transform: translateX(-100%);
    }
    .carousel-control-next.blog-carousel-control
    {
        transform: translateX(100%);
    }

    /* Item */
    .article-carousel .article-item.row.g-0
    {
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
    }
    .article-carousel .article-item .article-info
    {
        padding: 30px;
        background-color: #ebebeb;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }

    /* Article */
    .article-preview
    {
        display: flex;
        border-bottom: 1px solid #ccc;
    }
    .article-preview img
    {
        width: 260px;
        height: 150px;
        object-fit: cover;
    }
    .article-preview .article-info
    {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media screen and (max-width: 991px) {
        /* Carrossel */
        #blogCarousel
        {
            margin-top: <?php echo ($top_highlight_bar == 1) ? "99px" : "67px"; ?> !important;
        }
        #blogCarousel .carousel-control-prev
        {
            left: 32px;
        }
        #blogCarousel .carousel-control-next
        {
            right: 30px;
        }

        .article-carousel .article-item img
        {
            height: 300px !important;
        }

        .article-preview
        {
            display: block;
        }

        .article-preview img
        {
            width: 100%;
            height: auto;
        }
    }
</style>

<?php
    /*
     * Campos utilizados nesta página.
     *
     * O campo "content" é LONGTEXT e pode representar uma grande
     * quantidade de dados. Como ele não é utilizado nesta página,
     * não deve fazer parte das consultas.
     */
    $articleFields = 'id, name, image, link, date_create';

    /*
     * ============================================================
     * ARTIGOS EM DESTAQUE - CARROSSEL
     * ============================================================
     */
    $sqlFeaturedCarousel = "
        SELECT $articleFields
        FROM tb_articles
        WHERE shop_id = :shop_id
        AND status = 1
        AND emphasis = 1
        ORDER BY id DESC
        LIMIT 5
    ";

    $stmtFeaturedCarousel = $conn_pdo->prepare($sqlFeaturedCarousel);
    $stmtFeaturedCarousel->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtFeaturedCarousel->execute();

    $featuredCarouselArticles = $stmtFeaturedCarousel->fetchAll(PDO::FETCH_ASSOC);

    /*
     * ============================================================
     * ÚLTIMOS ARTIGOS
     * ============================================================
     */
    $sqlLatestArticles = "
        SELECT $articleFields
        FROM tb_articles
        WHERE shop_id = :shop_id
        AND status = 1
        ORDER BY id DESC
        LIMIT 20
    ";

    $stmtLatestArticles = $conn_pdo->prepare($sqlLatestArticles);
    $stmtLatestArticles->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtLatestArticles->execute();

    $latestArticles = $stmtLatestArticles->fetchAll(PDO::FETCH_ASSOC);

    /*
     * ============================================================
     * ARTIGOS EM DESTAQUE - LATERAL
     * ============================================================
     */
    $sqlFeaturedArticles = "
        SELECT $articleFields
        FROM tb_articles
        WHERE shop_id = :shop_id
        AND status = 1
        AND emphasis = 1
        ORDER BY id DESC
        LIMIT 10
    ";

    $stmtFeaturedArticles = $conn_pdo->prepare($sqlFeaturedArticles);
    $stmtFeaturedArticles->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
    $stmtFeaturedArticles->execute();

    $featuredArticles = $stmtFeaturedArticles->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($featuredCarouselArticles)): ?>

<div id="blogCarousel" class="carousel container slide mb-4 px-4" data-bs-ride="carousel" style="margin-top: <?php echo ($top_highlight_bar == 1) ? "186.39px" : "154.39px"; ?>;">

    <!-- Indicators -->
    <ol class="carousel-indicators">
        <?php foreach ($featuredCarouselArticles as $index => $article): ?>
            <li
                data-bs-target="#blogCarousel"
                data-bs-slide-to="<?= $index ?>"
                class="<?= $index === 0 ? 'active' : '' ?>"
            ></li>
        <?php endforeach; ?>
    </ol>

    <!-- Slides -->
    <div class="carousel-inner">
        <?php foreach ($featuredCarouselArticles as $index => $article): ?>
            <?php
                $date_create = date("d/m/Y", strtotime($article['date_create']));
                $articleName = htmlspecialchars($article['name'], ENT_QUOTES, 'UTF-8');
                $articleImage = htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8');
                $articleLink = htmlspecialchars($article['link'], ENT_QUOTES, 'UTF-8');
            ?>

            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="article-carousel">
                    <div class="article-item row g-0">
                        <img
                            src="<?= INCLUDE_PATH_DASHBOARD ?>back-end/articles/<?= $article['id'] ?>/<?= $articleImage ?>"
                            alt="<?= $articleName ?>"
                            class="col-md-8"
                            style="height: 535px; object-fit: cover;"
                        >

                        <div class="article-info col-md-4">
                            <h4 class="m-0"><?= $articleName ?></h4>
                            <small class="mb-3"><?= $date_create ?></small>

                            <div class="container-button">
                                <a
                                    href="<?= INCLUDE_PATH_LOJA ?>blog/<?= $articleLink ?>"
                                    class="btn btn-dark py-1 px-4"
                                >
                                    <?= __('see_more') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Controles -->
    <a class="carousel-control-prev blog-carousel-control" href="#blogCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><?= __('prev') ?></span>
    </a>

    <a class="carousel-control-next blog-carousel-control" href="#blogCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><?= __('next') ?></span>
    </a>
</div>

<?php endif; ?>

<div class="container">
    <div class="row p-2">

        <div class="col-md-8">
            <h5><?= __('latest_articles') ?></h5>

            <?php if (!empty($latestArticles)): ?>
                <?php foreach ($latestArticles as $article): ?>
                    <?php
                        $articleName = htmlspecialchars($article['name'], ENT_QUOTES, 'UTF-8');
                        $articleImage = htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8');
                        $articleLink = htmlspecialchars($article['link'], ENT_QUOTES, 'UTF-8');
                    ?>

                    <div class="article-preview pb-4 mb-4">
                        <img
                            src="<?= INCLUDE_PATH_DASHBOARD ?>back-end/articles/<?= $article['id'] ?>/<?= $articleImage ?>"
                            alt="<?= $articleName ?>"
                        >

                        <div class="article-info">
                            <h5 class="mb-0"><?= $articleName ?></h5>

                            <div class="container-button">
                                <a
                                    href="<?= INCLUDE_PATH_LOJA ?>blog/<?= $articleLink ?>"
                                    class="text-decoration-underline"
                                >
                                    <?= __('see_more') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <h5><?= __('featured_articles') ?></h5>

            <?php if (!empty($featuredArticles)): ?>
                <ul>
                    <?php foreach ($featuredArticles as $article): ?>
                        <?php
                            $articleName = htmlspecialchars($article['name'], ENT_QUOTES, 'UTF-8');
                            $articleLink = htmlspecialchars($article['link'], ENT_QUOTES, 'UTF-8');
                        ?>

                        <li>
                            <a href="<?= INCLUDE_PATH_LOJA ?>blog/<?= $articleLink ?>">
                                <?= $articleName ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

<?php if (!empty($featuredCarouselArticles)): ?>

<script>
    var blogCarrossel = new bootstrap.Carousel(document.getElementById('blogCarousel'), {
        interval: 2000,
        wrap: true
    });
</script>

<?php endif; ?>