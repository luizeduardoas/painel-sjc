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

$html = '';
if (GSecurity::verificarPermissao("HOMEGRAFICOS", false)) {

    // <editor-fold desc="Buscar dados de usuários">
    $mysql = new GDbMysql();
    $total_usuarios = $mysql->executeCombo("SELECT 'count', COUNT(usu_int_codigo) FROM ava_usuario;");
    $total_cargos = $mysql->executeCombo("SELECT 'count', COUNT(DISTINCT(usu_var_cargo)) FROM ava_usuario;");
    $total_funcoes = $mysql->executeCombo("SELECT 'count', COUNT(DISTINCT(usu_var_funcao)) FROM ava_usuario;");
    $total_cursos = $mysql->executeCombo("SELECT 'count', COUNT(cur_int_codigo) FROM ava_curso;");
    $total_escolas = $mysql->executeCombo("SELECT 'count', COUNT(esc_int_codigo) FROM escola;");
    $total_matriculas = $mysql->executeCombo("SELECT 'count', COUNT(mat_int_codigo) FROM ava_matricula;");
    $total_niveis = $mysql->executeCombo("SELECT 'count', COUNT(niv_int_codigo) FROM nivel;");
    $total_acessos = $mysql->executeCombo("SELECT 'count', COUNT(ace_int_codigo) FROM ava_acesso;");
    $total_naoacesso = $mysql->executeCombo("SELECT 'count', COUNT(usu_int_codigo) FROM ava_usuario usu WHERE NOT EXISTS (SELECT 1 FROM ava_acesso ace WHERE ace.usu_int_codigo = usu.usu_int_codigo);");
    $total_conclusoes = $mysql->executeCombo("SELECT 'count', COUNT(con_int_codigo) FROM ava_conclusao;");
    // </editor-fold>
    // <editor-fold desc="Buscar dados de gráfico">
    $grafico = carregarGraficoPizza('usuarios');
    // </editor-fold>
    // <editor-fold desc="PAGE CONTENT">
    $html .= '<div class="col-xs-12">';
    $html .= '<!-- PAGE CONTENT BEGINS -->';
    // <editor-fold desc="Caixas">
    $html .= '<div class="col-sm-7 infobox-container">';
    // <editor-fold desc="Caixas Brancas">
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/nivel/">';
    $html .= '    <div class="infobox infobox-blue">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-sitemap"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_niveis['count'] . '</span>';
    $html .= '            <div class="infobox-content">Estruturas Organizacionais</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/escola/">';
    $html .= '    <div class="infobox infobox-orange2">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-university"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_escolas['count'] . '</span>';
    $html .= '            <div class="infobox-content">Escolas</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/curso/">';
    $html .= '    <div class="infobox infobox-pink">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-book"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_cursos['count'] . '</span>';
    $html .= '            <div class="infobox-content">Cursos</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/matricula/">';
    $html .= '    <div class="infobox infobox-purple">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-graduation-cap"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_matriculas['count'] . '</span>';
    $html .= '            <div class="infobox-content">Matrículas</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/avausuario/">';
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
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/avausuario/">';
    $html .= '    <div class="infobox infobox-blue2">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-briefcase"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_cargos['count'] . '</span>';
    $html .= '            <div class="infobox-content">Cargos Diferentes</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'gerenciamento/avausuario/">';
    $html .= '    <div class="infobox infobox-orange">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-wrench"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_funcoes['count'] . '</span>';
    $html .= '            <div class="infobox-content">Funções Diferentes</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'tabelas/t_acessos/">';
    $html .= '    <div class="infobox infobox-green2">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-mouse-pointer"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_acessos['count'] . '</span>';
    $html .= '            <div class="infobox-content">Acessos ao AVA</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'tabelas/t_naoacesso/">';
    $html .= '    <div class="infobox infobox-brown">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-eye-slash"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_naoacesso['count'] . '</span>';
    $html .= '            <div class="infobox-content">Nunca Acessou o AVA</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    $html .= '  <a href="' . URL_SYS . 'tabelas/t_progresso/">';
    $html .= '    <div class="infobox infobox-green">';
    $html .= '        <div class="infobox-icon">';
    $html .= '            <i class="ace-icon fa fa-tasks"></i>';
    $html .= '        </div>';
    $html .= '        <div class="infobox-data">';
    $html .= '            <span class="infobox-data-number">' . $total_conclusoes['count'] . '</span>';
    $html .= '            <div class="infobox-content">Progressos ao AVA</div>';
    $html .= '        </div>';
    $html .= '    </div>';
    $html .= '  </a>';
    // </editor-fold>
    $html .= '</div>';
    // </editor-fold>
    $html .= '<div class="vspace-12-sm"></div>';
    // <editor-fold desc="Gráfico">
    $html .= '<div class="col-sm-5">';
    $html .= '    <div class="widget-box">';
    $html .= '        <div class="widget-header widget-header-flat widget-header-small">';
    $html .= '            <h5 class="widget-title"><i class="ace-icon fa fa-pie-chart"></i>TOP Cargos em quantidade de usuários</h5>';
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
<style>
    .infobox {
        width: 250px !important;
    }
    .infobox .infobox-content {
        max-width: 180px !important;
    }
</style>
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
                        radius: 0.9, // Diminuí levemente o gráfico para sobrar espaço nas bordas
                        innerRadius: 0, // 0 para pizza, 0.5 se quiser transformar em donut
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
                            radius: 1,
                            formatter: labelFormatter,
                            padding: 50, // Espaçamento extra
                            background: {
                                opacity: 0.8,
                                color: '#000' // Fundo preto para destacar o texto branco
                            }
                        },
                        combine: {
                            color: '#999',
                            threshold: 0.05, // Agrupa tudo que for menor que 5% em uma única fatia
                            label: 'Outros'
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
    function labelFormatter_old(label, series) {
        return "<div style='font-size:9pt; text-align:center; padding:2px; color:white; background: rgb(51 51 51 / 20%)'>" + label + "<br/><span style='font-weight: bold;'>" + series.data[0][1] + " (" + Math.round(series.percent) + "%)</span></div>";
    }
    function labelFormatter(label, series) {
        return "<div style='font-size:11px; text-align:center; padding:5px; color:white; font-weight:bold;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
</script>