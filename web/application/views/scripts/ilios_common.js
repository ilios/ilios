/**
 * @requires ilios.ui
 **/

ilios.namespace('common');
/**
 * @param args
 *    submit_override:
 *        if this is non-null the function is subscribed to
 *        the manual submit event of the dialog and the
 *        dialog is configured to fire that event when the
 *        user clicks the 'Done' button on the dialog.
 *    container:
 *        the container listing the present possible choices
 *        (as possibly filtered via autocompleter input)
 *    display_handler:
 *        a function which is called just prior to the dialog
 *        being displayed
 *    display_denier:
 *        if non-null, this boolean returning function is
 *        called during display; if it returns true the
 *        display will be aborted
 *    upload_handler:
 *        the method which will be invoked when the
 *        upload button is clicked
 *    manual_add_handler:
 *        the method which will be invoked when the
 *        manual add button is clicked
 *    panel_width:
 *        if this is non-null, it is expected to a valid CSS
 *        measurement, else '880px' will be used.
 *     trigger:
 *        the id string of the element which should invoke the
 *        display of this panel; clicking on this id
 *        string must be wired elsewhere to fire thusly:
 *        ilios.ui.onIliosEvent.fire({action: 'em_dialog_open', ...});
 *        where 'em_dialog_open' is the salient action,
 *        and the event may also contain a key for
 *        'container_number' the value of which will get
 *        assigned as property 'containerNumber' as
 *        dialog.containerNumber
 */
ilios.common.assembleAddNewMembersDialog = function (unused1, unused2, args) {
    var Event = YAHOO.util.Event;
    var handleCancel = function () {
        ilios.ui.onIliosEvent.fire({object: 'modal_em_dialog_panel', action: 'cancel', event: 'closing'});
        this.cancel();
    };
    var buttonArray = [{text: ilios_i18nVendor.getI18NString('general.terms.done'), handler: handleCancel, isDefault: true}];
    var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "880px";
    var displayOnTriggerHandler = null;
    var element = null;
    var dialog = new YAHOO.widget.Dialog('add_new_members_dialog', {
        width : panelWidth,
        x : 140,
        y : 50,
        modal : true,
        visible : false,
        constraintoviewport : false,
        buttons : buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = args['display_handler'];
        if (displayHandler != null) {
            displayHandler(dialog);
        }
        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    element = document.getElementById('em_upload_button');
    Event.addListener(element, 'click', function (e) {
        var uploadHandler = args['upload_handler'];
        uploadHandler(this);
    });

    element = document.getElementById('em_add_user_button');
    Event.addListener(element, 'click', function (e) {
        var addHandler = args['manual_add_handler'];
        addHandler(this);
    });

    if (args['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].event == args['trigger']) {
            if (handlerArgs[0].action == 'em_dialog_open') {
                var element = null;

                if (args['display_denier'] != null) {
                    var denier = args['display_denier'];
                    if (denier()) {
                        return;
                    }
                }

                element = document.getElementById('em_last_name');
                element.setAttribute('cnumber', handlerArgs[0].container_number);

                element = document.getElementById('em_upload_button');
                element.setAttribute('cnumber', handlerArgs[0].container_number);

                dialog.showDialogPane();
            }
        }
    };

    ilios.ui.onIliosEvent.subscribe(displayOnTriggerHandler);
}
