<?php

class GFooter extends GFooterParent {

    function __construct() {
        parent::__construct();
    }

    function show($isIframe = false, $fundoEscuro = false, $loadFunction = true) {
        $html = '';
        if (!$isIframe) {
            $html .= '              </div><!-- /.row -->';
            $html .= '          </div><!-- /.page-content -->';
            $html .= '      </div>';
            $html .= '  </div><!-- /.main-content -->';
            $html .= '</div><!-- /.main-container -->';
            $html .= '<div class="footer">';
            $html .= '  <div class="footer-inner">';
            $html .= '      <div class="footer-content" ' . (($fundoEscuro) ? 'style="color: #fff;"' : '') . '>';
            $html .= '          <span class="bigger-120">';
            $html .= '              &copy; Copyright 2025 - todos os direitos reservados';
            $html .= '          </span>';
            $html .= '          &nbsp; &nbsp;';
            $html .= '          <span class="pull-right">';
            $html .= '              Desenvolvido por <a href="http://gn3solutions.com/" target="_blank" class="green bolder">Gn3 Solutions</a>';
            $html .= '          </span>';
            $html .= '      </div>';
            $html .= '  </div>';
            $html .= '</div>';
            $html .= '<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse"><i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i></a>';
        } else {
            $html .= '</div>'; //__corpoFrame
        }
        echo '<script>jQuery(document).ready(function () { jQuery.gDisplay.loadStop("HTML"); });</script>';
        echo trim($html);
        if ($loadFunction) {
            parent::addScript(URL_SYS_TEMA . 'js/functions.js?' . filemtime(ROOT_SYS_TEMA . 'js/functions.js'));
        }
        parent::show($isIframe);
    }

}

?>