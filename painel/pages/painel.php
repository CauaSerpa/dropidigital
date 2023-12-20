<head>
    <!--Apex charts-->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<?php
    // Calcule a data de hoje
    $dataAtual = date('Y-m-d');

    // Calcule a data de um mês atrás a partir da data de hoje
    $dataUmMesAtras = date('Y-m-d', strtotime('-1 month', strtotime($dataAtual)));

    // Recupere os dados da tabela de visitas para o período desejado e armazene-os em um array PHP chamado $dadosPorDia
    // Consulta SQL para recuperar os dados da tabela de visitas no período desejado
    $sql = "SELECT * FROM tb_visits WHERE data BETWEEN :dataUmMesAtras AND :dataAtual ORDER BY data ASC";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':dataUmMesAtras', $dataUmMesAtras);
    $stmt->bindParam(':dataAtual', $dataAtual);
    $stmt->execute();

    // Crie um array associativo onde as chaves são as datas (rótulos) e os valores são as contagens
    $dadosPorDia = [];

    // Loop através dos resultados e preencha o array associativo $dadosPorDia
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Use a data como chave no formato "dia/mês/ano"
        $data = date('d/m', strtotime($row['data']));
        $dadosPorDia[$data] = $row['contagem'];
    }

    // Obtenha o número total de dias no intervalo de datas
    $dataInicial = new DateTime($dataUmMesAtras);
    $dataFinal = new DateTime($dataAtual);
    $intervalo = $dataInicial->diff($dataFinal);
    $numeroDias = $intervalo->days + 1;

    // Crie um array para armazenar os rótulos (datas) de todos os dias no intervalo
    $labels = [];

    // Preencha o array com os dias no intervalo no formato "dia/mês/ano"
    for ($i = 0; $i < $numeroDias; $i++) {
        $data = $dataInicial->format('d/m');
        $labels[] = $data;
        $dataInicial->modify('+1 day'); // Avance para o próximo dia
    }

    // Crie um array para armazenar os valores correspondentes a cada dia no intervalo
    $valoresPorDia = [];

    // Preencha o array de valores com os valores correspondentes a cada dia
    foreach ($labels as $rotulo) {
        if (isset($dadosPorDia[$rotulo])) {
            // Se houver um valor correspondente no banco de dados, use-o
            $valoresPorDia[] = $dadosPorDia[$rotulo];
        } else {
            // Caso contrário, defina o valor como 0 ou o valor desejado para ausência de dados
            $valoresPorDia[] = 0;
        }
    }

    // Converter os arrays em formato JSON e passar para JavaScript
    $labelsJson = json_encode($labels);
    $valoresPorDiaJson = json_encode($valoresPorDia);
?>

