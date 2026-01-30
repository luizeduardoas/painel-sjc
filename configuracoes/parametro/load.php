<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("parametro"));

$filter = new GFilter();

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("par_var_chave", "par_var_descricao", "par_txt_valor"), $search);
}

$arrColunas = array("", "par_int_codigo", "par_var_chave", "par_var_descricao", "par_txt_valor", "par_dti_atualizacao", "usu_var_nome");

$col = "par_dti_atualizacao";
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
//$filter->setGroupBy("par_int_codigo");

try {
    $arr = array();
    $parametroDao = new ParametroDao();
    $qtd = $parametroDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $parametroDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $parametro Parametro */
            foreach ($arrDados as $parametro) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $parametro->getPar_int_codigo() . "')";
                if (GSecurity::verificarPermissao("PARAMETRO_UPD", false)) {
                    $arrayBotoes["update"] = "__alterar('" . $parametro->getPar_int_codigo() . "')";
                }
                if (GSecurity::verificarPermissao("PARAMETRO_DEL", false)) {
                    $arrayBotoes["delete"] = "__excluir('" . $parametro->getPar_int_codigo() . "')";
                }
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>

                $arr[$i][] = $parametro->getPar_int_codigo();
                $arr[$i][] = $parametro->getPar_var_chave();
                $arr[$i][] = $parametro->getPar_var_descricao();
                $arr[$i][] = GF::truncate(strip_tags($parametro->getPar_txt_valor()), 50, "...");
                $arr[$i][] = $parametro->getPar_dti_atualizacao_format();
                $arr[$i][] = formataDadoVazio($parametro->getUsuario()->getUsu_var_nome());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>