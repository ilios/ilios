<?php
/**
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */

?>
<div class="tabdialog" id="edit_objective_text_dialog">
    <div class="hd"><?php echo $edit_objective_dialog_title; ?></div>
    <div class="bd">
        <form action="#">
            <div>
                <textarea id="eot_textarea" style="width:99%; height: 220px; margin-left: 2px;"></textarea>
            </div>
            <div style="margin-top: 6px; margin-left: 6px;">
                 <?php echo $select_competency; ?>
                 <br/>
                 <select id="eot_competency_pulldown" style="margin-top: 6px; margin-left: 32px; width: 680px;"></select>
            </div>
        </form>
    </div>
</div><!-- end #edit_objective_text_dialog -->

<script type="text/javascript">

    ilios.namespace('pm.eot');

    ilios.pm.eot.inEditObjectiveModel = null;

    ilios.pm.eot.editObjectiveTextDialog = null;


    // @private
    ilios.pm.eot.finishEditObjectiveTextDialogCancel = function (clickButton) {
        this.cancel();
        ilios.pm.eot.editObjectiveTextDialog.cancel();
    };

    /**
     * @param args
     *
     * submit_override : if this is non-null the function is subscribed to
     *                   the manual submit event of the dialog and the
     *                   dialog is configured to fire that event when the
     *                   user clicks the 'Done' button on the dialog.
     * container :       the container listing the present possible choices
     *                   (as possibly filtered via autocompleter input)
     * display_handler : a function which is called just prior to the dialog
     *                   being displayed
     * dom_root :        the id of the DOM element into which this markup
     *                   should be inserted
     */
    ilios.pm.eot.assembleEditObjectiveTextDialog = function (use, less, args) {
        var handleSave = function () {
            var isNew = false;
            var currentText = document.getElementById('eot_textarea').value;
            var competencyPulldown = document.getElementById('eot_competency_pulldown');
            var currentCompetency = competencyPulldown.selectedIndex;
            var currentCompetencyId = competencyPulldown.options[currentCompetency].value;

            if (! ilios.pm.eot.inEditObjectiveModel) {
                isNew = true;
                ilios.pm.eot.inEditObjectiveModel = new ObjectiveModel();
            }

            ilios.pm.eot.inEditObjectiveModel.setDescription(currentText);
            ilios.pm.eot.inEditObjectiveModel.setCompetencyId(currentCompetencyId);

            // add new objective model to DOM
            if (isNew) {
                ilios.pm.addNewObjectiveToDom(dialog.cnumber, ilios.pm.eot.inEditObjectiveModel);
            }
            ilios.pm.eot.editObjectiveTextDialog.cancel();
        };

        var handleCancel = function () {
            var currentText = this.eotEditor.cleanHTML();
            var competencyPulldown = document.getElementById('eot_competency_pulldown');
            var currentCompetency = competencyPulldown.selectedIndex;
            var currentCompetencyId = competencyPulldown.options[currentCompetency].value;

            if (ilios.pm.eot.inEditObjectiveModel
                && ((ilios.pm.eot.inEditObjectiveModel.getDescription() != currentText)
                    || (ilios.pm.eot.inEditObjectiveModel.getCompetencyId() != currentCompetencyId))
            ) {
                var dirtyStr = ilios_i18nVendor.getI18NString('program_management.warning.dirty_objective');
                var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
                    + '</p><center><b>' + continueStr + '</b></center>'),
                    yesStr, ilios.pm.eot.finishEditObjectiveTextDialogCancel);
                return;
            }
            this.cancel();
        };

        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var saveStr = ilios_i18nVendor.getI18NString('general.terms.done');
        var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                           {text: cancelStr, handler: handleCancel}];
        var panelWidth = "740px";
        var displayOnTriggerHandler = null;
        var eotEditor = new ilios.ui.RichTextEditor('eot_textarea');
        eotEditor.render();

        var dialog = new YAHOO.widget.Dialog(args['dom_root'], {
            width : panelWidth,
            modal: true,
            visible : false,
            constraintoviewport: false,
            buttons: buttonArray
        });

        dialog.eotEditor = eotEditor;

        dialog.showDialogPane = function () {
            var displayHandler = args['display_handler'];
            if (displayHandler != null) {
                displayHandler(dialog);
            }
            dialog.center();
            dialog.show();
            dialog.eotEditor.focus();
        };

        // Render the Dialog
        dialog.render();

        if (args['submit_override'] != null) {
            dialog.cfg.setProperty('postmethod', 'manual');
            dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
        }

        // register a listener on some page element to trigger the display of this popup dialog
        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].action == 'eot_dialog_open') {
                dialog.cnumber = handlerArgs[0].cnumber;
                dialog.showDialogPane();
            }
        };
        IEvent.subscribe(displayOnTriggerHandler);

        ilios.pm.eot.editObjectiveTextDialog = dialog;
    }
</script>
