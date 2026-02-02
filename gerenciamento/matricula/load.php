<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("matricula"));

$filter = new GFilter();

$filtro_curso = $_POST["filtro_curso"];
if ($filtro_curso != '-1')
    $filter->addFilter('AND', 'cur_int_codigo', '=', 'i', $filtro_curso);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("usu_int_codigo", "usu_int_userid", "usu_var_nome", "usu_var_cpf", "usu_var_matricula", "usu_var_cargo", "usu_var_funcao", "usu_var_email"), $search);
}

$arrColunas = array("", "mat_int_codigo", "cur_int_codigo", "usu_int_userid", "usu_var_nome", "usu_var_cpf", "usu_var_matricula", "usu_var_cargo", "usu_var_funcao", "usu_var_email", "mat_dti_criacao", "mat_dti_inicio", "mat_dti_termino");

$col = "usu_var_nome";
if (isset($_POST["order"][0]["column"])) {
    $col = $arrColunas[$_POST["order"][0]["column"]];
}
$ord = "ASC";
if (isset($_POST["order"][0]["dir"])) {
    $ord = $_POST["order"][0]["dir"];
}
$start = (isset($_POST["start"]) ? $_POST["start"] : 0);
$length = (isset($_POST["length"]) ? $_POST["length"] : 99999999);
$filter->setLimit($start, $length);
$filter->setOrder(array($col => $ord));
//$filter->setGroupBy("usu_int_codigo");

try {
    $arr = array();
    $matriculaDao = new MatriculaDao();
    $qtd = $matriculaDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $matriculaDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $matricula Matricula */
            foreach ($arrDados as $matricula) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $matricula->getMat_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = formataDadoVazio($matricula->getMat_int_codigo());
                if (GSecurity::verificarPermissao("CURSO", false))
                    $arr[$i][] = '<a data-toggle="tooltip" title="Visualizar curso" href="' . URL_SYS . 'gerenciamento/curso/view/' . $matricula->getCurso()->getCur_int_codigo() . '">' . $matricula->getCurso()->getDescricao() . '</a>';
                else
                    $arr[$i][] = $matricula->getCurso()->getDescricao();
                $usuario = $matricula->getUsuario();
                if (GSecurity::verificarPermissao("ESCOLA", false))
                    $arr[$i][] = '<a data-toggle="tooltip" title="Visualizar escola" href="' . URL_SYS . 'gerenciamento/escola/view/' . $matricula->getUsuario()->getEscola()->getEsc_int_codigo() . '">' . $matricula->getUsuario()->getEscola()->getDescricao() . '</a>';
                else
                    $arr[$i][] = $matricula->getUsuario()->getEscola()->getDescricao();
                $arr[$i][] = formataDadoVazio($usuario->getUsu_int_userid());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_nome());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_cpf());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_matricula());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_cargo());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_funcao());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_email());
                $arr[$i][] = formataDadoVazio($matricula->getMat_dti_criacao_format());
                $arr[$i][] = formataDadoVazio($matricula->getMat_dti_inicio_format());
                $arr[$i][] = formataDadoVazio($matricula->getMat_dti_termino_format());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
