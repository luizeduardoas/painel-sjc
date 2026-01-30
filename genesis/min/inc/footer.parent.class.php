<?php

/**
 * Classe para exibição do rodapé da página
 * 
 */
class GFooterParent {

    var $_scripts;

    function __construct() {
        
    }

    /**
     * Adiciona os arquivos do tema
     *
     * @param array $arquivos
     */
    function addTheme($arquivos) {
        foreach ($arquivos as $arq) {
            $tipo = explode("/", $arq);
            switch ($tipo[0]) {
                case "js":
                    $this->addScript(URL_SYS_TEMA . $arq);
                    break;
            }
        }
    }

    /**
     * Adicionar um script no array de scripts js
     *
     * @param String $script
     */
    function addScript($script) {
        $this->_scripts[] = $script;
    }

    function show($isIframe = false) {
        // gerar scripts montados
        $echo = '';
        if ($this->_scripts != "") {
            foreach ($this->_scripts as $js) {
                $echo .= '<script src="' . $js . '" type="text/javascript" charset="utf-8"></script>';
            }
        }
        $echo .= '</body>';
        $echo .= '</html>';
        echo $echo;
    }

    public function setScripts($_scripts) {
        $this->_scripts = $_scripts;
    }

    public function getScripts() {
        return $this->_scripts;
    }

}

?>