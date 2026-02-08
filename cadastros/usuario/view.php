<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("USUARIO");
GF::import(array("usuario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Cadastros >> Usuários", URL_SYS . 'cadastros/usuario/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Usuário", true);
$header->addMenu("USUARIO", "Visualização de Usuário", "Visualize as informações desse usuário do sistema");
$header->show(getIframe(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$usuario = new Usuario();
$usuario->setUsu_int_codigo($_id);
$usuarioDao = new UsuarioDao();
$usuario = $usuarioDao->selectById($usuario);
if (is_null($usuario->getUsu_var_nome())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Usuário',
        'id' => 'visualizacao',
        'col' => 8,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "usu_int_codigo", false, array("value" => $usuario->getUsu_int_codigo()));
    $arr['Nome'] = $usuario->getUsu_var_nome();
    if (GSecurity::verificarPermissao("PERFIL", false))
        $arr['Perfil'] = '<a data-toggle="tooltip" title="Visualizar perfil" href="' . URL_SYS . 'seguranca/perfil/view/' . $usuario->getPerfil()->getPef_int_codigo() . getIframe() . '">' . $usuario->getPerfil()->getDescricao() . '</a>';
    else
        $arr['Perfil'] = $usuario->getPerfil()->getDescricao();
    $arr['Identificador'] = $usuario->getUsu_var_identificador();
    $arr['E-mail'] = '<a href="mailto:' . $usuario->getUsu_var_email() . '"><i class="ace-icon fa fa-envelope bigger-110"></i> ' . $usuario->getUsu_var_email() . '</a>';
    $arr['Status'] = '<span class="label label-sm label-' . labelStatus($usuario->getUsu_cha_status()) . ' label-white middle"> ' . $usuario->getUsu_cha_status_format() . '</span>';
    if ($usuario->getUsu_cha_status() == 'I') {
        $arr['Motivo'] = formataDadoVazio($usuario->getUsu_var_motivo());
    }
    $arr['Validado'] = '<span class="label label-sm label-' . labelStatus($usuario->getUsu_cha_validado()) . ' label-white middle"> ' . $usuario->getUsu_cha_validado_format() . '</span>';
    $arr['Data de Cadastro'] = $usuario->getUsu_dti_criacao_format();
    $arr['Foto'] = '<a href="' . $usuario->getUsu_var_foto() . '" alt="' . $usuario->getUsu_var_nome() . '" title="' . $usuario->getUsu_var_nome() . '" data-rel="colorbox"><img style="border: 1px solid #ccc; background: #fff; max-width: 100px; max-height: 100px;" src="' . $usuario->getUsu_var_foto() . '"/></a>';
    $arr['Último Acesso'] = formataDadoVazio($usuario->getUsu_dti_ultimo_format());
    $html .= gerarCamposVisualizacao($arr);

    $arrayBotoes = array();
    if (!getIframe()) {
        $arrayBotoes["btn_todos"] = "Ver Todos";
        if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_UPD", false)) {
            $arrayBotoes["btn_alterar"] = "Alterar";
        }
        if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_REP", false)) {
            $arrayBotoes["btn_logar"] = "Representar";
        }
        if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_ENV_SEN", false)) {
            $arrayBotoes["btn_enviarsenha"] = "Enviar Senha";
        }
        if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_HIS", false)) {
            $arrayBotoes["btn_historico"] = "Histórico";
        }
        if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && $usuario->getUsu_cha_status() == 'I' && GSecurity::verificarPermissao("USUARIO_DEL", false)) {
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
$footer->show(getIframe());
?>
<style>
    #cboxTitle {
        padding-left: 10px !important;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('[data-rel="colorbox"]').colorbox(paramColorbox);
        jQuery("#btn_alterar").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>form/" + jQuery("#usu_int_codigo").val() + "/" + window.location.search;
        });
        jQuery("#btn_excluir").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente excluir esse Usuário?", "__calbackExcluir('" + jQuery("#usu_int_codigo").val() + "');");
        });
        jQuery("#btn_enviarsenha").click(function () {
            jQuery.gDisplay.showYN("Uma nova senha será ativada substituindo a antiga, e será enviado um e-mail com um link para alteração. Deseja realmente gerar e enviar um link para cadastro de uma nova senha para esse Usuário?", "__calbackEnviarSenha('" + jQuery("#usu_int_codigo").val() + "');");
        });
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>" + window.location.search;
        });
        jQuery("#btn_historico").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>historico/" + jQuery("#usu_int_codigo").val() + "/" + window.location.search;
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
        jQuery("#btn_logar").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente acessar como esse Usuário?", "__calbackLogarComo('" + jQuery("#usu_int_codigo").val() + "');");
        });
        jQuery("#btn_atualizar").click(function () {
            dt_dados.ajax.reload();
        });
    });
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'del', usu_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'cadastros/usuario/'; ?>' + window.location.search;", "");
    }
    function __calbackLogarComo(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'log', usu_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML');window.location = '<?php echo URL_SYS; ?>home/';", "", false);
    }
    function __calbackEnviarSenha(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", {acao: 'enviar', usu_int_codigo: codigo}, '', "");
    }
</script>