<?php

class GHeader extends GHeaderParent {

    private $menu;
    private $titulo;
    private $descricao;

    /**
     * Carrega todo o cabeçalho da página com o título passado e se caso for administrativo limpa a sessão da empresa
     *
     * @param String $tilulo
     */
    function __construct($tilulo, $theme = true) {
        parent::__construct(SYS_NOME . ' - ' . $tilulo, $theme);
    }

    /**
     * Adicionar uma String para título da página
     *
     * @param String $titulo
     * @param String $descricao
     */
    function addMenu($menu, $titulo, $descricao) {
        $this->menu = $menu;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
    }

    /**
     *
     * @param boolean $isIframe false
     * @param Breadcrumb $breadcrumb null
     */
    function show($isIframe = false, $breadcrumb = null, $menuCollapsed = false, $classMainContainer = '') {
        if (!$isIframe) {
            parent::addBodyClass("no-skin");
        }
        parent::show();
        $show = '';
        if (!$isIframe) {
            $__usuario = getUsuarioSessao();
            $show .= '<div id="navbar" class="navbar navbar-default navbar-collapse h-navbar ace-save-state navbar-fixed-top">';
            $show .= '    <div class="navbar-container ace-save-state" id="navbar-container">';
            $show .= '        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">';
            $show .= '            <span class="sr-only">Toggle sidebar</span>';
            $show .= '            <span class="icon-bar"></span>';
            $show .= '            <span class="icon-bar"></span>';
            $show .= '            <span class="icon-bar"></span>';
            $show .= '        </button>';
            $show .= '        <div class="navbar-header pull-left">';
            $show .= '            <a href="' . URL_SYS . '" class="navbar-brand" style="padding: 2px;" alt="vai para o ' . SYS_NOME . '" title="vai para o ' . SYS_NOME . '"><img src="' . URL_SYS_LOGO_BRANCA . '" height="36"/></a>';
            $show .= '        </div>';
            $show .= '        <div class="navbar-buttons navbar-header pull-right" role="navigation">';
            $show .= '            <ul class="nav ace-nav">';
            if ($__usuario) {
                include_once(ROOT_SYS . "pendencias.php");
                $pendencias = 0;
                $liPendencias = '';
                if ($arrPendencias && count($arrPendencias)) {
                    foreach ($arrPendencias as $pendencia) {
                        $pendencias++;
                        $liPendencias .= '<li>';
                        if (!isset($pendencia["frame"]) || seNuloOuVazio($pendencia["frame"])) {
                            $liPendencias .= '   <a data-toggle="tooltip" data-html="true" data-placement="bottom" title="' . $pendencia["motivo"] . '" href="' . $pendencia["link"] . '" class="clearfix">';
                        } else {
                            $liPendencias .= '   <a data-toggle="tooltip" data-html="true" data-placement="bottom" title="' . $pendencia["motivo"] . '" href="' . URL_SYS . 'minhaconta/minhaspendencias/" class="clearfix">';
                        }
                        $liPendencias .= '       <span class="pull-left itemPendencia"><i class="fa fa-' . $pendencia["icone"] . '"></i> ' . $pendencia["titulo"] . '</span>';
                        $liPendencias .= '       <span class="pull-right badge badge-info">' . $pendencia["qtd"] . '</span>';
                        $liPendencias .= '   </a>';
                        $liPendencias .= '</li>';
                    }
                }
                if ($pendencias > 0) {
                    $show .= '          <li class="purple dropdown-modal">';
                    $show .= '              <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-bell icon-animated-bell"></i><span class="badge badge-important">' . $pendencias . '</span></a>';
                    $show .= '              <ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">';
                    $show .= '                  <li class="dropdown-header"><i class="ace-icon fa fa-exclamation-triangle"></i>' . $pendencias . ' Notificação(ões)</li>';
                    $show .= '                  <li class="dropdown-content ace-scroll" style="position: relative; overflow: visible;"><div class="scroll-track" style="display: none;"><div class="scroll-bar"></div></div>';
                    $show .= '                      <div class="scroll-content" style="overflow: visible;">';
                    $show .= '                          <ul class="dropdown-menu dropdown-navbar">';
                    $show .= $liPendencias;
                    $show .= '                          </ul>';
                    $show .= '                      </div>';
                    $show .= '                  </li>';
                    $show .= '                  <li class="dropdown-footer"><a href="' . URL_SYS . 'minhaconta/minhaspendencias/">Ver todas pendências<i class="ace-icon fa fa-arrow-right"></i></a></li>';
                    $show .= '              </ul>';
                    $show .= '          </li>';
                }

                GF::import(array("mensagem", "destinatario"));
                $naoLidas = 0;
                $liMensagens = '';
                $mensagemDao = new MensagemDao();
                $destinatarioDao = new DestinatarioDao();

                $destinatario = new Destinatario();
                $destinatario->setDes_int_destinatario($__usuario->getUsu_int_codigo());
                $arrDestinatarios = $destinatarioDao->selectByDestinatario($destinatario);
                if (count($arrDestinatarios)) {
                    /* @var $destinatario Destinatario */
                    foreach ($arrDestinatarios as $destinatario) {
                        if ($destinatario->getDes_cha_status() == 0) {
                            $naoLidas++;
                            $liMensagens .= '<li>';
                            $liMensagens .= '   <a href="' . URL_SYS . 'minhaconta/minhasmensagens/recebidas/view/' . $destinatario->getMensagem()->getMen_int_codigo() . '/" class="clearfix">';
                            $liMensagens .= '       <img src="' . $destinatario->getMensagem()->getRemetente()->getUsu_var_foto() . '" class="msg-photo" alt="' . $destinatario->getMensagem()->getRemetente()->getUsu_var_nome() . '" />';
                            $liMensagens .= '       <span class="msg-body"><span class="msg-title"><span class="blue">' . $destinatario->getMensagem()->getRemetente()->getUsu_var_nome() . ':</span> ' . $destinatario->getMensagem()->getMen_var_titulo() . '</span>';
                            $liMensagens .= '       <span class="msg-time"><i class="ace-icon fa fa-clock-o"></i><span> ' . formatarTempo($destinatario->getMensagem()->getTempo()) . '</span></span></span>';
                            $liMensagens .= '   </a>';
                            $liMensagens .= '</li>';
                        }
                    }
                }
                if ($naoLidas > 0) {
                    $show .= '          <li class="green dropdown-modal">';
                    $show .= '              <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-envelope icon-animated-vertical"></i><span class="badge badge-success">' . $naoLidas . '</span></a>';
                    $show .= '              <ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">';
                    $show .= '                  <li class="dropdown-header"><i class="ace-icon fa fa-envelope-o"></i>' . $naoLidas . ' Mensagem(s)</li>';
                    $show .= '                  <li class="dropdown-content ace-scroll" style="position: relative;"><div class="scroll-track" style="display: none;"><div class="scroll-bar"></div></div>';
                    $show .= '                      <div class="scroll-content">';
                    $show .= '                          <ul class="dropdown-menu dropdown-navbar">';
                    $show .= $liMensagens;
                    $show .= '                          </ul>';
                    $show .= '                      </div>';
                    $show .= '                  </li>';
                    $show .= '                  <li class="dropdown-footer"><a href="' . URL_SYS . 'minhaconta/minhasmensagens/">Ver todas mensagens<i class="ace-icon fa fa-arrow-right"></i></a></li>';
                    $show .= '              </ul>';
                    $show .= '          </li>';
                    $show .= '<script>';
                    $show .= '$(".ace-scroll").ace_scroll();';
                    $show .= '</script>';
                }
            }
            $show .= '                <li class="light-blue dropdown-modal">';
            if ($__usuario) {
                $tag = '';
                $show .= '                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">';
                $show .= '                        <img class="nav-user-photo" src="' . $__usuario->getUsu_var_foto() . '" />';
                $show .= '                        <span class="user-info">';
                $show .= '                          <small>' . $__usuario->getPerfil()->getPef_var_descricao() . '</small>';
                $show .= '                          ' . $__usuario->getUsu_var_nome() . '';
                $show .= $tag;
                $show .= '                        </span>';
                $show .= '                        <i class="ace-icon fa fa-caret-down"></i>';
                $show .= '                    </a>';
                $show .= '                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-blue dropdown-caret dropdown-close">';
                if (GSecurity::verificarPermissao("ESCOLHERPERFIL", FALSE)) {
                    $urlEscolha = (strlen($_SERVER["REQUEST_URI"]) > 1) ? '?url=' . $_SERVER["REQUEST_URI"] : '';
                    $show .= '<li><a href="' . URL_SYS . 'minhaconta/escolherperfil/' . $urlEscolha . '"><i class="ace-icon fa fa-industry"></i>Escolher Perfil</a></li>';
                }
                if (GSecurity::verificarPermissao("ALTERARSENHA", FALSE)) {
                    $show .= '<li><a href="' . URL_SYS . 'minhaconta/alterarsenha/"><i class="ace-icon fa fa-key"></i>Alterar Senha</a></li>';
                }
                if (GSecurity::verificarPermissao("MEUSDADOS", FALSE)) {
                    $show .= '<li><a href="' . URL_SYS . 'minhaconta/meusdados/"><i class="ace-icon fa fa-user"></i>Meus Dados</a></li>';
                }
                if (GSecurity::verificarPermissao("MINHASPENDENCIAS", FALSE)) {
                    $show .= '<li><a href="' . URL_SYS . 'minhaconta/minhaspendencias/"><i class="ace-icon fa fa-bell"></i>Minhas Pendências</a></li>';
                }
                $show .= '                        <li class="divider"></li>';
                $show .= '                        <li><a href="' . URL_SYS . 'sair/?url=home"><i class="ace-icon fa fa-power-off"></i>Sair</a></li>';
                $show .= '                    </ul>';
            } else {
                $show .= '                    <a href="' . URL_SYS . 'admin/">Entrar</a>';
            }
            $show .= '                </li>';
            $show .= '            </ul>';
            $show .= '       </div>';
            $show .= '	</div><!-- /.navbar-container -->';
            $show .= '</div>';
            $show .= '<div class="main-container ace-save-state ' . $classMainContainer . '" id="main-container">';
            $show .= $this->loadMenu($menuCollapsed);
            $show .= '  <div class="main-content">';
            $show .= '      <div class="main-content-inner">';
            $show .= '          <div class="breadcrumbs ace-save-state" id="breadcrumbs">';
            if (!empty($breadcrumb->crumbs)) {
                $show .= $breadcrumb->retornar();
            }
            $show .= '              <div class="nav-search" id="nav-search">';
            $show .= '                  <form class="form-search" action="' . URL_SYS . 'search/" method="get">';
            $show .= '                      <span class="input-icon">';
            $show .= '                          <input id="f" name="f" type="text" placeholder="Procurar..." class="nav-search-input" autocomplete="off" />';
            $show .= '                          <i class="ace-icon fa fa-search nav-search-icon"></i>';
            $show .= '                      </span>';
            $show .= '                  </form>';
            $show .= '             </div><!-- /.nav-search -->';
            $show .= '      </div>';
            $show .= '      <div class="page-header">';
            $show .= '          <h1>' . $this->titulo . '<small><i class="ace-icon fa fa-angle-double-right"></i> ' . $this->descricao . '</small></h1>';
            $show .= '      </div>';
            $show .= '      <div class="page-content">';
            $show .= '          <div class="row">';
        } else {
            $show .= '<div class="__corpoFrame">';
        }
        echo $show;
    }