<?php
    // Defina o fuso horário, para garantir consistência nas datas
    date_default_timezone_set('America/Sao_Paulo');

    // Obtém o primeiro dia do mês atual
    $primeiroDiaDoMesAtual = date('Y-m-01');

    // Obtém o último dia do mês atual
    $ultimoDiaDoMesAtual = date('Y-m-t');

    // Obtém o primeiro dia do mês anterior
    $primeiroDiaDoMesAnterior = date('Y-m-01', strtotime('-1 month'));

    // Obtém o último dia do mês anterior
    $ultimoDiaDoMesAnterior = date('Y-m-t', strtotime('-1 month'));

    // Consulta SQL para o mês atual
    $sqlMesAtual = "SELECT SUM(contagem) AS visitas_mes_atual
                    FROM tb_visits
                    WHERE data BETWEEN :primeiroDiaMesAtual AND :ultimoDiaMesAtual";
    $stmtMesAtual = $conn_pdo->prepare($sqlMesAtual);
    $stmtMesAtual->bindParam(':primeiroDiaMesAtual', $primeiroDiaDoMesAtual);
    $stmtMesAtual->bindParam(':ultimoDiaMesAtual', $ultimoDiaDoMesAtual);
    $stmtMesAtual->execute();
    $resultadoMesAtual = $stmtMesAtual->fetch(PDO::FETCH_ASSOC);
    $visitasMesAtual = $resultadoMesAtual['visitas_mes_atual'];

    // Consulta SQL para o mês anterior
    $sqlMesAnterior = "SELECT SUM(contagem) AS visitas_mes_anterior
                    FROM tb_visits
                    WHERE data BETWEEN :primeiroDiaMesAnterior AND :ultimoDiaMesAnterior";
    $stmtMesAnterior = $conn_pdo->prepare($sqlMesAnterior);
    $stmtMesAnterior->bindParam(':primeiroDiaMesAnterior', $primeiroDiaDoMesAnterior);
    $stmtMesAnterior->bindParam(':ultimoDiaMesAnterior', $ultimoDiaDoMesAnterior);
    $stmtMesAnterior->execute();
    $resultadoMesAnterior = $stmtMesAnterior->fetch(PDO::FETCH_ASSOC);
    $visitasMesAnterior = $resultadoMesAnterior['visitas_mes_anterior'];

    // Calcular a diferença em porcentagem
    $diferenca_em_porcentagem = (($visitasMesAtual - $visitasMesAnterior) / $visitasMesAnterior) * 100;

    if ($diferenca_em_porcentagem > 0) {
        // Arredonda a porcentagem
        $porcentagem = round($diferenca_em_porcentagem, 2);
        // Transforma a porcentagem no formato "0,00"
        $porcentagem = number_format($porcentagem, 2, ",", ".");
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m10 10.414 4 4 5.707-5.707L22 11V5h-6l2.293 2.293L14 11.586l-4-4-7.707 7.707 1.414 1.414z"></path></svg>';
        $textPorcentagem = "Mais que mês passado";

        $corPrincipal = "rgb(1, 200, 155)";
    } else {
        // Arredonda a porcentagem
        $porcentagem = round(abs($diferenca_em_porcentagem), 2);
        // Transforma a porcentagem no formato "0,00"
        $porcentagem = number_format($porcentagem, 2, ",", ".");
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m14 9.586-4 4-6.293-6.293-1.414 1.414L10 16.414l4-4 4.293 4.293L16 19h6v-6l-2.293 2.293z"></path></svg>';
        $textPorcentagem = "Menos que mês passado";

        $corPrincipal = "rgb(229, 15, 56)";
    }
?>

<?php
    // Defina o mês e ano desejados
    $mes = date("m"); // Agosto
    $ano = date("Y");

    // Consulta SQL para recuperar as contagens de visitas por dia
    $sql = "SELECT DAY(data) AS dia, SUM(contagem) AS total_visitas
            FROM tb_visits
            WHERE MONTH(data) = :mes AND YEAR(data) = :ano
            GROUP BY dia";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':mes', $mes);
    $stmt->bindParam(':ano', $ano);
    $stmt->execute();

    // Inicialize um array para armazenar as contagens diárias
    $contagens_diarias = [];

    // Loop através dos resultados da consulta
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dia = $row['dia'];
        $total_visitas = $row['total_visitas'];
        $contagens_diarias[$dia] = $total_visitas;
    }

    // Calcule a soma total das contagens diárias
    $total_visitas_mes = array_sum($contagens_diarias);
?>

<style>
    /* Created by Tivotal */

    /* Google Fonts(Poppins) */
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap");

    :root {
        --color-primary: <?php echo $corPrincipal; ?>;
    }

    .details__content h1,
    .percent {
        color: var(--color-primary) !important;
    }

    .percent svg {
        fill: var(--color-primary) !important;
    }

    .card .chart-area {
        position: relative;
    }

    body .apexcharts-tooltip.apexcharts-theme-light {
        color: var(--color-text);
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px #DDDDDD;
        padding: 8px 6px 4px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: 0;
    }

    .apexcharts-tooltip-title,
    .apexcharts-zaxistooltip-bottom.apexcharts-theme-light {
        display: none;
    }

    /* Tooltip */
    .apexcharts-xaxistooltip.apexcharts-xaxistooltip-bottom.apexcharts-theme-light.apexcharts-active {
        color: var(--color-text);
        background: rgb(255, 255, 255);
        box-shadow: 0 2px 10px #DDDDDD;
        padding: 8px 6px 4px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.5px;
        border: 0;
    }

    .apexcharts-xaxistooltip-bottom:before,
    .apexcharts-xaxistooltip-bottom:after {
        border-bottom-color: rgb(255, 255, 255) !important;
    }
