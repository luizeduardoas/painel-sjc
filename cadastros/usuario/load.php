<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("usuario"));

$filtro_perfil = $_POST["filtro_perfil"];
$filtro_status = $_POST["filtro_status"];
$filter = new GFilter();
$filter->addFilter('AND', 'usu.pef_int_codigo', '<>', 'i', '0');

if ($filtro_perfil != '-1')
    $filter->addFilter('AND', 'usu.pef_int_codigo', '=', 'i', $filtro_perfil);
if ($filtro_status != '-1')
    $filter->addFilter('AND', 'usu_cha_status', '=', 's', $filtro_status);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("usu_var_nome", "usu_var_identificador", "usu_var_email"), $search);
}

$arrColunas = array("", "usu_int_codigo", "usu_var_foto", "usu_var_identificador", "usu_var_nome", "pef_int_codigo", "usu_var_email", "usu_cha_status", "usu_cha_validado", "usu_dti_criacao", "usu_dti_ultimo");

$col = "usu_dti_criacao";
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
//$filter->setGroupBy("usu_int_codigo");

try {
    $arr = array();
    $usuarioDao = new UsuarioDao();
    $qtd = $usuarioDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $usuarioDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $usuario Usuario */
            foreach ($arrDados as $usuario) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $usuario->getUsu_int_codigo() . "')";
                if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_UPD", false)) {
                    $arrayBotoes["update"] = "__alterar('" . $usuario->getUsu_int_codigo() . "')";
                }
                if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_HIS", false)) {
                    $arrayBotoes["historico"] = "__historico('" . $usuario->getUsu_int_codigo() . "')";
                }
                if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_ENV_SEN", false)) {
                    $arrayBotoes["enviarsenha"] = "__enviarSenha('" . $usuario->getUsu_int_codigo() . "')";
                }
                if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && GSecurity::verificarPermissao("USUARIO_REP", false)) {
                    $arrayBotoes["logarcomo"] = "__logarComo('" . $usuario->getUsu_int_codigo() . "')";
                }
                if ($usuario->getPerfil()->getPef_int_codigo() != PERFIL_ADMINISTRADOR && $usuario->getUsu_cha_status() == 'I' && GSecurity::verificarPermissao("USUARIO_DEL", false)) {
                    $arrayBotoes["delete"] = "__excluir('" . $usuario->getUsu_int_codigo() . "')";
                }
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>
                $arr[$i][] = $usuario->getUsu_int_codigo();
                $arr[$i][] = '<img data-toggle="tooltip" data-html="true" title="<img src=\'' . $usuario->getUsu_var_foto() . '\' width=\'150px\'/>" src="' . $usuario->getUsu_var_foto() . '" class="imgGridFoto tooltip-info" />';
                $arr[$i][] = $usuario->getUsu_var_identificador();
                $arr[$i][] = $usuario->getUsu_var_nome();
                if (GSecurity::verificarPermissao("PERFIL", false))
                    $arr[$i][] = '<a data-toggle="tooltip" title="Visualizar perfil" href="' . URL_SYS . 'seguranca/perfil/view/' . $usuario->getPerfil()->getPef_int_codigo() . '">' . $usuario->getPerfil()->getDescricao() . '</a>';
                else
                    $arr[$i][] = $usuario->getPerfil()->getDescricao();
                $arr[$i][] = $usuario->getUsu_var_email();
                $arr[$i][] = '<span class="label label-sm label-' . labelStatus($usuario->getUsu_cha_status()) . ' label-white middle">' . $usuario->getUsu_cha_status_format() . '</span>';
                $arr[$i][] = '<span class="label label-sm label-' . labelStatus($usuario->getUsu_cha_validado()) . ' label-white middle">' . $usuario->getUsu_cha_validado_format() . '</span>';
                $arr[$i][] = $usuario->getUsu_dti_criacao_format();
                $arr[$i][] = formataDadoVazio($usuario->getUsu_dti_ultimo_format());

                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
