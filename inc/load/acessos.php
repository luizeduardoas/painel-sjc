<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../global.php");

GSecurity::verificarAutenticacaoAjax();

$naoIdentificado = 'Motivo não identificado, execute o cron de atualização de Users Agents';

$usuario = new Usuario();
$usuario->setUsu_int_codigo($_POST["usu_int_codigo"]);
$usuarioDao = new UsuarioDao();
$usuario = $usuarioDao->selectById($usuario);
if (is_null($usuario->getUsu_var_nome())) {
    echo carregarPagina500();
} else {
    $sintetico = $_POST["filtro_sintetico"];
    $arrData = explode(" - ", $_POST["filtro_periodo"]);
    if ($sintetico == '1') {
        $query = "SELECT ace_var_ip, ace_var_sessao, ace_var_server, ace_var_agent FROM acesso ace WHERE ace_int_usuario = ? AND ace_dti_criacao BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59' GROUP BY ace_var_ip, ace_var_sessao, ace_var_server, ace_var_agent ORDER BY ace_dti_criacao DESC";
    } else {
        $query = "SELECT ace_int_codigo, DATE_FORMAT(ace_dti_criacao, '%d/%m/%Y %H:%i:%s') as ace_dti_criacao, ace_var_ip, ace_var_sessao, ace_var_server, ace_var_url, ace_txt_request, ace_var_agent, ace_txt_json FROM acesso ace WHERE ace_int_usuario = ? AND ace_dti_criacao BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59' ORDER BY ace_dti_criacao DESC LIMIT 5001;";
    }
    $mysql = new GDbMysql();
    $mysql->execute($query, array("i", $usuario->getUsu_int_codigo()));
    if ($mysql->numRows() > 0) {
        if ($sintetico == '1') {
            $arrTitulos = array("IP", "Server", "Configuração", "Sessão");
            $arrDados = array();
            while ($mysql->fetch()) {
                $arr = array();
                $arr["ace_var_ip"] = $mysql->res["ace_var_ip"];
                $arr["ace_var_server"] = $mysql->res["ace_var_server"];
                $arr["ace_var_agent"] = $mysql->res["ace_var_agent"];
                $arr["ace_var_sessao"] = $mysql->res["ace_var_sessao"];
                $arrDados[] = $arr;
            }
            $html = '<div style="border-left: 1px solid #e3e3e3;border-right: 1px solid #e3e3e3;">';
            $html .= '<table style="margin-bottom: 0;" class="table table-responsive table-bordered">';
            $html .= '<thead>';
            $html .= '<tr>';
            foreach ($arrTitulos as $titulo) {
                $html .= '<th>' . $titulo . '</th>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach ($arrDados as $dado) {
                $html .= '<tr class="tr_valido_A">';
                foreach ($dado as $key => $val) {
                    $html .= '<td>' . formataDadoVazio($val) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        } else {
            if ($mysql->numRows() <= 5000) {
                $dataAnterior = '';
                $html = '<div class="timeline-container timeline-style">';
                $html .= '<div class="align-right"><span class="green middle bolder">' . $mysql->numRows() . '</span> registros</div>';
                $i = 0;
                $grupo = 0;
                while ($mysql->fetch()) {
                    $i++;
                    $dataHora = explode(" ", $mysql->res["ace_dti_criacao"]);
                    if ($dataHora[0] != $dataAnterior) {
                        if ($dataAnterior != '') {
                            $html .= '</div>';
                        }
                        $html .= '<div class="timeline-label grupo grupo_' . $grupo . '" style="display:' . ($grupo == 0 ? 'block' : 'none') . ';"><span class="label label-primary arrowed-in-right label-lg"><b>' . $dataHora[0] . '</b></span></div>';
                        $html .= '<div class="timeline-items grupo grupo_' . $grupo . '" style="display:' . ($grupo == 0 ? 'block' : 'none') . ';margin-bottom: 3em;">';
                        $dataAnterior = $dataHora[0];
                    }
                    $problema = false;
                    $html .= '<div class="timeline-item clearfix linha_valido_A grupo grupo_' . $grupo . '" style="display:' . ($grupo == 0 ? 'block' : 'none') . ';">';
                    $html .= '  <div class="timeline-info">';
                    $html .= '      <i class="timeline-indicator ace-icon fa btn ' . ($problema ? 'fa-bug btn-danger' : 'fa-check btn-success') . ' no-hover"></i>';
                    $html .= '  </div>';
                    $html .= '  <div class="widget-box collapsed ' . ($problema ? 'widget-color-red2' : 'transparent') . '">';
                    $html .= '      <div class="widget-header widget-header-small">';
                    $url = '<i>' . $mysql->res["ace_var_url"] . '</i>';
                    $html .= '          <h5 class="widget-title smaller" style="' . ($problema ? 'color:#ffffff !important;' : '') . '">' . $url . '</h5>';
                    $html .= '          <span class="widget-toolbar no-border"><i class="ace-icon fa fa-clock-o bigger-110"></i> ' . $dataHora[1] . '</span>';
                    $html .= '          <span class="widget-toolbar"><a href="#" data-action="collapse"><i class="ace-icon fa fa-plus" data-icon-show="fa-plus" data-icon-hide="fa-minus"></i></a></span>';
                    $html .= '      </div>';
                    $html .= '      <div class="widget-body">';
                    $html .= '          <div class="widget-main" style="text-align: justify;">';
                    $html .= '              <ul>';
                    $html .= '                  <li>Navegador: ' . formataDadoVazio($mysql->res["ace_var_agent"]) . '</li>';
                    $html .= '                  <li>IP: ' . $mysql->res["ace_var_ip"] . '</li>';
                    $html .= '                  <li>Servidor: ' . $mysql->res["ace_var_server"] . '</li>';
                    $html .= '                  <li>Sessão: ' . $mysql->res["ace_var_sessao"] . '</li>';
                    $html .= '              </ul>';
                    $html .= '          </div>';
                    $html .= '      </div>';
                    $html .= '  </div>';
                    $html .= '</div>';
                    if ($i % 50 == 0) {
                        $html .= '</div>';
                        $dataAnterior = '';
                        $grupo++;
                        $html .= '<div class="clearfix"></div>';
                        $html .= '<div class="col-xs-12 text-center" style="display:' . ($grupo == 1 ? 'block' : 'none') . ';"><a class="btn btn-sm btn-warning btn_mais" rel="' . $grupo . '"><i class="fa fa-step-forward"></i> mais registros</a> <a class="btn btn-sm btn-pink btn_todos" rel="' . $grupo . '"><i class="fa fa-forward"></i> todos registros</a></div>';
                        $html .= '<div class="clearfix"></div>';
                    }
                }
                $html .= '</div>';
                $html .= '</div>';
            } else {
                $html .= '<div class="naoEncontrado">Essa consulta não pode ser exibida porque retorna mais de 5000 registros. Reduza o intervalo do período e tente novamente.</div>';
            }
        }
    } else {
        $html = '<div class="naoEncontrado">Nenhum histórico foi encontrado</div>';
    }
}
echo $html;
?>
<script>
    jQuery(document).ready(function () {
        jQuery(".btn_mais").click(function () {
            var rel = jQuery(this).attr('rel');
            jQuery(".grupo_" + rel).show('fast');
            jQuery(this).parent().hide();
            jQuery(".btn_mais").each(function () {
                if (jQuery(this).attr("rel") == (parseInt(rel) + 1)) {
                    jQuery(this).parent().show();
                }
            });
        });
        jQuery(".btn_todos").click(function () {
            jQuery(".grupo").show();
            jQuery(".btn_mais").parent().hide();
        });
    });
</script>