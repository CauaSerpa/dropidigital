<div class="article-carousel container" style="margin-top: 186.39px;">
    <div class="row px-4">
        <img src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/articles/<?php echo $article['id']; ?>/<?php echo $article['image']; ?>" alt="Imagem do artigo">
    </div>
</div>

<div class="container">
    <div class="row p-4">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item small"><a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="text-decoration-none">Blog</a></li>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page"><?php echo $article['name']; ?></li>
            </ol>
        </nav>
        <div class="col-md-8">
            <?php
                //Formatacao para data
                $date_create = date("d/m/Y", strtotime($article['date_create']));
            ?>
            <div class="title mb-3">
                <h2 class="mb-0"><?php echo $article['name']; ?></h2>
                <small><?php echo $date_create; ?></small>
            </div>
            <?php echo $article['content']; ?>
        </div>
        <div class="col-md-4">
            <h5>Artigos em destaque</h5>
            <li class="text-decoration-underline <?php echo ($article['emphasis'] !== 1) ? "d-none" : ""; ?>"><?php echo $article['name']; ?></li>
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_articles';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis AND id != :current_article ORDER BY id ASC";

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
