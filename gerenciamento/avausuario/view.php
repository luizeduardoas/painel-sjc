<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("AVAUSUARIO");
GF::import(array("avaUsuario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Usuários", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Usuários", true);
$header->addMenu("AVAUSUARIO", "Visualização de Usuários", "Visualize as informações desse Usuário do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$usuario = new AvaUsuario();
$usuario->setUsu_int_codigo($_id);
$usuarioDao = new AvaUsuarioDao();
$usuario = $usuarioDao->selectById($usuario);
if (is_null($usuario->getUsu_var_nome())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Usuários',
        'id' => 'visualizacao',
        'col' => 6,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "usu_int_codigo", false, array("value" => $usuario->getUsu_int_codigo()));
    $arr = array();
    $arr['Código'] = formataDadoVazio($usuario->getUsu_int_codigo());
    if (GSecurity::verificarPermissao("ESCOLA", false))
        $arr['Escola'] = '<a data-toggle="tooltip" title="Visualizar escola" href="' . URL_SYS . 'gerenciamento/escola/view/' . $usuario->getEscola()->getEsc_int_codigo() . '">' . $usuario->getEscola()->getDescricao() . '</a>';
    else
        $arr['Escola'] = $usuario->getEscola()->getDescricao();
    $arr['Identificador'] = formataDadoVazio($usuario->getUsu_int_userid());
    $arr['Nome'] = formataDadoVazio($usuario->getUsu_var_nome());
    $arr['cpf'] = formataDadoVazio($usuario->getUsu_var_cpf());
    $arr['Matrícula'] = formataDadoVazio($usuario->getUsu_var_matricula());
    $arr['Cargo'] = formataDadoVazio($usuario->getUsu_var_cargo());
    $arr['Função'] = formataDadoVazio($usuario->getUsu_var_funcao());
    $arr['Email'] = formataDadoVazio($usuario->getUsu_var_email());
    
    $html .= gerarCamposVisualizacao($arr);

    $arrayBotoes = array();
    if (!isFrame()) {
        $arrayBotoes["btn_todos"] = "Ver Todos";
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);
    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 6));

    echo $html;
}


/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'gerenciamento/avausuario/'; ?>";
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