</style>

<div class="system-message">
    <p>Boa Noite</p>
    <h2><?php echo $name; ?></h2>
</div>
<div class="card__container row g-3">
    <div class="card__box shop__info col-sm-4 grid">
        <div class="card grid">
            <div class="card__header">
                <h3 class="title">Acessos</h3>
            </div>
            <div class="site__metrics">
                <span class="percent">
                    <?php echo $svg; ?>
                    <?php echo $porcentagem; ?>%
                </span>
                <p><?php echo $textPorcentagem; ?></p>
            </div>
            <div class="info__number mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 64 64" fill="none"><path d="M16.1429 15.6429C14.8361 15.6429 13.5587 16.0304 12.4721 16.7564C11.3856 17.4824 10.5387 18.5143 10.0387 19.7216C9.53857 20.9288 9.40773 22.2573 9.66267 23.539C9.91761 24.8206 10.5469 25.9979 11.4709 26.922C12.3949 27.846 13.5722 28.4752 14.8539 28.7302C16.1355 28.9851 17.464 28.8543 18.6713 28.3542C19.8786 27.8541 20.9105 27.0073 21.6365 25.9207C22.3625 24.8342 22.75 23.5568 22.75 22.25C22.75 20.4977 22.0539 18.8171 20.8148 17.578C19.5757 16.339 17.8952 15.6429 16.1429 15.6429ZM16.1429 26.2143C15.3588 26.2143 14.5923 25.9818 13.9404 25.5462C13.2885 25.1106 12.7804 24.4914 12.4803 23.7671C12.1803 23.0427 12.1018 22.2456 12.2547 21.4766C12.4077 20.7076 12.7853 20.0012 13.3397 19.4468C13.8941 18.8924 14.6005 18.5148 15.3695 18.3619C16.1385 18.2089 16.9355 18.2874 17.6599 18.5875C18.3843 18.8875 19.0034 19.3956 19.439 20.0476C19.8746 20.6995 20.1071 21.4659 20.1071 22.25C20.1071 23.3014 19.6895 24.3097 18.946 25.0532C18.2026 25.7966 17.1943 26.2143 16.1429 26.2143ZM28.0357 40.75V39.4286C28.0357 36.9753 27.0612 34.6225 25.3265 32.8878C23.5917 31.1531 21.239 30.1786 18.7857 30.1786H13.5C11.0467 30.1786 8.69397 31.1531 6.95926 32.8878C5.22455 34.6225 4.25 36.9753 4.25 39.4286V40.75H6.89286V39.4286C6.89286 37.6762 7.58896 35.9957 8.82804 34.7566C10.0671 33.5175 11.7477 32.8214 13.5 32.8214H18.7857C20.538 32.8214 22.2186 33.5175 23.4577 34.7566C24.6967 35.9957 25.3929 37.6762 25.3929 39.4286V40.75H28.0357Z" fill="black"/><path d="M47.8572 15.6429C46.5504 15.6429 45.273 16.0304 44.1864 16.7564C43.0999 17.4824 42.253 18.5143 41.7529 19.7216C41.2529 20.9288 41.122 22.2573 41.377 23.539C41.6319 24.8206 42.2612 25.9979 43.1852 26.922C44.1092 27.846 45.2865 28.4752 46.5682 28.7302C47.8498 28.9851 49.1783 28.8543 50.3856 28.3542C51.5929 27.8541 52.6248 27.0073 53.3508 25.9207C54.0768 24.8342 54.4643 23.5568 54.4643 22.25C54.4643 20.4977 53.7682 18.8171 52.5291 17.578C51.29 16.339 49.6095 15.6429 47.8572 15.6429ZM47.8572 26.2143C47.0731 26.2143 46.3066 25.9818 45.6547 25.5462C45.0028 25.1106 44.4947 24.4914 44.1946 23.7671C43.8946 23.0427 43.8161 22.2456 43.969 21.4766C44.122 20.7076 44.4996 20.0012 45.054 19.4468C45.6084 18.8924 46.3148 18.5148 47.0838 18.3619C47.8528 18.2089 48.6498 18.2874 49.3742 18.5875C50.0986 18.8875 50.7177 19.3956 51.1533 20.0476C51.5889 20.6995 51.8214 21.4659 51.8214 22.25C51.8214 23.3014 51.4038 24.3097 50.6603 25.0532C49.9169 25.7966 48.9085 26.2143 47.8572 26.2143ZM59.75 40.75V39.4286C59.75 36.9753 58.7755 34.6225 57.0407 32.8878C55.306 31.1531 52.9533 30.1786 50.5 30.1786H45.2143C42.761 30.1786 40.4083 31.1531 38.6736 32.8878C36.9388 34.6225 35.9643 36.9753 35.9643 39.4286V40.75H38.6072V39.4286C38.6072 37.6762 39.3033 35.9957 40.5423 34.7566C41.7814 33.5175 43.462 32.8214 45.2143 32.8214H50.5C52.2523 32.8214 53.9329 33.5175 55.172 34.7566C56.411 35.9957 57.1072 37.6762 57.1072 39.4286V40.75H59.75Z" fill="black"/><rect x="24.9524" y="37.6667" width="14.0952" height="3.52381" fill="white"/><path d="M32 20.9286C30.6932 20.9286 29.4158 21.3161 28.3292 22.0421C27.2427 22.7681 26.3958 23.8 25.8958 25.0073C25.3957 26.2146 25.2648 27.543 25.5198 28.8247C25.7747 30.1064 26.404 31.2836 27.328 32.2077C28.252 33.1317 29.4293 33.761 30.711 34.0159C31.9926 34.2708 33.3211 34.14 34.5284 33.6399C35.7357 33.1398 36.7676 32.293 37.4936 31.2064C38.2196 30.1199 38.6071 28.8425 38.6071 27.5357C38.6071 25.7834 37.911 24.1028 36.6719 22.8638C35.4328 21.6247 33.7523 20.9286 32 20.9286ZM32 31.5C31.2159 31.5 30.4495 31.2675 29.7975 30.8319C29.1456 30.3963 28.6375 29.7772 28.3375 29.0528C28.0374 28.3284 27.9589 27.5313 28.1119 26.7623C28.2648 25.9933 28.6424 25.287 29.1968 24.7325C29.7512 24.1781 30.4576 23.8006 31.2266 23.6476C31.9956 23.4946 32.7927 23.5731 33.517 23.8732C34.2414 24.1732 34.8606 24.6814 35.2962 25.3333C35.7318 25.9852 35.9643 26.7517 35.9643 27.5357C35.9643 28.5871 35.5466 29.5954 34.8031 30.3389C34.0597 31.0823 33.0514 31.5 32 31.5ZM43.8928 46.0357V44.7143C43.8928 42.261 42.9183 39.9083 41.1836 38.1735C39.4489 36.4388 37.0961 35.4643 34.6428 35.4643H29.3571C26.9039 35.4643 24.5511 36.4388 22.8164 38.1735C21.0817 39.9083 20.1071 42.261 20.1071 44.7143V46.0357H22.75V44.7143C22.75 42.962 23.4461 41.2814 24.6852 40.0423C25.9242 38.8033 27.6048 38.1071 29.3571 38.1071H34.6428C36.3952 38.1071 38.0757 38.8033 39.3148 40.0423C40.5539 41.2814 41.25 42.962 41.25 44.7143V46.0357H43.8928Z" fill="black"/></svg>
                <h1><?php echo $total_visitas_mes; ?></h1>
            </div>
        </div>
    </div>






