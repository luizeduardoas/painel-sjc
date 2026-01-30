<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("perfil"));

$filter = new GFilter();
$filter->addFilter('AND', 'pef_int_codigo', '>', 'i', '0');

$filtro_status = $_POST["filtro_status"];
if ($filtro_status != '-1')
    $filter->addFilter('AND', 'pef_cha_status', '=', 's', $filtro_status);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("pef_var_descricao"), $search);
}

$arrColunas = array("", "pef_int_codigo", "pef_var_descricao", "pef_cha_status");

$col = "pef_var_descricao";
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
//$filter->setGroupBy("pef_int_codigo");

try {
    $arr = array();
    $perfilDao = new PerfilDao();
    $qtd = $perfilDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $perfilDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $perfil Perfil */
            foreach ($arrDados as $perfil) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $perfil->getPef_int_codigo() . "')";
                if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_UPD", false)) {
                    $arrayBotoes["update"] = "__alterar('" . $perfil->getPef_int_codigo() . "')";
                }
                if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_CLO", false)) {
                    $arrayBotoes["clonar"] = "__clonar('" . $perfil->getPef_int_codigo() . "')";
                }
                if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_DEL", false)) {
                    $arrayBotoes["delete"] = "__excluir('" . $perfil->getPef_int_codigo() . "')";
                }
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>

                $arr[$i][] = $perfil->getPef_int_codigo();
                $arr[$i][] = $perfil->getPef_var_descricao();
                $arr[$i][] = '<span class="label label-sm label-' . labelStatus($perfil->getPef_cha_status()) . ' label-white middle">' . $perfil->getPef_cha_status_format() . '</span>';
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
