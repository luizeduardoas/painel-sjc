// jQuery Alert Dialogs Plugin
//
// Version 1.0
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 29 December 2008
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//                jAlert( message, [title, callback] )
//                jConfirm( message, [title, callback] )
//                jPrompt( message, [value, title, callback] )
//
// History:
//
//                1.00 - Released (29 December 2008)
//
// License:
//
//                This plugin is licensed under the GNU General Public License: http://www.gnu.org/licenses/gpl.html
//
(function ($) {

    $.alerts = {
        // These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time

        verticalOffset: -75, // vertical offset of the dialog from center screen, in pixels
        horizontalOffset: 0, // horizontal offset of the dialog from center screen, in pixels/
        repositionOnResize: true, // re-centers the dialog on window resize
        overlayOpacity: 0.4, // transparency level of overlay
        overlayColor: '#000', // base color of overlay
        draggable: true, // make the dialogs draggable (requires UI Draggables plugin)
        okButton: 'Ok', // text for the OK button
        cancelButton: 'Cancelar', // text for the Cancel button
        yesButton: 'Sim',
        noButton: 'Não',
        dialogClass: null, // if specified, this class will be applied to all dialogs

        // Public methods

        alert: function (type, message, title, callback) {
            if (title == null)
                title = 'Alert';
            $.alerts._show(title, message, null, type, '', function (result) {
                if (callback)
                    callback(result);
            });
        },
        confirm: function (message, title, callback) {
            if (title == null)
                title = 'Confirm';
            $.alerts._show(title, message, null, 'confirm', '', function (result) {
                if (callback)
                    callback(result);
            });
        },
        YN: function (message, title, callback) {
            if (title == null)
                title = 'Confirm';
            $.alerts._show(title, message, null, 'YN', '', function (result) {
                if (callback)
                    callback(result);
            });
        },
        prompt: function (message, value, title, requerido, callback) {
            if (title == null)
                title = 'Prompt';
            $.alerts._show(title, message, value, 'prompt', requerido, function (result) {
                if (callback)
                    callback(result);
            });
        },
        // Private methods

        _show: function (title, msg, value, type, requerido, callback) {

            $.alerts._hide();
            $.alerts._overlay('show');

            $("BODY").append(
                    '<div id="popup_container">' +
                    '<h1 id="popup_title"></h1>' +
                    '<div id="popup_content">' +
                    '<div id="popup_message"></div>' +
                    '</div>' +
                    '</div>');

            if ($.alerts.dialogClass)
                $("#popup_container").addClass($.alerts.dialogClass);

            // IE6 Fix
            var pos = ($.browser.msie && parseInt($.browser.version) <= 6) ? 'absolute' : 'fixed';

            $("#popup_container").css({
                position: pos,
                zIndex: 99999,
                padding: 0,
                margin: 0,
                maxHeight: '100%',
                overflowY: 'auto'
            });
            $("#popup_container").addClass(type);
            $("#popup_title").text(title).addClass(type);
            $("#popup_content").addClass(type);
            $("#popup_message").text(msg);
            $("#popup_message").html($("#popup_message").text().replace(/\n/g, '<br />'));

            $("#popup_container").css({
                minWidth: $("#popup_container").outerWidth() + 20,
                maxWidth: $("#popup_container").outerWidth() + 20
            });

            $.alerts._reposition();
            $.alerts._maintainPosition(true);

            switch (type) {
                case 'info':
                case 'warning':
                case 'success':
                case 'error':
                    $("#popup_message").after('<div id="popup_panel"><button class="btn btn-sm btn-success tooltip-success" id="popup_ok" name="popup_ok" data-toggle="tooltip" title="' + $.alerts.okButton + '"><i class="ace-icon fa fa-check bigger-110"></i> ' + $.alerts.okButton + '</button></div>');
                    $("#popup_ok").click(function () {
                        $.alerts._hide();
                        callback(true);
                    });
                    $("#popup_ok").focus();
                    $("#popup_ok").keypress(function (e) {
                        if (e.keyCode == 13 || e.keyCode == 27)
                            $("#popup_ok").trigger('click');
                    });
                    break;
                case 'confirm':
                    $("#popup_message").after('<div id="popup_panel"><button class="btn btn-sm btn-success tooltip-success" id="popup_ok" name="popup_ok" data-toggle="tooltip" title="' + $.alerts.okButton + '"><i class="ace-icon fa fa-check bigger-110"></i> ' + $.alerts.okButton + '</button> <button class="btn btn-sm btn-danger tooltip-danger" id="popup_cancel" name="popup_cancel" data-toggle="tooltip" title="' + $.alerts.cancelButton + '"><i class="ace-icon fa fa-times bigger-110"></i> ' + $.alerts.cancelButton + '</button></div>');
                    $("#popup_ok").click(function () {
                        $.alerts._hide();
                        if (callback)
                            callback(true);
                    });
                    $("#popup_cancel").click(function () {
                        $.alerts._hide();
                        if (callback)
                            callback(false);
                    });
                    $("#popup_ok").focus();
                    $("#popup_ok, #popup_cancel").keypress(function (e) {
                        if (e.keyCode == 13)
                            $("#popup_ok").trigger('click');
                        if (e.keyCode == 27)
                            $("#popup_cancel").trigger('click');
                    });
                    break;
                case 'YN':
                    $("#popup_message").after('<div id="popup_panel"><button class="btn btn-sm btn-success tooltip-success" id="popup_ok" name="popup_ok" data-toggle="tooltip" title="' + $.alerts.yesButton + '"><i class="ace-icon fa fa-check bigger-110"></i> ' + $.alerts.yesButton + '</button> <button class="btn btn-sm btn-danger tooltip-danger" id="popup_cancel" name="popup_cancel" data-toggle="tooltip" title="' + $.alerts.noButton + '"><i class="ace-icon fa fa-times bigger-110"></i> ' + $.alerts.noButton + '</button></div>');
                    $("#popup_ok").click(function () {
                        $.alerts._hide();
                        if (callback)
                            callback(true);
                    });
                    $("#popup_cancel").click(function () {
                        $.alerts._hide();
                        if (callback)
                            callback(false);
                    });
                    $("#popup_ok").focus();
                    $("#popup_ok, #popup_cancel").keypress(function (e) {
                        if (e.keyCode == 13)
                            $("#popup_ok").trigger('click');
                        if (e.keyCode == 27)
                            $("#popup_cancel").trigger('click');
                    });
                    break;
                case 'prompt':
                    $("#popup_message").css("padding", "20px");
                    $("#popup_content").css("background-image", "none");
                    $("#popup_message").append('<textarea id="popup_prompt" style="max-width: 100%;min-width: calc(100% - 20px);min-height: 50px;max-height: 200px"></textarea>').after('<div id="popup_panel"><button class="btn btn-sm btn-success tooltip-success" id="popup_ok" data-toggle="tooltip" title="' + $.alerts.okButton + '"><i class="ace-icon fa fa-check bigger-110"></i> ' + $.alerts.okButton + '</button> <button class="btn btn-sm btn-danger tooltip-danger" id="popup_cancel" data-toggle="tooltip" title="' + $.alerts.cancelButton + '"><i class="ace-icon fa fa-times bigger-110"></i> ' + $.alerts.cancelButton + '</button></div>');
                    //$("#popup_prompt").width($("#popup_message").width());
                    $("#popup_ok").click(function () {
                        $("#popup_message").find("#popup_requerido").remove();
                        var val = $("#popup_prompt").val();
                        if (!requerido && val == '') {
                            val = '-';
                        }
                        if (val != '') {
                            $.alerts._hide();
                            if (callback)
                                callback(val);
                        } else {
                            $("#popup_message").append('<p id="popup_requerido">' + requerido + '</p>');
                        }
                    });
                    $("#popup_cancel").click(function () {
                        $.alerts._hide();
                        if (callback)
                            callback(null);
                    });
                    $("#popup_prompt, #popup_ok, #popup_cancel").keypress(function (e) {
                        if (e.keyCode == 13)
                            $("#popup_ok").trigger('click');
                        if (e.keyCode == 27)
                            $("#popup_cancel").trigger('click');
                    });
                    if (value)
                        $("#popup_prompt").val(value);
                    $("#popup_prompt").focus().select();
                    break;
            }

            // Make draggable
            if ($.alerts.draggable) {
                try {
                    $("#popup_container").draggable({
                        handle: $("#popup_title")
                    });
                    $("#popup_title").css({
                        cursor: 'move'
                    });
                } catch (e) { /* requires jQuery UI draggables */
                }
            }
        },
        _hide: function () {
            $("#popup_container").remove();
            $.alerts._overlay('hide');
            $.alerts._maintainPosition(false);
        },
        _overlay: function (status) {
            switch (status) {
                case 'show':
                    $.alerts._overlay('hide');

                    $("BODY").append('<div id="popup_overlay"></div>');
                    $("#popup_overlay").css({
                        position: 'fixed',
                        zIndex: 99998,
                        top: '0px',
                        left: '0px',
                        width: '100%',
                        height: $(window).height() + 'px',
                        background: $.alerts.overlayColor,
                        opacity: $.alerts.overlayOpacity
                    });
                    break;
                case 'hide':
                    $("#popup_overlay").remove();

                    break;
            }
        },
        _reposition: function () {
            var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
            var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
            if (top < 0)
                top = 0;
            if (left < 0)
                left = 0;

            // IE6 fix
            if ($.browser.msie && parseInt($.browser.version) <= 6)
                top = top + $(window).scrollTop();

            $("#popup_container").css({
                top: top + 'px',
                left: left + 'px'
            });
            $("#popup_overlay").height($(document).height());
        },
        _maintainPosition: function (status) {
            if ($.alerts.repositionOnResize) {
                switch (status) {
                    case true:
                        $(window).bind('resize', function () {
                            $.alerts._reposition();
                        });
                        break;
                    case false:
                        $(window).unbind('resize');
                        break;
                }
            }
        }
    }

    // Shortuct functions
    jAlert = function (type, message, title, callback) {
        $.alerts.alert(type, message, title, callback);
        $('[data-toggle="tooltip"]').tooltip();
    }

    jConfirm = function (message, title, callback) {
        $.alerts.confirm(message, title, callback);
        $('[data-toggle="tooltip"]').tooltip();
    };

    jYN = function (message, title, callback) {
        $.alerts.YN(message, title, callback);
        $('[data-toggle="tooltip"]').tooltip();
    };

    jPrompt = function (message, value, title, requerido, callback) {
        $.alerts.prompt(message, value, title, requerido, callback);
        $('[data-toggle="tooltip"]').tooltip();
    };

})(jQuery);