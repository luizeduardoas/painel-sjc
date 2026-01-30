(function ($) {
    $.gDisplay = {
        loadStart: function (target, background) {
            if (typeof target == "undefined")
                target = 'HTML';
            if (typeof background == "undefined") {
                background = 'background: rgba(0, 0, 0, 0.5) !important;';
            }
            jQuery(target).css('position', 'relative');
            var position = 'position: absolute; !important;';
            if (target == 'HTML' || target == 'BODY') {
                position = 'position: fixed; !important;';
            }
            if (target.toLowerCase() == '#dt_dados') {
                jQuery(target).append('<div class="__preloader" style="' + background + '"><i class="ace-icon fa fa-spinner fa-spin __default" style="background-attachment:scroll;"></i></div>');
            } else {
                jQuery(target).append('<div class="__preloader" style="' + position + '' + background + '"><i class="ace-icon fa fa-spinner fa-spin __default" style="background-attachment:fixed;"></i></div>');
            }
        },
        loadStop: function (target) {
            jQuery(target + ' div.__preloader').remove();
        },
        loadError: function (target, msg) {
            this.loadStop(target);
            console.log(msg);
        },
        showAlert: function (json, success, error) {
            if (json.status) {
                this.showSuccess(json.msg, success, json);
            } else {
                this.showError(json.msg, error, json);
            }
        },
        showSuccess: function (msg, success, json) {
            parent.jAlert('success', msg, 'Sucesso', function (r) {
                if (r) {
                    eval(success);
                }
            });
        },
        showError: function (msg, error, json) {
            parent.jAlert('error', msg, 'Atenção', function (r) {
                if (r)
                    eval(error);
            });
        },
        showConfirm: function (msg, ok, cancel, json) {
            parent.jConfirm(msg, "Atenção", function (r) {
                if (r)
                    eval(ok);
                else
                    eval(cancel);
            });
        },
        showYN: function (msg, yes, no, json) {
            parent.jYN(msg, "Atenção", function (r) {
                if (r)
                    eval(yes);
                else
                    eval(no);
            });
        },
        showPrompt: function (msg, value, titulo, requerido, ok) {
            parent.jPrompt(msg, value, titulo, requerido, function (r) {
                if (r)
                    eval(ok);
            });
        },
        showAtencao: function (msg) {
            parent.jAlert('info', msg, 'Atenção');
        }
    }
})(jQuery);


