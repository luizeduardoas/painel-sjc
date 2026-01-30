<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("USUARIO");
GF::import(array("usuario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Cadastros >> Usuários", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Cadastros >> Usuários", true);
$header->addMenu("USUARIO", "Cadastro de Usuários", "Insira, altere e exclua os usuários do sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */
$mysql = new GDbMysql();
$opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil ORDER BY pef_var_descricao;");

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
global $__arrayAtivo;
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_perfil", array("-1" => "Todos") + $opt_pef_var_descricao, buscarCookie("filtro_perfil", "-1"), "Perfil", array("class" => "chosen-select"), false, false, false) . '</div>';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_status", array("-1" => "Todos") + $__arrayAtivo, buscarCookie("filtro_status", "-1"), "Status", array("class" => "chosen-select"), false, false, false) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Usuários',
    'id' => 'lista',
    'botaoNovo' => true,
    'filtro' => $filtro,
    'botaoAtualizar' => true,
    'botaoNovoPermissao' => 'USUARIO_INS'
        ));
$largura = obterLarguraColunaAcoes(array("USUARIO_UPD", "USUARIO_DEL", "USUARIO_HIS", "USUARIO_ENV_SEN", "USUARIO_REP"));
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Foto", "largura" => "30px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Identificador", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Perfil", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "E-mail", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Status", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Validado", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Cadastro", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Último Acesso", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center")
);
$filtros = array(
    "filtro_perfil",
    "filtro_status"
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'cadastros/usuario/load.php', $filtros, $colunas, false, 25, false, true, true);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery("#btn_novo").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>form/" + window.location.search;
        });
        jQuery(".formFiltros select").change(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
    });

    function salvarFiltros() {
        setParametroCookie('filtro_perfil', jQuery('#filtro_perfil').val());
        setParametroCookie('filtro_status', jQuery('#filtro_status').val());
    }

    function __alterar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>form/" + codigo + "/" + window.location.search;
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>view/" + codigo + "/" + window.location.search;
    }

    function __historico(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>historico/" + codigo + "/" + window.location.search;
    }

    function __excluir(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir esse Usuário?", "__calbackExcluir('" + codigo + "');");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'del', usu_int_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }

    function __enviarSenha(codigo) {
        jQuery.gDisplay.showYN("Uma nova senha será ativada substituindo a antiga, e será enviado um e-mail com um link para alteração. Deseja realmente gerar e enviar um link para cadastro de uma nova senha para esse Usuário?", "__calbackEnviarSenha('" + codigo + "');");
    }
    function __calbackEnviarSenha(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'enviar', usu_int_codigo: codigo}, '', "");
    }

    function __logarComo(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente acessar como esse Usuário?", "__calbackLogarComo('" + codigo + "');");
    }
    function __calbackLogarComo(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'log', usu_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML');window.location = '<?php echo URL_SYS; ?>home/';", "", false);
    }
</script>