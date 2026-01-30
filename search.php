<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");
GSecurity::verificarAutenticacao();
require_once(ROOT_GENESIS . "inc/filter.class.php");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Pesquisando...");
$header->addMenu("", "Pesquisa", "Veja o que nós encontramos de acordo com sua pesquisa");
$header->show(false, $breadcrumb);
//echo '<script>jQuery.gDisplay.loadStart("HTML");</script>';
/* -------------------------------------------------------------------------- */

global $__param;
$tags = urldecode(str_replace("f=", "", $__param[1] ?? null));
$arrTags = explode("+", $tags);
$where = " WHERE 1=1 ";
if (count($arrTags)) {
    $where .= " AND ( 1<>1 ";
    foreach ($arrTags as $tag) {
        $where .= " OR UPPER(tag_txt_valores) LIKE '%" . maiusculo($tag) . "%' ";
    }
    $where .= " ) ";
} else {
    $where .= " AND UPPER(tag_txt_valores) LIKE '%" . maiusculo($tags) . "%' ";
}
$inicio = microtime(true);
$mysql = new GDbMysql();
$mysql->execute("SELECT tag_var_titulo, tag_var_url, tag_var_informacoes FROM tag " . $where . " AND tag.pem_var_codigo IN (SELECT pem_var_codigo FROM perfil_permissao pp WHERE pp.pef_int_codigo = ?)", array("i", getUsuarioSessao()->getPerfil()->getPef_int_codigo()));
$rows = $mysql->numRows();
$fim = microtime(true);
$tempo = round(((double) $fim - (double) $inicio), 6);

$html = '';
$html .= '<div class="search-page" id="search-page-2">';
$html .= '  <div class="col-xs-12 col-md-10 col-md-offset-1">';
$html .= '      <div class="search-area well no-margin-bottom">';
$html .= '          <form action="' . URL_SYS . 'search/" method="get">';
$html .= '		<div class="row">';
$html .= '                  <div class="col-md-6">';
$html .= '			<div class="input-group">';
$html .= '                          <input type="text" class="form-control" id="f" name="f" value="' . implode(" ", $arrTags) . '" />';
$html .= '                          <div class="input-group-btn">';
$html .= '                              <button type="submit" class="btn btn-primary btn-sm"><i class="ace-icon fa fa-search icon-on-right bigger-110"></i></button>';
$html .= '                          </div>';
$html .= '			</div>';
$html .= '                  </div>';
$html .= '              </div>';
$html .= '          </form>';
$html .= '          <div class="space space-6"></div>';
if ($rows) {
    $html .= '<span class="grey"><b>' . $rows . '</b> resultados (<i>' . $tempo . '</i> segundos)</span>';
} else {
    $html .= '<span class="grey">Nenhum resultado encontrado.</span>';
}
$html .= '      </div>';
$html .= '      <div class="search-results">';
while ($mysql->fetch()) {
    $html .= '          <div class="search-result">';
    $html .= '              <h5 class="search-title">';
    $html .= '                  <a href="' . URL_SYS . $mysql->res["tag_var_url"] . '">' . $mysql->res["tag_var_titulo"] . '</a>';
    $html .= '              </h5>';
    $html .= '              <a class="text-success" href="' . URL_SYS . $mysql->res["tag_var_url"] . '">' . URL_SYS . $mysql->res["tag_var_url"] . '</a>';
    $html .= '              <p class="search-content">' . $mysql->res["tag_var_informacoes"] . '</p>';
    $html .= '          </div>';
}
$html .= '      </div>';
$html .= '  </div>';
$html .= '</div>';
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<!--<script>
    jQuery(document).ready(function () {
        jQuery.gDisplay.loadStop('HTML');
    });
</script>-->