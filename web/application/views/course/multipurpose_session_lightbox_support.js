/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */


ilios.cm.session.multipurposeSessionLightbox = null;

ilios.cm.session.multipurposeUIListenersRegistered = false;

/*
 * This method is invoked as part of onDomReady; what DOM isn't built by the HTML in
 *  multipurpose_session_lightbox_include.php is crafted here. This includes YUI related components
 *  like calendars.
 *
 * Should be considered @protected
 */
ilios.cm.session.buildMultipurposeLightboxDOM = function () {
    var element = null;

    // MAY RETURN THIS BLOCK
    if (ilios.cm.session.multipurposeSessionLightbox != null) {
        return;
    }

    var handleSave = function () {
        ilios.cm.session.validateMultipurposeLightboxSave(this);
    };

    var handleCancel = function () {
        ilios.cm.session.userCanceledMultipurposeLightboxChanges(this);
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var saveStr = ilios_i18nVendor.getI18NString('general.terms.done');
    var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                       {text: cancelStr, handler: handleCancel}];

    var panelWidth = "530px";
    var dialog = new YAHOO.widget.Dialog('multipurpose_session_lightbox', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    var titleStr = ilios_i18nVendor.getI18NString('general.phrases.choose_date') + ':';

    dialog.showDialogPane = function () {
        this.center();
        this.show();
    };

    dialog.showEvent.subscribe(function() {
        // check if the instructor picker container is expanded
        // if and ONLY so, refresh the autocomplete list
        if ('none' !== YAHOO.util.Dom.getStyle('ilios_calendar_instructors_selector_div', 'display')) {
            ilios.cm.session.ilm.instructorGroupAutoCompleter.sendQuery('');
        }
    });

    // Render the Dialog
    dialog.render();

    ilios.cm.session.multipurposeSessionLightbox = dialog;

    element = new YAHOO.util.Element(document.getElementById('multipurpose_session_lightbox'));
    element.setStyle('display', 'block');
};

/*
 * This method is invoked onDomReady; it is responsible for setting up the wiring such that UI
 *  events on input widgets of interest affect the model state appropriately.
 *
 * Should be considered @protected
 */
ilios.cm.session.registerMultipurposeLightboxUIListeners = function () {
    if (! ilios.cm.session.multipurposeUIListenersRegistered) {
        var element = document.getElementById('calendar_student_group_collapse_selector');
        var itemSelectHandler = null;

        YAHOO.util.Event.addListener(element, 'click', function () { ilios.cm.session.showLearners(false); });

        ilios.cm.session.multipurposeUIListenersRegistered = true;
    }
};

/*
 * This is messaged when the user dismisses the light box with a click on the 'Save' button.
 *
 * Should be considered @private
 */
ilios.cm.session.validateMultipurposeLightboxSave = function (dialog) {
    if (! ilios.cm.session.multipurposeSessionLightbox.showingForMultiOffering) {
        ilios.cm.session.ilm.userAcceptedILMLightboxChanges(dialog);
    }
    else {
        ilios.cm.session.mo.validateMultiOfferingLightboxSave(dialog);
    }
};

// @private
ilios.cm.session.userCanceledMultipurposeLightboxChanges = function (dialog) {
    if (! ilios.cm.session.multipurposeSessionLightbox.showingForMultiOffering) {
        dialog.cancel();
    }
    else {
        ilios.cm.session.mo.userCanceledMultiOfferingLightboxChanges();
    }
};

/*
 * This method either displays or hides the DIV in the lightbox which allows selection of
 *  student groups.
 *
 * Should be considered @protected
 */
ilios.cm.session.showLearners = function (showSelectorDiv) {
    var element = document.getElementById('ilios_calendar_student_groups_selector');

    if (showSelectorDiv) {
        (new YAHOO.util.Element(element)).setStyle('display', 'none');
    } else {
        (new YAHOO.util.Element(element)).setStyle('display', 'inline');
    }

    ilios.utilities.toggleShowMoreOrLess('ilios_calendar_student_groups_selector_div', null);
};