    function loadMenu($menuCollapsed) {
        global $__arrMenu, $__arrMenuIcons, $__arrSubMenu, $__arrMinhaConta, $__arrAdministracao, $__arrSeguranca, $__arrCadastro, $__arrGerenciamento, $__arrGraficos, $__arrTabelas, $__arrConfiguracoes, $__arrRelatorio, $__arrMonitoramento;

        $arrMenu = array();
        if (GSecurity::verificarPermissao("MINHACONTA", FALSE)) {
            $arrMenu["MINHACONTA"] = $__arrMinhaConta;
        }
        if (GSecurity::verificarPermissao("ADMINISTRACAO", FALSE)) {
            $arrMenu["ADMINISTRACAO"] = $__arrAdministracao;
        }
        if (GSecurity::verificarPermissao("CADASTROS", FALSE)) {
            $arrMenu["CADASTROS"] = $__arrCadastro;
        }
        if (GSecurity::verificarPermissao("GERENCIAMENTO", FALSE)) {
            $arrMenu["GERENCIAMENTO"] = $__arrGerenciamento;
        }
        if (GSecurity::verificarPermissao("GRAFICOS", FALSE)) {
            $arrMenu["GRAFICOS"] = $__arrGraficos;
        }
        if (GSecurity::verificarPermissao("TABELAS", FALSE)) {
            $arrMenu["TABELAS"] = $__arrTabelas;
        }
        if (GSecurity::verificarPermissao("MONITORAMENTO", FALSE)) {
            $arrMenu["MONITORAMENTO"] = $__arrMonitoramento;
        }
        if (GSecurity::verificarPermissao("CONFIGURACOES", FALSE)) {
            $arrMenu["CONFIGURACOES"] = $__arrConfiguracoes;
        }
        if (GSecurity::verificarPermissao("SEGURANCA", FALSE)) {
            $arrMenu["SEGURANCA"] = $__arrSeguranca;
        }
        if (GSecurity::verificarPermissao("RELATORIOS", FALSE)) {
            $arrMenu["RELATORIOS"] = $__arrRelatorio;
        }

        $html = '';
        $html .= '<script type="text/javascript">';
        $html .= '  try{ace.settings.loadState("main-container")}catch(e){}';
        $html .= '</script>';
        $html .= '<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse ace-save-state sidebar-fixed">';
        $html .= '  <script type="text/javascript">try{ace.settings.loadState("sidebar")}catch(e){}</script>';
        $html .= '  <ul class="nav nav-list">';
        $html .= '      <li class="' . (($this->menu == 'HOME') ? 'active' : '') . '"><a href="' . URL_SYS . 'home/"><i class="menu-icon fa fa-home"></i><span class="menu-text"> Início </span></a><b class="arrow"></b></li>';
        //<editor-fold desc="Montar html Menu">
        $activeSub = array();
        foreach ($arrMenu as $indice => $menu) {
            $active = '';
            if (is_array($menu)) {
                foreach ($menu as $key => $value) {
                    if ($this->menu == $key) {
                        $active = 'active open hover';
                        break;
                    }
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if ($this->menu == $key . $k) {
                                $active = 'active open hover';
                                $activeSub[] = $key;
                                break;
                            }
                        }
                    }
                }
            }
            $html .= '  <li class="hover ' . $active . '"><a href="#" class="dropdown-toggle"><i class="menu-icon fa ' . $__arrMenuIcons[$indice] . '"></i><span class="menu-text"> ' . $__arrMenu[$indice] . ' </span><b class="arrow fa fa-angle-down"></b></a><b class="arrow"></b>';
            $html .= '      <ul class="submenu can-scroll">';
            if (is_array($menu)) {
                foreach ($menu as $key => $value) {
                    if (GSecurity::verificarPermissao($key, FALSE)) {
                        if (is_array($value)) {
                            $html .= '<li class="hover ' . (in_array($key, $activeSub) ? 'open' : '') . ' ' . (($this->menu == $key) ? 'active' : '') . '"><a href="#" class="dropdown-toggle"><i class="menu-icon fa fa-caret-right"></i>' . $__arrSubMenu[$key] . '<b class="arrow fa fa-angle-down"></b></a><b class="arrow"></b>';
                            $html .= '  <ul class="submenu can-scroll">';
                            foreach ($value as $k => $v) {
                                if (GSecurity::verificarPermissao($key . $k, FALSE)) {
                                    $html .= '<li class="' . (($this->menu == $key . $k) ? 'active' : '') . '"><a href="' . URL_SYS . strtolower($indice) . '/' . strtolower($key) . '/' . strtolower($k) . '/"><i class="menu-icon fa fa-caret-right"></i>' . $v . '</a><b class="arrow"></b></li>';
                                }
                            }
                            $html .= '  </ul>';
                            $html .= '</li>';
                        } else
                            $html .= '<li class="hover ' . (($this->menu == $key) ? 'active' : '') . '"><a href="' . URL_SYS . strtolower($indice) . '/' . strtolower($key) . '/"><i class="menu-icon fa fa-caret-right"></i>' . $value . '</a><b class="arrow"></b></li>';
                    } else if (ehLuiz() && getPerfilSessao() == PERFIL_ADMINISTRADOR) {
                        echo carregarMensagem("E", 'Menu não encontrado: ' . $key, 12);
                    }
                }
            }
            $html .= '      </ul>';
            $html .= '  </li>';
        }
        //</editor-fold>
        $html .= '  </ul><!-- /.nav-list -->';
        $html .= '</div>';
        return $html;
    }
}

?>
