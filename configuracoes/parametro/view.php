<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PARAMETRO");
GF::import(array("parametro"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Parâmetros", URL_SYS . 'configuracoes/parametro/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Parâmetro", true);
$header->addMenu("PARAMETRO", "Visualização de Parâmetro", "Visualize as informações desse parâmetro do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

global $_id;
$parametro = new Parametro();
$parametro->setPar_int_codigo($_id);
$parametroDao = new ParametroDao();
$parametro = $parametroDao->selectById($parametro);
if (is_null($parametro->getPar_var_chave())) {
    echo carregarPagina500();
} else {
    $html = gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Parâmetro',
        'id' => 'visualizacao',
        'col' => 8,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "par_int_codigo", false, array("value" => $parametro->getPar_int_codigo()));
    $html .= gerarCamposVisualizacao(array(
        'Chave' => $parametro->getPar_var_chave(),
        'Descrição' => $parametro->getPar_var_descricao(),
        'Valor' => $parametro->getPar_txt_valor(),
        'Atualização' => $parametro->getPar_dti_atualizacao_format(),
        'Usuário' => '<a href="' . ((!is_null($parametro->getUsuario()->getUsu_int_codigo())) ? URL_SYS . "cadastros/usuario/view/" . $parametro->getUsuario()->getUsu_int_codigo() : "#") . '">' . formataDadoVazio($parametro->getUsuario()->getUsu_var_nome()) . '</a>'
    ));

    $arrayBotoes = array();
    if (!isFrame()) {
        if (GSecurity::verificarPermissao("PARAMETRO_UPD", false)) {
            $arrayBotoes["btn_alterar"] = "Alterar";
        }
        $arrayBotoes["btn_todos"] = "Ver Todas";
        if (GSecurity::verificarPermissao("PARAMETRO_DEL", false)) {
            $arrayBotoes["btn_excluir"] = "Excluir";
        }
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);

    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 8));
    echo $html;
}
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_alterar").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>form/" + jQuery("#par_int_codigo").val() + "/?return=configuracoes/parametro/view/" + jQuery("#par_int_codigo").val();
        });
        jQuery("#btn_excluir").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente excluir esse Parâmetro?", "__calbackExcluir('" + jQuery("#par_int_codigo").val() + "');");
        });
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>";
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
    });
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/parametro/'; ?>exec.php", {acao: 'del', par_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'configuracoes/parametro/'; ?>';", "");
    }
</script>