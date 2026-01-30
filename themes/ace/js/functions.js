var URL_SYS_TEMA = URL_SYS + 'themes/ace/';
var paramCookie = {expires: 30, path: '/'};
var TEMA = 'ACE';

function getVisualizacaoCookie(modulo, sevazio) {
    var retorno = jQuery.cookie(modulo);
    if (retorno == '' || retorno == undefined) {
        retorno = sevazio;
    }
    return retorno;
}
function setVisualizacaoCookie(modulo, opcao) {
    return jQuery.cookie(modulo, opcao, paramCookie);
}

function getParametroCookie(parametro, sevazio) {
    var retorno = jQuery.cookie(parametro);
    if (retorno == '' || retorno == undefined) {
        retorno = sevazio;
    }
    return retorno;
}
function setParametroCookie(parametro, valor) {
    return jQuery.cookie(parametro, valor);
}
function setParametroCookieGeral(parametro, valor) {
    return jQuery.cookie(parametro, valor, paramCookie);
}
function setParametroCookieHoje(parametro, valor) {
    return jQuery.cookie(parametro, valor, {expires: 1, path: '/'});
}

//tooltip placement on right or left
function tooltip_placement(context, source) {
    var $source = $(source);
    var $parent = $source.closest('table');
    var off1 = $parent.offset();
    var w1 = $parent.width();

    var off2 = $source.offset();
    //var w2 = $source.width();

    if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2))
        return 'right';
    return 'left';
}
function formatarNota(nota) {
    var retorno = nota;
//    if (parseInt(nota) > 0 && parseInt(nota) < 100) {
//        retorno = (parseInt(nota) / 10).toString();
//        retorno = retorno.replace(".", ",");
//    } else if (parseInt(nota) == 100) {
//        retorno = "10";
//    }
    return retorno;
}
function recolherMenu() {
    jQuery("#sidebar").addClass("menu-min");
}
function espandirMenu() {
    jQuery("#sidebar").removeClass("menu-min");
}
function jsonConcat(o1, o2) {
    for (var key in o2) {
        o1[key] = o2[key];
    }
    return o1;
}

function isIframe() {
    if (window.location != window.parent.location) {
        jQuery('head').append('<link rel="stylesheet" type="text/css" href="' + URL_SYS_TEMA + 'css/iframe.css">');
        return true;
    } else {
        return false;
    }
}

function redirecionar(url, noback, parente, confirma) {
    if (parente == true) {
        parent.jQuery.gDisplay.loadStart('HTML');
    } else {
        jQuery.gDisplay.loadStart('HTML');
    }
    if (noback === false || noback === undefined) {
        if (parente === false || parente === undefined) {
            if (confirma !== undefined) {
                jQuery.gDisplay.loadStop('HTML');
                jQuery.gDisplay.showYN(confirma, "jQuery.gDisplay.loadStart('HTML');window.location.assign('" + url + "');");
            } else {
                window.location.assign(url);
            }
        } else {
            if (confirma !== undefined) {
                jQuery.gDisplay.loadStop('HTML');
                jQuery.gDisplay.showYN(confirma, "parent.jjQuery.gDisplay.loadStart('HTML');parent.window.location.assign('" + url + "');");
            } else {
                parent.window.location.assign(url);
            }
        }
    } else {
        if (parente === false || parente === undefined) {
            if (confirma !== undefined) {
                jQuery.gDisplay.loadStop('HTML');
                jQuery.gDisplay.showYN(confirma, "jQuery.gDisplay.loadStart('HTML');window.location.replace('" + url + "');");
            } else {
                window.location.replace(url);
            }
        } else {
            if (confirma !== undefined) {
                jQuery.gDisplay.loadStop('HTML');
                jQuery.gDisplay.showYN(confirma, "parent.jQuery.gDisplay.loadStart('HTML');parent.window.location.replace('" + url + "');");
            } else {
                parent.window.location.replace(url);
            }
        }
    }
}

function verListaSucessosErros() {
    jQuery(".listaSucessosErros").toggle("fast");
}

function formataInformacao(imovel) {
    var retorno = '';
    retorno += '<div class="imovel">';
    retorno += '    <div class="icon">';
    retorno += '        <a href="' + imovel.link_casa + '" target="_blank" alt="' + imovel.situacao_format + '" title="' + imovel.situacao_format + '"><i aria-hidden="true" class="fa fa-icon fa-home situacao_' + imovel.situacao + '"></i></a>';
    retorno += '        <span class="situacao_' + imovel.situacao + '"><a href="' + imovel.link_casa + '" target="_blank" alt="' + imovel.situacao_format + '" title="' + imovel.situacao_format + '">' + imovel.situacao_format + '</a></span>';
    retorno += '    </div>';
    retorno += '    <div class="detalhes">';
    retorno += '        <div class="descricao"><a href="' + imovel.link_beneficiario + '" target="_blank">' + imovel.descricao + '</a></div>';
    retorno += '        <div class="endereco">' + imovel.endereco + '</div>';
    retorno += '        <div class="icones">';
    retorno += '            <div>';
    retorno += '                <a href="' + imovel.link_casa + '" target="_blank" alt="' + imovel.situacao_format + '" title="' + imovel.situacao_format + '">';
    retorno += '                    <span>Lote:</span>';
    retorno += '                    <span class="valor">' + imovel.lote + '</span>';
    retorno += '                </a>';
    retorno += '            </div>';
    retorno += '            <div>';
    retorno += '                <a href="' + imovel.link_casa + '" target="_blank" alt="' + imovel.situacao_format + '" title="' + imovel.situacao_format + '">';
    retorno += '                    <span>Quadra:</span>';
    retorno += '                    <span class="valor">' + imovel.quadra + '</span>';
    retorno += '                </a>';
    retorno += '            </div>';
    retorno += '            <div>';
    retorno += '                <a href="' + imovel.link_casa + '" target="_blank" alt="' + imovel.situacao_format + '" title="' + imovel.situacao_format + '">';
    retorno += '                    <span>Número:</span>';
    retorno += '                    <span class="valor">' + imovel.numero + '</span>';
    retorno += '                </a>';
    retorno += '            </div>';
    retorno += '       </div>';
    retorno += '    </div>';
    retorno += '</div>';
    return retorno;
}
