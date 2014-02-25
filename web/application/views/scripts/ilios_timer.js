/**
 * Idle timer which logs the user out (after providing an alert) if they are idle for too long.
 *
 * Defines the following namespace:
 *
 * ilios.timer
 *
 * Dependencies:
 *
 * ilios_base
 * ilios_alert
 * ilios_i18nVendor
 * YUI utilities
 * YUI IdleTimer
 */

ilios.namespace('timer');

/**
 * Instantiates and starts the idle timer, subscribes a timeout-handler function to it.
 * The function is self-invoking and runs on DOMReady.
 * If the function finds a JSON object in #iliosIdleTimer, it will invoke the timer.
 * The JSON object should specify the logoutUrl and an optional timeout duration that
 *   defaults to 45 minutes.
 * @method startIdleTimer
 * @returns Function
 */
ilios.timer.startIdleTimer = (function () {
    var me = function () {
        YAHOO.util.Event.onDOMReady(function () {
            var idleTimer = YAHOO.util.IdleTimer;
            var data;

            data = ilios.global.readJsonFromDom('iliosIdleTimer');
            if (data) {
                data.timeout = YAHOO.lang.isNumber(data.timeout) ? data.timeout : 2700000; // default to 45 mins

                idleTimer.subscribe("idle", function () {
                    if (! YAHOO.util.IdleTimer.isIdle()) {
                        return;
                    }
                    ilios.alert.alert(
                        ilios_i18nVendor.getI18NString('general.notification.idle_timeout_message'),
                        ilios_i18nVendor.getI18NString('general.terms.ok'),
                        function () { window.location.href = data.logoutUrl; }
                    );
                });
                idleTimer.start(data.timeout, document);
            }
        });
    };

    // Run automatically onDOMReady.
    me();

    // Return so it can be run again for testing. Might also be useful for other use cases
    // although we don't have any right now.
    return me;
}());

