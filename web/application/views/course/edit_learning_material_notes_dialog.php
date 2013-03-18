<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 */

?>

        <div class="tabdialog" id="edit_learning_material_notes_dialog">
            <div class="hd"><?php echo $edit_learning_material_notes_dialog_title ?></div>

            <div class="bd">
                <div class="dialog_wrap" id="elmn_dialog_wrap">
                    <form action="no matter">
                        <textarea id="elmn_textarea" style="width: 100%; height: 210px;" ></textarea>
                        <input type="checkbox" id="learning_material_notes_viewable_checkbox" />
                        <label for="learning_material_notes_viewable_checkbox">
                            <?php echo $edit_learning_materials_hide_notes ?>
                        </label>
                    </form>
                </div>
            </div>
            <div class="ft"></div>
        </div>

        <script type="text/javascript">

        ilios.cm.editLearningMaterialNotesDialog = null;

        // @private
        ilios.cm.finishEditLearningMaterialNotesDialogCancel = function (clickButton) {
            this.cancel();

            ilios.cm.editLearningMaterialNotesDialog.cancel();
        };

        /**
         * @param args
         *          submit_override:            if this is non-null the function is subscribed to
         *                                          the manual submit event of the dialog and the
         *                                          dialog is configured to fire that event when the
         *                                          user clicks the 'Done' button on the dialog.
         *          container:                  the container listing the present possible choices
         *                                          (as possibly filtered via autocompleter input)
         *          display_handler:            a function which is called just prior to the dialog
         *                                          being displayed
         *          panel_width:                if this is non-null, it is expected to a valid CSS
         *                                          measurement, else '880px' will be used.
         */
        ilios.cm.assembleEditLearningMaterialNotesDialog = function (use, less, args) {
            var handleSave = function () {
                var currentText = document.getElementById('elmn_textarea').value;
                var element = document.getElementById('ilios_lm_notes');
                var containerNumber = ilios.common.lm.learningMaterialsDetailsDialog.cnumber;
                var model = null;

                if (element != null) {
                    element.innerHTML = currentText;
                }

                ilios.common.lm.learningMaterialsDetailsModel.setNotes(currentText);

                element = document.getElementById('learning_material_notes_viewable_checkbox');
                ilios.common.lm.learningMaterialsDetailsModel.setNotesArePubliclyViewable(! element.checked);

                if (containerNumber == -1) {
                    model = ilios.cm.currentCourseModel;
                }
                else {
                    model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
                }
                model.setDirtyAndNotify();

                ilios.cm.editLearningMaterialNotesDialog.cancel();
            };

            var handleCancel = function () {
                var currentText = document.getElementById('elmn_textarea').value;

                if (ilios.common.lm.learningMaterialsDetailsModel.getNotes() != currentText) {
                    var dirtyStr
                      = ilios_i18nVendor.getI18NString('learning_material.warning.dirty_notes');
                    var continueStr
                               = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

                    ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
                                    + '</p><center><b>' + continueStr + '</b></center>'),
                            yesStr, ilios.cm.finishEditLearningMaterialNotesDialogCancel);
                }
                else {
                    this.cancel();
                }
            };

            var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
            var saveStr = "<?php echo $word_done_string ?>";
            var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                               {text: cancelStr, handler: handleCancel}];
            var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "740px";
            var dialog = null;
            var displayOnTriggerHandler = null;

            dialog = new YAHOO.widget.Dialog('edit_learning_material_notes_dialog',
                                             {width: panelWidth,
                                              modal: true, visible: false,
                                              constraintoviewport: false,
                                              buttons: buttonArray});

            dialog.elmnEditor = new ilios.ui.RichTextEditor('elmn_textarea');
            dialog.elmnEditor.render();

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

            if (args['submit_override'] != null) {
                dialog.cfg.setProperty('postmethod', 'manual');
                dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
            }

            // register a listener on some page element to trigger the display of this popup dialog
            displayOnTriggerHandler = function (type, handlerArgs) {
                if (handlerArgs[0].action == 'elmn_dialog_open') {
                    dialog.showDialogPane();
                }
            };
            IEvent.subscribe(displayOnTriggerHandler);

            ilios.cm.editLearningMaterialNotesDialog = dialog;
        };

        </script>
