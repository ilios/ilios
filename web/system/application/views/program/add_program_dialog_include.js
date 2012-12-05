/**
 * "Add New Program" dialog support functions.
 * @see system/application/views/program/program_manager.php
 *
 * DEPENDENCIES
 *     YAHOO Global Object
 *     program/program_manager_dom.js
 *     scripts/ilios_utilities.js
 */
ilios.pm.clearProgramAddDialogContents = function (dialog) {
    var element = document.getElementById('new_program_hidden');
    element.value = '';
    element = document.getElementById('new_program_title');
    element.value = '';

    element = document.getElementById('new_short_title');
    element.value = '';

    element = document.getElementById('new_duration_selector');
    ilios.utilities.selectOptionWithValue(element, '4');
    return true;
};

// @private
ilios.pm.newProgramCreationResponseHandler = function (type, args) {
    if ((args[0].event == 'ajaxresponse') && (args[0].object == 'modal_dialog_panel')) {
        if (args[0].action == 'succeed') {
            var parsedObject = null;
            try {
                parsedObject = YAHOO.lang.JSON.parse(args[0].data);
            } catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);
            return;
            }
            if (args[0].target == "program_add_picked") {
                if (parsedObject.error != null) {
                    var i18nStr = ilios_i18nVendor.getI18NString('program_management.error.program_create');
                    ilios.alert.alert(i18nStr + ": " + parsedObject.error);
                    return;
                }
                // force a full page refresh
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname
                    + "?program_id=" + parsedObject.pid;
                window.location.href = newUrl;
                return;
            }
        } else {
            var i18nStr = ilios_i18nVendor.getI18NString('program_management.error.program_create');
            ilios.alert.alert(i18nStr);
            return;
        }
    }
};

