<?php

/**
 * Classe para montar formularios
 *
 * @author Luiz Eduardo
 */
class GForm {

    /**
     * Gerar HTML da abertura do formulário
     *
     * @param string $id Ex: 'frm_login' default = 'form'
     * @param string $metodo Ex: 'get' default = 'post'
     * @param string $target Ex: '_blanc' default = '_self'
     * @param string $acao Ex: 'frm_login.php' default = false
     * @param bool $enctype true ou false
     * @param string $class
     * @return string HTML de abertura do formulário gerado
     */
    function open($id = 'form', $metodo = 'post', $target = '_self', $acao = false, $enctype = false, $charset = "UTF-8", $class = "", $html = false) {
        $retorno = '<form id="' . $id . '" class="__form ' . $class . '" method="' . $metodo . '" target="' . $target . '"';
        if ($acao)
            $retorno .= ' action="' . $acao . '"';

        if ($enctype)
            $retorno .= ' enctype="multipart/form-data"';

        if ($charset)
            $retorno .= ' accept-charset="' . $charset . '"';

        $retorno .= ($html ? $html : '') . ' >';

        return $retorno;
    }

    /**
     * Gerar HTML de fechamento do formulário
     *
     * @return string HTML do fechamento do formulário gerado
     */
    function close() {
        return '</form>';
    }

    /**
     * Gerar HTML da abertura do formulário
     *
     * @param string $id Ex: 'frm_login' default = 'form'
     * @param string $class Ex: 'form' default = 'form'
     * @return string HTML de abertura do formulário gerado
     */
    function openClass($id = 'form', $class = 'form', $autoComplete = 'off') {
        $retorno = '<form id="' . $id . '" class="' . $class . '" method="post" target="_self" autocomplete="' . $autoComplete . '">';
        return $retorno;
    }

    /**
     *
     * @param string $idTab
     * @param array $titulos
     * @param array $conteudo
     * @param array $opcoes
     * @return string
     */
    function addTabs($idTab, $titulos, $conteudos, $opcoes = null) {
        $retorno = '';
        $retorno .= '<div id="' . $idTab . '" class="__tabs">';
        $retorno .= '<ul>';
        foreach ($titulos as $id => $titulo) {
            $retorno .= '<li><a href="#' . $id . '">' . $titulo . '</a></li>';
        }
        $retorno .= '</ul>';
        foreach ($conteudos as $id => $conteudo) {
            $retorno .= '<div id="' . $id . '">';
            $retorno .= $conteudo;
            $retorno .= '<div class="__clear"></div></div>';
        }
        $retorno .= '</div>';
        $retorno .= '<script>';
        $retorno .= 'jQuery("#' . $idTab . '").tabs({';
        if (!is_null($opcoes)) {
            foreach ($opcoes as $key => $value) {
                $retorno .= $key . ' : ' . $value . ',';
            }
            $retorno = substr($retorno, 0, -1);
        }
        $retorno .= '});';
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     *
     * @param array $params
     * @return string
     */
    function addTab($params) {
        $retorno = '';
        $css = '';
        $js = '';

        $tabs = array("tab_principal" => array("titulo" => "Principal", "icone" => "home", "cor" => "blue", "conteudo" => "Principal"));
        $idTab = '';
        $tipo = 'tabs-left'; //tabs-below
        $active = 'tab_principal';
        $scroll = false;
        $heightTab = 35;
        extract($params);
        $minHeight = ($heightTab * count($tabs));
        //$retorno .= '<div id="div_' . $idTab . '">';
        if ($scroll || $scroll == 'true') {
            $retorno .= '<div class="' . $idTab . '_scroller ' . $idTab . '_scroller-left"><i class="glyphicon glyphicon-chevron-left"></i></div>';
            $retorno .= '<div class="' . $idTab . '_scroller ' . $idTab . '_scroller-right"><i class="glyphicon glyphicon-chevron-right"></i></div>';
            $retorno .= '<div class="' . $idTab . '_wrapper">';
            $css .= '<style>';
            $css .= '.' . $idTab . '_wrapper { position:relative; margin:0 auto; overflow:hidden; padding:5px 0px; min-height:50px;}';
            $css .= '.' . $idTab . '_wrapper .' . $idTab . '_list { position:absolute; left:0px; top:4px; min-width:3000px; margin-left:12px; margin-top:0px; }';
            $css .= '.' . $idTab . '_wrapper .' . $idTab . '_list li{ display:table-cell; position:relative; text-align:center; cursor:grab; cursor:-webkit-grab; color:#efefef; vertical-align:middle; }';
            $css .= '.' . $idTab . '_scroller { text-align:center; cursor:pointer; display:none; padding:7px; padding-top:11px; white-space:no-wrap; vertical-align:middle; background-color:#fff; }';
            $css .= '.' . $idTab . '_scroller-right{ float:right; }';
            $css .= '.' . $idTab . '_scroller-left { float:left; }';
            $css .= '.iconeTab {height: 30px;}';
            $css .= '.nav-tabs { border-bottom: 0; }';
            $css .= '.nav-tabs>li { margin-bottom: -1px; }';
            $css .= '.nav-tabs>li>a { background-color: #efefef; padding: 5px;} ';
            $css .= '.widget-main .tab-content { border-width: 1px; background: #fff;} ';
            $css .= '.tab-content { min-height: ' . $minHeight . 'px; margin-top: 36px; overflow: visible !important; } ';
            $css .= '</style>';
            $js .= '<script>';
            $js .= 'var ' . $idTab . 'hidWidth;';
            $js .= 'var ' . $idTab . 'scrollBarWidths = 10;';
            $js .= 'var ' . $idTab . 'widthOfList = function () {';
            $js .= '    var ' . $idTab . 'itemsWidth = 0;';
            $js .= '    $(".' . $idTab . '_list li").each(function () {';
            $js .= '        var ' . $idTab . 'itemWidth = $(this).outerWidth();';
            $js .= '        ' . $idTab . 'itemsWidth += ' . $idTab . 'itemWidth;';
            $js .= '    });';
            $js .= '    return ' . $idTab . 'itemsWidth;';
            $js .= '};';
            $js .= 'var ' . $idTab . 'getLeftPosi = function () {';
            $js .= '    return $(".' . $idTab . '_list").position().left;';
            $js .= '};';
            $js .= 'var ' . $idTab . 'widthOfHidden = function () {';
            $js .= '    return (($(".' . $idTab . '_wrapper").outerWidth()) - ' . $idTab . 'widthOfList() - ' . $idTab . 'getLeftPosi()) - ' . $idTab . 'scrollBarWidths;';
            $js .= '};';
            $js .= 'var ' . $idTab . 'reAdjust = function () {';
//            $js .= '    console.log("a: "+$(".' . $idTab . '_wrapper").outerWidth());    ';
//            $js .= '    console.log("b: "+' . $idTab . 'widthOfList());    ';
//            $js .= '    console.log("c: "+' . $idTab . 'getLeftPosi());    ';
            $js .= '    if (($(".' . $idTab . '_wrapper").outerWidth()) < ' . $idTab . 'widthOfList()) {';
            $js .= '        $(".' . $idTab . '_scroller-right").show();';
            $js .= '    } else {';
            $js .= '        $(".' . $idTab . '_scroller-right").hide();';
            $js .= '    }';
            $js .= '    if (' . $idTab . 'getLeftPosi() < -12) {';
            $js .= '        $(".' . $idTab . '_scroller-left").show();';
            $js .= '    } else {';
            $js .= '        $(".item").animate({left: "-=" + ' . $idTab . 'getLeftPosi() + "px"}, "slow");';
            $js .= '        $(".' . $idTab . '_scroller-left").hide();';
            $js .= '    }';
            $js .= '};';
            $js .= '' . $idTab . 'reAdjust();';
            $js .= '$(window).on("resize", function (e) {';
            $js .= '    ' . $idTab . 'reAdjust();';
            $js .= '});';
            $js .= '$(".' . $idTab . '_scroller-right").click(function () {';
            $js .= '    $(".' . $idTab . '_scroller-left").fadeIn("slow");';
            $js .= '    $(".' . $idTab . '_scroller-right").fadeOut("slow");';
            $js .= '    $(".' . $idTab . '_list").animate({left: "+=" + ' . $idTab . 'widthOfHidden() + "px"}, "slow", function () { });';
            $js .= '});';
            $js .= '$(".' . $idTab . '_scroller-left").click(function () {';
            $js .= '    $(".' . $idTab . '_scroller-right").fadeIn("slow");';
            $js .= '    $(".' . $idTab . '_scroller-left").fadeOut("slow");';
            $js .= '    $(".' . $idTab . '_list").animate({left: "-=" + ' . $idTab . 'getLeftPosi() + "px"}, "slow", function () { });';
            $js .= '});';
            $js .= '</script>';
        }
        foreach ($tabs as $tab => $arr) {
            foreach ($arr as $cod => $val) {
                switch ($cod) {
                    case "titulo":
                        $titulos[$tab] = $val;
                        break;
                    case "icone":
                        $icones[$tab] = $val;
                        break;
                    case "cor":
                        $cores[$tab] = $val;
                        break;
                    case "conteudo":
                        $conteudos[$tab] = $val;
                        break;
                    default:
                        break;
                }
            }
        }
        $retorno .= '<div class="tabbable ' . $tipo . '">';
        $retorno .= '<ul class="nav nav-tabs ' . $idTab . '_list" id="' . $idTab . '">';
        foreach ($titulos as $id => $titulo) {
            $border = ($scroll || $scroll == 'true') ? 'style="border-top: 2px solid ' . $cores[$id] . '"' : '';
            $class = ($id == $active) ? 'active' : '';
            $retorno .= '<li class="' . $class . '">';
            $retorno .= '<a data-toggle="tab" href="#' . $id . '" ' . $border . '>';
            if (substr($icones[$id], 0, 4) == 'http') {
                $retorno .= '<i><img src="' . $icones[$id] . '" class="iconeTab"/></i>';
            } else {
                $retorno .= '<i class="' . $cores[$id] . ' ace-icon fa fa-' . $icones[$id] . ' bigger-110"></i>';
            }

            $retorno .= ' ' . $titulo . '</a>';
            $retorno .= '</li>';
        }
        $retorno .= '</ul>';
        $retorno .= '<div class="tab-content">';
        foreach ($conteudos as $id => $conteudo) {
            $class = ($id == $active) ? 'in active' : '';
            $retorno .= '<div id="' . $id . '" class="tab-pane ' . $class . '">';
            $retorno .= $conteudo;
            $retorno .= '</div>';
        }
        $retorno .= '</div>';
        $retorno .= '</div>';

        if ($scroll || $scroll == 'true') {
            $retorno .= '</div>';
            $retorno .= $css;
            $retorno .= $js;
        } else {
            $retorno .= '<style>';
            $retorno .= '.tab-content { min-height: ' . $minHeight . 'px; } ';
            $retorno .= '.tabs-left>.nav-tabs>li>a .badge { float: right; margin-left: 10px; } ';
            $retorno .= '</style>';
        }
//        $retorno .= '</div>';
        return $retorno;
    }

    /**
     * Gerar HTML de label
     *
     * @param string $id Ex: 'lbl_nome' default = 'label'
     * @param string $valor Ex: 'Nome' deafult = ''
     * @param array $parametros Ex: 'class'=>'cls_label' default = false
     * @return string HTML do label gerado
     */
    function addLabel($id, $valor, $parametros = false) {
        $retorno = '';
        $idLabel = 'lbl_' . $id;
        $retorno .= '<label id="' . $idLabel . '" for="' . $id . '" ';
        if ($parametros) {
            foreach ($parametros as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>' . $valor . '</label>';

        return $retorno;
    }

    /**
     * Gerar HTML com labels para visualização de informações
     * 
     * @param string $titulo
     * @param string $descricao
     * @param array $paramsTitulo
     * @param array $paramsDescricao
     * @return string
     */
    function addTituloDescricao($titulo, $descricao, $paramsTitulo = array(), $paramsDescricao = array()) {
        $retorno = '';
        $paramsTitulo = array_merge($paramsTitulo, array("class" => "cls_label"));

        $retorno .= '<label';
        if ($paramsTitulo) {
            foreach ($paramsTitulo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>' . $titulo . '</label>';

        $paramsDescricao = array_merge($paramsDescricao, array("class" => "cls_label"));

        $retorno .= '<label';
        if ($paramsDescricao) {
            foreach ($paramsDescricao as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>' . $descricao . '</label>';

        return $retorno;
    }

    /**
     * Carregar html com classe de legenda
     * 
     * @param string $legenda
     * @return string
     */
    function addLegenda($legenda) {
        $retorno = '<span class="__legenda">' . $legenda . '</span>';
        return $retorno;
    }

    /**
     * Gerar HTML de input
     *
     * @param string $tipo Ex: 'text', 'password', 'button'
     * @param string $id Ex: 'txt_nome'
     * @param string $titulo Ex: 'Nome' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'size'=>'100' default = false     
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @param boolean $group Default: False 
     * @param string $classInputGroup Default: '' 
     * @return string HTML do input gerado
     */
    function addInput($tipo, $id, $titulo = false, $paramCampo = false, $paramTitulo = false, $legendas = false, $group = false, $classInputGroup = '') {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';

        if ($titulo && $tipo != 'hidden')
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if (!$group) {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $this->addLegenda($legenda);
                    else
                        $legendaDepois = $this->addLegenda($legenda);
                } else {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $legenda;
                    else
                        $legendaDepois = $legenda;
                }
            }
        }
        if (!$group) {
            $retorno .= '<span class="relative" ' . (($tipo == 'hidden') ? 'style="display:none;"' : '') . '>';
        } else {
            $retorno .= '<div class="input-group ' . $classInputGroup . '">';
        }
        $retorno .= $legendaAntes . '<input type="' . $tipo . '" id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $val = is_array($value) ? implode(",", $value) : $value;
                $retorno .= ' ' . $parametro . '="' . htmlspecialchars($val) . '"';
            }
        }
        $retorno .= ' />' . $legendaDepois;
        if (!$group) {
            $retorno .= '</span>';
        } else {
            $retorno .= '</div>';
        }
        return $retorno;
    }

    /**
     * Gerar HTML de input com botoes spinner
     *
     * @param string $id Ex: 'txt_nome'
     * @param string $titulo Ex: 'Nome' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'size'=>'100' default = false     
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string HTML do input gerado
     */
    function addSpinner($id, $titulo = false, $paramCampo = false, $paramTitulo = false, $class = false, $min = 1, $max = 99, $step = 1, $onchange = false, $legendas = false) {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';
        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if ($tipoLegenda == 'A')
                    $legendaAntes = $this->addLegenda($legenda);
                else
                    $legendaDepois = $this->addLegenda($legenda);
            }
        }
        $retorno .= '<span class="relative ' . $class . '" style="display:block">';
        $retorno .= $legendaAntes . '<input type="text" id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
        }
        $retorno .= ' />' . $legendaDepois;
        $retorno .= '</span>';
        $retorno .= '<script>';
        $retorno .= 'var spinner_' . $id . ' = $("#' . $id . '").spinner({ ';
        if ($onchange) {
            $retorno .= 'stop: function(e,ui){ ';
            $retorno .= $onchange;
            $retorno .= '}, ';
        }
        $retorno .= '		create: function( event, ui ) {
        			$(this)
                                    .next().addClass("btn btn-success").html(\'<i class="ace-icon fa fa-plus"></i>\')
                                    .next().addClass("btn btn-danger").html(\'<i class="ace-icon fa fa-minus"></i>\')
				if("touchstart" in document.documentElement) 
                                    $(this).closest(".ui-spinner").addClass("ui-spinner-touch");
				}, min: ' . $min . ', max: ' . $max . ', step: ' . $step . '});';
        $retorno .= '$("#' . $id . '").blur(function(){';
        $retorno .= '   if ($(this).val() < ' . $min . ' || $(this).val() > ' . $max . ') {';
        $retorno .= '       jQuery.gDisplay.showError("O valor informado deve está entre ' . $min . ' e ' . $max . '.", "$(\'#' . $id . '\').focus();");';
        $retorno .= '   }';
        $retorno .= '});';
        $retorno .= '$("#' . $id . '").change(function(){';
        if ($onchange) {
            $retorno .= $onchange;
        }
        $retorno .= '});';
        $retorno .= '</script>';

