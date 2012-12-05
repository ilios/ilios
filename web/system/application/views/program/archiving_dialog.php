<?php
/**
 * DEPENDENCIES:
 * 		YUI toolkit
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_utilities.js
 */
?>

		<div class="tabdialog" id="archiving_dialog">
			<div class="hd" id="archiving_dialog_title">&nbsp;</div>
            <div class="bd">
                <div class="dialog_wrap" id="a_dialog_wrap">
					<form>
                        <script type="text/javascript">
                            ilios_i18nVendor.write('', 'program_management.archiving.text_1', ' ');
                        </script>
                        <h4 id="archiving_dialog_academic_year"></h4>
                        <script type="text/javascript">
                            ilios_i18nVendor.write(' ', 'program_management.archiving.text_2', '');
                        </script>

                        <ul class="no-bullets margin-l">
                            <li>
                                <input type="radio" name="archive_radio" id="archive_radio_lock" checked />
                                <script type="text/javascript">
                                    ilios_i18nVendor.write('', 'program_management.archiving.lock_option', '');
                                </script>
                            </li>
                            <li>
                                <input type="radio" name="archive_radio" id="archive_radio_lock_and_archive" />
                                <script type="text/javascript">
                                    ilios_i18nVendor.write('', 'program_management.archiving.lock_and_archive_option', '');
                                </script>
                        </ul>
					</form>
				</div>
			</div>
			<div class="ft"></div>
		</div>

		<script type="text/javascript">



		ilios.pm.archivingDialog = null;

		 ilios.pm.assembleArchivingDialog = function (use, less, args) {
			var handleSave = function () {
                ilios.pm.transaction.lockArchiveProgramYear();
			};

			var handleCancel = function () {
				this.cancel();
			};

			var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
			var saveStr = ilios_i18nVendor.getI18NString('general.terms.continue');
			var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
				               {text: cancelStr, handler: handleCancel}];
			var panelWidth = "470px";
			var displayOnTriggerHandler = null;
			var dialog = new YAHOO.widget.Dialog('archiving_dialog',
                                                 { width: panelWidth, modal: true,
                                                     visible: false, constraintoviewport: false,
                                                     buttons: buttonArray });

			dialog.showDialogPane = function () {
                var element = document.getElementById('archive_radio_lock');

                element.checked = 'checked';

				dialog.center();
				dialog.show();
			};

			dialog.render();

			displayOnTriggerHandler = function (type, handlerArgs) {
				if (handlerArgs[0].action == 'archive_dialog_open') {
                    var element = null;
                    var model = null;

                    dialog.cnumber = handlerArgs[0].container_number;

                    model
                        = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(dialog.cnumber);

                    // MAY RETURN THIS BLOCK
                    if (model.isLocked()) {
                        ilios.pm.transaction.lockArchiveProgramYear();

                        return;
                    }

                    element = document.getElementById('archiving_dialog_academic_year');
                    element.innerHTML = "" + model.getStartYear() + "-"
                                           + (model.getStartYear() + 1);

					dialog.showDialogPane();
				}
			};
			IEvent.subscribe(displayOnTriggerHandler);

			ilios.pm.archivingDialog = dialog;
		}

		</script>
