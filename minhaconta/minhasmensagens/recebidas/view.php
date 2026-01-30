<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("MINHASMENSAGENS");

GF::import(array("mensagem", "destinatario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Mensagens", URL_SYS . 'minhaconta/minhasmensagens/', 1);
$breadcrumb->add("Visualizar Recebida", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualizar Recebida", true);
$header->addMenu("MINHASMENSAGENSRECEBIDAS", "Visualizar Recebida", "Visualize a mensagem recebida");
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
    if (in_array($usuario->getUsu_int_codigo(), explode(",", $mensagem->getDestinatarios()))) {

        $destinatario = new Destinatario();
        $destinatario->setDes_int_destinatario($usuario->getUsu_int_codigo());
        $destinatarioDao = new DestinatarioDao();
        $destinatario = $destinatarioDao->selectByMensagemDestinatario($mensagem, $destinatario);
        if ($destinatario->getDes_cha_status() < 2) {
            $destinatario->setDes_cha_status(1);
            $destinatarioDao->update($destinatario);
        }

        $html .= gerarCabecalho(array(
            'tipo' => 'box',
            'titulo' => 'Visualização de Mensagem',
            'id' => 'visualizacao',
            'col' => 12,
            'fa' => 'eye'
        ));
        $html .= $form->open("form");
        $html .= $form->addInput("hidden", "men_int_codigo", false, array("value" => $mensagem->getMen_int_codigo()));
        $html .= gerarCamposVisualizacao(array(
            'Remetente' => $mensagem->getRemetente()->getUsu_var_nome(),
            'Assunto' => $mensagem->getMen_var_titulo(),
            'Mensagem' => $mensagem->getMen_txt_texto(),
            'Data de Envio' => $mensagem->getMen_dti_envio_format()
        ));
        $html .= carregarBotoes("RV");
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
        jQuery("#btn_responder").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            var men_int_codigo = jQuery("#men_int_codigo").val();
            window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/'; ?>enviarmensagem/" + men_int_codigo + "/" + window.location.search;
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/recebidas/'; ?>" + window.location.search;
    }
</script>
