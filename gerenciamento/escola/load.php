<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("escola"));

$filter = new GFilter();

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("esc_var_nome"), $search);
}

$arrColunas = array("", "esc_int_codigo", "esc_var_nome");

$col = "esc_var_nome";
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
$filter->setOrder(array($col => $ord, 'esc_int_codigo' => 'DESC'));
//$filter->setGroupBy("esc_int_codigo");

try {
    $arr = array();
    $escolaDao = new EscolaDao();
    $qtd = $escolaDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $escolaDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $escola Escola */
            foreach ($arrDados as $escola) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $escola->getEsc_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = formataDadoVazio($escola->getEsc_int_codigo());
                $arr[$i][] = formataDadoVazio($escola->getEsc_var_nome());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