<?php
    // Nome da tabela para a busca
    $tabela = 'tb_products';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela WHERE shop_id = :shop_id AND status = :status";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':shop_id', $id);
    $stmt->bindValue(':status', 1);
    $stmt->execute();

    // Recupere o resultado da consulta
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // O resultado contém o total de produtos na chave 'total_produtos'
    $totalProdutos = $resultado['total_produtos'];

    // Calcule a porcentagem
    if ($limitProducts === "ilimitado") {
        $porcentagemProdutos = 0; // Se for ilimitado, a porcentagem é 0
    } else {
        $porcentagemProdutos = ($totalProdutos / $limitProducts) * 100;
        $porcentagemProdutos = min($porcentagemProdutos, 100); // Garanta que a porcentagem não ultrapasse 100%
    }

    // Cores com base na porcentagem
    if ($porcentagemProdutos > 80) {
        $circleColor = "rgb(229, 15, 56)"; // Vermelho
    } elseif ($porcentagemProdutos > 60) {
        $circleColor = "rgb(251, 188, 5)"; // Amarelo
    } else {
        $circleColor = "rgb(1, 200, 155)"; // Verde
    }
?>
    <style>
    .skill {
        width: 96px;
        height: 96px;
        position: relative;
    }
    .outer {
        height: 96px;
        width: 96px;
        padding: 20px;
    }
    .inner {
        height: 55px;
        width: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #number {
        font-weight: 600;
        color: #555;
    }

    .skill svg circle {
        fill: none;
        stroke: <?php echo $circleColor; ?>;
        stroke-width: 20px;
        stroke-dasharray: 240;
        stroke-dashoffset: 240;
        transform: translateY(100%) rotate(270deg);
    }

    .skill svg circle#counterProducts {
        animation: animProducts 2s linear forwards;
    }

    .skill svg circle#counterDays {
        animation: animDays 2s linear forwards;
    }

    @keyframes animProducts {
        100% {
            stroke-dashoffset: calc(240 - (2.4 * var(--counterProducts))); /* A fórmula calcula o valor de dashoffset com base em --counterProducts */;
        }
    }

    @keyframes animDays {
        100% {
            stroke-dashoffset: calc(240 - (2.4 * var(--counterDays))); /* A fórmula calcula o valor de dashoffset com base em --counterDays */;
        }
    }

    .skill svg {
        position: absolute;
        top: 0;
        left: 0;
    }
