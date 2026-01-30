<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");


$arrParam = array();
foreach ($_POST as $key => $val) {
    $arrParam[$key] = $val;
}

$excel = false;
include_once(ROOT_SYS_INC . "querys/graficos_acessos.php");

if (seNuloOuVazio($aviso)) {
    $grafico = gerarDadosGraficoLinha($arrTitulos, $arrDados);
    ?>
    <script>
        jQuery(document).ready(function () {
    <?php echo $grafico; ?>

            var options = {
                series: [{
                        name: 'QTD',
                        data: linha
                    }],
                chart: {
                    type: 'bar',
                    height: 500
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5,
                        borderRadiusApplication: 'end'
                    }
                },
                dataLabels: {
                    enabled: true
                },
                yaxis: {
                    title: {
                        text: 'Logins'
                    }
                },
                xaxis: {
                    type: 'date',
                    categories: coluna/*,
                     labels: {
                     rotate: -90
                     }*/
                }
            };
            var chart = new ApexCharts(document.querySelector("#div_load"), options);
            chart.render();
        });
    </script>
    <?php
} else {
    echo carregarMensagem("A", $aviso, 12, false);
}
?>