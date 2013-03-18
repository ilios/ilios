<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 */

?>

        <div class="tabdialog" id="rollover_dialog">
            <div class="hd" id="rollover_dialog_title">
                <script type="text/javascript">
                    ilios_i18nVendor.write('', 'course_management.rollover.title', '');
                </script>
            </div>
            <div class="bd">
                <div class="dialog_wrap" id="a_dialog_wrap">
                    <form action="#">
                        <div id="rollover_dialog_content"></div>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">



        ilios.cm.rolloverDialog = null;

        ilios.cm.assembleRolloverDialog = function (use, less, args) {
            var handleSave = function () {
                ilios.cm.rollover.progressRolloverWorkflow();
            };

            var handleCancel = function () {
                this.cancel();
            };

            var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
            var saveStr = ilios_i18nVendor.getI18NString('general.terms.continue');
            var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                               {text: cancelStr, handler: handleCancel}];
            var panelWidth = "790px";
            var displayOnTriggerHandler = null;
            var dialog = new YAHOO.widget.Dialog('rollover_dialog',
                                                 { width: panelWidth, modal: true,
                                                     visible: false, constraintoviewport: false,
                                                     buttons: buttonArray });

            dialog.showDialogPane = function () {
                dialog.center();
                dialog.show();
            };

            dialog.render();

            displayOnTriggerHandler = function (type, handlerArgs) {
                if (handlerArgs[0].action == 'rollover_dialog_open') {
                    ilios.cm.rollover.prepareForRolloverWorkflow();

                    dialog.showDialogPane();
                }
            };
            IEvent.subscribe(displayOnTriggerHandler);

            ilios.cm.rolloverDialog = dialog;
        }

        </script>