        return $retorno;
    }

    /**
     * Gerar HTML de textarea
     *
     * @param string $id Ex: 'txt_texto' default = 'textarea'
     * @param string $valor Ex: 'texto de exemplo' default = ''
     * @param string $titulo Ex: 'Texto' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'cols'=>'10' 'rols'=>'3' default = false
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @return string HTML do textarea gerado
     */
    function addTextarea($id, $valor = false, $titulo = false, $paramCampo = false, $paramTitulo = false, $legendas = false) {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if ($tipoLegenda == 'A')
                    $legendaAntes = $this->addLegenda($legenda);
                else
                    $legendaDepois = $this->addLegenda($legenda);
            }
        }

        $retorno .= '<span class="relative">';
        $retorno .= $legendaAntes . '<textarea id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>' . $valor . '</textarea>' . $legendaDepois;
        $retorno .= '</span>';

        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para o funcionamento do CKEditor
     *
     * @param string $id Ex: 'txt_texto' default = 'textarea'
     * @param string $valor Ex: 'texto de exemplo' default = ''
     * @param string $titulo Ex: 'Texto' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'cols'=>'10' 'rols'=>'3' default = false
     * @param array $configCkeditor Ex: array("toolbar" => "'basico'") || array("toolbar" => "'minimo'")
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @return String
     */
    function addCKEditor($id, $valor = false, $titulo = false, $paramCampo = false, $configCkeditor = false, $paramTitulo = false, $legendas = false) {
        $retorno = '';

        $retorno .= $this->addTextarea($id, htmlspecialchars($valor), $titulo, $paramCampo, $paramTitulo, $legendas);

        $config = '';
        if ($configCkeditor) {
            foreach ($configCkeditor as $key => $value) {
                $config .= $key . ":" . $value . ",";
            }
            $config = substr($config, 0, -1);
        }

        $retorno .= '<script>';
        $retorno .= 'jQuery("#' . $id . '").ckeditor(function(){}, {' . $config . '} );';
        $retorno .= '</script>';

        return $retorno;
    }

    /**
     * Gerar HTML de Select
     *
     * @param string $id Ex: 'slc_tipo' default = 'select'
     * @param array $options Ex: '0' => 'Inativo', '1' => 'Ativo' default = '-1' => 'selecione...'
     * @param string $selectOption Ex: '1' default = '-1'
     * @param string $titulo Ex: 'Tipo' default = false
     * @param array $paramCampo Ex: 'class' => 'cls_campo', 'size' => '1' 'multiple' => 'multiple' default = false     *
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @param boolean $selecione "Para inserir o primeiro ítem 'Selecione...'" Ex: true (padrão), false
     * @param boolean $group Default: False 
     * @return string HTML do select gerado
     */
    function addSelect($id, $options, $selectedOption = '-1', $titulo = false, $paramCampo = false, $paramTitulo = false, $legendas = false, $selecione = true, $group = false, $paramOptions = false) {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if (!$group) {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $this->addLegenda($legenda);
                    else
                        $legendaDepois = $this->addLegenda($legenda);
                } else {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $legenda;
                    else
                        $legendaDepois = $legenda;
                }
            }
        }

        if (!$group) {
            $retorno .= '<span class="relative">';
        } else {
            $retorno .= '<div class="input-group">';
        }
        $retorno .= $legendaAntes . '<select id="' . $id . '" name="' . $id . '" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>';

        if ($selecione) {
            if ($selecione === true)
                $options = array('-1' => 'Selecione...') + $options;
            else
                $options = array('-1' => $selecione) + $options;
        }
        foreach ($options as $indice => $value) {
            $paramOpt = '';
            if (is_array($paramOptions)) {
                $paramOpt = $paramOptions[$indice];
            }
            if ($selectedOption == $indice)
                $retorno .= '<option selected="selected" value="' . $indice . '" ' . $paramOpt . '>' . $value . '</option>';
            else
                $retorno .= '<option value="' . $indice . '" ' . $paramOpt . '>' . $value . '</option>';
        }

        $retorno .= '</select>' . $legendaDepois;
        if (!$group) {
            $retorno .= '</span>';
        } else {
            $retorno .= '</div>';
        }

        return $retorno;
    }

    /**
     * Gerar HTML de Select
     *
     * @param string $id Ex: 'slc_tipo' default = 'select'
     * @param array $options Ex: '0' => 'Inativo', '1' => 'Ativo' default = '-1' => 'selecione...'
     * @param string $selectOption Ex: '1' default = '-1'
     * @param string $titulo Ex: 'Tipo' default = false
     * @param array $paramCampo Ex: 'class' => 'cls_campo', 'size' => '1' 'multiple' => 'multiple' default = false     *
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @param boolean $selecione "Para inserir o primeiro ítem 'Selecione...'" Ex: true (padrão), false
     * @param boolean $group Default: False 
     * @return string HTML do select gerado
     */
    function addSelectMulti($id, $options, $selectedOption = '-1', $titulo = false, $paramCampo = false, $paramTitulo = false, $legendas = false, $selecione = true, $group = false, $onDropdown = false) {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if (!$group) {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $this->addLegenda($legenda);
                    else
                        $legendaDepois = $this->addLegenda($legenda);
                } else {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $legenda;
                    else
                        $legendaDepois = $legenda;
                }
            }
        }

        if (!$group) {
            $retorno .= '<span class="relative" style="display: block;">';
        } else {
            $retorno .= '<div class="input-group">';
        }
        $retorno .= $legendaAntes . '<select multiple="multiple" id="' . $id . '" name="' . $id . '" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>';

        if ($selecione)
            $options = array('-1' => 'Selecione...') + $options;
        foreach ($options as $indice => $value) {
            if (in_array($indice, $selectedOption))
                $retorno .= '<option selected="selected" value="' . $indice . '">' . $value . '</option>';
            else
                $retorno .= '<option value="' . $indice . '">' . $value . '</option>';
        }

        $retorno .= '</select>' . $legendaDepois;
        if (!$group) {
            $retorno .= '</span>';
        } else {
            $retorno .= '</div>';
        }

        $retorno .= '<script>';
        $retorno .= '$("#' . $id . '").multiselect({ ';
        $retorno .= '   includeSelectAllOption: true, ';
        $retorno .= '   selectAllText: \'Selecionar Todas\', ';
        $retorno .= '   nSelectedText: \' - Selecionado(s)\', ';
        $retorno .= '   nonSelectedText: \'Nenhum...\',';
        $retorno .= '   allSelectedText: \'Todos Selecionados\', ';
        $retorno .= '   filterPlaceholder: \'Buscar...\', ';
        $retorno .= '   enableFiltering: true, ';
        $retorno .= '   numberDisplayed: 5, ';
        $retorno .= ' 	enableHTML: true,';
        $retorno .= ' 	buttonClass: "btn btn-white",';
        $retorno .= ' 	enableCaseInsensitiveFiltering: true, ';
        $retorno .= ' 	enableClickableOptGroups: true, ';
        $retorno .= ' 	enableCollapsibleOptGroups: true, ';
        $retorno .= ' 	templates: {';
        $retorno .= '       button: \'<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> &nbsp;<b class="fa fa-caret-down"></b></button>\',';
        $retorno .= '       ul: \'<ul class="multiselect-container dropdown-menu"></ul>\',';
        $retorno .= '       filter: \'<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>\',';
        $retorno .= '       filterClearBtn: \'<span class="input-group-btn"><button class="btn btn-default btn-white btn-grey multiselect-clear-filter" type="button"><i class="fa fa-times-circle red2"></i></button></span>\',';
        $retorno .= '       li: \'<li><a tabindex="0"><label></label></a></li>\',';
        $retorno .= '       divider: \'<li class="multiselect-item divider"></li>\',';
        $retorno .= '       liGroup: \'<li class="multiselect-item multiselect-group"><label></label></li>\'';
        $retorno .= '   }';
        if ($onDropdown) {
            $retorno .= '  , onDropdownHide: function(event) { ';
            $retorno .= $onDropdown;
            $retorno .= '    } ';
        }
        $retorno .= '});';
        $retorno .= '</script>';

        return $retorno;
    }

    /**
     * Gerar HTML de Select 2
     *
     * @param string $id Ex: 'slc_tipo' default = 'select'
     * @param array $options Ex: '0' => 'Inativo', '1' => 'Ativo' default = '-1' => 'selecione...'
     * @param string $selectOption Ex: '1' default = '-1'
     * @param string $titulo Ex: 'Tipo' default = false
     * @param array $paramCampo Ex: 'class' => 'cls_campo', 'size' => '1' 'multiple' => 'multiple' default = false     *
     * @param array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @param array $legendas Ex: 'A'=>'R$', 'D'=>'Ex:1' default = false
     * @param boolean $selecione "Para inserir o primeiro ítem 'Selecione...'" Ex: true (padrão), false
     * @param boolean $group Default: False 
     * @return string HTML do select gerado
     */
    function addSelect2($id, $options, $selectedOption = '-1', $titulo = false, $paramCampo = false, $paramTitulo = false, $legendas = false, $selecione = true, $group = false) {
        $retorno = '';
        $legendaAntes = '';
        $legendaDepois = '';

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        if ($legendas) {
            foreach ($legendas as $tipoLegenda => $legenda) {
                if (!$group) {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $this->addLegenda($legenda);
                    else
                        $legendaDepois = $this->addLegenda($legenda);
                } else {
                    if ($tipoLegenda == 'A')
                        $legendaAntes = $legenda;
                    else
                        $legendaDepois = $legenda;
                }
            }
        }

        if (!$group) {
            $retorno .= '<span class="relative">';
        } else {
            $retorno .= '<div class="input-group">';
        }
        $retorno .= $legendaAntes . '<select multiple="multiple" id="' . $id . '" name="' . $id . '" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>';

        if ($selecione)
            $options = array('-1' => 'Selecione...') + $options;
        foreach ($options as $indice => $value) {
            if (in_array($indice, $selectedOption))
                $retorno .= '<option selected="selected" value="' . $indice . '">' . $value . '</option>';
            else
                $retorno .= '<option value="' . $indice . '">' . $value . '</option>';
        }

        $retorno .= '</select>' . $legendaDepois;
        if (!$group) {
            $retorno .= '</span>';
        } else {
            $retorno .= '</div>';
        }

        $retorno .= '<script>';
        $retorno .= '$("#' . $id . '").css("max-width","95%").select2({allowClear: true});';
        $retorno .= '$(".select2").addClass("tag-input-style");';
        $retorno .= '</script>';

        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um campo de Data e/ou hora
     *
     * @param string $id
     * @param string $titulo
     * @param boolean $hora
     * @param array $paramCampo
     * @param array $paramTitulo
     * @return string 
     */
    function addDatePicker($id, $titulo, $hora = false, $paramCampo = false, $paramTitulo = false, $ace = true, $botaoHoje = false, $paramDateTime = false) {
        $retorno = '';
        $valor = '';
        if (isset($paramCampo["value"])) {
            $valor = $paramCampo["value"];
        }
        $valor = ( $hora) ? substr(GF::formatarData($valor, false), 0, 16) : substr(GF::formatarData($valor, false), 0, 10);
        $paramCampo["placeholder"] = ($hora) ? "dia/mês/ano hor:min" : "dia/mês/ano"; // "dia/mês/ano hor:min:seg"
        $paramCampo["data-date-format"] = ($hora) ? "DD/MM/YYYY HH:mm" : "dd/mm/yyyy"; // "DD/MM/YYYY HH:mm:ss"
        if (isset($paramCampo["validate"]) && $paramCampo["validate"] != "")
            $paramCampo["validate"] = ($hora) ? $paramCampo["validate"] . ";dataHora" : $paramCampo["validate"] . ";data";
        else
            $paramCampo["validate"] = ($hora) ? "dataHora" : "data";

        $propriedades = '';
        if ($paramDateTime) {
            foreach ($paramDateTime as $key => $val) {
                $propriedades .= ', ' . $key . ': ' . $val . ' ';
            }
        }
        if ($botaoHoje) {
            $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("D" => '<span class="input-group-btn"><button type="button" id="btn_hoje_' . $id . '" name="btn_hoje_' . $id . '" title="Hoje" alt="Hoje" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-info tooltip-info"><i class="ace-icon fa fa-calendar-o bigger-130"></i></button></span>'), true);
        } else {
            $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-calendar"></i></span>'), true);
        }
        $retorno .= '<script>';
        if ($hora) {
            if ($ace)
                $retorno .= "if(!ace.vars['old_ie']) { ";
            $retorno .= "$('#" . $id . "').datetimepicker({"; //format: 'MM/DD/YYYY h:mm:ss A',//use this option to display seconds
            $retorno .= "   icons: {";
            $retorno .= "	time: 'fa fa-clock-o',";
            $retorno .= "	date: 'fa fa-calendar',";
            $retorno .= "       up: 'fa fa-chevron-up',";
            $retorno .= "	down: 'fa fa-chevron-down',";
            $retorno .= "	previous: 'fa fa-chevron-left',";
            $retorno .= "	next: 'fa fa-chevron-right',";
            $retorno .= "	today: 'fa fa-arrows ',";
            $retorno .= "	clear: 'fa fa-trash',";
            $retorno .= "	close: 'fa fa-times'";
            $retorno .= "   }";
            $retorno .= $propriedades;
            $retorno .= "}).next().on(ace.click_event, function(){";
            $retorno .= "   $(this).prev().focus();";
            $retorno .= "});";
            if ($ace)
                $retorno .= "}";
        } else {
            $retorno .= "$('#" . $id . "').datepicker({";
            $retorno .= "   autoclose: true,";
            $retorno .= "   todayHighlight: true";
            $retorno .= $propriedades;
            $retorno .= "}).next().on(ace.click_event, function(){"; //show datepicker when clicking on the icon
            $retorno .= "   $(this).prev().focus();";
            $retorno .= "});";
            $retorno .= "$('#" . $id . "').mask('99/99/9999');";
        }
        if ($botaoHoje) {
            $retorno .= 'jQuery("#btn_hoje_' . $id . '").click(function(){';
            $retorno .= '   jQuery("#' . $id . '").datepicker("update", "' . date("d/m/Y") . '");';
            $retorno .= '   setTimeout(function() { $("#' . $id . '").datepicker("hide"); $("#' . $id . '").blur();}, 50);';
            $retorno .= $botaoHoje;
            $retorno .= '});';
        }
        $retorno .= '</script>';
        return $retorno;
    }

    function addDateInlinePicker($id, $titulo, $paramCampo = false, $paramConfig = false, $paramTitulo = false) {
        $retorno = '';
        $valor = '';
        if (isset($paramCampo["value"])) {
            $valor = $paramCampo["value"];
        }
        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-calendar"></i></span>'), true);
        $retorno .= '<script>';
        $retorno .= "$('#" . $id . "').datepicker({";
        $retorno .= "   format: 'dd/mm/yyyy', ";
        $retorno .= "   inline: true, ";
        $retorno .= "   multidate: true, ";
        if ($paramConfig) {
            foreach ($paramConfig as $key => $value) {
                $retorno .= $key . ' : ' . $value . ',';
            }
            $retorno = substr($retorno, 0, -1);
        }
        $retorno .= "}).next().on(ace.click_event, function(){"; //show datepicker when clicking on the icon
        $retorno .= "   $(this).prev().focus();";
        $retorno .= "});";
        $retorno .= '</script>';
        return $retorno;
    }

    function addYearPicker($id, $titulo, $paramCampo = false, $paramTitulo = false) {
        $retorno = '';
        $valor = '';
        if (isset($paramCampo["value"])) {
            $valor = $paramCampo["value"];
        }
        $paramCampo["placeholder"] = "ano";
        $paramCampo["validate"] = "year";
        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-calendar"></i></span>'), true);
        $retorno .= '<script>';
        $retorno .= "$('#" . $id . "').datepicker({";
        $retorno .= "   format: 'yyyy', ";
        $retorno .= "   viewMode: 'years', ";
        $retorno .= "   minViewMode: 'years', ";
        $retorno .= "   maxViewMode: 'years', ";
        $retorno .= "   todayHighlight: true, ";
        $retorno .= "   endDate: '+0d', ";
        $retorno .= "}).next().on(ace.click_event, function(){"; //show datepicker when clicking on the icon
        $retorno .= "   $(this).prev().focus();";
        $retorno .= "});";
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um campo de Data e/ou hora
     *
     * @param string $id
     * @param string $titulo
     * @param array $paramCampo
     * @param array $paramTitulo
     * @return string 
     */
    function addDatePickerRange($id, $titulo, $paramCampo = false, $paramTitulo = false) {
        $retorno = '';
        $paramCampo["value"] = substr(GF::formatarData($paramCampo["value"], false), 0, 10);
        $paramCampo["placeholder"] = "dia/mês/ano";
        $paramCampo["data-date-format"] = "DD/MM/YYYY";
        if ($paramCampo["validate"] != "")
            $paramCampo["validate"] = $paramCampo["validate"] . ";dataRange";
        else
            $paramCampo["validate"] = "dataRange";
        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-calendar"></i></span>'), true);
        $retorno .= '<script>';
        $retorno .= "if(!ace.vars['old_ie']) $('#" . $id . "').daterangepicker({"; ////to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
        $retorno .= "   'applyClass' : 'btn-sm btn-success',
                            'cancelClass' : 'btn-sm btn-default',
                            locale: {
                                applyLabel: 'Apply',
                                cancelLabel: 'Cancel',
                            }";
        $retorno .= "}).next().on(ace.click_event, function(){";
        $retorno .= "   $(this).prev().focus();";
        $retorno .= "});";
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * 
     * @param string $id
     * @param string $titulo
     * @param array $paramCampo
     * @param array $paramTitulo
     * @param string $defaultTime
     * @param bool $showSeconds
     * @param int $stepMinutes Default '15'
     * @param string $min Default '00:00'
     * @param string $max Default '23:59'
     * @return string
     */
    function addTimePicker($id, $titulo, $paramCampo = false, $paramTitulo = false, $defaultTime = 'current', $showSeconds = false, $stepMinutes = '15', $min = '00:00', $max = '23:59') {
        $retorno = '';

        $arrMin = explode(":", $min);
        $timeMin = $arrMin[1] + ($arrMin[0] * 60);
        $arrMax = explode(":", $max);
        $timeMax = $arrMax[1] + ($arrMax[0] * 60);

        $paramCampo["placeholder"] = "hor:min";
        if ($paramCampo["validate"] != "")
            $paramCampo["validate"] = "time";
        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-clock-o"></i></span>'), true, 'bootstrap-timepicker');
        $retorno .= "<script>";
        $retorno .= "if(!ace.vars['old_ie']) $('#" . $id . "').timepicker({";
        $retorno .= "   minuteStep: " . $stepMinutes . ",";
        $retorno .= "   use24hours: true,";
        $retorno .= "   timeFormat: 'HH:mm',";
        $retorno .= "   format: 'HH:mm',";
        //$retorno .= "   maxHours: " . ($arrMax[0] + 1) . ",";
        $retorno .= "   showSeconds: " . ($showSeconds ? 'true' : 'false' ) . ",";
        if ($paramCampo["value"] == '')
            $retorno .= "   defaultTime: '" . $defaultTime . "',";
        $retorno .= "   showMeridian: false,";
        $retorno .= "   disableFocus: true,";
        $retorno .= "   icons: {";
        $retorno .= "       up: 'fa fa-chevron-up',";
        $retorno .= "       down: 'fa fa-chevron-down'";
        $retorno .= "   }";
        $retorno .= "}).on('focus', function() {";
        $retorno .= "   $('#" . $id . "').timepicker('showWidget');";
        $retorno .= "}).next().on(ace.click_event, function(){";
        $retorno .= "   $(this).prev().focus();";
        $retorno .= "});";

        $retorno .= "$('#" . $id . "').timepicker().on('changeTime.timepicker', function (e) { ";
        //$retorno .= "    console.log(e.time.hours); console.log(e.time.minutes);";
        $retorno .= "    var tempo = (e.time.minutes + (e.time.hours * 60));";
        $retorno .= "    if (tempo > " . $timeMax . ") {";
        $retorno .= "        jQuery.gDisplay.showError('O campo " . $titulo . " não pode ser maior que " . $max . ".', '');";
        $retorno .= "        $('#" . $id . "').val('" . $max . "'); $(this).prev().focus();";
        $retorno .= "    }";
        $retorno .= "    if (tempo < " . $timeMin . ") {";
        $retorno .= "        jQuery.gDisplay.showError('O campo " . $titulo . " não pode ser menor que " . $min . ".', '');";
        $retorno .= "        $('#" . $id . "').val('" . $min . "'); $(this).prev().focus();";
        $retorno .= "    }";
        $retorno .= "});";

        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um campo de Data e/ou hora
     *
     * @param string $id
     * @param string $titulo
     * @param boolean $hora
     * @param array $paramCampo
     * @param array $paramConfig
     * @param array $legendas 
     * @param array $paramTitulo
     * @param boolean $limpar
     * @return string 
     */
    function addDateField($id, $titulo, $hora = false, $paramCampo = false, $paramConfig = false, $legendas = false, $paramTitulo = false, $limpar = false) {
        $retorno = '';
        $paramCampo["value"] = ( $hora) ? substr(GF::formatarData($paramCampo["value"], false), 0, 16) : substr(GF::formatarData($paramCampo["value"], false), 0, 10);
        $paramCampo["placeholder"] = ($hora) ? "dia/mês/ano hor:min" : "dia/mês/ano";
        if ($paramCampo["validate"] != "")
            $paramCampo["validate"] = ($hora) ? $paramCampo["validate"] . ";dataHora" : $paramCampo["validate"] . ";data";
        else
            $paramCampo["validate"] = ($hora) ? "dataHora" : "data";
        //$paramCampo["readOnly"] = "readOnly";
        $paramCampo["class"] .= ($hora) ? " dateTimePicker max-200-px" : " datePicker max-100-px ";
        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, $legendas);
        if ($limpar)
            $retorno .= '<a class="ui-datepicker-empty" id="empty_' . $id . '" alt="Apagar Data" title="Apagar data"></a>';
        $retorno .= '<script>';
        $retorno .= ( $hora) ? 'jQuery("#' . $id . '").datetimepicker({' : 'jQuery("#' . $id . '").datepicker({';
        if ($paramConfig) {
            foreach ($paramConfig as $key => $value) {
                $retorno .= $key . ' : ' . $value . ',';
            }
            $retorno = substr($retorno, 0, -1);
        }
        $retorno .= '});';
        if ($limpar)
            $retorno .= 'jQuery("#empty_' . $id . '").click(function(){ jQuery("#' . $id . '").val(""); });';
        $retorno .= '</script>';
        return $retorno;
    }

    function addDateRange($id, $titulo, $hora = false, $paramCampo = false, $paramConfig = false, $botoes = true, $paramTitulo = false) {
        $retorno = '';
//        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo);
        $retorno .= $this->addLabel($id, $titulo, $paramTitulo);

        $tamanho = 171;
        if (is_array($botoes)) {
            $tamanho += count($botoes) * 47;
        } elseif ($botoes === true) {
            $tamanho = 312;
        }


        $retorno .= '<div class="/*col-xs-12 no-padding*/ relative spanCampoLista" style="width: ' . $tamanho . 'px;">'; // 265px
        $retorno .= '<div class="input-group">';
        $retorno .= '<input type="text" id="' . $id . '" name="' . $id . '" value="" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                if ($parametro == "class")
                    $retorno .= ' ' . $parametro . '="form-control ' . htmlspecialchars($value) . '"';
                else
                    $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
        }
        $retorno .= ' class="form-control" ';
        $retorno .= ' />';
        if ($botoes === true || is_array($botoes)) {
            $retorno .= '<span class="input-group-btn">';
            if ($botoes === true || (isset($botoes["hoje"]) && $botoes["hoje"])) {
                $retorno .= '<button type="button" id="btn_hoje_' . $id . '" name="btn_hoje_' . $id . '" title="Hoje" alt="Hoje" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-info tooltip-info"><i class="ace-icon fa fa-calendar-o bigger-130"></i></button>';
            }
            if ($botoes === true || (isset($botoes["mes"]) && $botoes["mes"])) {
                $retorno .= '<button type="button" id="btn_mes_' . $id . '" name="btn_mes_' . $id . '" title="Esse Mês" alt="Esse Mês" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-warning tooltip-warning"><i class="ace-icon fa fa-calendar bigger-130"></i></button>';
            }
            if ($botoes === true || (isset($botoes["ano"]) && $botoes["ano"])) {
                $retorno .= '<button type="button" id="btn_ano_' . $id . '" name="btn_ano_' . $id . '" title="Esse Ano" alt="Esse Ano" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-info tooltip-info"><i class="ace-icon fa fa-calendar-o bigger-130"></i></button>';
            }
            if ($botoes === true || (isset($botoes["todos"]) && $botoes["todos"])) {
                $retorno .= '<button type="button" id="btn_todos_' . $id . '" name="btn_todos_' . $id . '" title="Todas as Datas" alt="Todas as Datas" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-success tooltip-success"><i class="ace-icon fa fa-calendar-check-o bigger-130"></i></button>';
            }
            $retorno .= '</span>';
        }
        $retorno .= '</div>'; //.input-group
        $retorno .= '</div>';

        $retorno .= '<script>';
        $retorno .= ( $hora) ? 'jQuery("#' . $id . '").daterangepicker({' : 'jQuery("#' . $id . '").daterangepicker({';
        $retorno .= '"locale": { "format": "DD/MM/YYYY", "separator": " - ", "applyLabel": "Aplicar", "cancelLabel": "Cancelar", "fromLabel": "De", "toLabel": "Até", "customRangeLabel": "Custom",  "weekLabel": "W", "daysOfWeek": [
                     "D","S","T","Q","Q","S","S"], "monthNames": ["Janeiro","Fevereiro","Março","Abril","Maio","Junho", "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
                    "firstDay": 1},';
        if (isset($paramCampo["value"])) {
            $arr = explode(" - ", $paramCampo["value"]);
            $retorno .= '"startDate": "' . $arr[0] . '", "endDate": "' . $arr[1] . '",';
        }
        if ($paramConfig) {
            foreach ($paramConfig as $key => $value) {
                $retorno .= $key . ' : ' . $value . ',';
            }
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '});';
        $retorno .= 'jQuery("#btn_hoje_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setStartDate("' . date("d/m/Y") . '");';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setEndDate("' . date("d/m/Y") . '");';
        $retorno .= '   jQuery("#' . $id . '").trigger("apply.daterangepicker");';
        $retorno .= '});';
        $retorno .= 'jQuery("#btn_mes_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setStartDate("' . buscarPrimeiroDiaMes() . '");';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setEndDate("' . buscarUltimoDiaMes() . '");';
        $retorno .= '   jQuery("#' . $id . '").trigger("apply.daterangepicker");';
        $retorno .= '});';
        $retorno .= 'jQuery("#btn_ano_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setStartDate("01/01/' . date("Y") . '");';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setEndDate("31/12/' . date("Y") . '");';
        $retorno .= '   jQuery("#' . $id . '").trigger("apply.daterangepicker");';
        $retorno .= '});';
        $retorno .= 'jQuery("#btn_todos_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setStartDate("01/01/2000");';
        $retorno .= '   jQuery("#' . $id . '").data("daterangepicker").setEndDate("31/12/2100");';
        $retorno .= '   jQuery("#' . $id . '").trigger("apply.daterangepicker");';
        $retorno .= '});';
        $retorno .= '</script>';
        return $retorno;
    }

//    function addDateRange($id, $titulo, $hora = false, $paramCampo = false, $paramConfig = false, $legendas = false, $paramTitulo = false, $limpar = false) {
//        $retorno = '';
//        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, $legendas);
//        if ($limpar)
//            $retorno .= '<a class="ui-datepicker-empty" id="empty_' . $id . '" alt="Apagar Data" title="Apagar data"></a>';
//        $retorno .= '<script>';
//        $retorno .= ( $hora) ? 'jQuery("#' . $id . '").daterangepicker({' : 'jQuery("#' . $id . '").daterangepicker({';
//        $retorno .= '"locale": { "format": "DD/MM/YYYY", "separator": " - ", "applyLabel": "Aplicar", "cancelLabel": "Cancelar", "fromLabel": "De", "toLabel": "Até", "customRangeLabel": "Custom",  "weekLabel": "W", "daysOfWeek": [
//                     "D", "S", "T", "Q", "Q",  "S", "S" ], "monthNames": ["Janeiro","Fevereiro","Março", "Abril","Maio","Junho", "Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
//                    "firstDay": 1 },';
//        if (isset($paramCampo["value"])) {
//            $arr = explode(" - ", $paramCampo["value"]);
//            $retorno .= '"startDate": "' . $arr[0] . '", "endDate": "' . $arr[1] . '",';
//        }
//        if ($paramConfig) {
//            foreach ($paramConfig as $key => $value) {
//                $retorno .= $key . ' : ' . $value . ',';
//            }
//        }
//        $retorno = substr($retorno, 0, -1);
//        $retorno .= '});';
//        if ($limpar)
//            $retorno .= 'jQuery("#empty_' . $id . '").click(function(){ jQuery("#' . $id . '").val(""); });';
//        $retorno .= '</script>';
//        return $retorno;
//    }

    /**
     * Gerar HTML e Javascript para um campo de Data e/ou hora com plugin mootools
     *
     * @param string $id
     * @param string $titulo
     * @param boolean $hora
     * @param array $paramCampo
     * @param array $paramTitulo
     * @return string
     */
    function addDateFieldMootools($id, $titulo, $hora = false, $paramCampo = false, $paramTitulo = false, $legendas = false) {
        $retorno = '';

        $formatoDatepicker = 'timePicker: false, format: "d/m/Y", inputOutputFormat: "Y-m-d"';
        $formatoData = 'data.getDate()+"/"+data.getMonth()+"/"+data.getFullYear()';
        if ($hora) {
            $formatoDatepicker = 'timePicker: true, format: "d/m/Y H:i", inputOutputFormat: "Y-m-d H:i"';
            $formatoData = 'data.getDate()+"/"+data.getMonth()+"/"+data.getFullYear()+" "+data.getHours()+":"+data.getMinutes()';
        }

        $valor = 'var data = new Date(); var datetime = ' . $formatoData . ';';

        if ($paramCampo['value'] != '') {
            $valor = 'var datetime = "' . $paramCampo['value'] . '";';
        }

        $retorno .= $this->addInput('text', $id, $titulo, $paramCampo, $paramTitulo, $legendas);

        $retorno .= '<script>';
        $retorno .= 'new DatePicker("#' . $id . '", { pickerClass: "datepicker_vista", ' . $formatoDatepicker . ', allowEmpty: true});';
        $retorno .= $valor . ' $("' . $id . '").fireEvent("change",datetime);';
        $retorno .= '</script>';

        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um upload de arquivos
     *
     * @param string $id Ex: 'img_1'
     * @param string $titulo Ex: 'Fotografia'
     * @param array $param Ex: array("action" => "http://url.com/upload.php")
     * @param int $width Ex: 100 Default: 100
     * @param int $height Ex: 100 Default: 100
     * @return string
     */
    function addUploadField($id, $titulo, $param, $width = 100, $height = 100) {
        $retorno = '';
        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo);

        $retorno .= '<div id="' . $id . '"></div>';
        $retorno .= '<script>';
        $retorno .= 'jQuery("#' . $id . '").gFileUploader({';
        foreach ($param as $key => $value) {
            $retorno .= $key . ' : ' . $value . ',';
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '});';
        $retorno .= '</script>';

        $retorno .= '<style>';
        $retorno .= '.qq-upload-button, a.qq-upload-cancel, .qq-upload-cancel, .qq-upload-button input[type="file"] {';
        $retorno .= 'width: ' . $width . 'px !important;';
        $retorno .= 'height: ' . $height . 'px !important;';
        $retorno .= 'max-width: ' . $width . 'px !important;';
        $retorno .= 'max-height: ' . $height . 'px !important;';
        $retorno .= 'cursor: pointer !important;';
        $retorno .= '}';
        $retorno .= '</style>';

        return $retorno;
    }

    function addCroppic($params) {
        $retorno = '';

        $id = '';
        $titulo = '';
        $paramTitulo = false;
        $imagem = '';
        $displayCroppic = '';
        $width = '200px';
        $height = '200px';
        $paramCroppicDefault = array(
            "uploadUrl" => 'upload.php',
            "cropUrl" => 'crop.php',
            "outputUrlId" => 'croppic',
//            "imgEyecandy" => "true",
//            "imgEyecandyOpacity" => "0.2",
            "modal" => "true",
            "rotateControls" => "false",
            "doubleZoomControls" => "false",
            "loaderHtml" => '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>'
                //"loaderHtml" => '<div class="__preloader"><span class="__default" style="background-attachment:fixed"></span></div>'
        );

        extract($params);
        $paramCroppic["outputUrlId"] = $id;
        $paramCroppic = array_unique(array_merge($paramCroppicDefault, $paramCroppic));

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);
        if ($imagem) {
            $retorno .= '<div class="croppic_original" id="original_' . $id . '">';
            $retorno .= '   <div class="cropControls cropControlsUpload"><i id="croppic_excluir_' . $id . '" class="cropControlRemoveCroppedImage"></i></div>';
            $retorno .= '   <img src="' . $imagem . '"/>';
            $retorno .= '</div>';
            $displayCroppic = 'display:none;';
        }
        $retorno .= '<div class="croppic_croppic" id="croppic_' . $id . '" style="' . $displayCroppic . '"></div>';
        $retorno .= '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $imagem . '"/>';

        $retorno .= '<script>';
        $retorno .= 'var croppic_' . $id . ' = undefined;';
        $retorno .= 'var paramCroppic_' . $id . ' = {';
        foreach ($paramCroppic as $key => $value) {
            if ($value == "true" || $value == "false") {
                $retorno .= $key . ' : ' . $value . ',';
            } else {
                $retorno .= $key . ' : \'' . $value . '\',';
            }
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '};';
        $retorno .= 'jQuery(document).ready(function () {';
        if ($imagem) {
            $retorno .= 'jQuery("#croppic_excluir_' . $id . '").click(function(){';
            $retorno .= '   jQuery("#croppic_' . $id . '").show();';
            $retorno .= '   jQuery("#original_' . $id . '").remove();';
            $retorno .= '   jQuery("#' . $id . '").val("");';
            $retorno .= '   if (croppic_' . $id . ' != undefined) { croppic_' . $id . '.destroy(); }';
            $retorno .= '   croppic_' . $id . ' = new Croppic("croppic_' . $id . '", paramCroppic_' . $id . ');';
            $retorno .= '});';
        } else {
            $retorno .= 'croppic_' . $id . ' = new Croppic("croppic_' . $id . '", paramCroppic_' . $id . ');';
        }
        $retorno .= '});';
        $retorno .= '</script>';

        $retorno .= '<style>';
        $retorno .= '#croppic_' . $id . ', .croppic_original {';
        $retorno .= 'position: relative;';
        $retorno .= 'height: ' . $height . ';';
        $retorno .= 'width: ' . $width . ';';
        $retorno .= 'border:1px solid #ccc;';
        $retorno .= 'background: #fff;';
        $retorno .= '}';
        $retorno .= '.croppic_original img {';
        $retorno .= 'width: 100%;';
        $retorno .= 'display: block;';
        $retorno .= '}';
        $retorno .= '#croppic_' . $id . '_imgUploadField {';
        $retorno .= 'width: ' . $width . ';';
        $retorno .= '}';
        $retorno .= '</style>';

        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um upload de Fotos
     *
     * @param string $id Ex: 'img_1'
     * @param array $param Ex: array("action" => "http://url.com/upload.php")
     * @return string
     */
    function addUploadFotos($id, $param) {
        $retorno = '';

        $retorno .= '<div id="' . $id . '"></div>';
        $retorno .= '<script>';
        $retorno .= 'jQuery("#' . $id . '").gFileUploader({';
        foreach ($param as $key => $value) {
            $retorno .= $key . ' : ' . $value . ',';
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '});';
        $retorno .= '</script>';

        $retorno .= '<style>';
        $retorno .= '#' . $id . ' {';
        $retorno .= 'display: inline-block;';
        $retorno .= 'min-height: 28px;';
        $retorno .= 'width: 100%;';
        $retorno .= '}';
        $retorno .= '</style>';

        return $retorno;
    }

    /**
     * Gerar HTML e Javascript para um upload de arquivos passando parametros
     *
     * @param string $id Ex: 'img_1'
     * @param string $action Ex: 'http://url.com/upload.php'
     * @param string $hidden Ex: 'arq_var_imagem'
     * @param string $sizeLimite Ex: '5M' Default: '5M'
     * @param string $elemento Ex: '#__arquivo' Default: ''
     * @param string $local Ex: 'http://url.com/upload/' Default: URL_UPLOAD 
     * @return string
     */
    function addUploadFieldParamFoto($id, $action, $hidden, $sizeLimite = '5M', $elemento = '', $local = URL_UPLOAD) {
        $retorno = '';

        $class = substr($elemento, 1);
        $complete = "function(id, fileName, json){";
        $complete .= "jQuery.gDisplay.loadStop('.__painelBotoes');";
        $complete .= "var filenameUpload = json.filename;";
        $complete .= "if (filenameUpload != undefined) {";
        $complete .= "jQuery('" . $elemento . "').attr('src', '" . $local . "'+filenameUpload);";
        $complete .= "jQuery('" . $hidden . "').val(filenameUpload);";
        $complete .= "} else ";
        $complete .= "jQuery('" . $elemento . "').attr('src', '" . URL_UPLOAD_PEDIDO . "unknown.png');";
        $complete .= "jQuery('#" . $id . " .qq-upload-list').empty();";
        $complete .= "jQuery('" . $elemento . "').addClass('" . $class . "');";
        $complete .= "jQuery('#" . $id . " .qq-uploader').find('.qq-upload-button').show();";
        $complete .= "}";

        $submit = "function(id, fileName){";
        $submit .= "jQuery('" . $elemento . "').attr('src', '" . URL_UPLOAD_PEDIDO . "loading.gif');";
        $submit .= "jQuery('" . $hidden . "').val('');";
        $submit .= "jQuery('#" . $id . " .qq-uploader').find('.qq-upload-button').hide();";
        $submit .= "jQuery('#alteracao').val('true');";
        $submit .= "}";

        $param = array("action" => "'" . $action . "'",
            "multiple" => "false",
            "fileExt" => "'" . EXTENSIONS . "'",
            "sizeLimit" => "'" . $sizeLimite . "'",
            "onComplete" => $complete,
            "onSubmit" => $submit);

        $retorno .= '<div id="' . $id . '" class="objetoImagem"></div>';
        $retorno .= '<script>';
        $retorno .= 'jQuery("#' . $id . '").gFileUploader({';
        foreach ($param as $key => $value) {
            $retorno .= $key . ' : ' . $value . ',';
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '});';
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML e flash para um mp3 player
     *
     * @param string $id
     * @param string $mp3
     * @param string $skin dewplayer-bubble, dewplayer-mini, dewplayer-multi, dewplayer-playlist-cover, dewplayer-playlist, dewplayer-rect, dewplayer-vinyl, dewplayer-vol, dewplayer
     * @param string $titulo
     */
    function addMp3Player($id, $mp3, $skin = 'dewplayer-vol', $titulo = false) {

        if ($titulo)
            $this->addLabel($id, $titulo);

        $swf = URL_GENESIS . 'css/swf/dewplayer/' . $skin . '.swf';

        return '<object type="application/x-shockwave-flash" data="' . $swf . '?mp3=' . $mp3 . '" width="240" height="20" id="' . $id . '"><param name="wmode" value="transparent" /><param name="movie" value="' . $swf . '?mp3=' . $mp3 . '" /></object>';
    }

    /**
     * Gerar HTML e JavaScript para a criação de um campo slider
     *
     * Como pegar e setar um valor no componente slider:<br>
     * get<br>
     * var value = $( ".selector" ).slider( "option", "atributo" );<br>
     * set<br>
     * $( ".selector" ).slider( "option", "atributo", 37 );<br>
     *
     * Atributos:<br>
     * disabled - Type: Boolean | Default: false<br>
     * animate - Type: Boolean, String, Number | Default: false<br>
     * max - Type: Number | Default: 100<br>
     * min - Type: Number | Default: 0<br>
     * orientation - Type: String | Default: 'horizontal'<br>
     * range - Type: Boolean, String | Default: false<br>
     * step - Type: Number | Default: 1<br>
     * value - Type: Number | Default: 0<br>
     * values - Type: Array | Default: null
     *
     * @param string $id
     * @param array $paramSlider
     * @param string $titulo
     * @param string $largura 200px
     */
//    function addSlider($id, $titulo = false, $paramSlider = false, $largura = '200px') {
//        $retorno = '';
//
//        if ($titulo)
//            $retorno .= $this->addLabel($id, $titulo);
//
//        $retorno .= '<script>var slider = jQuery( \'<div id="sl_' . $id . '" class="slider" style="float: left;  width:' . $largura . '"></div> \' ).slider({';
//        $params = '';
//        $paramCampo = false;
//        if ($paramSlider) {
//            foreach ($paramSlider as $param => $value) {
//                $params .= $param . ':' . $value . ',';
//                if ($param == 'value') {
//                    $paramCampo = array("value" => $value);
//                }
//            }
//            $retorno .= $params;
//        }
//        $retorno .= 'slide: function( event, ui ) { jQuery("#' . $id . '").val(ui.value); jQuery("#c_' . $id . '").html(ui.value); } });
//            jQuery("#lbl_' . $id . '").after(\'<span id="c_' . $id . '" class="count"></span>\');
//            jQuery("#lbl_' . $id . '").after(slider);
//
//            jQuery(function() {
//                jQuery("#c_' . $id . '").html(jQuery("#sl_' . $id . '").slider( "option", "value" ));
//            });
//        </script>';
//        $retorno .= $this->addInput('hidden', $id, false, $paramCampo);
//        return $retorno;
//    }

    /**
     * Gerar HTML e JavaScript para a criação de um campo slider
     *
     * Como pegar e setar um valor no componente slider:<br>
     * get<br>
     * var value = $( ".selector" ).slider( "option", "atributo" );<br>
     * set<br>
     * $( ".selector" ).slider( "option", "atributo", 37 );<br>
     *
     * Atributos:<br>
     * disabled - Type: Boolean | Default: false<br>
     * animate - Type: Boolean, String, Number | Default: false<br>
     * max - Type: Number | Default: 100<br>
     * min - Type: Number | Default: 0<br>
     * orientation - Type: String | Default: 'horizontal'<br>
     * step - Type: Number | Default: 1<br>
     * value - Type: Number | Default: 0
     *
     * @param array $params
     */
    function addSlider($params) {
        $retorno = '';

        $id = 'slider';
        $titulo = false;
        $largura = '200px';
        $event = false;
        $style_slider_values = '';
        $paramSliderDefault = array("min" => "0", "max" => "100", "value" => "0", "step" => "1");

        extract($params);

        if (isset($paramSlider)) {
            $paramSlider = array_merge($paramSliderDefault, $paramSlider);
        } else {
            $paramSlider = $paramSliderDefault;
        }

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo);

        $parametros = '';
        $paramCampo = false;
        foreach ($paramSlider as $key => $value) {
            $parametros .= $key . ':' . $value . ',';
            if ($key == 'values') {
                $paramCampo = array("value" => $value);
            }
        }
        $retorno .= $this->addInput('hidden', $id, false, $paramCampo);
        $retorno .= '<span class="relative">';
        $retorno .= '<div id="sld_' . $id . '" class="slider" style="margin-top: 5px; width:' . $largura . '"></div>';
        $retorno .= '<div class="slider_values">';
        $retorno .= '   <span id="sld_' . $id . '_val" class="slider_val"></span>';
        $retorno .= '</div>';
        $retorno .= '</span>';
        $retorno .= '<script>';
        $retorno .= '   var slider = jQuery("#sld_' . $id . '").slider({';
        $retorno .= $parametros;
        $retorno .= '       slide: function( event, ui ) {';
        $retorno .= '           jQuery("#' . $id . '").val(ui.value);';
        $retorno .= '           jQuery("#sld_' . $id . '_val").html(formatarNota(ui.value)); ';
        $retorno .= '       } ';
        if ($event) {
            $retorno .= '   ,stop: function(event, ui) { ';
            $retorno .= $event;
            $retorno .= '   } ';
        }
        $retorno .= '   });';
        $retorno .= '   jQuery(function() { ';
        $retorno .= '       jQuery("#sld_' . $id . '_val").html(formatarNota(jQuery("#sld_' . $id . '").slider("option", "value"))); ';
        $retorno .= '       jQuery("#' . $id . '").val(jQuery("#sld_' . $id . '").slider("option", "value"));';
        $retorno .= '   });';
        $retorno .= '</script>';
        $retorno .= '<style>';
        $retorno .= '   .slider_values {margin: auto; display: block; text-align: center; margin-top: 5px; ' . $style_slider_values . '} ';
        $retorno .= '   .slider_val {margin: auto; display: block;} ';
        $retorno .= '</style>';
        return $retorno;
    }

    /**
     * Gerar HTML e JavaScript para a criação de um campo slider
     *
     * Como pegar e setar um valor no componente slider:<br>
     * get<br>
     * var value = $( ".selector" ).slider( "option", "atributo" );<br>
     * set<br>
     * $( ".selector" ).slider( "option", "atributo", 37 );<br>
     *
     * Atributos:<br>
     * disabled - Type: Boolean | Default: false<br>
     * animate - Type: Boolean, String, Number | Default: false<br>
     * max - Type: Number | Default: 100<br>
     * min - Type: Number | Default: 0<br>
     * orientation - Type: String | Default: 'horizontal'<br>
     * step - Type: Number | Default: 1<br>
     * values - Type: Array | Default: [0,100]
     *
     * @param array $paramSlider
     */
    function addSliderRanger($param) {
        $retorno = '';

        $id = 'slider';
        $titulo = false;
        $largura = '200px';
        $event = false;
        $paramSliderDefault = array("range" => "true", "min" => "0", "max" => "100", "values" => "[0,100]", "step" => "1");

        extract($param);

        if (isset($paramSlider)) {
            $paramSlider = array_merge($paramSliderDefault, $paramSlider);
        } else {
            $paramSlider = $paramSliderDefault;
        }

        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo);

        $parametros = '';
        $paramCampo = false;
        foreach ($paramSlider as $key => $value) {
            $parametros .= $key . ':' . $value . ',';
            if ($key == 'values') {
                $paramCampo = array("value" => str_replace(",", ";", str_replace("]", "", str_replace("[", "", $value))));
            }
        }
        $retorno .= $this->addInput('hidden', $id, false, $paramCampo);
        $retorno .= '<span class="relative">';
        $retorno .= '<div id="sld_' . $id . '" class="slider" style="margin-top: 5px; width:' . $largura . '"></div>';
        $retorno .= '<div class="slider_values">';
        $retorno .= '   <span id="sld_' . $id . '_min" class="slider_min slider_range"></span>';
        $retorno .= '   <span id="sld_' . $id . '_max" class="slider_max slider_range"></span>';
        $retorno .= '</div>';
        $retorno .= '</span>';
        $retorno .= '<script>';
        $retorno .= '   var slider = jQuery("#sld_' . $id . '").slider({';
        $retorno .= $parametros;
        $retorno .= '       slide: function( event, ui ) {';
        $retorno .= '           jQuery("#' . $id . '").val(ui.values[0] + ";" + ui.values[1]);';
        $retorno .= '           jQuery("#sld_' . $id . '_min").html(\'<i class="ace-icon fa fa-chevron-circle-down bigger-110"></i> \' + formatarNota(ui.values[0])); ';
        $retorno .= '           jQuery("#sld_' . $id . '_max").html(formatarNota(ui.values[1]) + \' <i class="ace-icon fa fa-chevron-circle-up bigger-110"></i>\'); ';
        $retorno .= '       } ';
        if ($event) {
            $retorno .= '   ,stop: function(event, ui) { ';
            $retorno .= $event;
            $retorno .= '   } ';
        }
        $retorno .= '   });';
        $retorno .= '   jQuery(function() { ';
        $retorno .= '       jQuery("#sld_' . $id . '_min").html(\'<i class="ace-icon fa fa-chevron-circle-down bigger-110"></i> \' + formatarNota(jQuery("#sld_' . $id . '").slider("values", 0))); ';
        $retorno .= '       jQuery("#sld_' . $id . '_max").html(formatarNota(jQuery("#sld_' . $id . '").slider("values", 1)) + \' <i class="ace-icon fa fa-chevron-circle-up bigger-110"></i>\'); ';
        $retorno .= '   });';
        $retorno .= '</script>';
        $retorno .= '<style>';
        $retorno .= '   .slider_values {display: block; margin-top: 5px;} ';
        $retorno .= '   .slider_min {float: left; color: #d36e6e;} ';
        $retorno .= '   .slider_max {float: right; color: #87b87f;} ';
        $retorno .= '</style>';
        return $retorno;
    }

    /**
     * Gerar HTML de checkbox
     *
     * @param string $id Ex: 'ckb_nome'
     * @param string $titulo Ex: 'Nome'
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'size'=>'100' default = array("class" => "ace ace-checkbox-2")
     * @return string HTML do input gerado
     */
    function addCheckbox($id, $titulo, $paramCampo = array("class" => "ace ace-checkbox-2")) {
        $retorno = '';
        $retorno .= '<div class="checkbox">';
        $retorno .= '<label>';
        $retorno .= '<input type="checkbox" id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= ' />';
        $retorno .= '<span class="lbl"> ' . $titulo . '</span>';
        $retorno .= '</label>';
        $retorno .= '</div>';
        return $retorno;
    }

    /**
     * Gerar HTML de checkbox
     *
     * @param string $id Ex: 'ckb_nome'
     * @param string $titulo Ex: 'Nome' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'size'=>'100' default = false
     * @param array $paramTitulo
     * @param string $content Ex: 'ativo-inativo', 'sim-nao'
     * @return string HTML do input gerado
     */
    function addSwitch($id, $titulo = false, $paramCampo = false, $paramTitulo = false, $content = "ativo-inativo") {
        $retorno = '';
        $paramTitulo = ($paramTitulo) ? $paramTitulo : array();
        if ($titulo)
            $retorno .= $this->addLabel($id, $titulo, array("for" => $id) + $paramTitulo);
        $retorno .= '<span class="relative">';
        $retorno .= '<input type="hidden" id="' . $id . '" name="' . $id . '" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                if ($parametro == "value")
                    $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '/>';
        $retorno .= '<label style="display: block; height: 30px;">';
        $retorno .= '<input type="checkbox" id="' . $id . '_switch" name="' . $id . '_switch" class="ace ace-switch ace-switch-7 ' . $content . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= ' />';
        $retorno .= '<span class="lbl"></span>';
        $retorno .= '</label>';
        $retorno .= '</span>';

        return $retorno;
    }

    /**
     * Gerar HTML de grupo de checkboxs
     *
     * @param string $idField
     * @param array $campos
     * @param string $checked
     * @param string $titulo
     * @param array $paramCampo
     * @param array $paramTitulo
     * @param boolean $vertical
     * @return string
     */
    function addCheckboxGroup($idField, $campos, $checked = '', $titulo = FALSE, $paramCampo = FALSE, $paramTitulo = FALSE, $vertical = FALSE, $classe = FALSE) {
        $retorno = '';
        if ($titulo)
            $retorno .= $this->addLabel($idField, $titulo, $paramTitulo);
        $class = ($vertical) ? '__radioGroupVertical' : '';
        $retorno .= '<span id="span_' . $idField . '" class="relative __grupoCheckbox" ' . $class . ' ' . $classe . ' input-height grey-bg" rel="' . $titulo . '">';
        if ($campos) {
            foreach ($campos as $id => $value) {
                $retorno .= '<div class="__itemGrupoCheckbox">';
                $retorno .= '<input type="checkbox" id="' . $idField . '_' . $id . '" name="' . $idField . '[]" class="checkbox ' . $idField . '"';
                if (is_array($checked)) {
                    $retorno .= (in_array($id, $checked)) ? ' checked ' : '';
                } else {
                    $retorno .= ( $checked == $id) ? ' checked ' : '';
                }
                $retorno .= ' value="' . $id . '" ';
                if ($paramCampo) {
                    foreach ($paramCampo as $p => $v) {
                        $retorno .= ' ' . $p . '="' . htmlspecialchars($v) . '"';
                    }
                }
                $retorno .= '/><label class="__labelRadio __hover ' . $classe . '" for="' . $idField . '_' . $id . '">' . $value . '</label>';
                if ($vertical)
                    $retorno .= '<br/>';
                $retorno .= '</div>';
            }
        }
        $retorno .= '</span>';
        return $retorno;
    }

    /**
     * Gerar HTML de radio
     *
     * @param string $id Ex: 'rad_nome'
     * @param string $titulo Ex: 'Nome' default = false
     * @param array $paramCampo Ex: 'class'=>'cls_campo', 'size'=>'100' default = false
     * @return string HTML do input gerado
     */
    function addRadio($id, $titulo = false, $paramCampo = false) {
        $retorno = '';

        $retorno .= '<input type="radio" id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= ' />' . $titulo;
        return $retorno;
    }

    /**
     *
     * @param string $id
     * @param string $titulo
     * @param array $paramCampo
     */
    function addButton($id, $titulo, $paramCampo = false) {
        $retorno = '';

        $retorno = '<button type="button" id="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                $retorno .= ' ' . $parametro . '="' . $value . '"';
            }
        }
        $retorno .= '>' . $titulo . '</button>';
        return $retorno;
    }

    /**
     * Gerar HTML com dois campos onde um é o código e outro a descricao
     * pode ser pesquisado usando qualquer um dos campos ou pelo botão que lista todos
     *
     * @param String $id Ex: pro_int_codigo
     * @param String $titulo Ex: Produto
     * @param String $path Ex 'http://url.com/lista.php'
     * @param Array $paramCampo Ex: array("validate" => "required") Default: false
     * @param String $valueCodigo Ex: 1 Default: ''
     * @param String $valueValor Ex: 1 Default: ''
     * @param String $filtro Ex: 1 Default: ''
     * @param Array $template Ex: array('800px', '400px') Default: array('800px', '400px')
     * @param Array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string
     */
    function addLov($params) {
        //function addLov($id, $titulo, $path, $paramCampo = false, $valueCodigo = '', $valueValor = '', $filtro = '', $template = array('835px', '605px'), $paramTitulo = false, $paramCodigo = false) {

        $id = 'lov';
        $titulo = 'Lista';
        $path = 'lista.php';
        $paramChave = array();
        $paramCodigo = array("placeholder" => "Código");
        $paramCampo = array("placeholder" => "Descrição");
        $paramTitulo = false;
        $sizeCodigo = "col-xs-2";
        $sizeValue = "col-xs-10";
        $valueCodigo = '';
        $valueIdentificador = '';
        $valueValor = '';
        $nomeFiltro = '';
        $filtro = '';
        $nomeFiltro2 = '';
        $filtro2 = '';
        $template = array('835px', '620px');
        $templateView = array('835px', '620px');
        $onkeypress = ' onkeypress="return somenteNumero(event)" ';
        $visualizador = array("btnViewSelect" => false);
        $adicionar = array("btnAddSelect" => false);
        $historico = array("btnHistSelect" => false);

        extract($params);

        $p_filtro = '';
        if ($filtro != '') {
            if ($nomeFiltro == '')
                $nomeFiltro = $filtro;
            $p_filtro = ',' . $nomeFiltro . ': jQuery("#' . $filtro . '").val()';
        }
        if ($filtro2 != '') {
            if ($nomeFiltro2 == '')
                $nomeFiltro2 = $filtro2;
            $p_filtro = ',' . $nomeFiltro2 . ': jQuery("#' . $filtro2 . '").val()';
        }

        //$valueIdentificador = ($valueIdentificador == '') ? $valueCodigo : $valueIdentificador;

        $retorno = '';
        $retorno .= $this->addLabel($id, $titulo, array("style" => "display:none;"));
        $retorno .= $this->addInput("hidden", $id, false, array_merge(array("value" => $valueCodigo), $paramChave));
        if ($adicionar["btnAddSelect"]) {
            $retorno .= $this->addInput("hidden", "lista-" . $id);
        }
        $retorno .= $this->addLabel('txt_cod_' . $id, $titulo, $paramTitulo);
        $retorno .= $this->addLabel('txt_val_' . $id, $titulo, array("style" => "display: none;"));
        $retorno .= '<div class="relative spanCampoLista">';
        $retorno .= '<div class="' . $sizeCodigo . ' no-padding">';
        $retorno .= '<input type="text" id="txt_cod_' . $id . '" name="txt_cod_' . $id . '" value="' . $valueIdentificador . '"' . $onkeypress;
        if ($paramCodigo) {
            foreach ($paramCodigo as $parametro => $value) {
                if ($parametro == "class")
                    $retorno .= ' ' . $parametro . '="form-control txtCampoLista ' . htmlspecialchars($value) . '"';
                else
                    $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
            $retorno .= ' class="form-control txtCampoLista" ';
        } else
            $retorno .= ' class="form-control txtCampoLista" size="4" maxlength="5" onkeypress="return somenteNumero(event)" ';
        $retorno .= ' />';
        $retorno .= '</div>'; //.col-xs-2 no-padding
        $retorno .= '<div class="' . $sizeValue . ' no-padding">';
        $retorno .= '<div class="input-group">';
        $retorno .= '<input type="text" id="txt_val_' . $id . '" name="txt_val_' . $id . '" value="' . $valueValor . '" ';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                if ($parametro == "class")
                    $retorno .= ' ' . $parametro . '="form-control txtCampoLista ' . htmlspecialchars($value) . '"';
                else
                    $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
            $retorno .= ' class="form-control txtCampoLista" ';
        } else
            $retorno .= ' class="form-control txtCampoLista" size="50" maxlength="100"';
        $retorno .= ' />';

        $retorno .= '<span class="input-group-btn">';
        $retorno .= '<button type="button" id="btn_buscar_' . $id . '" name="btn_buscar_' . $id . '" title="Buscar" alt="Buscar" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-info cboxElement tooltip-info"><i class="ace-icon fa fa-search bigger-130"></i></button>';
        $retorno .= '<button type="button" id="btn_limpar_' . $id . '" name="btn_limpar_' . $id . '" title="Limpar" alt="Limpar" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-danger tooltip-error"><i class="ace-icon fa fa-ban bigger-130"></i></button>';
        if ($visualizador["btnViewSelect"]) {
            $retorno .= '<button type="button" id="btn_view_' . $id . '" name="btn_view_' . $id . '" title="Visualizar" alt="Visualizar" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-warning tooltip-warning"><i class="ace-icon fa fa-info-circle bigger-130"></i></button>';
        }
        if ($adicionar["btnAddSelect"]) {
            $retorno .= '<button type="button" id="btn_add_' . $id . '" name="btn_add_' . $id . '" title="Adicionar" alt="Adicionar" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-success tooltip-success"><i class="ace-icon fa fa-plus bigger-130"></i></button>';
        }
        if ($historico["btnHistSelect"]) {
            $retorno .= '<button type="button" id="btn_hist_' . $id . '" name="btn_hist_' . $id . '" title="Histórico" alt="Histórico" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-grey tooltip-grey"><i class="ace-icon fa fa-history bigger-130"></i></button>';
        }
        $retorno .= '</span>';
        $retorno .= '</div>'; //.input-group
        $retorno .= '</div>'; //.col-xs-10 no-padding
        $retorno .= '<div class="__clear"></div>';
        $retorno .= '</div>'; //.relative spanCampoLista
        $retorno .= '<script>';
        $paramColorbox = " reposition: true, scrolling: true, close: '&times;', maxWidth: '100%', maxHeight: '100%', title: function(){return 'Escolha uma das opções clicando em <i class=\"ace-icon fa fa-check green bigger-130\"></i>';}, ";
        $retorno .= 'jQuery(document).ready(function(){';
        if ($visualizador["btnViewSelect"]) {
            $retorno .= 'jQuery("#btn_view_' . $id . '").click(function(){ ';
            $retorno .= '   if (jQuery("#' . $id . '").val() != "" && jQuery("#' . $id . '").val() != "-1") {';
            $retorno .= '       jQuery(this).colorbox({ ';
            $retorno .= "           reposition: true, scrolling: true, close: '&times;', maxWidth: '100%', maxHeight: '100%', title: function(){return 'Visualizando registro selecionado';}, ";
            $retorno .= '           iframe:true,';
            $retorno .= '           width:"' . $templateView[0] . '",';
            $retorno .= '           height:"' . $templateView[1] . '",';
            $retorno .= '           href:"' . $visualizador["btnViewSelect"] . '" + jQuery("#' . $id . '").val() + "/?iframe=on&timeline=off"';
            $retorno .= '       });';
            $retorno .= '   } else {';
            $retorno .= '       jQuery(this).colorbox.remove();';
            $retorno .= '       jQuery.gDisplay.showAtencao("Favor selecionar um(a) ' . $titulo . ' para visualizar.");';
            $retorno .= '   }';
            $retorno .= '});';
        }
        if ($adicionar["btnAddSelect"]) {
            $retorno .= 'jQuery("#btn_add_' . $id . '").click(function(){ ';
            $retorno .= '   if (jQuery("#' . $id . '").val() != "" && jQuery("#' . $id . '").val() != "-1") { ';
            $retorno .= '       if (jQuery("#tabela-' . $id . ' tbody").find(".vazio").length) {';
            $retorno .= '           jQuery("#tabela-' . $id . ' tbody").empty();';
            $retorno .= '       } else {';
            $retorno .= '           var jaExiste = false;';
            $retorno .= '           jQuery("#tabela-' . $id . ' tbody tr").each(function () {';
            $retorno .= '               if (jQuery("#' . $id . '").val() == jQuery(this).find("td.identificador").attr("rel")) {';
            $retorno .= '                   jaExiste = true;';
            $retorno .= '               }';
            $retorno .= '           });';
            $retorno .= '       }';
            $retorno .= '       if (jaExiste) {';
            $retorno .= '           jQuery.gDisplay.showAtencao("Essa ' . $titulo . ' já está na lista.");';
            $retorno .= '       } else {';
            $retorno .= '           jQuery.ajax({';
            $retorno .= '               type: "POST",';
            $retorno .= '               url: "' . $path . '",';
            $retorno .= '               data: {tipo: "tabela", id: jQuery("#txt_cod_' . $id . '").val()' . $p_filtro . '},';
            $retorno .= '               async: true,';
            $retorno .= '               beforeSend: function () { jQuery.gDisplay.loadStart("HTML"); },';
            $retorno .= '               error: function () { jQuery.gDisplay.loadStop("HTML"); },';
            $retorno .= '               success: function (resp) { jQuery("#tabela-' . $id . ' tbody").append(resp); jQuery("#btn_limpar_' . $id . '").click();jQuery.gDisplay.loadStop("HTML");}';
            $retorno .= '           });';
            $retorno .= '       }';
            $retorno .= '   } else {';
            $retorno .= '       jQuery.gDisplay.showAtencao("Favor selecionar um(a) ' . $titulo . ' para adicionar.");';
            $retorno .= '   }';
            $retorno .= '});';
        }
        if ($historico["btnHistSelect"]) {
            $retorno .= 'jQuery("#btn_hist_' . $id . '").click(function(){ ';
            $retorno .= '   if (jQuery("#' . $id . '").val() != "" && jQuery("#' . $id . '").val() != "-1") {';
            $retorno .= '       jQuery(this).colorbox({ ';
            $retorno .= "           reposition: true, scrolling: true, close: '&times;', maxWidth: '100%', maxHeight: '100%', title: function(){return 'Histórico do registro selecionado';}, ";
            $retorno .= '           iframe:true,';
            $retorno .= '           width:"' . $template[0] . '",';
            $retorno .= '           height:"' . $template[1] . '",';
            $retorno .= '           href:"' . $historico["btnHistSelect"] . '" + jQuery("#' . $id . '").val() + "/?iframe=on"';
            $retorno .= '       });';
            $retorno .= '   } else {';
            $retorno .= '       jQuery(this).colorbox.remove();';
            $retorno .= '       jQuery.gDisplay.showAtencao("Favor selecionar um(a) ' . $titulo . ' para acessar o histórico.");';
            $retorno .= '   }';
            $retorno .= '});';
        }
        if ($filtro != '') {
            if ($filtro2 != '') {
                $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
                $retorno .= '   var filtro = jQuery("#' . $filtro . '").val();';
                $retorno .= '   var filtro2 = jQuery("#' . $filtro2 . '").val();';
                $retorno .= '   jQuery(this).colorbox({';
                $retorno .= $paramColorbox;
                $retorno .= '       iframe:true,';
                $retorno .= '       width:"' . $template[0] . '",';
                $retorno .= '       height:"' . $template[1] . '",';
                $retorno .= '       href:"' . $path . '?campo=' . $id . '&' . $nomeFiltro . '="+filtro+"&' . $nomeFiltro2 . '="+filtro2,';
                $retorno .= '       onClosed: function () {';
                $retorno .= '           jQuery("#' . $id . '").trigger("change");';
                $retorno .= '       }';
                $retorno .= '   });';
                $retorno .= '});';
            } else {
                $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
                $retorno .= '   var filtro = jQuery("#' . $filtro . '").val();';
                $retorno .= '   jQuery(this).colorbox({';
                $retorno .= $paramColorbox;
                $retorno .= '       iframe:true,';
                $retorno .= '       width:"' . $template[0] . '",';
                $retorno .= '       height:"' . $template[1] . '",';
                $retorno .= '       href:"' . $path . '?campo=' . $id . '&' . $nomeFiltro . '="+filtro,';
                $retorno .= '       onClosed: function () {';
                $retorno .= '           jQuery("#' . $id . '").trigger("change");';
                $retorno .= '       }';
                $retorno .= '   });';
                $retorno .= '});';
            }
        } else {
            $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
            $retorno .= '   jQuery(this).colorbox({';
            $retorno .= $paramColorbox;
            $retorno .= '       iframe:true,';
            $retorno .= '       width:"' . $template[0] . '",';
            $retorno .= '       height:"' . $template[1] . '",';
            $retorno .= '       href:"' . $path . '?campo=' . $id . '",';
            $retorno .= '       onClosed: function () {';
            $retorno .= '           jQuery("#' . $id . '").trigger("change");';
            $retorno .= '       }';
            $retorno .= '   });';
            $retorno .= '});';
        }
        $retorno .= 'jQuery("#btn_limpar_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").val("").trigger("change");';
        $retorno .= '   jQuery("#txt_cod_' . $id . '").val("");';
        $retorno .= '   jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");';
        $retorno .= '   jQuery("#txt_val_' . $id . '").val("");';
        $retorno .= '});';
        $retorno .= 'jQuery("#txt_cod_' . $id . '").blur(function(){';
        $retorno .= '   if ((jQuery("#txt_cod_' . $id . '").val().length > 0) && ((jQuery("#txt_cod_' . $id . '").val() != jQuery("#' . $id . '").val()))){';
        $retorno .= '       jQuery.ajax({';
        $retorno .= '           type: "POST",';
        $retorno .= '           url: "' . $path . '",';
        $retorno .= '           data: {id: jQuery("#txt_cod_' . $id . '").val()' . $p_filtro . '},';
        $retorno .= '           async: true,';
        $retorno .= '           dataType: \'json\',';
        $retorno .= '           beforeSend: function () { jQuery.gDisplay.loadStart("HTML"); jQuery("#txt_cod_' . $id . '").removeClass("erro_lov"); jQuery("#' . $id . '").val(""); },';
        $retorno .= '           error: function () { jQuery.gDisplay.loadStop("HTML"); jQuery("#txt_cod_' . $id . '").addClass("erro_lov"); jQuery("#' . $id . '").val("").trigger("change"); jQuery("#txt_val_' . $id . '").val(""); },';
        $retorno .= '           success: function (resp) { jQuery("#txt_val_' . $id . '").val(resp.value); jQuery("#' . $id . '").val(resp.id).trigger("change"); if (resp.imagem !== undefined) {jQuery("#txt_img_' . $id . '").attr("src", resp.imagem);} jQuery.gDisplay.loadStop("HTML"); jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");}';
        $retorno .= '       });';
        $retorno .= '   }';
        $retorno .= '});';
        $retorno .= 'jQuery("#txt_val_' . $id . '").autocomplete({';
        if ($filtro != '') {
            $retorno .= 'source: function(request, response) {';
            $retorno .= '              jQuery.ajax({';
            $retorno .= '                  type: "POST",';
            $retorno .= '                  url: "' . $path . '",';
            $retorno .= '                  dataType: "json",';
            $retorno .= '                  data: {';
            $retorno .= '                    term: request.term' . $p_filtro;
            $retorno .= '                  },';
            $retorno .= '                  success: function(data) {';
            $retorno .= '                    response(data);';
            $retorno .= '                  }';
            $retorno .= '                });';
            $retorno .= '              },';
        } else {
            $retorno .= 'source: "' . $path . '",';
        }
        $retorno .= '   minLength: 3,';
        $retorno .= '   select: function( event, ui ) {';
        $retorno .= '       jQuery("#' . $id . '").val(ui.item.id).trigger("change");';
        $retorno .= '       if (ui.item.identificador === undefined) {';
        $retorno .= '           jQuery("#txt_cod_' . $id . '").val(ui.item.id);';
        $retorno .= '       } else {';
        $retorno .= '           jQuery("#txt_cod_' . $id . '").val(ui.item.identificador);';
        $retorno .= '       }';
        $retorno .= '       if (ui.item.imagem !== undefined) {';
        $retorno .= '           jQuery("#txt_img_' . $id . '").attr("src",ui.item.imagem);';
        $retorno .= '       }';
        $retorno .= '       jQuery("#txt_val_' . $id . '").val(ui.item.value);';
        $retorno .= '       jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");';
        $retorno .= '       jQuery("#' . $id . '").trigger("change");';
        $retorno .= '   },';
//        $retorno .= '   search: function( event, ui ) {';
//        $retorno .= '       jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");';
//        $retorno .= '   },';
        $retorno .= '   response: function( event, ui ) {';
        $retorno .= '       if (!ui.content.length) { ';
        $retorno .= '           var noResult = { value:"",label:"Nenhum(a) ' . $titulo . ' encontrado(a)" }; ';
        $retorno .= '           ui.content.push(noResult);';
        $retorno .= '       }';
//        $retorno .= '       jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");';
//        $retorno .= '   },';
//        $retorno .= '   close: function( event, ui ) {';
//        $retorno .= '       jQuery("#txt_cod_' . $id . '").removeClass("erro_lov");';
        $retorno .= '   }';
        $retorno .= '});';
        $retorno .= '});';
        if ($adicionar["btnAddSelect"]) {
            $retorno .= 'function excluirLinha(element){';
            $retorno .= '   jQuery(element).parent().parent().remove();';
            $retorno .= '   if (!jQuery("#tabela-' . $id . ' tbody").find("tr").length) {';
            $retorno .= '       jQuery("#tabela-' . $id . ' tbody").html(\'' . $adicionar["btnAddSelect"] . '\');';
            $retorno .= '   }';
            $retorno .= '}';
        }
        $retorno .= '</script>';
        $retorno .= '<style>';
        $retorno .= '   .spanCampoLista .check-error {';
        $retorno .= '       right: 71px !important;';
        $retorno .= '   }';
        $retorno .= '   #cboxTitle {';
        $retorno .= '       padding-left: 10px !important;';
        $retorno .= '   }';
        $retorno .= '</style>';
        return $retorno;
    }

    /**
     * Gerar HTML com um botão abre um popup que lista todos
     *
     * @param String $id Ex: arquivos
     * @param String $titulo Ex: Arquivos
     * @param String $path Ex 'http://url.com/lista.php'
     * @param String $filtro Ex: 1 Default: ''
     * @param Array $template Ex: array('800px', '400px') Default: array('800px', '400px')
     * @param Array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string
     */
    function addLovMult($params) {
        $id = 'lov';
        $titulo = 'Lista';
        $path = 'lista.php';
        $paramChave = array();
        $filtro = '';
        $selecionados = '';
        $template = array('835px', '605px');
        extract($params);

        $retorno = '';
        $retorno .= '<h3 class="header smaller lighter blue">';
        $retorno .= $titulo;
        $retorno .= ' <button type="button" id="btn_buscar_' . $id . '" name="btn_buscar_' . $id . '" title="Buscar" alt="Buscar" class="btn btn-xs btn-info cboxElement"><i class="ace-icon fa fa-search"></i></button>';
        $retorno .= '</h3>';
        $retorno .= $this->addLabel($id, $titulo, array("style" => "display:none;"));
        $retorno .= $this->addInput("hidden", $id, false, array_merge(array("value" => $selecionados), $paramChave));
        $retorno .= '<div class="space __clear space-8"></div>';
        $retorno .= '<script>';
        $paramColorbox = " reposition: true, scrolling: true, close: '&times;', maxWidth: '100%', maxHeight: '100%', title: function(){return '<center>Selecione as opções desejadas e clique no botão Confirmar logo abaixo.</center>';}, ";
        $retorno .= 'jQuery(document).ready(function(){';
        if ($filtro != '') {
            $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
            $retorno .= '   var filtro = jQuery("#' . $filtro . '").val();';
            $retorno .= '   var selecionados = jQuery("#' . $id . '").val();';
            $retorno .= '   jQuery(this).colorbox({';
            $retorno .= $paramColorbox;
            $retorno .= '       iframe:true,';
            $retorno .= '       width:"' . $template[0] . '",';
            $retorno .= '       height:"' . $template[1] . '",';
            $retorno .= '       href:"' . $path . '?campo=' . $id . '&selecionados="+selecionados+"&' . $filtro . '="+filtro';
            $retorno .= '   });';
            $retorno .= '});';
        } else {
            $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
            $retorno .= '   var selecionados = jQuery("#' . $id . '").val();';
            $retorno .= '   jQuery(this).colorbox({';
            $retorno .= $paramColorbox;
            $retorno .= '       iframe:true,';
            $retorno .= '       width:"' . $template[0] . '",';
            $retorno .= '       height:"' . $template[1] . '",';
            $retorno .= '       href:"' . $path . '?campo=' . $id . '&selecionados="+selecionados';
            $retorno .= '   });';
            $retorno .= '});';
        }
        $retorno .= '});';
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML com uma imagem que será buscada em uma lista por popup
     *
     * @param String $id Ex: img_int_codigo
     * @param String $titulo Ex: Imagem
     * @param String $path Ex 'http://url.com/imagem.php'
     * @param Array $paramChave Ex: array("validate" => "required") Default: false
     * @param String $value Ex: 1 Default: ''
     * @param String $img Ex: 'http://url.com/imagem.png'
     * @param String $filtro Ex: 1 Default: ''
     * @param Array $template Ex: array('800px', '400px') Default: array('800px', '400px')
     * @param Array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string
     */
    function addLovImagem($params) {
        $id = 'lovImagem';
        $titulo = 'Imagem';
        $path = 'imagem.php';
        $paramChave = array("validate" => "required");
        $paramTitulo = false;
        $value = '';
        $img = URL_UPLOAD . 'imagem/unknown.jpg';
        $colImagem = 'col-md-10 col-xs-8';
        $colBotoes = 'col-md-2 col-xs-4';
        $filtro = '';
        $filtro2 = '';
        $template = array('835px', '620px');

        if ($filtro != '') {
            $p_filtro = ',' . $filtro . ': jQuery("#' . $filtro . '").val()';
        }
        if ($filtro2 != '') {
            $p_filtro = ',' . $filtro2 . ': jQuery("#' . $filtro2 . '").val()';
        }

        extract($params);

        $retorno = '';
        $retorno .= $this->addLabel($id, $titulo, $paramTitulo);
        $retorno .= $this->addInput("hidden", $id, false, array_merge(array("value" => $value), $paramChave));
        $retorno .= '<div class="row">';
        $retorno .= '<div class="' . $colImagem . '">';
        $retorno .= '<img id="img_' . $id . '" src="' . $img . '" class="img-responsive">';
        $retorno .= '</div>';
        $retorno .= '<div class="' . $colBotoes . '">';
        $retorno .= '<button type="button" id="btn_buscar_' . $id . '" name="btn_buscar_' . $id . '" title="Buscar" alt="Buscar" data-toggle="tooltip" data-placement="top" class="btn btn-block btn-sm btn-info cboxElement"><i class="ace-icon fa fa-search bigger-130"></i> Buscar</button>';
        $retorno .= '<button type="button" id="btn_limpar_' . $id . '" name="btn_limpar_' . $id . '" title="Limpar" alt="Limpar" data-toggle="tooltip" data-placement="top" class="btn btn-block btn-sm btn-danger"><i class="ace-icon fa fa-ban bigger-130"></i> Apagar</button>';
        $retorno .= '</div>';
        $retorno .= '</div>'; //.relative
        $retorno .= '<script>';
        $paramColorbox = " reposition: true, scrolling: true, close: '&times;', maxWidth: '100%', maxHeight: '100%', title: function(){return 'Escolha uma das imagens clicando na imagem escolhida e em seguida no botão <i class=\"ace-icon fa fa-check green bigger-130\"></i> Escolher';}, ";
        $retorno .= 'jQuery(document).ready(function(){';

        if ($filtro != '') {
            if ($filtro2 != '') {
                $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
                $retorno .= '   var filtro = jQuery("#' . $filtro . '").val();';
                $retorno .= '   var filtro2 = jQuery("#' . $filtro2 . '").val();';
                $retorno .= '   jQuery(this).colorbox({';
                $retorno .= $paramColorbox;
                $retorno .= '       iframe:true,';
                $retorno .= '       width:"' . $template[0] . '",';
                $retorno .= '       height:"' . $template[1] . '",';
                $retorno .= '       href:"' . $path . '?campo=' . $id . '&' . $filtro . '="+filtro+"&' . $filtro2 . '="+filtro2,';
                $retorno .= '       onClosed: function () {';
                $retorno .= '           jQuery("#' . $id . '").trigger("change");';
                $retorno .= '       }';
                $retorno .= '   });';
                $retorno .= '});';
            } else {
                $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
                $retorno .= '   var filtro = jQuery("#' . $filtro . '").val();';
                $retorno .= '   jQuery(this).colorbox({';
                $retorno .= $paramColorbox;
                $retorno .= '       iframe:true,';
                $retorno .= '       width:"' . $template[0] . '",';
                $retorno .= '       height:"' . $template[1] . '",';
                $retorno .= '       href:"' . $path . '?campo=' . $id . '&' . $filtro . '="+filtro,';
                $retorno .= '       onClosed: function () {';
                $retorno .= '           jQuery("#' . $id . '").trigger("change");';
                $retorno .= '       }';
                $retorno .= '   });';
                $retorno .= '});';
            }
        } else {
            $retorno .= 'jQuery("#btn_buscar_' . $id . '").click(function(){';
            $retorno .= '   jQuery(this).colorbox({';
            $retorno .= $paramColorbox;
            $retorno .= '       iframe:true,';
            $retorno .= '       width:"' . $template[0] . '",';
            $retorno .= '       height:"' . $template[1] . '",';
            $retorno .= '       href:"' . $path . '?campo=' . $id . '",';
            $retorno .= '       onClosed: function () {';
            $retorno .= '           jQuery("#' . $id . '").trigger("change");';
            $retorno .= '       }';
            $retorno .= '   });';
            $retorno .= '});';
        }
        $retorno .= 'jQuery("#btn_limpar_' . $id . '").click(function(){';
        $retorno .= '   jQuery("#' . $id . '").val("");';
        $retorno .= '   jQuery("#img_' . $id . '").attr("src", "' . $img . '");';
        $retorno .= '});';

        $retorno .= '});';
        $retorno .= '</script>';
        $retorno .= '<style>';
//        $retorno .= '   .spanCampoLista .check-error {';
//        $retorno .= '       right: 71px !important;';
//        $retorno .= '   }';
//        $retorno .= '   #cboxTitle {';
//        $retorno .= '       padding-left: 10px !important;';
//        $retorno .= '   }';
        $retorno .= '</style>';
        return $retorno;
    }

    /**
     * Gerar HTML com um campo com a opção de auto completar
     *
     * @param String $id Ex: pro_int_codigo
     * @param String $titulo Ex: Produto
     * @param String $path Ex 'http://url.com/lista.php'
     * @param Array $paramCampo Ex: array("validate" => "required") Default: false
     * @param String $valueCodigo Ex: 1 Default: ''
     * @param String $valueValor Ex: 1 Default: ''
     * @param String $filtro Ex: 1 Default: ''
     * @param Array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string
     */
    function addAutoComplete($id, $titulo, $path, $paramCampo = false, $valueCodigo = '', $valueValor = '', $filtro = '', $paramTitulo = false) {
        $retorno = '';
        $retorno .= $this->addInput("hidden", $id, false, array("value" => $valueCodigo));
        $retorno .= $this->addLabel('txt_val_' . $id, $titulo, $paramTitulo);
        $retorno .= '<span class="relative spanCampoLista">';
        $retorno .= '<input type="text" id="txt_val_' . $id . '" name="txt_val_' . $id . '" value="' . $valueValor . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                if ($parametro == "class")
                    $retorno .= ' ' . $parametro . '="txtCampoLista ' . htmlspecialchars($value) . '"';
                else
                    $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
        }
        $retorno .= ' />';
        $retorno .= '<span id="validate_' . $id . '" class="campoValidado" style="display:none;"></span>';
        $retorno .= '</span>';
        $retorno .= '<script>';
        $retorno .= 'jQuery(document).ready(function(){';
        $retorno .= '   jQuery("#txt_val_' . $id . '").autocomplete({';
        if ($filtro != '') {
            $retorno .= 'source: "' . $path . '?' . $filtro . '="+jQuery("#' . $filtro . '").val(),';
        } else {
            $retorno .= 'source: "' . $path . '",';
        }
        $retorno .= '       minLength: 1,';
        $retorno .= '       select: function( event, ui ) {';
        $retorno .= '           jQuery("#' . $id . '").val(ui.item.id);';
        $retorno .= '           jQuery("#txt_val_' . $id . '").val(ui.item.label);';
        $retorno .= '           jQuery("#txt_val_' . $id . '").removeClass("ui-autocomplete-loading");';
        $retorno .= '           jQuery("#validate_' . $id . '").css("display", "inline-block");';
        $retorno .= '       },';
        $retorno .= '       search: function( event, ui ) {';
        $retorno .= '           jQuery("#' . $id . '").val("");';
        $retorno .= '           jQuery("#txt_val_' . $id . '").removeClass("ui-autocomplete-loading");';
        $retorno .= '           if (jQuery("#' . $id . '").val() == "")';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "none");';
        $retorno .= '           else';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "inline-block");';
        $retorno .= '       },';
        $retorno .= '       response: function( event, ui ) {';
        $retorno .= '           jQuery("#txt_val_' . $id . '").removeClass("ui-autocomplete-loading");';
        $retorno .= '           if (jQuery("#' . $id . '").val() == "")';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "none");';
        $retorno .= '           else';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "inline-block");';
        $retorno .= '       },';
        $retorno .= '       close: function( event, ui ) {';
        $retorno .= '           jQuery("#txt_val_' . $id . '").removeClass("ui-autocomplete-loading");';
        $retorno .= '           if (jQuery("#' . $id . '").val() == "")';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "none");';
        $retorno .= '           else';
        $retorno .= '               jQuery("#validate_' . $id . '").css("display", "inline-block");';
        $retorno .= '       }';
        $retorno .= '   });';
        $retorno .= '});';
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML com um combobox carregando os dados de uma requisição ajax
     *
     * @param String $id Ex: pro_int_codigo
     * @param String $titulo Ex: Produto
     * @param String $path Ex 'http://url.com/lista.php'
     * @param Array $paramCampo Ex: array("validate" => "required") Default: false
     * @param String $valueCodigo Ex: 1 Default: ''
     * @param String $change Ex: executarAlgo(); Default: ''
     * @param Array $paramTitulo Ex: 'class'=>'cls_titulo' default = false
     * @return string
     */
    function addSelectLoad($id, $titulo, $path, $paramCampo = false, $valueCodigo = '', $change = '', $paramTitulo = false) {
        $retorno = '';
        $retorno .= $this->addLabel($id, $titulo, $paramTitulo);
        $retorno .= '<span id="span_' . $id . '" class="relative spanCampoLista">';
        $retorno .= '<select id="' . $id . '" name="' . $id . '"';
        if ($paramCampo) {
            foreach ($paramCampo as $parametro => $value) {
                if ($parametro == "class")
                    $retorno .= ' ' . $parametro . '="cbbCampoLista ' . htmlspecialchars($value) . '"';
                else
                    $retorno .= ' ' . $parametro . '="' . htmlspecialchars($value) . '"';
            }
        }
        $retorno .= '><option value="-1">Selecione...</option></select>';
        $retorno .= '</span>';
        $retorno .= '<script>';
        $retorno .= 'jQuery(document).ready(function(){';
        $retorno .= '   loadCbb_' . $id . '();';
        $retorno .= '   jQuery("#' . $id . '").change(function(){ var id = jQuery(this).val(); ' . $change . ' });';
        $retorno .= '});';
        $retorno .= 'function loadCbb_' . $id . '(param){';
        $retorno .= '   jQuery.ajax({';
        $retorno .= '       type: "POST",';
        $retorno .= '       url: "' . $path . '",';
        $retorno .= '       data: jsonConcat({tipo:"cbb"}, param),';
        $retorno .= '       async: true,';
        $retorno .= '       beforeSend: function () { jQuery.gDisplay.loadStart("#span_' . $id . '"); },';
        $retorno .= '       error: function () { jQuery.gDisplay.loadStop("#span_' . $id . '"); },';
        $retorno .= '       success: function (resp) { jQuery("#' . $id . '").html(resp); jQuery("#' . $id . '").val("' . $valueCodigo . '"); jQuery.gDisplay.loadStop("#span_' . $id . '"); }';
        $retorno .= '   });';
        $retorno .= '}';
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML de radio
     *
     * @param string $idField
     * @param array $campos
     * @param string $checked
     * @param string $titulo
     * @param array $paramCampo
     * @param array $paramTitulo
     * @param boolean $vertical
     * @return string
     */
    function addRadioGroup($idField, $campos, $checked, $titulo = FALSE, $paramCampo = FALSE, $paramTitulo = FALSE, $vertical = FALSE, $classe = FALSE) {
        $retorno = '';
        if ($titulo)
            $retorno .= $this->addLabel($idField, $titulo, $paramTitulo);
        $class = ($vertical) ? '__radioGroupVertical' : '';
        $retorno .= '<span id="span_' . $idField . '" class="relative __radioGroup ' . $class . ' ' . $classe . ' input-height grey-bg" rel="' . $titulo . '">';
        if ($campos) {
            $count = 0;
            foreach ($campos as $id => $value) {
                $retorno .= '<div class="__radioGroupItem">';
                $retorno .= '<input type="radio" id="' . $idField . '_' . $id . '" name="' . $idField . '" class="radio radioGroup ' . $idField . ' ' . $classe . '"';
                $retorno .= ( $checked == $id) ? ' checked ' : '';
                $retorno .= ' value="' . $id . '" ';
                if ($paramCampo) {
                    foreach ($paramCampo as $p => $v) {
                        $retorno .= ' ' . $p . '="' . htmlspecialchars($v) . '"';
                    }
                }
                $retorno .= ' /><label class="__labelRadio ' . $classe . '" for="' . $idField . '_' . $id . '">' . $value . '</label>';
                if ($vertical)
                    $retorno .= '<br/>';
                $retorno .= '</div>';
            }
        }
        $retorno .= '</span>';
        return $retorno;
    }

    /**
     * Gerar HTML com upload automático de imagem
     * 
     * @param array $params
     * @return atring
     */
    function addImgUpload($params) {
        $html = '';
        $script = '';

        $img_upload = 'img_upload';
        $titulo = false;
        $paramTituloDefault = array("style" => "display:block;");
        $id = 'usu_var_foto';
        $arquivo = 'unknown.png';
        $urlUpload = 'upload.php';
        $classImg = '';
        $style = 'max-height: 200px;';
        $maxSize = MAX_SIZE;
        $paramTitulo = array();

        extract($params);
        $pk = $id . '_' . rand(1, 10);

        if (isset($requerido) && $requerido == true) {
            $required = ' validate="unknown" ';
        } else {
            $required = '';
        }

        if ($titulo) {
            $paramTitulo = array_unique(array_merge($paramTituloDefault, $paramTitulo));
            $html .= $this->addLabel($id, $titulo, $paramTitulo);
        }

        $html .= '<span class="profile-picture">' . "\n";
        $html .= '<img data-pk="' . $pk . '" src="' . $arquivo . '" class="img-responsive ' . $classImg . '" id="' . $img_upload . '" style="' . $style . '" />' . "\n";
        $html .= '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $arquivo . '" ' . $required . '/>' . "\n";
        $html .= '</span>' . "\n";

        $script .= '<script type="text/javascript">' . "\n";
        $script .= 'jQuery(function($) {' . "\n";
        $script .= '	$.fn.editable.defaults.mode = "inline";' . "\n";
        $script .= '	$.fn.editableform.loading = \'<div class="editableform-loading"><i class="ace-icon fa fa-spinner fa-spin fa-2x light-blue"></i></div>\';' . "\n";
        $script .= '	$.fn.editableform.buttons = \'<button type="submit" class="btn btn-info editable-submit"><i class="ace-icon fa fa-check"></i></button>\'+' . "\n";
        $script .= '                                \'<button type="button" class="btn editable-cancel"><i class="ace-icon fa fa-times"></i></button>\';' . "\n";

        $script .= '    try {' . "\n"; //ie8 throws some harmless exceptions, so let\'s catch\'em first let\'s add a fake appendChild method for Image element for browsers that have a problem with this because editable plugin calls appendChild, and it causes errors on IE
        $script .= '        try {' . "\n";
        $script .= '            document.createElement("IMG").appendChild(document.createElement("B"));' . "\n";
        $script .= '        } catch(e) {' . "\n";
        $script .= '            console.log(e);';
        $script .= '            Image.prototype.appendChild = function(el){}' . "\n";
        $script .= '        }' . "\n";
        $script .= '        var last_gritter;' . "\n";
        $script .= '        jQuery("#' . $img_upload . '").editable({' . "\n";
        $script .= '            type: "image",' . "\n";
        $script .= '		name: "' . $img_upload . '",' . "\n";
        $script .= '		value: null,' . "\n";
        $script .= '		image: { ' . "\n"; //specify ace file input plugin\'s options here
        $script .= '                btn_choose: "Enviar Imagem",' . "\n";
        $script .= '                droppable: true, ' . "\n";
        $script .= '                thumbnail: "large",' . "\n"; // small | large
        $script .= '                maxSize: ' . $maxSize . ',' . "\n";
        //and a few extra ones here
        $script .= '                name: "' . $img_upload . '",' . "\n"; //put the field name here as well, will be used inside the custom plugin
        $script .= '                on_error : function(error_type) { ' . "\n"; //on_error function will be called when the selected file has a problem
        $script .= '                    if(last_gritter) $.gritter.remove(last_gritter);' . "\n";
        $script .= '                    if(error_type == 1) { ' . "\n"; //file format error
        $script .= '                        last_gritter = $.gritter.add({' . "\n";
        $script .= '                            title: "Esse arquivo não é uma imagem!",' . "\n";
        $script .= '                            text: "Favor escolher entre as opções jpg gif png de imagem!",' . "\n";
        $script .= '				class_name: "gritter-error"' . "\n";
        $script .= '                        });' . "\n";
        $script .= '			} else if(error_type == 2) { ' . "\n"; //file size rror
        $script .= '                        last_gritter = $.gritter.add({' . "\n";
        $script .= '				title: "Arquivo muito grande!",' . "\n";
        $script .= '				text: "Imagem excede o tamanho máximo de ' . formatarBytes($maxSize) . '!",' . "\n";
        $script .= '                            class_name: "gritter-error"' . "\n";
        $script .= '                        });' . "\n";
        $script .= '			}' . "\n";
        $script .= '			else { alert(error_type); ' . "\n"; //other error
        $script .= '			}' . "\n";
        $script .= '                },' . "\n";
        $script .= '                on_success : function() {' . "\n";
        $script .= '                    $.gritter.removeAll();' . "\n";
        $script .= '                }' . "\n";
        $script .= '            },' . "\n";
        $script .= '            url: function(params) {' . "\n";
        $script .= '                var submit_url = "' . $urlUpload . '";' . "\n"; //please modify submit_url accordingly
        $script .= '                var deferred = null;' . "\n";
        $script .= '                var ' . $img_upload . ' = "#' . $img_upload . '";' . "\n";
        //if value is empty (""), it means no valid files were selected but it may still be submitted by x-editable plugin because "" (empty string) is different from previous non-empty value whatever it was so we return just here to prevent problems
        $script .= '                var value = $(' . $img_upload . ').next().find("input[type=hidden]:eq(0)").val();' . "\n";
        $script .= '                if(!value || value.length == 0) {' . "\n";
        $script .= '                    deferred = new $.Deferred' . "\n";
        $script .= '                    deferred.resolve();' . "\n";
        $script .= '                    return deferred.promise();' . "\n";
        $script .= '                }' . "\n";
        $script .= '                var $form = $(' . $img_upload . ').next().find(".editableform:eq(0)")' . "\n";
        $script .= '                var file_input = $form.find("input[type=file]:eq(0)");' . "\n";
        $script .= '                var pk = $(' . $img_upload . ').attr("data-pk"); ' . "\n"; //primary key to be sent to server
        $script .= '                var ie_timeout = null' . "\n";
        $script .= '                if( "FormData" in window ) {' . "\n";
        $script .= '                    var formData_object = new FormData(); ' . "\n"; //create empty FormData object
        //serialize our form (which excludes file inputs)
        $script .= '                    $.each($form.serializeArray(), function(i, item) {' . "\n";
        //add them one by one to our FormData
        $script .= '                        formData_object.append(item.name, item.value);' . "\n";
        $script .= '                    });' . "\n";
        //and then add files
        $script .= '                    $form.find("input[type=file]").each(function(){' . "\n";
        $script .= '                        var field_name = $(this).attr("name");' . "\n";
        $script .= '                        var files = $(this).data("ace_input_files");' . "\n";
        $script .= '                        if(files && files.length > 0) {' . "\n";
        $script .= '                            formData_object.append(field_name, files[0]);' . "\n";
        $script .= '                        }' . "\n";
        $script .= '                    });' . "\n";
        //append primary key to our formData
        $script .= '                    formData_object.append("pk", pk);' . "\n";

        $script .= '			jQuery.gDisplay.loadStart("HTML");' . "\n";

        $script .= '                    deferred = $.ajax({' . "\n";
        $script .= '                        url: submit_url,' . "\n";
        $script .= '                        type: \'POST\',' . "\n";
        $script .= '                        processData: false, ' . "\n"; //important
        $script .= '                        contentType: false, ' . "\n"; //important
        $script .= '                        dataType: \'json\', ' . "\n"; //server response type
        $script .= '                        data: formData_object' . "\n";
        $script .= '                    })' . "\n";
        $script .= '                }' . "\n";
        $script .= '                else {' . "\n";
        $script .= '                    deferred = new $.Deferred' . "\n";

        $script .= '			var temporary_iframe_id = \'temporary-iframe-\'+(new Date()).getTime()+\'-\'+(parseInt(Math.random()*1000));' . "\n";
        $script .= '			var temp_iframe = $(\'<iframe id="\'+temporary_iframe_id+\'" name="\'+temporary_iframe_id+\'" frameborder="0" width="0" height="0" src="about:blank" style="position:absolute; z-index:-1; visibility: hidden;"></iframe>\').insertAfter($form);' . "\n";

        $script .= '			$form.append(\'<input type="hidden" name="temporary-iframe-id" value="\'+temporary_iframe_id+\'" />\');' . "\n";

        //append primary key (pk) to our form
        $script .= '			$(\'<input type="hidden" name="pk" />\').val(pk).appendTo($form);' . "\n";

        $script .= '			temp_iframe.data("deferrer" , deferred);' . "\n";
        //we save the deferred object to the iframe and in our server side response we use "temporary-iframe-id" to access iframe and its deferred object
        $script .= '			$form.attr({' . "\n";
        $script .= '                        action: submit_url,' . "\n";
        $script .= '                        method: "POST",' . "\n";
        $script .= '                        enctype: "multipart/form-data",' . "\n";
        $script .= '                        target: temporary_iframe_id ' . "\n"; //important
        $script .= '			});' . "\n";

        $script .= '			jQuery.gDisplay.loadStart("HTML");' . "\n";
        $script .= '			$form.get(0).submit();' . "\n";

        //if we dont receive any response after 30 seconds, declare it as failed!
        $script .= '			ie_timeout = setTimeout(function(){' . "\n";
        $script .= '                        ie_timeout = null;' . "\n";
        $script .= '                        temp_iframe.attr("src", "about:blank").remove();' . "\n";
        $script .= '                        deferred.reject({"status":"fail", "message":"Tempo esgotado!"});' . "\n";
        $script .= '			} , 30000);' . "\n";
        $script .= '                }' . "\n";

        //deferred callbacks, triggered by both ajax and iframe solution
        $script .= '                deferred' . "\n";
        $script .= '                    .done(function(result) { ' . "\n"; //success
        $script .= '                        jQuery.gDisplay.loadStop("HTML");' . "\n";
        $script .= '                        if (result.length) { ' . "\n";
        $script .= '                            var res = result[0]; ' . "\n"; //the `result` is formatted by your server side response and is arbitrary
        $script .= '                            if (res.status == "success") {' . "\n";
        $script .= '                                $(' . $img_upload . ').get(0).src = res.url;' . "\n";
        $script .= '                                $("#' . $id . '").val(res.url);' . "\n";
        $script .= '                            } else {' . "\n";
        $script .= '                                alert(res.message);' . "\n";
        $script .= '                            }' . "\n";
        $script .= '                        } else {' . "\n";
        $script .= '                            alert("Imagem excede o tamanho máximo de ' . formatarBytes($maxSize) . '!");' . "\n";
        $script .= '                        }' . "\n";
        $script .= '			})' . "\n";
        $script .= '			.fail(function(result) { ' . "\n"; //failure
        $script .= '                        jQuery.gDisplay.loadStop("HTML");' . "\n";
        $script .= '                        alert("Ocorreu um erro");' . "\n";
        $script .= '			})' . "\n";
        $script .= '			.always(function() { ' . "\n"; //called on both success and failure
        $script .= '                        jQuery.gDisplay.loadStop("HTML");' . "\n";
        $script .= '                        if(ie_timeout) clearTimeout(ie_timeout)' . "\n";
        $script .= '                            ie_timeout = null;' . "\n";
        $script .= '                    });' . "\n";

        $script .= '                    return deferred.promise();' . "\n";
        // ***END OF UPDATE IMAGEM HERE*** //
        $script .= '                },' . "\n";

        $script .= '                success: function(response, newValue) {' . "\n";
        $script .= '		}' . "\n";
        $script .= '        });' . "\n";

        $script .= '	} catch(e) { console.log(e);}' . "\n";

        $script .= '});' . "\n";

        $script .= 'if(location.protocol == "file:") alert("Para fazer o upload para o servidor, você deve acessar esta página usando http protocal, ou seja, através de um servidor web.");' . "\n";
        $script .= '</script>' . "\n";

        return $html . $script;
    }

    function addFileUpload($params) {
        $id = 'arquivo';
        $valor = '';
        $titulo = false;
        $action = URL_SYS . 'upload.php';
        $sizeLimite = PESO_PDF;
        $local = URL_UPLOAD;
        $paramTituloDefault = array("style" => "display:block;");
        $onSuccess = '';

        extract($params);

        if (isset($requerido) && $requerido == true) {
            $requerido = ' validate="required" ';
            $paramTitulo["class"] = "required";
        } else {
            $requerido = '';
        }

        $retorno = '';

        $complete = "function(id, fileName, json){";
//        $complete .= "  jQuery.gDisplay.loadStop('HTML');";
        $complete .= "  var filenameUpload = json.filename;";
        $complete .= "  if (filenameUpload != undefined) {";
        $complete .= "      jQuery('#link_" . $id . "').attr('href', '" . $local . "'+filenameUpload);";
        $complete .= "      jQuery('#link_" . $id . "').show();";
        $complete .= "      jQuery('#link_excluir_" . $id . "').show();";
        $complete .= "      jQuery('#" . $id . "').val(filenameUpload);";
        $complete .= $onSuccess;
        $complete .= "  } else { ";
        $complete .= "      jQuery('#link_" . $id . "').hide();";
        $complete .= "      jQuery('#link_excluir_" . $id . "').hide();";
        $complete .= "      jQuery('#qq-" . $id . "').find('.qq-uploader').find('.qq-upload-list').empty();";
        $complete .= "  }";
        $complete .= "}";

        $submit = "function(id, fileName, json){";
//        $submit .= "    jQuery.gDisplay.loadStart('HTML');";
        $submit .= "    jQuery('#" . $id . "').val('');";
        $submit .= "    jQuery('#link_" . $id . "').hide();";
        $submit .= "    jQuery('#link_excluir_" . $id . "').hide();";
        $submit .= "    jQuery('#qq-" . $id . "').find('.qq-uploader').find('.qq-upload-list').empty();";
        $submit .= "}";

        $cancel = "function(id, fileName, json){";
//        $cancel .= "    jQuery.gDisplay.loadStop('HTML');";
        $cancel .= "    jQuery('#" . $id . "').val('');";
        $cancel .= "    jQuery('#link_" . $id . "').hide();";
        $cancel .= "    jQuery('#link_excluir_" . $id . "').hide();";
        $cancel .= "    jQuery('#qq-" . $id . "').find('.qq-uploader').find('.qq-upload-list').empty();";
        $cancel .= "}";

        $param = array("action" => "'" . $action . "'",
            "multiple" => "false",
            "sizeLimit" => "'" . $sizeLimite . "'",
            "onComplete" => $complete,
            "onSubmit" => $submit,
            "onCancel" => $cancel);

        if ($titulo) {
            $paramTitulo = (isset($paramTitulo)) ? array_unique(array_merge($paramTituloDefault, $paramTitulo)) : $paramTituloDefault;
            $retorno .= $this->addLabel($id, $titulo, $paramTitulo);
        }
        $retorno .= '<div class="__clear"></div>';
        $retorno .= '<div class="__block">';
        $retorno .= '<div class="col-xs-9 no-padding-left">';
        $retorno .= '   <div id="qq-' . $id . '"></div>';
        $retorno .= '</div>';
        $retorno .= '<div class="col-xs-3 no-padding">';
        $retorno .= '   <input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $valor . '" ' . $requerido . '/>';
        $display = (seNuloOuVazio($valor)) ? ' style="margin:7px 0;display:none;" ' : ' style="margin:7px 0" ';
        $linkDow = (seNuloOuVazio($valor)) ? '#' : $local . $valor;
        $retorno .= '   <a href="' . $linkDow . '" ' . $display . ' id="link_' . $id . '" target="_blanc" class="btn btn-success btn-sm qq-link-download"><i class="ace-icon fa fa-download no-margin"></i></a>';
        $retorno .= '   <a href="#" ' . $display . ' id="link_excluir_' . $id . '" class="btn btn-danger btn-sm qq-link-excluir"><i class="ace-icon fa fa-times no-margin"></i></a>';
        $retorno .= '</div>';
        $retorno .= '<div class="__clear"></div>';
        $retorno .= '</div>';
        $retorno .= '<script>';
        $retorno .= 'jQuery("#qq-' . $id . '").gFileUploader({';
        foreach ($param as $key => $value) {
            $retorno .= $key . ' : ' . $value . ',';
        }
        $retorno = substr($retorno, 0, -1);
        $retorno .= '});';
        $retorno .= 'jQuery("#link_excluir_' . $id . '").click(function(){ ';
        $retorno .= "    jQuery('#" . $id . "').val('');";
        $retorno .= "    jQuery('#link_" . $id . "').hide();";
        $retorno .= "    jQuery('#link_excluir_" . $id . "').hide();";
        $retorno .= "    jQuery('#qq-" . $id . "').find('.qq-uploader').find('.qq-upload-list').empty();";
        $retorno .= '});';
        if (!seNuloOuVazio($valor)) {
            $retorno .= "jQuery('#qq-" . $id . "').find('.qq-uploader').find('.qq-upload-list').empty().html('<li><span class=\"qq-upload-file\">" . $valor . "</span></li>');";
        }
        $retorno .= '</script>';
        return $retorno;
    }

    /**
     * Gerar HTML com estrelas de votação
     * 
     * @param array $params
     * @return atring
     */
    function addStar($params) {
        $html = '';
        $script = '';

        $id = 'star';
        $size = 24;
        $icon = false;
        $score = 0;
        extract($params);

        $html .= '<div class="star" id="' . $id . '"></div>' . "\n";

        $script .= '<script type="text/javascript">' . "\n";
        $script .= '$(".star").raty({';
        $script .= '    score: ' . $score . ',';
        $script .= '    cancel: true,';
        $script .= '    hints: ["Péssimo", "Ruim", "Regular", "Bom", "Ótimo"],';
        if ($icon) {
            $script .= '    cancelOff: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/cancel-off.png",';
            $script .= '    cancelOn: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/cancel.png",';
            $script .= '    iconRange: [';
            $script .= '        {range: 1, on: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_1.png", off: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_1-off.png"},';
            $script .= '        {range: 2, on: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_2.png", off: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_2-off.png"},';
            $script .= '        {range: 3, on: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_3.png", off: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_3-off.png"},';
            $script .= '        {range: 4, on: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_4.png", off: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_4-off.png"},';
            $script .= '        {range: 5, on: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_5.png", off: "' . URL_SYS_TEMA . 'images/raty/' . $size . '/raty_5-off.png"}';
            $script .= '    ]';
        } else {
            $script .= '    starType : "i"';
        }
        $script .= '});';
        $script .= '</script>' . "\n";

        return $html . $script;
    }

    /**
     * Insere na página um elemento para upload de arquivos com clicar e arrastar
     * 
     * Documentação https://www.dropzonejs.com/#config-maxFilesize
     * @param array $params
     * @return string
     */
    function addDropzone($params) {
        $html = '';
        $script = '';

        $id = 'arquivo';
        $hidden = 'hdd_arquivo';
        $class = 'dropzone well';
        $url = 'upload.php';
        $value = '';
        $maxFiles = 1; // quantidade máxima
        $maxFilesize = 2; // Mb
        $acceptedFiles = 'application/pdf,.doc,.docx';
        $addRemoveLinks = true;
        $dictDefaultMessage = '<span class="bigger-150 bolder"><i class="ace-icon fa fa-caret-right red"></i> Arraste para aqui seu  arquivo</span><br/><span class="smaller-80 grey">(ou clique aqui)</span><br /><i class="upload-icon ace-icon fa fa-cloud-upload blue fa-3x"></i>';
        $dictFileTooBig = 'Esse arquivo é muito grande, tamanho máximo: ' . $maxFilesize . 'Mb.';
        $dictRemoveFile = 'Excluir Arquivo';

        extract($params);

        $param = array(
            "url" => "'" . $url . "'",
            "maxFiles" => "'" . $maxFiles . "'",
            "maxFilesize" => "'" . $maxFilesize . "'",
            "acceptedFiles" => "'" . $acceptedFiles . "'",
            "addRemoveLinks" => "'" . $addRemoveLinks . "'",
            "dictDefaultMessage" => "'" . $dictDefaultMessage . "'",
            "dictRemoveFile" => "'" . $dictRemoveFile . "'",
            "dictFileTooBig" => "'" . $dictFileTooBig . "'"
        );

        $html .= '<div class="' . $class . ' text-center" id="' . $id . '">';
        $html .= '</div>';

        $script .= '<script type="text/javascript">';
        $script .= 'Dropzone.autoDiscover = false;';
        $script .= 'jQuery("#' . $id . '").dropzone({';
        foreach ($param as $key => $val) {
            $script .= $key . ' : ' . $val . ',';
        }
        if ($value != '') {
            $script .= ' init: function() {';
            $script .= '    myDropzone = this;';
            $script .= '    myDropzone.options.addedfile.call(myDropzone, {name: "" });';
            $script .= ' },';
        }
        $script .= ' success: function(file, response){ ';
        $script .= '    var json = JSON.parse(response.substring(1, (response.length-1)));';
        $script .= '    if (json.status == "success") { ';
        $script .= '        jQuery("#' . $hidden . '").val(json.url);';
        $script .= '    } else {';
        $script .= '        jQuery.gDisplay.showError(json.message, "");';
        $script .= '    }';
        $script .= ' }';
        $script .= '});';
        $script .= '</script>';

        return $html . $script;
    }
}

?>