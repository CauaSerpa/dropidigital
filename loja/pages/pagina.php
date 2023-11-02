<div class="container">
    <div class="row p-4">
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item small"><a href="<?php echo INCLUDE_PATH_LOJA; ?>" class="text-decoration-none">Página inicial</a></li>
                <li class="breadcrumb-item small fw-semibold text-body-secondary text-decoration-none ms-2 active" aria-current="page"><?php echo $page['name']; ?></li>
            </ol>
        </nav>
        <div class="col-md-4">
            <h5>Institucional</h5>
            <li class="text-decoration-underline"><?php echo $page['name']; ?></li>
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_pages';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND id != :current_product ORDER BY id ASC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindParam(':current_product', $page['id']);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $product) {
                    echo "<li><a href='" . INCLUDE_PATH_LOJA . "atendimento/" . $product['link'] . "'>" . $product['name'] . "</a></li>";
                }
            ?>
        </div>
        <div class="col-md-8">
            <h3 class="mb-3"><?php echo $page['name']; ?></h3>
            <?php echo $page['content']; ?>
        </div>
    </div>
</div>
