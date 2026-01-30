<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("MINHASMENSAGENS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Mensagens", URL_SYS . 'minhaconta/minhasmensagens/', 1);
$breadcrumb->add("Recebidas", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Mensagens Recebidas", true);
$header->addMenu("MINHASMENSAGENSRECEBIDAS", "Mensagens Recebidas", "Visualize as mensagens recebidas no sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Mensagens Recebidas',
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
    array("titulo" => "Remetente", "largura" => false, "ordem" => false, "visivel" => true, "classe" => "left"),
    array("titulo" => "Assunto", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Status", "largura" => "80px", "ordem" => true, "visivel" => true, "classe" => "center")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'minhaconta/minhasmensagens/recebidas/load.php', false, $colunas, false, 25, false, true, true);
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
        window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/recebidas/'; ?>view/" + men_int_codigo + "/" + window.location.search;
    }

    function __excluir(men_int_codigo, usu_int_codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir essa mensagem?", "__calbackExcluir(" + men_int_codigo + "," + usu_int_codigo + ");");
    }
    function __calbackExcluir(men_int_codigo, usu_int_codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'minhaconta/minhasmensagens/'; ?>exec.php", {acao: 'del', men_int_codigo: men_int_codigo, usu_int_codigo: usu_int_codigo}, "dt_dados.ajax.reload();", "");
    }

    function __naolida(men_int_codigo, usu_int_codigo) {
        jQuery.gDisplay.showYN("Deseja realmente marcar como não lida essa mensagem?", "__calbackNaolida(" + men_int_codigo + "," + usu_int_codigo + ");");
    }
    function __calbackNaolida(men_int_codigo, usu_int_codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'minhaconta/minhasmensagens/'; ?>exec.php", {acao: 'naolida', men_int_codigo: men_int_codigo, usu_int_codigo: usu_int_codigo}, "window.location.reload();", "");
    }
</script>