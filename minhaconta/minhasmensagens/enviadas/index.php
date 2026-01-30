<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("MINHASMENSAGENS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Mensagens", URL_SYS . 'minhaconta/minhasmensagens/', 1);
$breadcrumb->add("Enviadas", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Mensagens Enviadas", true);
$header->addMenu("MINHASMENSAGENSENVIADAS", "Mensagens Enviadas", "Visualize as mensagens enviadas para outros usuários no sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Mensagens Enviadas',
    'id' => 'lista',
    'botaoNovo' => true,
    'tituloBotaoNovo' => 'Enviar Mensagem',
    'iconeBotaoNovo' => 'paper-plane',
    'export' => false
        ));
$colunas = array(
    array("titulo" => "", "largura" => "30px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Data e Hora", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Destinatários", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Assunto", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'minhaconta/minhasmensagens/enviadas/load.php', false, $colunas, false, 25, false, true, true);
$html .= gerarRodape(array('tipo' => 'full'));

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#btn_novo").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/enviarmensagem/'; ?>" + window.location.search;
        });
    });
    function __visualizar(men_int_codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/enviadas/'; ?>view/" + men_int_codigo + "/" + window.location.search;
    }
</script>