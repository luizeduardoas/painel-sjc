function removeSustenido(url) {
    return url.replace(/^.*#/, '');
}


function trim(str) {
    if (str != undefined) {
        str.replace(/^\s+|\s+$/g, "");
    }
    return str;
}

function ltrim(str) {
    return str.replace(/^\s+/, "");
}

function rtrim(str) {
    return str.replace(/\s+$/, "");
}

function formatMoney(value) {
    var ret = '';
    ret = value.replace('R$ ', '');
    ret = ret.replace(',', '.');
    return ret;
}

function closeColorbox() {
    parent.jQuery.fn.colorbox.close();
}

function Money(e) {
    var tecla = (window.event) ? event.keyCode : e.which;

    if ((tecla > 47 && tecla < 58))
        return true;
    else {
        if (tecla == 46 || tecla == 13 || tecla == 0)
            return true;
        if (tecla != 8)
            return false;
        else
            return true;
    }
}

function roundNumber(num, dec) {
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    return result;
}

function roundNumberFormat(num, dec) {
    var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
    result = result.toString().replace('.', ',');
    return result;
}

function quantidadeCaracteres(e, elemento, quant) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if (tecla == 0)
        return true;
    if (tecla == 8)
        return true;

    var valor = jQuery(elemento).val();
    var total = valor.length;
    if (total < quant)
        return true;
    else {
        jQuery(elemento).val(valor.substr(0, quant));
        jQuery.gDisplay.showError("Quantidade de caracteres máximo (" + quant + ") atingido", "jQuery('#" + jQuery(elemento).attr("id") + "').focus();");
        return false;
    }
}

function criarPermalink(str) {
    str = retiraAcentos(str);
    return str.replace(/[^a-z0-9]+/gi, '-').replace(/^-*|-*$/g, '').toLowerCase();
}

function retiraAcentos(Campo) {
    var Acentos = "áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇabcdefghijklmnopqrstuvxwyz";
    var Traducao = "AAAAAAAAAEEEEIIOOOOOOUUUUCCABCDEFGHIJKLMNOPQRSTUVXWYZ";
    var Posic, Carac;
    var TempLog = "";
    for (var i = 0; i < Campo.length; i++)
    {
        Carac = Campo.charAt(i);
        Posic = Acentos.indexOf(Carac);
        if (Posic > -1)
            TempLog += Traducao.charAt(Posic);
        else
            TempLog += Campo.charAt(i);
    }
    return (TempLog);
}

