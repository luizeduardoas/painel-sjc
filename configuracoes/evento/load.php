<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("evento"));

$filtro_periodo = $_POST["filtro_periodo"];
$filtro_tipo = $_POST["filtro_tipo"];
$filter = new GFilter();
if ($filtro_tipo != '-1') {
    $filter->addFilter('AND', 'eve_cha_tipo', '=', 's', $filtro_tipo);
}
$arrData = explode(" - ", $filtro_periodo);
$filter->addClause("AND (eve.eve_dti_criacao BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("eve_var_titulo", "eve_var_identificador", "eve_txt_dados"), $search);
}

$arrColunas = array("", "eve_int_codigo", "eve_dti_criacao", "eve_var_titulo", "eve_cha_tipo", "eve_txt_dados", "eve_var_identificador");

$col = "eve_int_codigo";
if (isset($_POST["order"][0]["column"])) {
    $col = $arrColunas[$_POST["order"][0]["column"]];
}
$ord = "DESC";
if (isset($_POST["order"][0]["dir"])) {
    $ord = $_POST["order"][0]["dir"];
}
$start = (isset($_POST["start"]) ? $_POST["start"] : 0);
$length = (isset($_POST["length"]) ? $_POST["length"] : 99999999);
$filter->setLimit($start, $length);
$filter->setOrder(array($col => $ord));
//$filter->setGroupBy("eve_int_codigo");

try {
    $arr = array();
    $eventoDao = new EventoDao();
    $qtd = $eventoDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $eventoDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $evento Evento */
            foreach ($arrDados as $evento) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $evento->getEve_int_codigo() . "')";
                $arrayBotoes["popup"] = "__popup('" . $evento->getEve_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>

                $arr[$i][] = $evento->getEve_int_codigo();
                $arr[$i][] = $evento->getEve_dti_criacao_format();
                $arr[$i][] = '<span class="label label-sm label-' . labelStatusEvento($evento->getEve_cha_tipo()) . ' label-white middle">' . $evento->getEve_cha_tipo_format() . '</span>';
                $arr[$i][] = $evento->getEve_var_titulo();
                $arr[$i][] = ((substr($evento->getEve_txt_dados(), 0, 1) == '[') ? '<i>...DADOS INTERNOS...</i>' : GF::truncate($evento->getEve_txt_dados(), 120, "..."));
                $arr[$i][] = formataDadoVazio($evento->getUsuario()->getUsu_var_nome());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>