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
</style>

<div id="blogCarousel" class="carousel container slide mb-4 px-4" data-bs-ride="carousel" style="margin-top: <?php echo ($top_highlight_bar == 1) ? "186.39px" : "154.39px"; ?>;">
    <!-- Indicators (pontos de navegação) -->
    <ol class="carousel-indicators">
    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_articles';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->bindValue(':emphasis', 1);
        $stmt->execute();

        $countBanners = $stmt->rowCount();

        // Inicializa uma variável para contar os IDs
        $contador = 0;

        // Verifica se há IDs para evitar um loop vazio
        if ($stmt->rowCount() > 0) {
            // Faça um loop para contar os IDs sequencialmente
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currentId = $row['id'];

                $active = ($contador == 0) ? 'active' : ''; // Adicione a classe active ao ID 1
                echo "<li data-bs-target='#blogCarousel' data-bs-slide-to='" . $contador . "' class='" . $active . "'></li>";

                $contador++;
            }
        } else {
            echo "Nenhum ID encontrado.";
        }
    ?>
    </ol>

    <!-- Slides (itens do carrossel) -->
    <div class="carousel-inner">
        <?php
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis ORDER BY id DESC";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->bindValue(':status', 1);
            $stmt->bindValue(':emphasis', 1);
            $stmt->execute();

            // Recuperar os resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Inicialize uma variável de controle
            $primeiroElemento = true;

            // Loop através dos resultados e exibir todas as colunas
            foreach ($resultados as $article) {
                //Formatacao para data
                $date_create = date("d/m/Y", strtotime($article['date_create']));

                // Adicione a classe especial apenas ao primeiro elemento
                $active = $primeiroElemento ? 'active' : '';

                echo '<div class="carousel-item ' . $active . '">';
                echo     '<div class="article-carousel">';
                echo         '<div class="article-item row g-0">';
                echo             '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/articles/' . $article['id'] . '/' . $article['image'] . '" alt="' . $article['name'] . '" class="col-md-8" style="height: 535px; object-fit: cover;">';
                echo             '<div class="article-info col-md-4">';
                echo                 '<h4 class="m-0">' . $article['name'] . '</h4>';
                echo                 '<small class="mb-3">' . $date_create . '</small>';
                echo                 '<div class="container-button">';
                echo                     '<a href="' . INCLUDE_PATH_LOJA . 'blog/' . $article['link'] . '" class="btn btn-dark py-1 px-4">Ver mais</a>';
                echo                 '</div>';
                echo             '</div>';
                echo         '</div>';
                echo     '</div>';
                echo '</div>';

                // Marque que o primeiro elemento foi processado
                $primeiroElemento = false;
            }
        ?>
    </div>

    <!-- Controles (setas de navegação) -->
    <a class="carousel-control-prev blog-carousel-control" href="#blogCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </a>
    <a class="carousel-control-next blog-carousel-control" href="#blogCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Próximo</span>
    </a>
</div>

<div class="container">
    <div class="row p-2">
        <div class="col-md-8">
            <h5>Últimos Artigos</h5>
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_articles';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $article) {
                    echo '<div class="article-preview pb-4 mb-4">';
                    echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/articles/' . $article['id'] . '/' . $article['image'] . '" alt="' . $article['name'] . '">';
                    echo '<div class="article-info">';
                    echo '<h5 class="mb-0">' . $article['name'] . '</h5>';
                    echo '<div class="container-button">';
                    echo '<a href="' . INCLUDE_PATH_LOJA . 'blog/' . $article['link'] . '" class="text-decoration-underline">Ver mais</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            ?>
        </div>
        <div class="col-md-4">
            <h5>Artigos em destaque</h5>
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_articles';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
                $stmt->bindValue(':emphasis', 1);
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

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

<script>
    var blogCarrossel = new bootstrap.Carousel(document.getElementById('blogCarousel'), {
        interval: 2000, // Tempo de exibição de cada slide em milissegundos (opcional)
        wrap: true // Se o carrossel deve voltar ao primeiro slide após o último (opcional)
    });
</script>