function clearForm(ele) {
    jQuery(ele).find(':input').each(function () {
        switch (this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'textarea':
                jQuery(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });
}

function resetForm(id) {
    $('#' + id).each(function () {
        this.reset();
    });
}

function __paginateLoad(id, post, target, tipo, count, display, start, rp, page, query, order, sort, qtd, paramExtra) {
    if (page > count)
        page = 1;
    jQuery.gAjax.load(post, jsonConcat({
        tipo: tipo,
        count: count,
        start: start,
        rp: rp,
        page: page,
        query: query,
        order: order,
        sort: sort,
        qtd: qtd
    }, paramExtra), target);
    jQuery("#" + id).paginate({
        count: count,
        start: page,
        display: display,
        onChange: function (page) {
            jQuery.gAjax.load(post, jsonConcat({
                tipo: tipo,
                count: count,
                start: start,
                rp: rp,
                page: page,
                query: query,
                order: order,
                sort: sort,
                qtd: qtd
            }, paramExtra), target);
        }
    });
}

function pressEnter(obj, acao) {
    if (jQuery.support.mozilla) {
        jQuery(obj).keypress(function (e) {
            if (e.keyCode == 13)
                eval(acao);
        });
    } else {
        jQuery(obj).keydown(function (e) {
            if (e.keyCode == 13)
                eval(acao);
        });
    }
}

function retirarMask(text) {
    var proc = ".-/";
    for (var i = 0; i < text.length; i++) {
        if (proc.indexOf(text.charAt(i)) > -1)
            text = text.replace(text.charAt(i), "");
    }
    return text;
}

function formatarData(string, brasil) {
    var retorno = string;
    var dataHora = '';
    var data = '';
    if (brasil) {
        dataHora = string.split(" ");
        data = dataHora[0].split("/");
        if (data.length > 1) {
            retorno = data[2] + "-" + data[1] + "-" + data[0] + ' ' + dataHora[1];
        }
    } else {
        dataHora = string.split(" ");
        data = dataHora[0].split("/");
        if (data.length > 1) {
            retorno = data[2] + "/" + data[1] + "/" + data[0] + ' ' + dataHora[1];
        }
    }
    return retorno;
}

function trocarPonto(valor, tipo) {
    if (tipo == 'V')
        return valor.replace(',', '.');
    else
        return valor.replace('.', ',');
}

function formatarValor(valor) {
    var ret = valor;
    var io = valor.indexOf(',');
    if (io != -1) {
        var arr = valor.split(',');
        if (arr[1].length == 1) {
            ret = valor + '0';
        }
    } else {
        ret = valor + ',00';
    }

    return ret;
}

function verificaNumero(e) {
    if ((e.ctrlKey && e.which == 99) || (e.ctrlKey && e.which == 118))
        return true;
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        return false;
    return true;
}

function somenteNumero(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla > 47 && tecla < 58))
        return true;
    else {
        if (tecla == 8 || tecla == 0)
            return true;
        else
            return false;
    }
}

function somenteNumeroVirgulaPonto(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla > 47 && tecla < 58) || (tecla == 44) || (tecla == 46)) // 44 virgula 46 ponto
        return true;
    else {
        if (tecla == 8 || tecla == 0)
            return true;
        else
            return false;
    }
}


function validarEmail(email) {
    var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
    return (er.test(email)) ? true : false;
}

function jsonConcat(o1, o2) {
    for (var key in o2) {
        o1[key] = o2[key];
    }
    return o1;
}

function sleep(ms) {
    var unixtime_ms = new Date().getTime();
    while (new Date().getTime() < unixtime_ms + ms) {
    }
}

/**
 * Carrega via ajax os dados e coloca no form
 * 
 * @param string pag
 * @param array param
 * @param function callback
 */
function loadCallback(pag, param, callback, async) {
    async = (async == undefined) ? false : async;
    $.ajax({
        type: "POST",
        url: pag,
        data: param,
        dataType: 'json',
        async: async,
        beforeSend: function () {
            $.gDisplay.loadStart('HTML');
        },
        error: function () {
            $.gDisplay.loadError('HTML', "Erro ao carregar a página...");
        },
        success: function (json) {
            $.gDisplay.loadStop('HTML');
            if (json.status === undefined) {
                if (typeof callback === 'function') {
                    callback.call(this, json);
                }
            } else {
                $.gDisplay.showError(json.msg);
            }
            return true;
        }
    });
}
function validarUsuario(e) {
    var expressao = /[0-9a-z._-]/;
    return (expressao.test(String.fromCharCode(e.keyCode)));
}

function removeItem(array, item) {
    for (var i in array) {
        if (array[i] == item) {
            array.splice(i, 1);
            break;
        }
    }
}

function supportsCanvas() {
    return !!document.createElement('canvas').getContext;
}

function isEmpty(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop))
            return false;
    }
    return true;
}

jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

(function ($) {
    $.fn.extend({
        limiter: function (limit, elem) {
            $(this).on("keyup focus", function () {
                setCount(this, elem);
            });
            function setCount(src, elem) {
                var chars = src.value.length;
                if (chars > limit) {
                    src.value = src.value.substr(0, limit);
                    chars = limit;
                }
                elem.html(limit - chars);
            }
            setCount($(this)[0], elem);
        }
    });
})(jQuery);