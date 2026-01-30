<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("avaCurso"));

$filter = new GFilter();

$filtro_nivel = $_POST["filtro_nivel"];
if ($filtro_nivel != '-1')
    $filter->addFilter('AND', 'cur.niv_int_codigo', '=', 'i', $filtro_nivel);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("cur_var_nome"), $search);
}

$arrColunas = array("", "cur_int_codigo", "cur_var_nome", "cur_int_courseid", "niv_int_codigo");

$col = "cur_var_nome";
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
$filter->setOrder(array($col => $ord, 'cur_int_codigo' => 'DESC'));
//$filter->setGroupBy("cur_int_codigo");

try {
    $arr = array();
    $cursoDao = new AvaCursoDao();
    $qtd = $cursoDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $cursoDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $curso AvaCurso */
            foreach ($arrDados as $curso) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $curso->getCur_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = formataDadoVazio($curso->getCur_int_codigo());
                $arr[$i][] = formataDadoVazio($curso->getCur_var_nome());
                $arr[$i][] = formataDadoVazio($curso->getCur_int_courseid());
                $arr[$i][] = formataDadoVazio($curso->getNivel()->getDescricao());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
