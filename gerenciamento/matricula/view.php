<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("MATRICULA");
GF::import(array("matricula"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Matrículas", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Matrículas", true);
$header->addMenu("MATRICULA", "Visualização de Matrículas", "Visualize as informações dessa Matrícula do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$matricula = new Matricula();
$matricula->setMat_int_codigo($_id);
$matriculaDao = new MatriculaDao();
$matricula = $matriculaDao->selectById($matricula);
if (is_null($matricula->getMat_dti_criacao())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Matrículas',
        'id' => 'visualizacao',
        'col' => 6,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "mat_int_codigo", false, array("value" => $matricula->getMat_int_codigo()));
    $arr = array();
    $arr['Código'] = formataDadoVazio($matricula->getMat_int_codigo());
    if (GSecurity::verificarPermissao("CURSO", false))
        $arr['Curso'] = '<a data-toggle="tooltip" title="Visualizar curso" href="' . URL_SYS . 'gerenciamento/curso/view/' . $matricula->getCurso()->getCur_int_codigo() . '">' . $matricula->getCurso()->getDescricao() . '</a>';
    else
        $arr['Curso'] = $matricula->getCurso()->getDescricao();
    $usuario = $matricula->getUsuario();
    if (GSecurity::verificarPermissao("ESCOLA", false))
        $arr['Escola'] = '<a data-toggle="tooltip" title="Visualizar escola" href="' . URL_SYS . 'gerenciamento/escola/view/' . $matricula->getUsuario()->getEscola()->getEsc_int_codigo() . '">' . $matricula->getUsuario()->getEscola()->getDescricao() . '</a>';
    else
        $arr['Escola'] = $matricula->getUsuario()->getEscola()->getDescricao();
    $arr['Identificador'] = formataDadoVazio($usuario->getUsu_int_userid());
    $arr['Nome'] = formataDadoVazio($usuario->getUsu_var_nome());
    $arr['cpf'] = formataDadoVazio($usuario->getUsu_var_cpf());
    $arr['Matrícula'] = formataDadoVazio($usuario->getUsu_var_matricula());
    $arr['Cargo'] = formataDadoVazio($usuario->getUsu_var_cargo());
    $arr['Função'] = formataDadoVazio($usuario->getUsu_var_funcao());
    $arr['Email'] = formataDadoVazio($usuario->getUsu_var_email());
    $arr['DH. de Criação'] = formataDadoVazio($matricula->getMat_dti_criacao_format());
    $arr['DH. de Início'] = formataDadoVazio($matricula->getMat_dti_inicio_format());
    $arr['DH. de Término'] = formataDadoVazio($matricula->getMat_dti_termino_format());

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
            window.location.href = "<?php echo URL_SYS . 'gerenciamento/matricula/'; ?>";
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
