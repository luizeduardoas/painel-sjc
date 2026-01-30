(function ($) {
    $.gAjax = {
        link: function (url) {
            var arraySplit = url.split("?");
            var page = arraySplit[0];
            var param = arraySplit[1];
            var target = arraySplit[2];

            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function () {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                },
                success: function (resp) {
                    jQuery.gDisplay.loadStop(target);
                    jQuery(target).html(resp);
                }
            });
        },
        load: function (page, param, target, store, async, preload) {
            if (preload === undefined)
                preload = target;
            if (async === undefined)
                async = true;
            if (store != undefined)
                bookmarks.sethash(store, page, target, param);
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(preload);
                },
                error: function () {
                    jQuery.gDisplay.loadError(preload, "Erro ao carregar a página...");
                },
                success: function (resp) {
                    jQuery.gDisplay.loadStop(preload);
                    if (jQuery(target).html() != resp) {
//                        console.log(target);
//                        console.log(jQuery(target).html());
//                        console.log(resp);
                        jQuery(target).html(resp);
                    } else {
                        console.log(target);
                    }
                }
            });
        },
        exec: function (page, param, success, error, alert, async, target) {
            if (async === undefined)
                async = true;
            if (target === undefined)
                target = 'HTML';
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function (jqXHR) {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                    jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:block">' + jqXHR.responseText + '</div>', error);
                },
                success: function (json) {
                    jQuery.gDisplay.loadStop(target);
                    if (alert === undefined || alert == true) {
                        try
                        {
                            var json = JSON.parse(json);
                        } catch (e)
                        {
                            jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:block">' + json + '</div>', error);
                        }
                        jQuery.gDisplay.showAlert(json, success, error);
                    } else {
                        if (json.status)
                            eval(success);
                        else
                            jQuery.gDisplay.showError(json.msg, error);
                    }
                }
            });
        },
        execReturn: function (page, param) {
            var data;
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                async: false,
                error: function (jqXHR) {
                    jQuery.gDisplay.loadError('HTML', "Erro ao carregar a página...");
                    jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:block">' + jqXHR.responseText + '</div>');
                    data = {"status": false, "msg": jqXHR.responseText};
                },
                success: function (json) {
                    data = json;
                }
            });
            return data;
        },
        execData: function (page, param, success, error, alert, async, target) {
            if (async === undefined)
                async = true;
            if (target === undefined)
                target = 'HTML';
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                processData: false,
                contentType: false,
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function (jqXHR) {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                    if (SERVIDOR == 'P') {
                        jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:none">' + jqXHR.responseText + '</div>', error);
                    } else {
                        jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<pre>' + jqXHR.responseText + '</pre>', error);
                    }
                },
                success: function (json, textStatus, jqXHR) {
                    jQuery.gDisplay.loadStop(target);
                    if (alert === undefined || alert == true) {
                        try
                        {
                            var json = JSON.parse(json);
                        } catch (e)
                        {
                            if (SERVIDOR == 'P') {
                                jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:none">' + jqXHR.responseText + '</div>', error);
                            } else {
                                jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<pre>' + jqXHR.responseText + '</pre>', error);
                            }
                        }
                        jQuery.gDisplay.showAlert(json, success, error);
                    } else {
                        if (json.status)
                            eval(success);
                        else
                            jQuery.gDisplay.showError(json.msg, error);
                    }
                }
            });
        },
        execFuncao: function (page, param, funcao, success, error, alert, async, target) {
            if (async === undefined)
                async = true;
            if (target === undefined)
                target = 'HTML';
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function () {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                },
                success: function (json) {
                    jQuery.gDisplay.loadStop(target);
                    if (json.status)
                        eval(funcao);
                    if (alert === undefined || alert == true)
                        jQuery.gDisplay.showAlert(json, success, error);
                    else {
                        if (json.status)
                            eval(success);
                        else
                            jQuery.gDisplay.showError(json.msg, error);
                    }
                }
            });
        },
        execFuncaoSemAlert: function (page, param, funcao, async, target) {
            if (async === undefined)
                async = true;
            if (target === undefined)
                target = 'HTML';
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function () {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                },
                success: function (json) {
                    jQuery.gDisplay.loadStop(target);
                    if (json.status)
                        eval(funcao);
                }
            });
        },
        login: function (page, param, success, error) {
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                dataType: 'json',
                beforeSend: function () {
                    jQuery.gDisplay.loadStart('HTML');
                },
                error: function () {
                    jQuery.gDisplay.loadError('HTML', "Erro ao carregar a página...");
                },
                success: function (json) {
                    jQuery.gDisplay.loadStop('HTML');
                    if (json.status)
                        eval(success);
                    else
                        jQuery.gDisplay.showError(json.msg, error);

                }
            });
        },
        loadInput: function (page, param, input, async) {
            if (async === undefined)
                async = true;
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart('HTML');
                },
                error: function () {
                    jQuery.gDisplay.loadError('HTML', "Erro ao carregar a página...");
                },
                success: function (resp) {
                    jQuery.gDisplay.loadStop('HTML');
                    jQuery(input).val(resp);
                }
            });
        },
        loadAppend: function (page, param, target, store, async) {
            if (async === undefined)
                async = true;
            if (store != undefined)
                bookmarks.sethash(store, page, target, param);
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart(target);
                },
                error: function () {
                    jQuery.gDisplay.loadError(target, "Erro ao carregar a página...");
                },
                success: function (resp) {
                    jQuery.gDisplay.loadStop(target);
                    jQuery(target).append(resp);
                }
            });
        },
        loadTarget: function (page, param, target, store, async) {
            if (async === undefined)
                async = true;
            if (store != undefined)
                bookmarks.sethash(store, page, target, param);
            jQuery.ajax({
                type: "POST",
                url: page,
                data: param,
                async: async,
                beforeSend: function () {
                    jQuery.gDisplay.loadStart("HTML");
                },
                error: function () {
                    jQuery.gDisplay.loadError("HTML", "Erro ao carregar a página...");
                },
                success: function (resp) {
                    jQuery.gDisplay.loadStop("HTML");
                    jQuery(target).html(resp);
                    jQuery('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }
})(jQuery);