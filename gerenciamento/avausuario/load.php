<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("avaUsuario"));

$filter = new GFilter();

$filtro_escola = $_POST["filtro_escola"];
if ($filtro_escola != '-1')
    $filter->addFilter('AND', 'esc_int_codigo', '=', 'i', $filtro_escola);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("usu_int_codigo", "usu_int_userid", "usu_var_nome", "usu_var_cpf", "usu_var_matricula", "usu_var_cargo", "usu_var_funcao", "usu_var_email"), $search);
}

$arrColunas = array("", "usu_int_codigo", "esc_int_codigo", "usu_int_userid", "usu_var_nome", "usu_var_cpf", "usu_var_matricula", "usu_var_cargo", "usu_var_funcao", "usu_var_email");

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
    $usuarioDao = new AvaUsuarioDao();
    $qtd = $usuarioDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $usuarioDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $usuario AvaUsuario */
            foreach ($arrDados as $usuario) {

                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $usuario->getUsu_int_codigo() . "')";
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = formataDadoVazio($usuario->getUsu_int_codigo());
                if (GSecurity::verificarPermissao("ESCOLA", false))
                    $arr[$i][] = '<a data-toggle="tooltip" title="Visualizar escola" href="' . URL_SYS . 'gerenciamento/escola/view/' . $usuario->getEscola()->getEsc_int_codigo() . '">' . $usuario->getEscola()->getDescricao() . '</a>';
                else
                    $arr[$i][] = $usuario->getEscola()->getDescricao();
                $arr[$i][] = formataDadoVazio($usuario->getUsu_int_userid());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_nome());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_cpf());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_matricula());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_cargo());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_funcao());
                $arr[$i][] = formataDadoVazio($usuario->getUsu_var_email());
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
