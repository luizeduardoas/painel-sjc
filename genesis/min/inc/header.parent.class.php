<?php

require_once ('header.lib.php');

/**
 * Classe para exibição do cabeçalho da página
 *
 */
class GHeaderParent {

    var $_titulo = "";
    var $_metas = array();
    var $_css = array();
    var $_scripts = array();
    var $_texts = array();
    var $_bodyClass = "";
    var $_min = "min";
    var $_bibliotecas = array();
    var $_favicon = "";
    var $_color = SYS_COLOR;

    /**
     * Método construtor para classe header passando o titulo
     *
     * @param String $titulo
     * @param boolean $isTheme Default true
     */
    function __construct($titulo, $isTheme = true) {
        $this->_titulo = $titulo;
        $this->_bibliotecas = getLibs();
        if ($isTheme) {
            require_once (ROOT_SYS_TEMA . 'theme.php');
            $theme = new Theme();
            $this->addTheme($theme->getFiles());
        }
        $libDefault = explode(',', SYS_LIB_DEFAULT);
        $this->addLib($libDefault);
        $this->_favicon = URL_SYS_TEMA_GLOBAL . 'images/favicon/';
    }

    /**
     * Adiciona uma String com metas extras
     *
     * @param String $metas
     */
    function addMetas($metas) {
        $this->_metas[] = $metas;
    }

    /**
     * Adicionarc style no array de css
     *
     * @param String $scripts
     */
    function addCSS($css) {
        $this->_css[] = $css;
    }

    /**
     * Remover css
     *
     * @param string $css
     */
    function removeCSS($css) {
        $key = array_search($css, $this->_css);
        unset($this->_css[$key]);
    }

    /**
     * Adicionar um script no array de scripts js
     *
     * @param String $script
     */
    function addScript($script) {
        $this->_scripts[] = $script;
    }

    /**
     * Remover javascript
     *
     * @param string $js
     */
    function removeScript($js) {
        $key = array_search($js, $this->_scripts);
        unset($this->_scripts[$key]);
    }

    /**
     * Adicionar um script no inicio do array de scripts js
     *
     * @param <type> $script
     */
    function addScriptInicio($script) {
        array_unshift($this->_scripts, $script);
    }

    /**
     * Adicionar um script no array de scripts js
     *
     * @param String $script
     */
    function addText($text) {
        $this->_texts[] = $text;
    }

    /**
     * Adicionar uma String com a classe do body
     *
     * @param String $bodyClass
     */
    function addBodyClass($bodyClass) {
        $this->_bodyClass = $bodyClass;
    }

