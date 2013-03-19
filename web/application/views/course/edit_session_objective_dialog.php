<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 */

?>

<div class="tabdialog" id="edit_session_objective_dialog">
    <div class="hd"><?php echo $edit_objective_dialog_title; ?></div>
    <div class="bd">
        <div class="dialog_wrap" id="eso_dialog_wrap" style="height: 276px;">
            <form action="no matter">
                <div>
                    <textarea id="eso_textarea" style="width: 99%; height: 112px; margin-left: 2px;" ></textarea>
                </div>
                <div style="margin-top: 6px; margin-left: 6px;">
                    <?php echo $select_parent_objectives_string; ?>
                    <div id="eso_parent_objectives_div"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">

    ilios.cm.session.editSessionObjectiveDialog = null;

    // @private
    ilios.cm.session.finishEditSessionObjectiveDialogCancel = function (clickButton) {
        this.cancel();
        ilios.cm.session.editSessionObjectiveDialog.cancel();
    };

    /**
     * @param args
     *      submit_override:
     *          if this is non-null the function is subscribed to
     *          the manual submit event of the dialog and the
     *          dialog is configured to fire that event when the
     *          user clicks the 'Done' button on the dialog.
     *      display_handler:
     *          a function which is called just prior to the dialog
     *          being displayed
     */
    ilios.cm.session.assembleEditSessionObjectiveDialog = function (use, less, args) {

        var handleSave = function () {
            var isNew = false;
            var currentText = document.getElementById('eso_textarea').value;

            var parentObjectives = ilios.cm.session.getArrayOfCurrentlySelectedSessionParentObjectives();

            if (! ilios.cm.inEditObjectiveModel) {
                isNew = true;
                ilios.cm.inEditObjectiveModel = new ObjectiveModel();

            }
            ilios.cm.inEditObjectiveModel.setDescription(currentText);
            ilios.cm.inEditObjectiveModel.setParentObjectives(parentObjectives);
            // add new objective model to DOM
            if (isNew) {
                ilios.cm.addNewSessionObjectiveToDom(dialog.cnumber, ilios.cm.inEditObjectiveModel);
            }
            ilios.cm.session.editSessionObjectiveDialog.cancel();
        };

        var handleCancel = function () {
            var currentText = document.getElementById('eso_textarea').value;
            var parentObjectives = ilios.cm.session.getArrayOfCurrentlySelectedSessionParentObjectives();


            if ((ilios.cm.inEditObjectiveModel)
                && ((ilios.cm.inEditObjectiveModel.getDescription() != currentText)
                    || (! equalObjectives))
            ) {
                var equalObjectives = ilios.utilities.simplyArrayEquality(parentObjectives,
                    ilios.cm.inEditObjectiveModel.getParentObjectives());
                var dirtyStr = ilios_i18nVendor.getI18NString('course_management.warning.dirty_objective');
                var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

                ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
                    + '</p><center><b>' + continueStr + '</b></center>'),
                    yesStr, ilios.cm.session.finishEditSessionObjectiveDialogCancel);
                return;
            }
            this.cancel();
        };

        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var saveStr = "<?php echo $word_done_string; ?>";
        var buttonArray = [
            {text: saveStr, handler: handleSave, isDefault: true},
            {text: cancelStr, handler: handleCancel}
        ];
        var panelWidth = "740px";
        var dialog = null;
        var displayOnTriggerHandler = null;

        dialog = new YAHOO.widget.Dialog('edit_session_objective_dialog', {
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
        });

        dialog.esoEditor = new ilios.ui.RichTextEditor('eso_textarea',{
            resize: false
        });
        dialog.esoEditor.render();

        dialog.showDialogPane = function () {
            var displayHandler = args['display_handler'];

            if (displayHandler != null) {
                displayHandler(dialog);
            }

            dialog.center();
            dialog.show();
            dialog.esoEditor.focus();
        };

        // Render the Dialog
        dialog.render();

        if (args['submit_override'] != null) {
            dialog.cfg.setProperty('postmethod', 'manual');
            dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
        }

        // register a listener on some page element to trigger the display of this popup dialog
        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].action == 'eso_dialog_open') {
                dialog.cnumber = handlerArgs[0].cnumber;
                dialog.showDialogPane();
            }
        };

        IEvent.subscribe(displayOnTriggerHandler);

        ilios.cm.session.editSessionObjectiveDialog = dialog;
    };
</script>
