<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("CRONARQUIVOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Limpeza de arquivos não utilizados", true);
$header->addMenu("CRONARQUIVOS", "Cron de limpeza dos arquivos não utilizados", "Execute para realizar a limpeza dos arquivos não utilizados");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$html = '';

$html .= '<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">';
$html .= '  <h3 class="header smaller lighter green">Limpeza de arquivos não utilizados</h3>';
$html .= '  <p>Ao executar esse Cron, o sistema irá buscar os arquivos não utilizados e remover do sistema.</p>';
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
        window.location.href = "<?php echo URL_SYS . 'administracao/cronarquivos/'; ?>exec/";
    }
</script>