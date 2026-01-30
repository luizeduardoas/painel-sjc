<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("MINHASMENSAGENS");

GF::import(array("mensagem", "destinatario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Mensagens", URL_SYS . 'minhaconta/minhasmensagens/', 1);
$breadcrumb->add("Visualizar Enviada", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualizar Enviada", true);
$header->addMenu("MINHASMENSAGENSENVIADAS", "Visualizar Enviada", "Visualize a mensagem enviada");
$header->show(getiframe(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$usuario = getUsuarioSessao();

$html = '';
$form = new GForm();

global $_id;
$mensagem = new Mensagem();
$mensagem->setMen_int_codigo($_id);
$mensagemDao = new MensagemDao();
$mensagem = $mensagemDao->selectById($mensagem);
if (is_null($mensagem->getMen_var_titulo())) {
    echo carregarPagina500();
} else {
    if ($mensagem->getRemetente()->getUsu_int_codigo() == $usuario->getUsu_int_codigo()) {
        $html .= gerarCabecalho(array(
            'tipo' => 'box',
            'titulo' => 'Visualização de Mensagem',
            'id' => 'visualizacao',
            'col' => 12,
            'fa' => 'eye'
        ));
        $html .= $form->open("form");
        $html .= gerarCamposVisualizacao(array(
            'Destinatários' => formatarGroupConcat($mensagem->getDestinatarios(), "usuario", "usu_int_codigo", "CONCAT(' ', usu_var_nome)"),
            'Assunto' => $mensagem->getMen_var_titulo(),
            'Mensagem' => $mensagem->getMen_txt_texto(),
            'Data de Envio' => $mensagem->getMen_dti_envio_format()
        ));
        $html .= carregarBotoes("V");
        $html .= $form->close();
        $html .= gerarRodape(array('tipo' => 'box', 'col' => 12));
        echo $html;
    } else {
        echo carregarMensagem("E", "Somente o remetente dessa mensagem pode visualizá-la.");
    }
}

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(getiframe());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_voltar").click(function () {
            calbackCancelar();
        });
    });
    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/enviadas/'; ?>" + window.location.search;
    }
</script>
