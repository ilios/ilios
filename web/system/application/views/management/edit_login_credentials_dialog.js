/*
 * Defines the "Edit Login Credentials" dialog.
 *
 * Dependency: YUI utilities
 * Dependency: YUI Container Family
 * Dependency: ilios.ui
 * Dependency: ilios.utilities
 * Dependency: ilios_i18nVendor
 * Dependency: management_user_accounts.js
 * Dependency: management_transaction.js
 */
(function () {

    ilios.namespace("management.user_accounts.widget");

    /**
    * "Edit Login Credentials" dialog.
    * @namespace ilios.management.user_accounts.
    * @class EditLoginCredentialsDialog
    * @extends YAHOO.widget.Dialog
    * @constructor
    * @param {String} el The element ID representing the dialog
    * <em>OR</em>
    * @param {HTMLElement} el The element representing the dialog
    * @param {Object} userConfig The configuration object literal containing
    * the configuration that should be set for this dialog.
    */
    ilios.management.user_accounts.widget.EditLoginCredentialsDialog = function (el, userConfig){
        var _dialog = this;

        var defaultConfig = {
            width: "500px",
            modal: true,
            fixedcenter: true,
            visible: false,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.done'),
                    handler: function () {
                        var usernameEl = document.getElementById('ua_edit_login_username_tf');
                        var passwordEl = document.getElementById('ua_edit_login_password_tf');
                        var username = usernameEl.value;
                        var password = passwordEl.value;
                        var userModel = this.model;

                        // client-side input validation
                        // @todo

                        // send request
                        var url = controllerURL + "updateLoginCredentials";
                        var method = "POST";
                        var paramString = "username=" + username + "&password=" + encodeURIComponent(password) + '&user_id=' + userModel.getDBId();
                        var ajaxCallback = {
                            success: function (resultObject) {
                                var parsedObject = null;
                                var msg;
                                try {
                                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                                } catch (e) {
                                    ilios.global.defaultAJAXFailureHandler(null, e);
                                    return;
                                }
                                if (parsedObject.error != null) {
                                    msg = "The system failed to update the login credentials for the following reasons:";
                                    ilios.alert.alert(msg + "<br><br><div class=\"small\">" + parsedObject.error.join('<br />') + "</span>");
                                    return;
                                }
                                // success!
                                userModel.setLoginName(username);
                                ilios.management.user_accounts.updateLoginNameDisplay(username);
                                _dialog.cancel();
                            },
                            failure: function (resultObject) {
                                ilios.global.defaultAJAXFailureHandler(resultObject);
                            }
                        };

                        // statusElement.innerHTML = 'saving change';
                        YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
                    },
                    isDefault: true
                },
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                        this.model = null;
                        this.cancel();
                    }
                }
            ]
        };

        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        ilios.management.user_accounts.widget.EditLoginCredentialsDialog.superclass.constructor.call(this, el, config);

        // user model
        this.model = null;
    }

    // inheritance
    YAHOO.lang.extend(ilios.management.user_accounts.widget.EditLoginCredentialsDialog, YAHOO.widget.Dialog, {

        /**
         * Sets the internal user model for this dialog.
         * @method setUserModel
         * @param {UserManagementModel} model
         */
        setUserModel : function (model) {
            this.model = model;
            this.resetForm(this.model.getLoginName())
        },

        resetForm : function (loginName) {
            var usernameEl = document.getElementById('ua_edit_login_username_tf');
            var passwordEl = document.getElementById('ua_edit_login_password_tf');
            if (false !== loginName) {
                usernameEl.value = loginName;
            } else {
                usernameEl.value = '';
            }
            passwordEl.value = '';
        }
    });
}());
