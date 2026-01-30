(function ($) {
    $.fn.gValidate = function () {
        var ret = true;
        var form = jQuery(this).attr("id");
        jQuery("input, select, textarea", this).each(function () {
            var typesValidates = jQuery(this).attr("validate");
            if (typesValidates !== undefined) {
                var idField = jQuery(this).attr("id");
                jQuery("#" + form + " #" + idField).parent().find(".check-error").remove();
                jQuery("#" + form + " #" + idField).removeClass("error");
                jQuery("#" + form + " #" + idField).parent().removeClass("error");
                var $label = jQuery("#" + form + ' label[for="' + idField + '"]');
                var nameField = $label.html();
                var validates = typesValidates.split(";");
                for (var i = 0; i < validates.length; i++) {
                    if (!validate(idField, nameField, validates[i], $label, form)) {
                        ret = false;
                        return false;
                    }
                }
            }
        });
        return ret;
    };

    function validate(idField, nameField, typeValidate, $label, form) {
        if (typeValidate.indexOf("~") >= 0) {
            if (jQuery("#" + form + " #" + idField).is(":visible") || jQuery("#" + form + " #" + idField + "_chosen").is(":visible")) {
                var valueField = jQuery("#" + form + " #" + idField).val();
                var param = typeValidate.split("|");
                var expressao = param[0];
                var msg = param[1];
                if (msg === undefined) {
                    msg = "inválido!";
                }
                expressao = replaceAll(expressao, "[", "'");
                expressao = replaceAll(expressao, "]", "'");
                expressao = replaceAll(expressao, "~", valueField);
                if (!eval(expressao)) {
                    jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é " + msg, 'setFocus("' + idField + '","' + form + '");');
                    jQuery("#" + form + " #" + idField).addClass("error");
                    jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                    return false;
                }
            }
        } else {
            switch (typeValidate) {
                case"required":
                    if (strip_tags(trim(jQuery("#" + form + " #" + idField).val())) == "") {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é requerido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).after('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"requiredVisible":
                    if ((jQuery("#" + form + " #" + idField).is(":visible")) && (strip_tags(trim(jQuery("#" + form + " #" + idField).val())) == "")) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é requerido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"requiredCkeditor":
                    if (strip_tags(trim(jQuery("#" + form + " #" + idField).val())) == "") {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é requerido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #cke_" + idField).addClass("error");
                        jQuery("#" + form + " #cke_" + idField).after('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"radio":
                    idField = jQuery("#" + form + " #" + idField).attr("name");
                    nameField = jQuery("#" + form + " #span_" + idField).attr("rel");
                    if (!jQuery("#" + form + " input[type=radio][name=" + idField + "]:checked").val()) {
                        jQuery.gDisplay.showError("Favor selecionar <b>" + nameField + "</b>", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " input[type=radio][name=" + idField + "]").parent().addClass("error");
                        jQuery("#" + form + " input[type=radio][name=" + idField + "]").parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"radioVisible":
                    idField = jQuery("#" + form + " #" + idField).attr("name");
                    nameField = jQuery("#" + form + " #span_" + idField).attr("rel");
                    if ((jQuery("#" + form + " input[type=radio][name=" + idField + "]").is(":visible")) && !jQuery("#" + form + " input[type=radio][name=" + idField + "]:checked").val()) {
                        jQuery.gDisplay.showError("Favor selecionar <b>" + nameField + "</b>", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " input[type=radio][name=" + idField + "]").parent().addClass("error");
                        jQuery("#" + form + " input[type=radio][name=" + idField + "]").parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"checkbox":
                    idField = jQuery("#" + form + " #" + idField).attr("name").replace('[]', '');
                    nameField = jQuery("#" + form + " #span_" + idField).attr("rel");
                    var marcado = false;
                    jQuery("#" + form + " ." + idField).each(function () {
                        if (jQuery(this).is(":checked")) {
                            marcado = true;
                            return true;
                        }
                    });
                    if (marcado == false) {
                        jQuery.gDisplay.showError("Favor selecionar <b>" + nameField + "</b>", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " input[type=checkbox][name=" + idField + "]").parent().addClass("error");
                        jQuery("#" + form + " input[type=checkbox][name=" + idField + "]").parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"checkboxVisible":
                    idField = jQuery("#" + form + " #" + idField).attr("name").replace('[]', '');
                    nameField = jQuery("#" + form + " #span_" + idField).attr("rel");
                    if (jQuery("#" + form + " ." + idField).is(":visible")) {
                        var marcado = false;
                        jQuery("#" + form + " ." + idField).each(function () {
                            if (jQuery(this).is(":checked")) {
                                marcado = true;
                                return true;
                            }
                        });
                        if (marcado == false) {
                            jQuery.gDisplay.showError("Favor selecionar <b>" + nameField + "</b>", 'setFocus("' + idField + '","' + form + '");');
                            jQuery("#" + form + " input[type=checkbox][name=" + idField + "]").parent().addClass("error");
                            jQuery("#" + form + " input[type=checkbox][name=" + idField + "]").parent().append('<span class="check-error"></span>');
                            return false;
                        }
                    }
                    break;
                case"cpf":
                    if ((jQuery("#" + form + " #" + idField).is(":visible")) && (!verifyCpf(jQuery("#" + form + " #" + idField).val()))) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é inválido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"cnpj":
                    if ((jQuery("#" + form + " #" + idField).is(":visible")) && (!verifyCnpj(jQuery("#" + form + " #" + idField).val()))) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é inválido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"email":
                    if (!verifyEmail(jQuery("#" + form + " #" + idField).val())) {
                        jQuery.gDisplay.showError("E-mail <b>" + jQuery("#" + form + " #" + idField).val() + "</b> é inválido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"url":
                    if (!verifyUrl(jQuery("#" + form + " #" + idField).val())) {
                        jQuery.gDisplay.showError("Url <b>" + jQuery("#" + form + " #" + idField).val() + "</b> é inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"usuario":
                    if ((!verifyUsuario(jQuery("#" + form + " #" + idField).val())) || (jQuery("#" + form + " #" + idField).val().length < 3)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> deve conter apenas letras e/ou números e/ou pontos e/ou hifens e/ou underline, e conter no mínimo 3 caracteres!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"senha":
                    if ((jQuery("#" + form + " #" + idField).val().length < 6) && (jQuery("#" + form + " #" + idField).val().length > 0)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> deve conter no mínimo 6 caracteres!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"conferencia":
                    if (jQuery("#" + form + " #" + idField).val() != jQuery("#" + form + " #" + idField + "_conf").val()) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> não pode ser diferente do campo Repita a senha!", 'setFocus("' + idField + '_conf","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"time":
                    if (!validaHora(jQuery("#" + form + " #" + idField).val())) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"date":
                    if (!validaData(jQuery("#" + form + " #" + idField).val(), false)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"data":
                    if (!validaData(jQuery("#" + form + " #" + idField).val(), true)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"dateTime":
                    if (!validaDataHora(jQuery("#" + form + " #" + idField).val(), false)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"dataHora":
                    if (!validaDataHora(jQuery("#" + form + " #" + idField).val(), true)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"unknown":
                    if (!validarUnknown(jQuery("#" + form + " #" + idField).val())) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é requerido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                case"dataInicial":
                    if (!validaDataHora(jQuery("#" + form + " #" + idField).val(), true)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    } else {
                        if (!validaDataHora(jQuery("#" + form + " .dataFinal").val(), true)) {
                            var idField = jQuery("#" + form + " .dataFinal").attr("id");
                            var nameFieldFinal = jQuery("#" + form + ' label[for="' + idField + '"]').html();
                            jQuery.gDisplay.showError("O campo <b>" + nameFieldFinal + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                            jQuery("#" + form + " #" + idField).addClass("error");
                            jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                            return false;
                        } else {
                            if (!validaDataHoraInicialFinal(jQuery("#" + form + " #" + idField).val(), form, true)) {
                                var idField = jQuery("#" + form + " .dataFinal").attr("id");
                                var nameFieldFinal = jQuery("#" + form + ' label[for="' + idField + '"]').html();
                                jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> não pode ser maior que o campo <b>" + nameFieldFinal + "</b>!", 'setFocus("' + idField + '","' + form + '");');
                                jQuery("#" + form + " #" + idField).addClass("error");
                                jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                                return false;
                            }
                        }
                    }
                    break;
                case"dateInicial":
                    if (!validaDataHora(jQuery("#" + form + " #" + idField).val(), false)) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    } else {
                        if (!validaDataHora(jQuery("#" + form + " .dataFinal").val(), false)) {
                            var idField = jQuery("#" + form + " .dataFinal").attr("id");
                            var nameFieldFinal = jQuery("#" + form + ' label[for="' + idField + '"]').html();
                            jQuery.gDisplay.showError("O campo <b>" + nameFieldFinal + "</b> é uma data e hora inválida!", 'setFocus("' + idField + '","' + form + '");');
                            jQuery("#" + form + " #" + idField).addClass("error");
                            jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                            return false;
                        } else {
                            if (!validaDataHoraInicialFinal(jQuery("#" + form + " #" + idField).val(), form, false)) {
                                var idField = jQuery("#" + form + " .dataFinal").attr("id");
                                var nameFieldFinal = jQuery("#" + form + ' label[for="' + idField + '"]').html();
                                jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> não pode ser maior que o campo <b>" + nameFieldFinal + "</b>!", 'setFocus("' + idField + '","' + form + '");');
                                jQuery("#" + form + " #" + idField).addClass("error");
                                jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                                return false;
                            }
                        }
                    }
                    break;
                case"year":
                    if (!validaYear(jQuery("#" + form + " #" + idField).val())) {
                        jQuery.gDisplay.showError("O campo <b>" + nameField + "</b> é um ano inválido!", 'setFocus("' + idField + '","' + form + '");');
                        jQuery("#" + form + " #" + idField).addClass("error");
                        jQuery("#" + form + " #" + idField).parent().append('<span class="check-error"></span>');
                        return false;
                    }
                    break;
                default:
                    jQuery.gDisplay.showError('Não foi encontrada a validação de tipo : "' + nameField + '" para o campo: "' + idField + '"', "");
                    return false;
            }
        }
        return true
    }
    function replaceAll(string, token, newtoken) {
        while (string.indexOf(token) != -1) {
            string = string.replace(token, newtoken);
        }
        return string;
    }
    function verifyCpf(cpf) {
        cpf = retirarMask(cpf);
        if (cpf == "") {
            return true;
        }
        var numeros, digitos, soma, i, resultado, digitos_iguais;
        digitos_iguais = 1;
        if (cpf.length < 11) {
            return false;
        }
        for (i = 0; i < cpf.length - 1; i++) {
            if (cpf.charAt(i) != cpf.charAt(i + 1)) {
                digitos_iguais = 0;
                break;
            }
        }
        if (!digitos_iguais) {
            numeros = cpf.substring(0, 9);
            digitos = cpf.substring(9);
            soma = 0;
            for (i = 10; i > 1; i--) {
                soma += numeros.charAt(10 - i) * i
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0)) {
                return false;
            }
            numeros = cpf.substring(0, 10);
            soma = 0;
            for (i = 11; i > 1; i--) {
                soma += numeros.charAt(11 - i) * i
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1)) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    function verifyCnpj(cnpj) {
        cnpj = retirarMask(cnpj);
        if (cnpj == "") {
            return true;
        }
        var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
        digitos_iguais = 1;
        if (cnpj.length < 14 && cnpj.length < 15) {
            return false;
        }
        for (i = 0; i < cnpj.length - 1; i++) {
            if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
                digitos_iguais = 0;
                break;
            }
        }
        if (!digitos_iguais) {
            tamanho = cnpj.length - 2;
            numeros = cnpj.substring(0, tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) {
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0)) {
                return false;
            }
            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) {
                    pos = 9;
                }
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1)) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    function countCaracters(e, elemento, quant) {
        var tecla = (window.event) ? event.keyCode : e.which;
        if (tecla == 0) {
            return true;
        }
        if (tecla == 8) {
            return true;
        }
        var valor = jQuery(elemento).val();
        var total = valor.length;
        if (total < quant) {
            return true;
        } else {
            jQuery(elemento).val(valor.substr(0, quant));
            jAlert("error", "Quantidade de caracteres máximo (" + quant + ") atingido.", "Atenção");
            return false;
        }
    }
    function verifyEmail(email) {
        if (email == "") {
            return true;
        }
        var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        if (er.test(email)) {
            return true;
        } else {
            return false;
        }
    }
    function verifyUrl(url) {
//        var pattern = new RegExp('^(https?:\/\/)?((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|((\d{1,3}\.){3}\d{1,3}))(\:\d+)?(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$', 'i');
        if (url != '') {
            var rg = /(([a-zA-Z0-9$\-_.+!*'(),;:&=]|%[0-9a-fA-F]{2})+@)?(((25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9][0-9]|[1-9][0-9]|[0-9])){3})|localhost|([a-zA-Z0-9\-\u00C0-\u017F]+\.)+([a-zA-Z]{2,}))(:[0-9]+)?(\/(([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*(\/([a-zA-Z0-9$\-_.+!*'(),;:@&=]|%[0-9a-fA-F]{2})*)*)?(\?([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?(\#([a-zA-Z0-9$\-_.+!*'(),;:@&=\/?]|%[0-9a-fA-F]{2})*)?)?$/;
            if (!rg.test(url)) {
                return false;
            } else {
                return true;
            }
        } else
            return true;
    }
    function verifyUsuario(user) {
        var expressao = /[0-9a-z._\-]/;
        for (var i = 0; i < user.length; i++) {
            if (!expressao.test(user.charAt(i))) {
                return false;
            }
        }
        return true;
    }
    function validaData(value, brasil) {
        if (value.length != 0) {
            if (value.length != 10) {
                return false;
            }
            if (value.includes("/")) {
                var data = value;
                var dia = data.substr(0, 2);
                var barra1 = data.substr(2, 1);
                var mes = data.substr(3, 2);
                var barra2 = data.substr(5, 1);
                var ano = data.substr(6, 4);
                if ((data.length != 10) || barra1 != "/" || barra2 != "/" || isNaN(dia) || isNaN(mes) || isNaN(ano) || dia > 31 || mes > 12) {
                    return false;
                }
                if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia == 31) {
                    return false;
                }
                if (mes == 2 && (dia > 29 || (dia == 29 && ano % 4 != 0))) {
                    return false;
                }
            } else {
                var dataA = value;
                var anoA = dataA.substr(0, 4);
                var barra1A = dataA.substr(4, 1);
                var mesA = dataA.substr(5, 2);
                var barra2A = dataA.substr(7, 1);
                var diaA = dataA.substr(8, 2);
                if ((dataA.length != 10) || barra1A != "-" || barra2A != "-" || isNaN(diaA) || isNaN(mesA) || isNaN(anoA) || diaA > 31 || mesA > 12) {
                    return false;
                }
                if ((mesA == 4 || mesA == 6 || mesA == 9 || mesA == 11) && diaA == 31) {
                    return false;
                }
                if (mesA == 2 && (diaA > 29 || (diaA == 29 && anoA % 4 != 0))) {
                    return false;
                }
            }
        }
        return true;
    }
    function validaHora(value) {
        if (value.length != 0) {
            var horario = value;
            if (value.length == 4 || value.length == 7) {
                horario = '0' + value;
            }
            var hora = horario.substr(0, 2);
            var doispontos = horario.substr(2, 1);
            var minuto = horario.substr(3, 2);
            var doispontos2 = horario.substr(5, 1);
            var segundo = horario.substr(6, 2);
            if ((horario.length != 5) && (horario.length != 8)) {
                return false;
            }
            if ((horario.length == 5) && (isNaN(hora) || isNaN(minuto) || hora > 23 || minuto > 59 || doispontos != ":")) {
                return false;
            }
            if ((horario.length == 8) && (isNaN(hora) || isNaN(minuto) || isNaN(segundo) || hora > 23 || minuto > 59 || segundo > 59 || doispontos != ":" || doispontos2 != ":")) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    function validaDataHora(value, brasil) {
        if (value.length != 0) {
            if (value.length != 16 && value.length != 19) {
                return false;
            }
            var arrOpcoes = value.split(" ");
            if (arrOpcoes.length != 2) {
                return false;
            }
            if ((!validaData(arrOpcoes[0], brasil)) || (!validaHora(arrOpcoes[1]))) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    function validaYear(value) {
        if (value.length != 0) {
            if (value.length != 4) {
                return false;
            }
            if (value < 1900 || value > 2099) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    function validarUnknown(arquivo) {
        var str = arquivo.indexOf(".");
        if (str > 0) {
            var arrPonto = arquivo.split(".");
            var arrBarra = arrPonto[arrPonto.length - 2].split("/");
            if ((arrBarra[arrBarra.length - 1].trim() == 'unknown') || (arrBarra[arrBarra.length - 1].trim() == '')) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    function validaDataHoraInicialFinal(value, form, brasil) {
        var dInicial = value;
        var dInicialParte = '';
        var dataInicial = '';
        var horaInicial = '';
        var dataInicial = '';
        var dFinal = jQuery("#" + form + " .dataFinal").val();
        var dFinalParte = '';
        var dataFinal = '';
        var horaFinal = '';
        var dataFinal = '';
        if (brasil) {
            dInicialParte = dInicial.split(' ');
            dataInicial = dInicialParte[0].split('/');
            horaInicial = dInicialParte[1].split(':');
            dataInicial = new Date(dataInicial[2], parseInt(dataInicial[1], 10) - 1, dataInicial[0], horaInicial[0], horaInicial[1]);
            dFinalParte = dFinal.split(' ');
            dataFinal = dFinalParte[0].split('/');
            horaFinal = dFinalParte[1].split(':');
            dataFinal = new Date(dataFinal[2], parseInt(dataFinal[1], 10) - 1, dataFinal[0], horaFinal[0], horaFinal[1]);
        } else {
            dInicialParte = dInicial.split(' ');
            dataInicial = dInicialParte[0].split('-');
            horaInicial = dInicialParte[1].split(':');
            dataInicial = new Date(dataInicial[0], parseInt(dataInicial[1], 10) - 1, dataInicial[2], horaInicial[0], horaInicial[1]);
            dFinalParte = dFinal.split(' ');
            dataFinal = dFinalParte[0].split('-');
            horaFinal = dFinalParte[1].split(':');
            dataFinal = new Date(dataFinal[0], parseInt(dataFinal[1], 10) - 1, dataFinal[2], horaFinal[0], horaFinal[1]);
        }
        return (dataFinal.getTime() >= dataInicial.getTime());
    }
})(jQuery);
function setFocus(idField, form) {
    if (jQuery("#" + form + " #tabs").length) {
        jQuery("#" + form + " #tabs").tabs("select", jQuery("#" + form + " #" + idField).parent().attr("id"));
    }
    setTab(jQuery("#" + idField).closest('.tab-pane').attr('id'));
    jQuery("#" + form + " #" + idField).focus();
}
function setTab(tab) {
    jQuery(".tab-pane").removeClass("active");
    jQuery("#" + tab).addClass("active");
    jQuery(".nav-tabs li").removeClass("active");
    jQuery('a[href$="' + tab + '"]').parent().addClass("active");
}