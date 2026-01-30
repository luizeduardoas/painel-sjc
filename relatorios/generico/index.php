<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GF::import(array("relatorio", "filtro"));

global $id;
$relatorio = new Relatorio();
$relatorio->setRel_var_permissao($id);
$relatorioDao = new RelatorioDao();
$relatorio = $relatorioDao->selectByPermissao($relatorio);
if (!is_null($relatorio->getRel_var_titulo())) {
    if (!seNuloOuVazio($relatorio->getRel_var_permissao())) {
        GSecurity::verificarPermissao($relatorio->getRel_var_permissao());
    }

    $breadcrumb = new Breadcrumb();
    $breadcrumb->add("Início", URL_SYS . 'home/', 0);
    $breadcrumb->add($relatorio->getRel_var_titulo(), $_SERVER["REQUEST_URI"], 1);

    $header = new GHeader($relatorio->getRel_var_titulo(), true);
    $header->addMenu("RELATORIOS", $relatorio->getRel_var_titulo(), "Visualize " . $relatorio->getRel_var_titulo() . " do sistema");
    $header->show(false, $breadcrumb);
    /* -------------------------------------------------------------------------- */


    $filtro = new Filtro();
    $filtroDao = new FiltroDao();
    $arrFiltros = $filtroDao->selectByRelatorio($relatorio);




//$arrFiltros = array();
//$arrFiltros[] = array(
//    "fil_var_identificador" => "txt_procurar",
//    "fil_var_titulo" => "Procurar",
//    "fil_cha_tipo" => "VAR",
//    "fil_int_tamanho" => "200",
//    "fil_int_ordem" => "1",
//    "fil_var_default" => ""
//);
//$arrFiltros[] = array(
//    "fil_var_identificador" => "cmb_status",
//    "fil_var_titulo" => "Status",
//    "fil_cha_tipo" => "CHA",
//    "fil_int_tamanho" => "1",
//    "fil_int_ordem" => "2",
//    "fil_txt_valores" => "A:Ativo;C:Cancelado;I:Inativo;B:Bloqueado;T:Todos",
//    "fil_var_default" => "T"
//);
//$arrFiltros[] = array(
//    "fil_var_identificador" => "dti_datahora",
//    "fil_var_titulo" => "Data e Hora",
//    "fil_cha_tipo" => "DTI",
//    "fil_int_tamanho" => "16",
//    "fil_int_ordem" => "3",
//);
//$arrFiltros[] = array(
//    "fil_var_identificador" => "dtr_periodo",
//    "fil_var_titulo" => "Período",
//    "fil_cha_tipo" => "DTR",
//    "fil_int_tamanho" => "35",
//    "fil_int_ordem" => "4",
//);
//$arrFiltros[] = array(
//    "fil_var_identificador" => "dat_data",
//    "fil_var_titulo" => "Data",
//    "fil_cha_tipo" => "DAT",
//    "fil_int_tamanho" => "10",
//    "fil_int_ordem" => "5",
//);
//$arrFiltros[] = array(
//    "fil_var_identificador" => "txt_quantidade",
//    "fil_var_titulo" => "Quantidade",
//    "fil_cha_tipo" => "INT",
//    "fil_int_tamanho" => "3",
//    "fil_int_ordem" => "6",
//    "fil_txt_valores" => "1-300",
//    "fil_var_default" => "1"
//);

    $form = new GForm();
    $html = '';
    $filtros = '<h3 class="header smaller lighter green">Filtros</h3>';
    $filtros .= $form->open("form_filtros");
    $filtros .= $form->addInput("hidden", "rel_int_codigo", false, array("value" => $id));
    foreach ($arrFiltros as $filtro) {
        $identificador = getPosicaoSplit($filtro->getFil_var_identificador(), '.', 1);
        switch ($filtro->getFil_cha_tipo()) {
            case "CHA":
                $arrValores = explode(";", $filtro->getFil_txt_valores());
                $arrItens = array();
                foreach ($arrValores as $valores) {
                    list($k, $v) = explode(":", $valores);
                    $arrItens[$k] = $v;
                }
                $filtros .= '<div class="filtroInline" style="max-width: 200px;">' . $form->addSelect($identificador, $arrItens, $filtro->getFil_var_default(), $filtro->getFil_var_titulo(), array("class" => "chosen-select"), false, false, false) . '</div>';
                break;
            case "VAR":
                $filtros .= '<div class="filtroInline" style="max-width: 200px;">' . $form->addInput("text", $identificador, $filtro->getFil_var_titulo(), array("value" => $filtro->getFil_var_default(), "class" => "form-control input", "maxlength" => $filtro->getFil_int_tamanho())) . '</div>';
                break;
            case "DAT":
                $filtros .= '<div class="filtroInline" style="max-width: 180px;">' . $form->addDatePicker($identificador, $filtro->getFil_var_titulo(), false, array("value" => date("d/m/Y"), "maxlength" => $filtro->getFil_int_tamanho(), "class" => "form-control", "style" => "width: 100px;")) . '</div>';
                break;
            case "DTI":
                $filtros .= '<div class="filtroInline" style="max-width: 200px;">' . $form->addDatePicker($identificador, $filtro->getFil_var_titulo(), true, array("value" => date("d/m/Y H:i"), "maxlength" => $filtro->getFil_int_tamanho(), "class" => "form-control", "style" => "width: 140px;")) . '</div>';
                break;
            case "DTR":
                $filtros .= '<div class="filtroInline" style="max-width: 200px;">' . $form->addDateRange($identificador, $filtro->getFil_var_titulo(), false, array("value" => date("d/m/Y") . ' - ' . date("d/m/Y"), "class" => "form-control campo", "style" => "width: 180px;")) . '</div>';
                break;
            case "INT":
                list($i, $f) = explode("-", $filtro->getFil_txt_valores());
                $filtros .= '<div class="filtroInline" style="max-width: 100px;">' . $form->addSpinner($identificador, $filtro->getFil_var_titulo(), array("value" => $filtro->getFil_var_default(), "class" => "form-control max-80-px", "maxlength" => $filtro->getFil_int_tamanho(), "onkeypress" => "return somenteNumero(event)"), false, false, $i, $f, 1) . '</div>';
                break;
            default:
                break;
        }
    }
    $filtros .= '<div class="filtroInline" style="max-width: 100px;vertical-align: bottom;">';
    $filtros .= '<a id="btn_listar" class="btn btn-sm btn-warning botaoCabecalho" data-toggle="tooltip" data-placement="top" alt="Listar em Tabela" title="Listar em Tabela"><i class="ace-icon fa fa-table align-top bigger-125"></i> Listar</a>';
    $filtros .= '</div>';
    $filtros .= '<div class="filtroInline" style="max-width: 100px;vertical-align: bottom;">';
    $filtros .= '<a id="btn_exportar" class="btn btn-sm btn-success botaoCabecalho" data-toggle="tooltip" data-placement="top" alt="Exportar em Excel" title="Exportar em Excel"><i class="ace-icon fa fa-file-excel-o align-top bigger-125"></i> Exportar</a>';
    $filtros .= '</div>';
    $filtros .= $form->close();

    $html .= gerarCabecalho(array(
        'tipo' => 'full',
        'titulo' => $relatorio->getRel_var_titulo(),
        'id' => 'lista',
        'filtro' => $filtros,
        'botaoNovo' => false,
    ));
    $html .= '<div style="border: 1px solid #003c69;" id="load"><p style="margin-bottom: 0;" class="alert alert-warning center"><i class="ace-icon fa fa-exclamation-triangle bigger-150 icon-animated-vertical"></i> Selecione os filtros e clique em Listar</p></div>';
    $html .= gerarRodape(array('tipo' => 'full'));

    echo $html;

    /* -------------------------------------------------------------------------- */
    $footer = new GFooter();
    $footer->show();
}
?>
<style>
    .formFiltros { display: block; float: none;}
    .formVisao, .tableTools-container{display: none;}
</style>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_listar").click(function () {
            jQuery.gAjax.load("<?php echo URL_SYS . 'relatorios/generico/'; ?>load.php", jQuery("#form_filtros").serializeArray(), "#load");
        });
    });
</script>