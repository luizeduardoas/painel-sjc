<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("ACESSO");
GF::import(array("acesso"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Monitoramento >> Acessos", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Monitoramento >> Acessos", true);
$header->addMenu("ACESSO", "Visualização de Acessos", "Visualize as informações desse Acesso do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$acesso = new Acesso();
$acesso->setAce_int_codigo($_id);
$acessoDao = new AcessoDao();
$acesso = $acessoDao->selectById($acesso);
if (is_null($acesso->getAce_var_ip())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Acessos',
        'id' => 'visualizacao',
        'col' => 12,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "ace_int_codigo", false, array("value" => $acesso->getAce_int_codigo()));
    $arr = array();
    $arr['Código'] = formataDadoVazio($acesso->getAce_int_codigo());
    $arr['Data e Hora'] = formataDadoVazio($acesso->getAce_dti_criacao_format());
    $arr['Usuário'] = '<a data-toggle="tooltip" title="Visualizar Usuário" href="' . URL_SYS . 'cadastros/usuario/view/' . $acesso->getAce_int_usuario() . getIframe() . '">' . $acesso->getAce_int_usuario_nome() . '</a>';
    $arr['IP'] = formataDadoVazio($acesso->getAce_var_ip());
    $arr['Sessão'] = formataDadoVazio($acesso->getAce_var_sessao());
    $arr['Server'] = formataDadoVazio($acesso->getAce_var_server());
    $arr['URL'] = formataDadoVazio($acesso->getAce_var_url());
    $arr['Request'] = formataDadoVazio($acesso->getAce_txt_request());
    $arr['Agent'] = formataDadoVazio($acesso->getAce_var_agent());
    $arr['Json'] = '<iframe width="100%" height="300px" src="' . URL_SYS . 'inc/load/json_banco.php?tipo=api_log&id=' . $acesso->getAce_int_codigo() . '"></iframe>';
    $arr['Lead'] = formataDadoVazio($acesso->getAce_int_lead());
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
            window.location.href = "<?php echo URL_SYS . 'monitoramento/acesso/'; ?>";
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
</script>
