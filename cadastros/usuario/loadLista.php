<?php

require_once("../../inc/global.php");

GSecurity::verificarAutenticacaoAjax(true);

require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("usuario"));

$filter = new GFilter();

$filtro_perfil = $_POST["filtro_perfil"];
if ($filtro_perfil != '-1')
    $filter->addFilter('AND', 'usu.pef_int_codigo', '=', 'i', $filtro_perfil);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addClause("AND ( usu_var_identificador = '$search'");
    $filter->addFilter('OR', 'usu_var_identificador', 'like', 's', '%' . $search . '%');
    $filter->addFilter('OR', 'usu_var_nome', 'like', 's', '%' . $search . '%');
    $filter->addFilter('OR', 'usu_var_email', 'like', 's', '%' . $search . '%');
    $filter->addClause(')');
}

$arrColunas = array("", "usu_var_foto", "usu_var_identificador", "usu_var_nome", "pef_var_descricao", "usu_var_email", "usu_cha_status", "usu_dti_cricao");

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

$arr = array();
$usuarioDao = new UsuarioDao();
$arrDados = $usuarioDao->select($filter->getWhere() . " ORDER BY " . $col . " " . $ord . " LIMIT " . $start . ", " . $length, $filter->getParam());
$qtd = $usuarioDao->selectCount($filter->getWhere(), $filter->getParam());
if (count($arrDados) > 0) {
    $i = 0;
    /* @var $usuario Usuario */
    foreach ($arrDados as $usuario) {
        // <editor-fold defaultstate="collapsed" desc="Botões">
        $arr[$i][] = '<div class="action-buttons"><a class="green tooltip-info __pointer" onclick="__selecionar(\'' . $usuario->getUsu_int_codigo() . '\', \'' . $usuario->getUsu_var_identificador() . '\', \'' . $usuario->getUsu_var_nome() . '\')" data-toggle="tooltip" data-placement="top" title="Selecionar"><i class="ace-icon fa fa-check bigger-130"></i></a></div>';
        // </editor-fold>
        $arr[$i][] = $usuario->getUsu_var_identificador();
        $arr[$i][] = $usuario->getUsu_var_nome();
        $arr[$i][] = $usuario->getPerfil()->getDescricao();
        $arr[$i][] = '<span class="label label-sm label-' . labelStatus($usuario->getUsu_cha_status()) . '  label-white middle">' . $usuario->getUsu_cha_status_format() . '</span>';
        $i++;
    }
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
