<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("EVENTO");
GF::import(array("evento"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Eventos", URL_SYS . 'configuracoes/evento/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Evento", true);
$header->addMenu("EVENTO", "Visualização de Evento", "Visualize todas informações desse evento do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

global $_id;
$evento = new Evento();
$evento->setEve_int_codigo($_id);
$eventoDao = new EventoDao();
$evento = $eventoDao->selectById($evento);
if (is_null($evento->getEve_var_titulo())) {
    echo carregarPagina500();
} else {
    $html = gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Evento',
        'id' => 'visualizacao',
        'col' => 12,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "eve_int_codigo", false, array("value" => $evento->getEve_int_codigo()));
    $arr = array();
    $arr['Data e Hora'] = $evento->getEve_dti_criacao_format();
    $arr['Título'] = formataDadoVazio($evento->getEve_var_titulo());
    $arr['Tipo'] = '<span class="label label-sm label-' . labelStatusEvento($evento->getEve_cha_tipo()) . ' label-white middle">' . $evento->getEve_cha_tipo_format() . '</span>';
    $arr['Dados'] = $evento->getEve_txt_dados();
    $arr['Usuário'] = '<a href="' . ((!is_null($evento->getUsuario()->getUsu_int_codigo())) ? URL_SYS . "cadastros/usuario/view/" . $evento->getUsuario()->getUsu_int_codigo() : "#") . '">' . formataDadoVazio($evento->getUsuario()->getUsu_var_nome()) . '</a>';
    $html .= gerarCamposVisualizacao($arr);
    $arrayBotoes = array();
    if (!isFrame()) {
        $arrayBotoes["btn_todos"] = "Ver Todos";
    }
    if (getClose()) {
        $arrayBotoes["btn_close"] = "Fechar";
        $style = '<style>';
        $style .= ' .profile-info-name {';
        $style .= '     max-width: 50px;';
        $style .= ' }';
        $style .= '</style>';
    } else {
        $style = '';
        $arrayBotoes["btn_voltar"] = "Voltar";
    }

    $html .= carregarBotoes($arrayBotoes);
    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 12));
    echo $html;
}
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
echo $style;
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/evento/'; ?>";
        });
        jQuery("#btn_voltar").click(function () {
            if (window.history.length > 1) {
                jQuery.gDisplay.loadStart('HTML');
                window.history.back();
            }
        });
        if (window.history.length < 2) {
            jQuery("#btn_voltar").attr("disabled", "disabled");
        }
        jQuery("#btn_close").click(function () {
            closeColorbox();
        });
    });
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/evento/'; ?>exec.php", {acao: 'del', eve_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'configuracoes/evento/'; ?>';", "");
    }
</script>