</style>









    <div class="card__box shop__info col-sm-4 grid">
        <div class="card grid">
            <div class="card__header">
                <h3 class="title">Produtos Ativos</h3>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="link">Alterar Plano</a>
            </div>
            <div class="plan__metrics">
                <div class="chart-js">
                    <div class="skill">
                        <div class="outer">
                            <div class="inner">
                            </div>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="96px" height="96px">
                            <defs>
                                <linearGradient id="GradientColor">
                                    <stop offset="0%" stop-color="#DA22FF" />
                                    <stop offset="100%" stop-color="#9733EE" />
                                </linearGradient>
                            </defs>
                            <circle id="counterProducts" cx="48" cy="48" r="38" stroke-linecap="round"></circle>
                        </svg>
                    </div>
                </div>
                <div class="text line-height align-self-end">
                    <div class="d-flex align-items-baseline mb-3">
                        <h1 class="fw-semibold mb-0 mx-2"><?php echo $totalProdutos; ?></h1>
                        <p class="fs-5">de <?php echo $limitProducts; ?></p>
                    </div>
                    <?php
                        if ($limitProducts == "ilimitado")
                        {
                            echo '<span class="warning">Sua loja não possui limite de produtos</span>';
                        } else {
                            echo '<span class="warning">Sua loja consumiu ' . $porcentagemProdutos . '% do limite.</span>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Valor inserido pelo php
        let valor = <?php echo $totalProdutos; ?>;
        let valorInserido = <?php echo $porcentagemProdutos; ?>; // Porcentagem desejada

        let duracaoPadrao = 2000; // Duração da animação em milissegundos

        // Calcule o intervalo com base na valorInserido (quanto menor a valorInserido, maior o intervalo)
        let intervalo = (duracaoPadrao * valorInserido) / 100;

        // Função para atualizar a porcentagem e ajustar a variável --counterProducts
        function updatePercentage(newPercentage) {
            if (newPercentage >= 0 && newPercentage <= 100) {
                counter = newPercentage;
                document.documentElement.style.setProperty('--counterProducts', counter); // Atualiza a variável CSS
                number.innerHTML = `${counter}%`;
            }
        }

        // Simule uma mudança na porcentagem, por exemplo, para valorInserido%
        setTimeout(() => {
            updatePercentage(valorInserido);
        }, intervalo);
    </script>










<?php
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    $sql = "SELECT * FROM $tabela WHERE status = :status OR status = :status1 AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 'ACTIVE');
    $stmt->bindValue(':status1', 'RECEIVED');
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    // Recuperar os resultados
    $subs = $stmt->fetch(PDO::FETCH_ASSOC);

    // Data final do plano (substitua isso pela sua data)
    $dataFinalDoPlano = $subs['due_date'];

    // Obtém o timestamp da data final do plano
    $timestampDataFinal = strtotime($dataFinalDoPlano);

    // Obtém o timestamp da data atual
    $timestampDataAtual = time();

    // Calcula a diferença em segundos
    $diferencaEmSegundos = $timestampDataFinal - $timestampDataAtual;

    // Calcula a diferença em dias
    $diferencaEmDias = floor($diferencaEmSegundos / (60 * 60 * 24));

    // Defina o número total de dias
    $diasTotais = 30;

    // Calcula a porcentagem de dias restantes
    $percentDays = ($diferencaEmDias / $diasTotais) * 100;
?>





    <div class="card__box shop__info col-sm-4 grid">
        <div class="card grid">
            <div class="card__header">
                <h3 class="title">Ciclo do Cartão</h3>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="link">Alterar Plano</a>
            </div>
            <div class="plan__metrics">
                <div class="chart-js">
                    <div class="skill">
                        <div class="outer">
                            <div class="inner">
                            </div>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="96px" height="96px">
                            <defs>
                                <linearGradient id="GradientColor">
                                    <stop offset="0%" stop-color="#DA22FF" />
                                    <stop offset="100%" stop-color="#9733EE" />
                                </linearGradient>
                            </defs>
                            <circle id="counterDays" cx="48" cy="48" r="38" stroke-linecap="round"></circle>
                        </svg>
                    </div>
                </div>
                <div class="text line-height">
                    <h4 class="fw-semibold mb-0"><?php echo $diferencaEmDias; ?> dias</h4>
                    <p class="fs-6">para finalizar o ciclo</p>
                    <span class="warning">Seu plano <?php echo $plan_name; ?> será renovado automaticamente.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Valor inserido pelo php
    let valor1 = <?php echo $percentDays; ?>;

    let duracaoPadrao1 = 2000; // Duração da animação em milissegundos

    // Calcule o intervalo com base na valorInserido (quanto menor a valorInserido, maior o intervalo)
    let intervalo1 = (duracaoPadrao1 * valor1) / 100;

    // Função para atualizar a porcentagem e ajustar a variável --counterDays
    function updatePercentage1(newPercentage1) {
        if (newPercentage1 >= 0 && newPercentage1 <= 100) {
            counter = newPercentage1;
            document.documentElement.style.setProperty('--counterDays', counter); // Atualiza a variável CSS
            number.innerHTML = `${counter}%`;

            console.log(counter + "%");
        }
    }

    // Simule uma mudança na porcentagem, por exemplo, para valorInserido%
    setTimeout(() => {
        updatePercentage1(valor1);
    }, intervalo1);
</script>

<div class="card__container row g-3">
    <div class="card__box shop__info col-sm-8 grid">
        <div class="card grid">
            <div class="card__header">
                <h3 class="title">Acessos</h3>
                <a href="#" class="link">Ver Mais</a>
            </div>
            <div class="shop__details">
                <p class="text-light">ACESSOS NO MÊS</p>
                <div class="details__content">
                    <h1><?php echo $total_visitas_mes; ?></h1>
                    <div class="shop__metrics">
                        <span class="percent">
                            <?php echo $svg; ?>
                            <?php echo $porcentagem; ?>%
                        </span>
                        <p><?php echo $textPorcentagem; ?></p>
                    </div>
                </div>
                <div class="chart-area">
                    <div class="grid"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card__box top__products col-sm-4 grid">
        <div class="card grid">
            <div class="card__header">
                <h5 class="fw-semibold mb-0">
                    TOP Produtos
                    <i class="bx bx-help-circle small"></i>
                </h5>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="link">Ver Mais</a>
            </div>
            <div class="top-products">
                <ul class="mb-0">
                    <li class="product__item">
                        <div class="container__image">
                            <img class="product__image" src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/23/foto_teste.png" alt="Imagem do produto">
                        </div>
                        <div class="product__details">
                            <h6 class="product__name fs-6 fw-semibold mb-0">Produto 1</h6>
                            <div class="product__info">
                                <p class="small">923 Cliques</p>
                                <p class="small"><span class="fw-semibold">Média de tempo:</span> 19 min.</p>
                            </div>
                        </div>
                    </li>
                    <li class="product__item">
                        <div class="container__image">
                            <img class="product__image" src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/23/foto_teste.png" alt="Imagem do produto">
                        </div>
                        <div class="product__details">
                            <h6 class="product__name fs-6 fw-semibold mb-0">Produto 2</h6>
                            <div class="product__info">
                                <p class="small">563 Cliques</p>
                                <p class="small"><span class="fw-semibold">Média de tempo:</span> 23 min.</p>
                            </div>
                        </div>
                    </li>
                    <li class="product__item">
                        <div class="container__image">
                            <img class="product__image" src="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/23/foto_teste.png" alt="Imagem do produto">
                        </div>
                        <div class="product__details">
                            <h6 class="product__name fs-6 fw-semibold mb-0">Produto 3</h6>
                            <div class="product__info">
                                <p class="small">214 Cliques</p>
                                <p class="small"><span class="fw-semibold">Média de tempo:</span> 14 min.</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <a href="https://pay.hotmart.com/I87196553U" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-1.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a href="https://mpago.la/1b4q8Mk" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-2.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a href="https://mpago.la/2gmWgJQ" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-3.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a herf="https://mpago.la/2aJm9Bz" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-4.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a href="https://mpago.la/1piXCSS" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-5.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a href="https://mpago.li/25cjbHY" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-6.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <a href="https://mpago.li/2NoQfiV" target="__black" class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-7.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </a>
    <div class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-8.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </div>
    <div class="col-sm-4">
        <img src="<?php echo INCLUDE_PATH; ?>assets/images/home-banners/banner-9.jpeg" class="w-100 rounded-2" alt="Anúncio Banner">
    </div>
</div>

<div class="card__container grid one tabPanel" style="display: grid;">
    <div class="card__box grid">
        <div class="card table">
            <div class="card__header">
                <h3 class="title h5 fw-semibold">Produtos Cadastrados</h3>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="link">Ver Mais</a>
            </div>
    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_products';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $id);
        $stmt->execute();

        $countProduct = $stmt->rowCount();

        if ($stmt->rowCount() > 0) {
    ?>
            <table>
                <thead>
                    <tr>
                        <th class="small">Nome</th>
                        <th class="small">Valor</th>
                        <th class="small">Categoria</th>
                        <th class="small">SKU</th>
                        <th class="small">Data de Criação</th>
                    </tr>
                </thead>
                <?php
                // Nome da tabela para a busca
                $tabela = 'tb_products';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC LIMIT 10";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $id);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $usuario) {
                    //Formatacao preco
                    // $price = str_replace(',', '.', str_replace('.', '', $usuario['price']));
                    $preco = $usuario['price'];

                    // Transforma o número no formato "R$ 149,90"
                    $price = "R$ " . number_format($preco, 2, ",", ".");

                    //Formatacao para data
                    $date_create = date("d/m/Y", strtotime($usuario['date_create']));

                    echo '
                        <tbody>
                            <tr>
                                <td>
                    ';

                    // Consulta SQL para selecionar todas as colunas com base no ID
                    $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':usuario_id', $usuario['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($imagens as $imagem) {
                        echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '" alt="Capa do Produto" style="width: 38px; height: 38px; object-fit: cover;">';
                    }
                    
                    echo '
                                    ' . $usuario['name'] . '
                                </td>
                                <td>' . $price . '</td>';

                    // Nome da tabela para a busca
                    $tabela = 'tb_categories';

                    $sql = "SELECT (name) FROM $tabela WHERE id = :id ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':id', $usuario['categories']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($categories as $category) {
                        echo "<td>";
                        echo $category['name'];
                        echo "</td>";
                    }
        
                    echo '
                                <td>' . $usuario['sku'] . '</td>
                                <td>' . $date_create . '</td>
                            </tr>
                        </tbody>
                    ';
                }
            ?>
            </table>
        </div>
        <?php
            } else {
                echo '
                        <div class="p-2 text-center">
                            <i class="bx bx-package" style="font-size: 3.5rem;"></i>
                            <p class="fw-semibold mb-4">Você não possui nenhum produto ativo!</p>
                            <a href="' . INCLUDE_PATH_DASHBOARD . 'criar-produto" class="btn btn-success btn-create-product text-decoration-none">+ Criar Produto</a>
                        </div>
                    ';
            }
        ?>
    </div>
