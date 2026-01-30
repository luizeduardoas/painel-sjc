<?php

class Theme {

    var $info = array();

    function __construct() {
        $this->info['tema'] = 'ace';
        $this->info['desc'] = 'Ace Master';
        $this->info['cor'] = SYS_COLOR;
        $this->info['autor'] = '';
        $this->info['arquivos-header']['css'] = array(
            URL_SYS_TEMA_GLOBAL . 'css/style' . getMinify() . '.css?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'css/style' . getMinify() . '.css'),
            URL_SYS_TEMA . 'css/style' . getMinify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/style' . getMinify() . '.css'),
            URL_SYS_TEMA . 'css/cores' . getMinify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/cores' . getMinify() . '.css'),
            URL_SYS_TEMA . 'css/maps' . getMinify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/maps' . getMinify() . '.css')
        );
        $this->info['arquivos-header']['js'] = array(
            URL_SYS_TEMA_GLOBAL . 'js/env-' . strtolower(SERVER) . getMinify() . '.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'js/env-producao' . getMinify() . '.js'),
            URL_SYS_TEMA_GLOBAL . 'js/functions' . getMinify() . '.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'js/functions' . getMinify() . '.js')
        );
        $this->info['arquivos-header']['text'] = array(
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap' . getMinify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap' . getMinify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/chosen' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/chosen' . getminify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'font-awesome/4.7.0/css/font-awesome' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'font-awesome/4.7.0/css/font-awesome' . getminify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/colorbox' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/colorbox' . getminify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/fonts.googleapis.com.css?' . filemtime(ROOT_SYS_TEMA . 'css/fonts.googleapis.com.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/jquery-ui' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/jquery-ui' . getminify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/ace' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/ace' . getminify() . '.css') . '" class="ace-main-stylesheet" id="main-ace-style" />',
            '<!--[if lte IE 9]>
                    <link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/ace-part2' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/ace-part2' . getminify() . '.css') . '" class="ace-main-stylesheet" />
		<![endif]-->',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/ace-skins' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/ace-skins' . getminify() . '.css') . '" />',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/ace-rtl' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/ace-rtl' . getminify() . '.css') . '" />',
            '<!--[if lte IE 9]>
                    <link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/ace-ie' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/ace-ie' . getminify() . '.css') . '" />
		<![endif]-->',
            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/daterangepicker' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/daterangepicker' . getminify() . '.css') . '" />',
            '<!--[if lte IE 8]>
                    <script src="' . URL_SYS_TEMA . 'js/html5shiv' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/html5shiv' . getminify() . '.js') . '"></script>
                    <script src="' . URL_SYS_TEMA . 'js/respond' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/respond' . getminify() . '.js') . '"></script>
		<![endif]-->',
            '<!--[if !IE]> -->
                    <script src="' . URL_SYS_TEMA . 'js/jquery-2.1.4' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery-2.1.4' . getminify() . '.js') . '"></script>
		<!-- <![endif]-->',
            '<!--[if IE]>
                    <script src="' . URL_SYS_TEMA . 'js/jquery-1.11.3' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery-1.11.3' . getminify() . '.js') . '"></script>
                <![endif]-->',
            '<script src="' . URL_SYS_TEMA . 'js/jquery-ui' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery-ui' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/jquery.ui.touch-punch' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.ui.touch-punch' . getminify() . '.js') . '"></script>',
            '<script type="text/javascript">
			if(\'ontouchstart\' in document.documentElement) document.write("<script src=\'' . URL_SYS_TEMA . '/js/jquery.mobile.custom' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.mobile.custom' . getminify() . '.js') . '\'>"+"<"+"/script>");
		</script>',
            '<script src="' . URL_SYS_TEMA . 'js/bootstrap' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/jquery.colorbox' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.colorbox' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/chosen.jquery' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/chosen.jquery' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/ace-elements' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/ace-elements' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/ace' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/ace' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/query-string.js?' . filemtime(ROOT_SYS_TEMA . 'js/query-string.js') . '"></script>',
            '<!-- ace settings handler -->
		<script src="' . URL_SYS_TEMA . 'js/ace-extra' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/ace-extra' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/moment' . getminify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/moment' . getminify() . '.js') . '"></script>',
            '<script src="' . URL_SYS_TEMA . 'js/daterangepicker' . getMinify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/daterangepicker' . getMinify() . '.js') . '"></script>'
        );
    }

    function getFiles($tipo = 'header') {
        if (isset($this->info['arquivos-' . $tipo]))
            return $this->info['arquivos-' . $tipo];
    }

    static function addLib($libs) {
        $return = array();
        if (count($libs)) {
            $return['text'] = array();
            foreach ($libs as $lib) {
                switch ($lib) {
                    case "datepicker":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-datepicker3.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-datepicker3.min.css') . '" />',
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-timepicker.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-timepicker.min.css') . '" />',
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/daterangepicker.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/daterangepicker.min.css') . '" />',
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-datetimepicker.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-datetimepicker.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-datepicker.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-datepicker.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-timepicker.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-timepicker.min.js') . '"></script>',
//                            '<script src="' . URL_SYS_TEMA . 'js/moment.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/moment.min.js') . '"></script>',
//                            '<script src="' . URL_SYS_TEMA . 'js/daterangepicker.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/daterangepicker.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-datetimepicker.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-datetimepicker.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-datepicker.pt-br.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-datepicker.pt-br.min.js') . '"></script>'
                        ));
                        break;
                    case "colorpicker":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-colorpicker.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-colorpicker.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-colorpicker.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-colorpicker.min.js') . '"></script>'
                        ));
                        break;
                    case "editable":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/jquery.gritter.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/jquery.gritter.min.css') . '" />',
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-editable.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-editable.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-editable.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-editable.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/ace-editable.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/ace-editable.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.gritter.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.gritter.min.js') . '"></script>'
                        ));
                        break;
                    case "wysiwyg":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/jquery-ui.custom.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/jquery-ui.custom.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery-ui.custom.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery-ui.custom.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/markdown.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/markdown.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-markdown.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-markdown.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.hotkeys.index.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.hotkeys.index.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-wysiwyg.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-wysiwyg.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootbox' . getMinify() . '.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootbox' . getMinify() . '.js') . '"></script>'
                        ));
                    case "multiselect":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/bootstrap-multiselect.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-multiselect.min.css') . '" />',
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/select2.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/select2.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.bootstrap-duallistbox.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.bootstrap-duallistbox.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-multiselect.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-multiselect.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/select2.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/select2.min.js') . '"></script>'
                        ));
                        break;
                    case "raty":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.raty.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.raty.min.js') . '"></script>'
                        ));
                        break;
                    case "tag":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/bootstrap-tag.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/bootstrap-tag.min.js') . '"></script>'
                        ));
                        break;
                    case "tree":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/tree.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/tree.min.js') . '"></script>'
                        ));
                        break;
                    case "graficos":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.easypiechart.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.easypiechart.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.sparkline.index.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.sparkline.index.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.pie.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.pie.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.resize.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.resize.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.saturated.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.saturated.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.browser.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.browser.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.drawSeries.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.drawSeries.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.uiConstants.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.uiConstants.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.flot.time.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.flot.time.js') . '"></script>'
                        ));
                        break;
                    case "autosize":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/autosize.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/autosize.min.js') . '"></script>'
                        ));
                        break;
                    case "inputlimiter":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.inputlimiter.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.inputlimiter.min.js') . '"></script>'
                        ));
                        break;
                    case "nestable":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.nestable.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.nestable.min.js') . '"></script>'
                        ));
                        break;
                    case "typeahead":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery-typeahead.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery-typeahead.js') . '"></script>'
                        ));
                        break;
                    case "dropzone":
                        $return['text'] = array_merge($return['text'], array(
                            '<link rel="stylesheet" href="' . URL_SYS_TEMA . 'css/dropzone.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/dropzone.min.css') . '" />',
                            '<script src="' . URL_SYS_TEMA . 'js/dropzone.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/dropzone.min.js') . '"></script>'
                        ));
                        break;
                    case "mask":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/jquery.maskedinput.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.maskedinput.min.js') . '"></script>'
                        ));
                        break;
                    case "wizard":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA . 'js/wizard.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/wizard.min.js') . '"></script>'
                        ));
                        break;
                    case "html2canvas":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/html2canvas/html2canvas.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/html2canvas/html2canvas.min.js') . '"></script>'
                        ));
                        break;
                    case "jspdf":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/jspdf/jspdf.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/jspdf/jspdf.min.js') . '"></script>'
                        ));
                        break;
                    case "generatePDF":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/html2canvas/html2canvas.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/html2canvas/html2canvas.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/jspdf/jspdf.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/jspdf/jspdf.min.js') . '"></script>'
                        ));
                        break;
                    case "apexchart":
                        $return['text'] = array_merge($return['text'], array(
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/apexcharts/apexcharts.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/apexcharts/apexcharts.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/apexcharts/classList.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/apexcharts/classList.min.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/apexcharts/findindex_polyfill_mdn.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/apexcharts/findindex_polyfill_mdn.js') . '"></script>',
                            '<script src="' . URL_SYS_TEMA_GLOBAL . 'plugins/apexcharts/polyfill.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/apexcharts/polyfill.min.js') . '"></script>'
                        ));
                        break;
                }
            }
        }
        return $return;
    }
}

?>
