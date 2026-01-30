<?php

/**
 * função que carrega as bibliotecas
 *
 * @return array bibliotecas
 */
function getLibs() {
    return array(
        'php.js' => array('js/php.min.js?' . filemtime(ROOT_GENESIS . 'js/php.min.js')),
        'gFunctions' => array('js/gFunctions' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gFunctions' . getMinify() . '.js')),
        'gDisplay' => array('js/gDisplay' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gDisplay' . getMinify() . '.js')),
        'gAjax' => array('js/gAjax' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gAjax' . getMinify() . '.js')),
        'gValidate' => array('js/gValidate' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gValidate' . getMinify() . '.js')),
        'genesis' => array('js/gFunctions' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gFunctions' . getMinify() . '.js'), 'js/gDisplay' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gDisplay' . getMinify() . '.js'), 'js/gAjax' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gAjax' . getMinify() . '.js'), 'js/gValidate' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gValidate' . getMinify() . '.js')),
        'jalert' => array('css/jquery.alerts' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/jquery.alerts' . getMinify() . '.css'), 'js/jquery.alerts' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.alerts' . getMinify() . '.js')),
        'jalert-custom' => array('css/jquery.alerts-custom' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/jquery.alerts-custom' . getMinify() . '.css'), 'js/jquery.alerts' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.alerts' . getMinify() . '.js')),
        'croppic' => array('css/croppic' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/croppic' . getMinify() . '.css'), 'js/croppic' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/croppic' . getMinify() . '.js')),
        'jcrop' => array('js/jquery.Jcrop' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.Jcrop' . getMinify() . '.js'), 'css/jquery.Jcrop' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/jquery.Jcrop' . getMinify() . '.css')),
        'ckeditor' => array('js/ckeditor/ckeditor' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/ckeditor/ckeditor' . getMinify() . '.js'), 'js/ckeditor/adapters/jquery' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/ckeditor/adapters/jquery' . getMinify() . '.js')),
        'maskMoney' => array('js/jquery.maskMoney' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.maskMoney' . getMinify() . '.js')),
        'cookie' => array('js/jquery.cookie' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.cookie' . getMinify() . '.js')),
        'imagem' => array('imagem.class.php'),
        'pdfToImage' => array('pdfToImage/Pdf.php'),
        'jqprint' => array('js/jquery.jqprint.0.3' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.jqprint.0.3' . getMinify() . '.js')),
        'json' => array('js/jquery.json' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.json' . getMinify() . '.js')),
        'animate' => array('css/animate' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/animate' . getMinify() . '.css')),
        'gfileuploader' => array('js/gFileUploader.com' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/gFileUploader.com' . getMinify() . '.js'), 'css/gFileUploader' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/gFileUploader' . getMinify() . '.css')),
        'colorbox' => array('js/jquery.colorbox' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.colorbox' . getMinify() . '.js'), 'css/colorbox' . getMinify() . '.css?' . filemtime(ROOT_GENESIS . 'css/colorbox' . getMinify() . '.css')),
        'html2canvas' => array('js/html2canvas' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/html2canvas' . getMinify() . '.js')),
        'jquery-ui' => array('js/jquery-ui' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery-ui' . getMinify() . '.js')),
        'touch-punch' => array('js/jquery.ui.touch-punch' . getMinify() . '.js?' . filemtime(ROOT_GENESIS . 'js/jquery.ui.touch-punch' . getMinify() . '.js')),
        'fpdf186' => array('fpdf186/fpdf.php'),
        'html2pdf' => array('fpdf186/html2pdf.php'),
    );
}

?>