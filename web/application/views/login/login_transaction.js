/**
 * DEPENDENCIES:
 *         YUI lib
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */

function attemptLogin () {
    var url = controllerURL + "login";
    var method = "POST";
    var paramString = "username=" + document.getElementById('user_name').value
                        + "&password=" + document.getElementById('password').value;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                } catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);
                    return;
                }

                if (parsedObject.error != null) { // login failure
                    document.getElementById('login_status_message').innerHTML = parsedObject.error;
                    return;
                }
                // login success!
                // force a full page refresh and redirect to dashboard
                window.location.href = dashboardControllerUrl;
                return;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    document.getElementById('login_status_message').innerHTML = ilios_i18nVendor.getI18NString('login.authenticating_status');

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
}
