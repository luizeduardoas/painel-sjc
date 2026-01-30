<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("acesso"));

$filter = new GFilter();

$filtro_usuario = $_POST["filtro_usuario"];
if ($filtro_usuario != '-1')
    $filter->addFilter('AND', 'ace_int_usuario', '=', 'i', $filtro_usuario);

$filtro_periodo = $_POST["filtro_periodo"];
$arrData = explode(" - ", $filtro_periodo);
$filter->addClause("AND (ace_dti_criacao BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("ace_var_ip", "ace_var_sessao", "ace_var_server", "ace_var_url", "ace_var_agent"), $search);
}

$arrColunas = array("", "ace_int_codigo", "ace_dti_criacao", "ace_int_usuario", "ace_var_ip", "ace_var_sessao", "ace_var_server", "ace_var_url", "ace_txt_request", "ace_var_agent", "ace_txt_json", "ace_int_lead");

$col = "ace_dti_criacao";
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
$filter->setOrder(array($col => $ord, 'ace_int_codigo' => 'DESC'));
//$filter->setGroupBy("ace_int_codigo");

try {
    $arr = array();
    $acessoDao = new AcessoDao();
    $qtd = $acessoDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $acessoDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $acesso Acesso */
            foreach ($arrDados as $acesso) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $acesso->getAce_int_codigo() . "')";
                $arrayBotoes["popup"] = "__popup('" . $acesso->getAce_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = formataDadoVazio($acesso->getAce_int_codigo());
                $arr[$i][] = formataDadoVazio($acesso->getAce_dti_criacao_format());
                $arr[$i][] = '<a data-toggle="tooltip" title="Visualizar Usuário" href="' . URL_SYS . 'cadastros/usuario/view/' . $acesso->getAce_int_usuario() . getIframe() . '">' . $acesso->getAce_int_usuario_nome() . '</a>';
                $arr[$i][] = formataDadoVazio($acesso->getAce_var_ip());
                $arr[$i][] = formataDadoVazio($acesso->getAce_var_sessao());
                $arr[$i][] = formataDadoVazio($acesso->getAce_var_server());
                $arr[$i][] = formataDadoVazio($acesso->getAce_var_url());
                $arr[$i][] = formataDadoVazio($acesso->getAce_txt_request());
                $arr[$i][] = formataDadoVazio($acesso->getAce_var_agent());
                $arr[$i][] = formataDadoVazio($acesso->getAce_txt_json());
                $arr[$i][] = formataDadoVazio($acesso->getAce_int_lead());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
