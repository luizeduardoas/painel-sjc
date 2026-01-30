<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("CRONCURSO");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Cron de atualização de curso", true);
$header->addMenu("CRONCURSO", "Cron de atualização de curso", "Execute para realizar a atualização de curso");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$html = '';

$html .= '<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">';
$html .= '  <h3 class="header smaller lighter green">Cron de atualização de curso</h3>';
$html .= '  <p>Ao executar esse Cron, o sistema irá buscar os dados de curso no Moodle a atualizar no painel.</p>';
$html .= '  <div class="col-xs-6 col-xs-offset-3">';
$html .= '      <button class="btn btn-lg btn-block btn-warning" onclick="__executar()"><i class="ace-icon fa fa-cog bigger-130"></i>Executar</button>';
$html .= '  </div>';
$html .= '</div>';

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    function __executar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'administracao/croncurso/'; ?>exec/";
    }
</script>