</div>

<script>
    // Seus dados de exemplo (substitua pelos seus próprios dados)
    const dadosPorDia = <?php echo json_encode($valoresPorDia); ?>;

    // Filtrar rótulos para mostrar apenas os dias com valores maiores que zero
    const rótulosFiltrados = <?php echo json_encode($labels); ?>.filter((_, i) => dadosPorDia[i] > 0);

    // Filtrar valores correspondentes aos rótulos filtrados
    const valoresFiltrados = dadosPorDia.filter(valor => valor > 0);

    /* Created by Tivotal */
    let primaryColor = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-primary")
    .trim();

    let labelColor = getComputedStyle(document.documentElement)
    .getPropertyValue("--color-label")
    .trim();

    let fontFamily = getComputedStyle(document.documentElement)
    .getPropertyValue("--font-family")
    .trim();

    let defaultOptions = {
        chart: {
            tollbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
            width: "100%",
            height: 180,
            offsetY: 18,
        },

        dataLabels: {
            enabled: false,
        },
    };

    let barOptions = {
        ...defaultOptions,

        chart: {
            ...defaultOptions.chart,
            type: "area",
        },

        tooltip: {
            enabled: true,
            style: {
                fontFamily: fontFamily,
            },
            y: {
                formatter: (value) => `${value}`,
            },
        },

        series: [
            {
                name: "Visitas",
                data: valoresFiltrados, // Os valores do gráfico (contagens por dia)
            },
        ],

        colors: [primaryColor],

        fill: {
            type: "gradient",
            gradient: {
                type: "vertical",
                opacityFrom: 1,
                opacityTo: 0,
                stops: [0, 100],
                colorStops: [
                    {
                        offset: 0,
                        opacity: 0.6,
                        color: "<?php echo $corPrincipal; ?>",
                    },
                    {
                        offset: 100,
                        opacity: 0,
                        color: "rgb(255, 255, 255)",
                    },
                ],
            },
        },

        stroke: {
            colors: [primaryColor],
            lineCap: "round",
        },

        grid: {
            borderColor: "rgba(0, 0, 0, 0)",
            padding: {
                top: -30,
                right: 0,
                bottom: -8,
                left: 12,
            },
        },

        markers: {
            strokeColors: primaryColor,
        },

        yaxis: {
            show: false,
        },

        xaxis: {
            labels: {
                show: false,
            },

            axisTicks: {
                show: false,
            },

            axisBorder: {
                show: false,
            },

            crosshairs: {
                show: true,
            },

            categories: rótulosFiltrados, // Os rótulos do gráfico (datas)
        },
    };

    let chart = new ApexCharts(document.querySelector(".chart-area"), barOptions);

    chart.render();
</script>