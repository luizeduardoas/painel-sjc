<?php

/*
 * Breadcrumbs class
 * Luiz Eduardo
 */

class Breadcrumb {

    var $output;
    var $crumbs = array();
    var $location;

    /*
     * Constructor
     */

    function Breadcrumb() {
        if ($_SESSION[SYS_MODULO]["breadcrumb"] != null) {
            $this->crumbs = $_SESSION[SYS_MODULO]["breadcrumb"];
        }
    }

    /*
     * Add a crumb to the trail:
     * @param $label - texto de exibição
     * @param $url - caminho
     * @param $level - nivel
     *
     */

    function add($label, $url, $level) {
        $crumb = array();
        $crumb["label"] = $label;
        $crumb["url"] = $url;

        if ($crumb["label"] != null && $crumb["url"] != null && isset($level)) {
            while (count($this->crumbs) > $level) {
                array_pop($this->crumbs);
            }
//            if (!isset($this->crumbs[0]) && $level > 0) {
//                $this->crumbs[0]["url"] = "/index.php";
//                $this->crumbs[0]["label"] = "Home";
//            }
            $this->crumbs[$level] = $crumb;
        }
        $_SESSION[SYS_MODULO]["breadcrumb"] = $this->crumbs;
        //$this->crumbs[$level]["url"] = null;
    }

    /*
     * Saída de uma lista de links.
     */

    function exibir() {
        echo retornar();
    }

    function retornar() {
        $ret = '<ul class="breadcrumb">';
        foreach ($this->crumbs as $i => $crumb) {
            if ($crumb['url'] != null) {
                $marcador = '';
                if ($crumb["url"] == URL_SYS) {
                    $marcador = '<i class="ace-icon fa fa-home home-icon"></i>';
                }
                $ret .= '<li>' . $marcador . '<a href="' . $crumb["url"] . '" title="' . $crumb["label"] . '">' . $crumb["label"] . '</a></li>';
            } else {
                $ret .= '<li class="active">' . $marcador . $crumb['label'] . '</li>';
            }
        }
        $ret .= '</ul><!-- /.breadcrumb -->';
        return $ret;
    }

}

?>