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
    $grafico = gerarDadosGrafico($arrTitulos, $arrDados);
    if (!seNuloOuVazio($grafico)) {
        $tipoGrafico = $arrParam["tipo"] ?? 'barras';
        ?>
        <div id="grafico"></div>
        <script>
            jQuery(document).ready(function () {
        <?php
        echo $grafico;
        switch ($tipoGrafico) {
            case 'barras':
                ?>
                        var alturaCalculada = linha.length * 30; // 30px por barra, ajuste como preferir
                        // Garante uma altura mínima para poucos dados
                        if (alturaCalculada < 300) {
                            alturaCalculada = 300;
                        }
                        var options = {
                            series: [{
                                    name: 'QTD',
                                    data: linha
                                }],
                            chart: {
                                type: 'bar',
                                height: alturaCalculada
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: true,
                                    barHeight: '85%', // Aumente para barras mais grossas, diminua para mais finas
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                style: {
                                    colors: ['#333333']
                                },
                                background: {
                                    enabled: true,
                                    foreColor: '#fff',
                                    padding: 6,
                                    borderRadius: 4,
                                    borderWidth: 1,
                                    borderColor: '#fff',
                                    opacity: 0.9,
                                    dropShadow: {
                                        enabled: true,
                                        top: 1,
                                        left: 1,
                                        blur: 1,
                                        color: '#000',
                                        opacity: 0.45
                                    }
                                },
                                dropShadow: {
                                    enabled: true,
                                    top: 1,
                                    left: 1,
                                    blur: 1,
                                    color: '#000',
                                    opacity: 0.45
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Logins'
                                },
                                labels: {
                                    show: true,
                                    rotate: 0,
                                    hideOverlappingLabels: true,
                                    style: {
                                        fontSize: '11px'
                                    }
                                }
                            },
                            xaxis: {
                                type: 'date',
                                categories: coluna
                            }
                        };
                <?php
                break;
            case 'pizza':
                ?>
                        var options = {
                            series: linha,
                            chart: {
                                type: 'pie',
                                height: 800,
                                toolbar: {show: true}
                            },
                            labels: coluna,
                            colors: [
                                '#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800', '#7D02EB', '#D4526E', '#8D5B4C',
                                '#F86624', '#D7263D', '#1B998B', '#2E294E', '#F46036', '#E71D36', '#C5D86D', '#4CC9F0',
                                '#4361EE', '#3A0CA3', '#7209B7', '#F72585', '#00F5D4', '#00BBF9', '#FEE440', '#38B000',
                                '#9EF01A', '#FF5400', '#335C67', '#FFF3B0', '#E09F3E', '#9E2A2B', '#540B0E', '#AD2831'
                            ],
                            dataLabels: {
                                enabled: true,
                                formatter: function (val, opts) {
                                    // Mostra a porcentagem na fatia apenas se for maior que 3%
                                    return val > 3 ? val.toFixed(1) + "%" : "";
                                },
                                style: {
                                    colors: ['#333333']
                                },
                                background: {
                                    enabled: true,
                                    foreColor: '#fff',
                                    padding: 6,
                                    borderRadius: 4,
                                    borderWidth: 1,
                                    borderColor: '#fff',
                                    opacity: 0.9,
                                    dropShadow: {
                                        enabled: true,
                                        top: 1,
                                        left: 1,
                                        blur: 1,
                                        color: '#000',
                                        opacity: 0.45
                                    }
                                },
                                dropShadow: {
                                    enabled: true,
                                    top: 1,
                                    left: 1,
                                    blur: 1,
                                    color: '#000',
                                    opacity: 0.45
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val, opts) {
                                        // Mostra o nome do curso e a quantidade absoluta no hover
                                        return val + " Alunos";
                                    }
                                }
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center',
                                fontSize: '12px',
                                offsetY: 7,
                                markers: {width: 12, height: 12},
                                itemMargin: {
                                    horizontal: 10,
                                    vertical: 2
                                }
                            },
                            responsive: [{
                                    breakpoint: 480,
                                    options: {
                                        chart: {width: 300},
                                        legend: {position: 'bottom'}
                                    }
                                }]
                        };
                <?php
                break;
            default:
                ?>
                        var options = {
                            chart: {
                                type: 'line'
                            }
                        }
                <?php
                break;
        }
        ?>
                var chart = new ApexCharts(document.querySelector("#grafico"), options);
                chart.render();
            });
        </script>
        <?php
    } else {
        echo carregarMensagem("A", "Nenhum dado encontrado para os filtros informados", 12, false);
    }
} else {
    echo carregarMensagem("A", $aviso, 12, false);
}
?>