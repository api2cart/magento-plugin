
$ja(function(){
    var bridge_general = $ja('#bridge_general');
    var loaderOrigin = bridge_general.find(".loader");
    var message = new Message(bridge_general.find(".message"));

    updateContent();

    bindBtn(bridge_general.find('.connect'), {
        'action': 'connect',
        'successMsg': 'Bridge installed successfully.',
        'loaderAdd': function(btn, loader) {
            btn.before(loader);
        }
    });

    bindBtn(bridge_general.find('.disconnect'), {
        'action': 'disconnect',
        'successMsg': 'Bridge removed successfully.',
        'loaderAdd': function(btn, loader) {
            bridge_general.find('.store-key').before(loader);
        }
    });

    bindBtn(bridge_general.find('.update'), {
        'action': 'updateToken',
        'successMsg': 'Store key updated.',
        'loaderAdd': function(btn, loader) {
            bridge_general.find('.store-key').before(loader);
        }
    });

    function bindBtn(element, params) {
        element.on('click', function() {
            var btn = $ja(this);
            var loader = loaderOrigin.clone().show();
            if (typeof params == 'object' && typeof params.loaderAdd == 'function') {
                params.loaderAdd(btn, loader);
            }
            btn.attr('disabled', 'disabled');
            if (typeof params == 'object' && params.action) {
                send(params.action).done(function (answ) {
                    if (typeof answ == 'object' && answ.code == 200) {
                        window.storeKey = answ.token;
                        if (typeof params == 'object' && typeof params.successMsg) {
                            message.show(params.successMsg);
                        }
                    } else {
                        var textMsg = (typeof answ == 'object' && typeof answ.msg != 'undefined') ? answ.msg : 'Error has occurred.';
                        message.show(textMsg, 'error');
                    }
                }).fail(function () {
                    message.show('Error has occurred.', 'error');
                }).always(function() {
                    updateContent();
                    loader.remove();
                    btn.removeAttr('disabled');
                });
            }
        });
    }

    function updateContent() {
        if (window.storeKey) {
            bridge_general.find('.installed').show();
            bridge_general.find('.uninstalled').hide();
            bridge_general.find('.installed .store-key-content').text(window.storeKey);
        } else {
            bridge_general.find('.installed').hide();
            bridge_general.find('.uninstalled').show();
        }
        bridge_general.find('.container').show();
    }

    function send(action, data){
        if (typeof data == 'object') {
            data.form_key = window.FORM_KEY;
        } else {
            data = {'form_key' : window.FORM_KEY};
        }

        return $ja.ajax({
            url: window.ajaxUrl + action +  '/key/'+ window.FORM_KEY +'/?isAjax=true',
            type: 'post',
            data: data,
            dataType: 'json'
        });
    }
});

function Message(element) {
    var e = element;
    this.show = function(textMsg, type) {
        e.finish();
        e.text(textMsg).show().fadeOut(5000);
        if (type == 'error') {
            e.addClass('bridge_error');
        } else {
            e.removeClass('bridge_error');
        }
    };
}