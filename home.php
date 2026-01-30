<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

GSecurity::verificarPermissao("HOME");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Início");
$header->addMenu("HOME", "Início", "Página principal do sistema");
$header->addTheme(Theme::addLib(array("graficos")));
$header->show(isFrame(), $breadcrumb);

if (GSecurity::verificarPermissao("GRAFICOS", false)) {

    // <editor-fold desc="Buscar dados de usuários">
    $mysql = new GDbMysql();
    $total_usuarios = $mysql->executeCombo("SELECT 'count', COUNT(usu_int_codigo) FROM usuario;");
    $total_cursos = $mysql->executeCombo("SELECT 'count', COUNT(cur_int_codigo) FROM ava_curso;");
    $total_escola = $mysql->executeCombo("SELECT 'count', COUNT(esc_int_codigo) FROM escola;");

    $logins = $mysql->executeCombo("SELECT DATE_FORMAT(eve_dti_criacao, '%d/%m/%Y') as data, COUNT(eve_int_codigo) FROM evento WHERE eve_var_titulo = 'Autenticação realizada com Sucesso' GROUP BY DATE_FORMAT(eve_dti_criacao, '%d/%m/%Y') ORDER BY data LIMIT 7;");
    $arrLogins = array_values($logins);
    $loginsTotais = array_sum($arrLogins);
    $loginsDias = implode(",", $arrLogins);
    // </editor-fold>
    // <editor-fold desc="Buscar dados de gráfico">
    $grafico = carregarGraficoPizza('escola');
    // </editor-fold>

    $html = '';
    // <editor-fold desc="PAGE CONTENT">
    $html .= '<div class="col-xs-12">';
    $html .= '<!-- PAGE CONTENT BEGINS -->';
    // <editor-fold desc="Caixas">
    $html .= '<div class="col-sm-7 infobox-container">';
    // <editor-fold desc="Caixas Brancas">
    $html .= '  <a href="' . URL_SYS . 'cadastros/usuario/">';
    $html .= '    <div class="infobox infobox-red">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-users"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_usuarios['count'] . '</span>';
    $html .= '            <div class="infobox-content">Usuários</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/curso/">';
    $html .= '    <div class="infobox infobox-pink">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-list-alt"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_cursos['count'] . '</span>';
    $html .= '            <div class="infobox-content">Cursos</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/escola/">';
    $html .= '    <div class="infobox infobox-orange2">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-industry"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_escola['count'] . '</span>';
    $html .= '            <div class="infobox-content">Escolas</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    // </editor-fold>
    $html .= '    <div class="space-6"></div>';
    // <editor-fold desc="Caixas Coloridas">
    $html .= '    <div class="infobox infobox-blue infobox-small infobox-dark">';
    $html .= '        <div class="infobox-chart">';
    $html .= '            <span class="sparkline" data-values="' . $loginsDias . '"></span>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <div class="infobox-content">Logins/dia</div>';
    $html .= '            <div class="infobox-content">Total: ' . $loginsTotais . '</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    // </editor-fold>
    $html .= '</div>';
    // </editor-fold>
    $html .= '<div class="vspace-12-sm"></div>';
    // <editor-fold desc="Gráfico">
    $html .= '<div class="col-sm-5">';
    $html .= '    <div class="widget-box">';
    $html .= '        <div class="widget-header widget-header-flat widget-header-small">';
    $html .= '            <h5 class="widget-title"><i class="ace-icon fa fa-pie-chart"></i>Matrículas por Status</h5>';
    $html .= '        </div>';
    $html .= '        <div class="widget-body">';
    $html .= '            <div class="widget-main">';
    $html .= '                <div id="piechart-placeholder"></div>';
    $html .= '            </div><!-- /.widget-main -->';
    $html .= '        </div><!-- /.widget-body -->';
    $html .= '    </div><!-- /.widget-box -->';
    $html .= '</div><!-- /.col -->';
    // </editor-fold>
    $html .= '<!-- PAGE CONTENT ENDS -->';
    $html .= '</div><!-- /.col -->';
    // </editor-fold>
}

echo $html;

$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    jQuery(document).ready(function () {
        $('.easy-pie-chart.percentage').each(function () {
            var $box = $(this).closest('.infobox');
            var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
            var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
            var size = parseInt($(this).data('size')) || 50;
            $(this).easyPieChart({
                barColor: barColor,
                trackColor: trackColor,
                scaleColor: false,
                lineCap: 'butt',
                lineWidth: parseInt(size / 10),
                animate: ace.vars['old_ie'] ? false : 1000,
                size: size
            });
        });
        $('.sparkline').each(function () {
            var $box = $(this).closest('.infobox');
            var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
            $(this).sparkline('html',
                    {
                        tagValuesAttribute: 'data-values',
                        type: 'bar',
                        //barWidth: 10,
                        barColor: barColor,
                        chartRangeMin: $(this).data('min') || 0
                    });
        });
        var placeholder = $('#piechart-placeholder').css({'width': '100%', 'min-height': '300px'});
<?php echo $grafico; ?>
        function drawPieChart(placeholder, data, position) {
            $.plot(placeholder, data, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        highlight: {
                            opacity: 0.25
                        },
                        stroke: {
                            color: '#fff',
                            width: 2
                        },
                        startAngle: 2,
                        label: {
                            show: true,
                            radius: 3 / 5,
                            formatter: labelFormatter
                        }
                    }
                },
                legend: {
                    show: false
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });
        }
        drawPieChart(placeholder, data);
        placeholder.data('chart', data);
        placeholder.data('draw', drawPieChart);
    });
    function labelFormatter(label, series) {
        return "<div style='font-size:9pt; text-align:center; padding:2px; color:white; background: rgb(51 51 51 / 20%)'>" + label + "<br/><span style='font-weight: bold;'>" + series.data[0][1] + " (" + Math.round(series.percent) + "%)</span></div>";
    }
</script>