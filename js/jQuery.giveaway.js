(function ($) {

    $.fn.giveAway = function (url, type_id, language) {

        var settings = {
            url: url,
            type_id: type_id,
            language: language
        };

        var initialized = false;
        var obj = $.extend(
            $($(this).first()),
            {
                rerun_interval: 1000 * 30,
                auto_update_enabled: true,
                container_items: {
                    button: jQuery('<button type="button" style="opacity: 0.8; float: right; ">...</button>'),
                    span: jQuery('<span>...</span>')
                },
                clearText: function () {
                    obj.container_items.span.html(' ');
                },
                setText: function (text) {
                    obj.container_items.span.html(text);
                },
                hideButton: function () {
                    obj.container_items.button.hide();
                },
                showButton: function () {
                    obj.container_items.button.show();
                },
                disableButton: function () {
                    obj.container_items.button.attr('disabled', 'disabled');
                },
                enableButton: function () {
                    obj.container_items.button.removeAttr('disabled');
                },
                hash: {
                    // source: http://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript-jquery
                    getCode: function (text) {
                        var hash = 0, i, char;
                        if (text.length == 0)
                            return hash;
                        for (i = 0, l = text.length; i < l; i++) {
                            char = text.charCodeAt(i);
                            hash = ((hash << 5) - hash) + char;
                            hash |= 0; // Convert to 32bit integer
                        }
                        return hash;
                    },
                    getString: function (text) {
                        var fchar = text.substr(0, 1);
                        var lchar = text.substr(text.length - 1);
                        var len = text.length;
                        var code = obj.hash.getCode(text);
                        return escape(fchar + lchar) + '_' + len + '_' + code;
                    }
                },
                storage: {
                    isSupported: function () {
                        return localStorage && localStorage.getItem;
                    },
                    set: function (key, value) {
                        if (!obj.storage.isSupported())
                            return;
                        var lk = obj.storage.getTrueKey(key);
                        localStorage.setItem(lk, value);
                    },
                    get: function (key) {
                        if (!obj.storage.isSupported())
                            return;
                        var lk = obj.storage.getTrueKey(key);
                        return localStorage.getItem(lk);
                    },
                    getTrueKey: function (key) {
                        return 'giveaways_widget_' + key;
                    }
                },
                claimed_status: {
                    getIDString: function () {
                        var hash = obj.hash.getString(obj.getValidationString().toString());
                        var id = 'cl_' + settings.type_id + '_' + hash;
                        return id;
                    },
                    isClaimed: function () {
                        return (!(!obj.claimed_status.getClaimedString()));
                    },
                    claim: function () {
                        obj.storage.set(obj.claimed_status.getIDString(), obj.container_items.span.html());
                        obj.currentDataCache.save();
                    },
                    getClaimedString: function () {
                        var id = obj.claimed_status.getIDString();
                        var status = obj.storage.get(id);
                        return status;
                    }
                },
                cacheSystem: {
                    save: function (type_name, data) {
                        var fields = [];
                        var prefix = type_name + '_' + settings.type_id;
                        for (var x in data) {
                            var name = prefix + '_value_' + x;
                            fields.push(x);
                            obj.storage.set(name, data[x]);
                        }
                        obj.storage.set(prefix + '_fields', fields.join(' '));
                    },
                    load: function (type_name) {
                        var prefix = type_name + '_' + settings.type_id;
                        var field_names = obj.storage.get(prefix + '_fields');
                        if (!field_names)
                            return {};
                        field_names = field_names.split(' ');
                        var data = {};
                        for (var i = 0; i < field_names.length; i++) {
                            var name = prefix + '_value_' + field_names[i];
                            var value = obj.storage.get(name);
                            if (!value)
                                continue;
                            data[field_names[i]] = value;
                        }
                        return data;
                    }
                },
                currentDataCache: {
                    save: function () {
                        obj.cacheSystem.save('current_data', obj.current_data);
                    },
                    load: function () {
                        obj.current_data = obj.cacheSystem.load('current_data');
                    }
                },
                current_data: {
                    codes_left: -1,
                    codes_count: -2
                },
                response: {
                    parseMessages: function (messages) {
                        for (var i = 0; i < messages.length; i++) {
                            var msg = messages[i];
                            switch (msg.type) {
                                case 0:
                                    obj.setText(msg.message);
                                    break;
                                case 1:
                                    obj.setText(obj.languageConstants.data.error_prefix + msg.message);
                                    break;
                            }
                        }
                    }
                },
                dummyAjaxRequest: {
                    getURL: function () {
                        return settings.url + '/process.php';
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 0) {
                            console.log('Not connect.\n Verify Network.');
                        } else if (jqXHR.status == 404) {
                            console.log('Requested page not found. [404]');
                        } else if (jqXHR.status == 500) {
                            console.log('Internal Server Error [500].');
                        } else if (exception === 'parsererror') {
                            console.log('Requested JSON parse failed.');
                        } else if (exception === 'timeout') {
                            console.log('Time out error.');
                        } else if (exception === 'abort') {
                            console.log('Ajax request aborted.');
                        } else {
                            console.log('Uncaught Error.\n' + jqXHR.responseText);
                        }
                    },
                    statusCode: {
                        403: function () {
                            obj.setText(obj.languageConstants.data.error_no_giveaway);
                            obj.hideButton();
                        },
                        404: function () {
                            obj.setText(obj.languageConstants.data.error_no_giveaway);
                            obj.hideButton();
                        },
                        400: function () {
                            if (!obj.auto_update_enabled)
                                return;
                            setTimeout(obj.updateInfo.exec, obj.rerun_interval)
                        },
                        500: function () {
                            obj.setText(obj.languageConstants.data.error_server_too_bussy);
                            obj.hideButton();
                        }
                    },
                },
                updateInfo: {
                    getData: function () {
                        return {
                            show_headers: 0,
                            logging_enabled: 0,
                            format: 'jsonp',
                            lang: settings.language,
                            actions: [
                                {
                                    module: 'giveaways',
                                    name: 'GetInfo',
                                    params: {
                                        type: settings.type_id
                                    }
                                }
                            ]
                        };
                    },
                    success: function (data) {
                        if (!initialized) {
                            initialized = true;

                            obj.html(' ');

                            obj.container_items.button.html(obj.languageConstants.data.take_one);
                            obj.setText(obj.languageConstants.data.please_wait);

                            obj.append(obj.container_items.button);
                            obj.append(obj.container_items.span);

                            obj.container_items.button.click(function () {
                                if (!obj.storage.isSupported()) {
                                    obj.setText(obj.languageConstants.data.error_prefix + obj.languageConstants.data.error_your_browser_too_old);
                                    obj.hideButton();
                                } else {
                                    obj.codeGet.exec();
                                }
                            });
                        }
                        if (!obj.auto_update_enabled)
                            return;
                        if (data.responses[0].messages) {
                            obj.response.parseMessages(data.responses[0].messages);
                        }
                        if (!data.responses[0].data)
                            return;
                        var current_response = data.responses[0].data.giveaway;
                        var changes = (current_response.codes_left + current_response.codes_count) -
                            (obj.current_data.codes_left + obj.current_data.codes_count);
                        obj.current_data = current_response;
                        if (changes == 0) {
                            setTimeout(obj.updateInfo.exec, obj.rerun_interval)
                        } else if (obj.current_data.codes_left == 0) {
                            obj.setText(obj.languageConstants.data.error_not_codes_left);
                            obj.hideButton();
                        } else {
                            obj.setText(obj.languageConstants.data.msg_codes_left.replace('%d', obj.current_data.codes_left));
                            obj.showButton();
                            obj.enableButton();
                            setTimeout(obj.updateInfo.exec, obj.rerun_interval);
                        }
                    },
                    timeout: function () {
                        setTimeout(obj.updateInfo.exec, obj.rerun_interval);
                    },
                    exec: function () {
                        jQuery.ajax({
                            url: obj.dummyAjaxRequest.getURL(),
                            data: obj.updateInfo.getData(),
                            method: 'get',
                            dataType: 'jsonp',
                            crossDomain: true,
                            timeout: obj.updateInfo.timeout,
                            success: obj.updateInfo.success,
                            error: obj.dummyAjaxRequest.error,
                            statusCode: obj.dummyAjaxRequest.statusCode
                        });
                    },
                },
                getValidationString: function () {
                    var source = null;
                    if (obj.current_data.login_text) {
                        source = jQuery('html').html().split("\n").join(" ").split("\r").join(" ").replace(/<surfmarktag>/g, '').replace(/<\/surfmarktag>/g, '');
                        var regexp = new RegExp(obj.current_data.login_text, "g");
                        source = source.match(regexp);
                    }
                    if (source === null)
                        source = '';
                    return source;
                },
                languageConstants: {
                    data: {},
                    getData: function () {
                        return {
                            show_headers: 0,
                            logging_enabled: 0,
                            format: 'jsonp',
                            lang: settings.language,
                            actions: [
                                {
                                    module: 'giveaways',
                                    name: 'GetLanguageConstants',
                                }
                            ]
                        };
                    },
                    cache: {
                        save: function () {
                            obj.cacheSystem.save('language_' + settings.language + '_data', obj.languageConstants.data);
                            obj.storage.set('language_' + settings.language + '_stored', true);
                        },
                        load: function () {
                            obj.languageConstants.data = obj.cacheSystem.load('language_' + settings.language + '_data');
                        },
                        isCached: function () {
                            return false;
                            if (!obj.storage.get('language_' + settings.language + '_stored'))
                                return false;
                            return true;
                        }
                    },
                    success: function (data) {
                        obj.languageConstants.data = data.responses[0].data.language_constants;
                        obj.auto_update_enabled = true;
                        obj.updateInfo.exec();
                        obj.languageConstants.cache.save();
                    },
                    autoLoad: function () {
                        if (obj.languageConstants.cache.isCached()) {
                            obj.languageConstants.cache.load();
                            obj.auto_update_enabled = true;
                            obj.updateInfo.exec();
                        } else {
                            obj.languageConstants.exec();
                        }
                    },
                    exec: function () {
                        obj.disableButton();
                        obj.auto_update_enabled = false;
                        jQuery.ajax({
                            url: obj.dummyAjaxRequest.getURL(),
                            data: obj.languageConstants.getData(),
                            method: 'post',
                            dataType: 'jsonp',
                            crossDomain: true,
                            success: obj.languageConstants.success,
                            error: obj.dummyAjaxRequest.error,
                            statusCode: obj.dummyAjaxRequest.statusCode
                        });
                    },
                },
                codeGet: {
                    getData: function () {
                        return {
                            show_headers: 0,
                            logging_enabled: 0,
                            format: 'jsonp',
                            lang: settings.language,
                            actions: [
                                {
                                    module: 'giveaways',
                                    name: 'Claim',
                                    params: {
                                        type: settings.type_id,
                                        source_page: obj.getValidationString()
                                    }
                                }
                            ]
                        };
                    },
                    success: function (data) {
                        if (data.responses[0].messages) {
                            obj.response.parseMessages(data.responses[0].messages);
                        }
                        obj.hideButton();
                        if (data.responses[0].data && data.responses[0].data.is_claimed) {
                            obj.claimed_status.claim();
                        }
                    },
                    exec: function () {
                        obj.disableButton();
                        obj.auto_update_enabled = false;
                        jQuery.ajax({
                            url: obj.dummyAjaxRequest.getURL(),
                            data: obj.codeGet.getData(),
                            method: 'post',
                            dataType: 'jsonp',
                            crossDomain: true,
                            success: obj.codeGet.success,
                            error: obj.dummyAjaxRequest.error,
                            statusCode: obj.dummyAjaxRequest.statusCode
                        });
                    },
                },
            }
        );

        obj.currentDataCache.load();
        if (obj.claimed_status.isClaimed()) {
            obj.html(' ');
            obj.append(obj.container_items.span);
            obj.setText(obj.claimed_status.getClaimedString());
        } else {
            obj.languageConstants.autoLoad();
        }

    };

}(jQuery));