    /**
     * Adicionar bibliotecas
     *
     * @param array $bibliotecas ex:['flexigrid','datepicker','ckeditor']
     *
     */
    function addLib($bibliotecas) {
        foreach ($bibliotecas as $bib) {
            if ($bib != '') {
                $arquivos = $this->_bibliotecas[$bib];
                foreach ($arquivos as $arq) {
                    $tipo = explode("/", $arq);
                    switch ($tipo[0]) {
                        case "css":
                            $this->addCSS(URL_STATIC_GN . $arq);
                            break;
                        case "js":
                            $this->addScript(URL_STATIC_GN . $arq);
                            break;
                        default:
                            require_once($arq);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Remover biblioteca
     *
     * @param string $biblioteca
     */
    function removeLib($biblioteca) {
        $arquivos = $this->_bibliotecas[$biblioteca];
        foreach ($arquivos as $arq) {
            $tipo = explode("/", $arq);
            switch ($tipo[0]) {
                case "css":
                    $key = array_search($arq, $this->_css);
                    unset($this->_css[$key]);
                    break;
                case "js":
                    $key = array_search($arq, $this->_scripts);
                    unset($this->_scripts[$key]);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Adiciona os arquivos do tema
     *
     * @param array $arquivos
     */
    function addTheme($arquivos) {
        foreach ($arquivos as $tipo => $arquivo) {
            switch ($tipo) {
                case "css":
                    foreach ($arquivo as $arq) {
                        $this->addCSS($arq);
                    }
                    break;
                case "js":
                    foreach ($arquivo as $arq) {
                        $this->addScript($arq);
                    }
                    break;
                case "text":
                    foreach ($arquivo as $arq) {
                        $this->addText($arq);
                    }
                    break;
                case "php":
                    foreach ($arquivo as $arq) {
                        require_once($arq);
                    }
                    break;
            }
        }
    }

    /**
     * Remover os arquivos do tema atual
     *
     */
    function removeTheme() {
        require_once (ROOT_SYS_TEMA . 'theme.php');
        $theme = new Theme();
        $arquivos = $theme->getFiles();
        foreach ($arquivos as $arq) {
            $tipo = explode("/", $arq);
            switch ($tipo[0]) {
                case "css":
                    $this->removeCSS(URL_SYS_TEMA . $arq);
                    break;
                case "js":
                    $this->removeScript(URL_SYS_TEMA . $arq);
                    break;
            }
        }
    }

    /**
     * Exibe todo o Cabeçalho da página com todos os parametros.
     */
    function show() {

        echo '<!doctype html>';
        echo '<!--[if lt IE 8 ]><html lang="en" class="no-js ie ie7"><![endif]-->';
        echo '<!--[if IE 8 ]><html lang="en" class="no-js ie"><![endif]-->';
        echo '<!--[if (gt IE 8)|!(IE)]><!--><html lang="en" class="no-js"><!--<![endif]-->';

        // abrir head
        echo '<head>';
        include_once(ROOT_SYS . "analyticstracking.php");
        $echo = '';
        // gerar titulo
        $echo .= '<title>' . $this->_titulo . '</title>';
        // gerar meta default
        $echo .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=4" />';
        $echo .= '<meta http-equiv="Content-type" content="text/html; charset=utf-8" />';
        $echo .= '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
        $echo .= '<meta property="og:locale" content="pr_BR">';
        $echo .= '<meta property="og:site_name" content="' . SYS_NOME . '">';
//        $echo .= '<link rel="apple-touch-icon" href="' . $this->_favicon . 'apple-touch-icon.png">';
//        $echo .= '<link rel="icon" type="image/png" sizes="512x512"  href="' . $this->_favicon . 'android-chrome-512x512.png">';
//        $echo .= '<link rel="icon" type="image/png" sizes="192x192"  href="' . $this->_favicon . 'android-chrome-192x192.png">';
//        $echo .= '<link rel="icon" type="image/png" sizes="32x32" href="' . $this->_favicon . 'favicon-32x32.png">';
//        $echo .= '<link rel="icon" type="image/png" sizes="16x16" href="' . $this->_favicon . 'favicon-16x16.png">';
        $echo .= '<link rel="icon" type="image/png" href="' . $this->_favicon . 'favicon.png">';
        $echo .= '<link rel="shortcut icon" type="image/x-icon" href="' . $this->_favicon . 'favicon.ico">';
//        $echo .= '<link rel="manifest" href="' . $this->_favicon . 'site.webmanifest">';
        $echo .= '<meta name="msapplication-TileImage" content="' . $this->_favicon . 'favicon.png">';
        $echo .= '<meta name="msapplication-TileColor" content="' . $this->_color . '">';
        $echo .= '<meta name="theme-color" content="' . $this->_color . '">';

        // gerar metas
        if ($this->_metas != "") {
            foreach ($this->_metas as $metas) {
                $echo .= $metas;
            }
        }

        // gerar texts montados
        if ($this->_texts != "") {
            foreach ($this->_texts as $text) {
                $echo .= $text;
            }
        }

        // gerar css montado
        if ($this->_css != "") {
            foreach ($this->_css as $style) {
                $echo .= '<link href="' . $style . '" rel="stylesheet" type="text/css" />';
            }
        }

        // gerar scripts montados
        if ($this->_scripts != "") {
            foreach ($this->_scripts as $js) {
                $echo .= '<script src="' . $js . '" type="text/javascript" charset="utf-8"></script>';
            }
        }



        // fechar head
        $echo .= '</head>';
        //Verifica se foi passada alguma classe para o body
        if ($this->_bodyClass != "")
            $echo .= '<body class="' . $this->_bodyClass . '">';
        else
            $echo .= '<body>';
        echo $echo;
        echo '<div class="__preloader" style="background: rgba(0, 0, 0, 0.5) !important;"><i class="ace-icon fa fa-spinner fa-spin __default" style="background-attachment:fixed;"></i></div>';
    }

    public function getScripts() {
        return $this->_scripts;
    }
}

?>