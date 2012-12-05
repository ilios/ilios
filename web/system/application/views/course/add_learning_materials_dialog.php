<?php
/**
 * Includes script for rigging the "learning materials picker" dialog up to the course management page.
 *
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_base.js
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 */

?>
<div class="tabdialog" id="add_learning_materials_dialog">
    <div class="hd"><?php echo $learning_materials_dialog_title; ?></div>
    <div class="bd">
        <div class="dialog_wrap" style="height: 600px">
<?php
    $attributes = array('id' => 'alm_upload_form');
    echo form_open_multipart('learning_materials/uploadLearningMaterial', $attributes);
?>
            <div>
                <input type="hidden" name="course_id" id="alm_cid_value" value="" />
                <input type="hidden" name="session_id" id="alm_sid_value" value="" />
                <input type="hidden" name="displayed_tab" id="alm_displayed_tab_value" value="" />
                <input type="hidden" name="full_file_path" id="alm_filepath_value" value="" />
                <div style="margin-bottom: 0px; position: relative; padding: 10px 0px 0px;">
                    <div id="add_type_tab_3" style="float: right;
                        border-top: 1px #999999 solid; border-right: 1px #999999 solid; padding: 2px 5px;">
                        <a href="" onclick="ilios.cm.lm.handleLearningMaterialAddTypeClick(3); blur(); return false;">
                            <?php echo $word_citation_string; ?></a>
                    </div>
                    <div id="add_type_tab_2" style="float: right;
                        border-top: 1px #999999 solid; border-right: 1px #999999 solid; padding: 2px 5px;">
                        <a href="" onclick="ilios.cm.lm.handleLearningMaterialAddTypeClick(2); blur(); return false;">
                            <?php echo $word_link_string; ?></a>
                    </div>
                    <div id="add_type_tab_1" style="float: right;
                        border-top: 2px #000000 solid; border-left: 2px #000000 solid;
                        border-right: 2px #000000 solid; padding: 2px 5px;">
                        <a href="" onclick="ilios.cm.lm.handleLearningMaterialAddTypeClick(1); blur(); return false;">
                            <?php echo $word_file_string; ?></a>
                    </div>
                    <div id="add_type_tab_0" style="float: right;
                        border-top: 2px #000000 solid; border-left: 2px #000000 solid;
                        border-right: 2px #000000 solid; padding: 2px 5px;">
                        <a href="" onclick="ilios.cm.lm.handleLearningMaterialAddTypeClick(0); blur(); return false;">
                            <?php echo $word_search_string; ?></a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div id="alm_tab_pane"></div>

<?php
    // done as an 'echo' here to avoid witty IDEs complaining about a closing tag without
    // an opening one (the opening tag is generated in the php block above
    echo "</form>\n";
?>
            </div>
            <div id="alm_transaction_status"
                style="position: absolute; left: 12px; bottom: 2px; color: #800000; font-weight: bold;"></div>
        </div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">

    ilios.cm.lm.learningMaterialDialog = null;
    ilios.cm.lm.learningMaterialStatuses = [];
    ilios.cm.lm.learningMaterialOwnerRoles = [];

    ilios.cm.lm.almLearningMaterialModel = null;

    ilios.cm.lm.buildStatusAndOwnerRoleStructures = function () {
<?php
    foreach ($learning_material_statuses as $status) :
?>
        ilios.cm.lm.learningMaterialStatuses["<?php echo $status["learning_material_status_id"]; ?>"]
            = "<?php echo $status["title"]; ?>";
<?php
    endforeach;
    foreach ($learning_material_roles as $role) :
?>
            ilios.cm.lm.learningMaterialOwnerRoles["<?php echo $role["learning_material_user_role_id"]; ?>"]
                    = "<?php echo $role["title"]; ?>";
<?php
    endforeach;
?>
    };

    ilios.cm.lm.finishAddLearningMaterialDialogCancel = function (clickButton) {
        this.cancel();
        ilios.cm.lm.almLearningMaterialModel = null;
        ilios.cm.lm.learningMaterialDialog.cancel();
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
    ilios.cm.lm.assembleAddLearningMaterialsDialog = function (use, less, args) {
        var handleCancel = function () {
            if (ilios.cm.lm.learningMaterialLightboxIsDirty
                && (1 == ilios.cm.lm.currentlyShowingTab)) { // only pop up the confirmation dialog on the "file" tab
                var dirtyStr = ilios_i18nVendor.getI18NString('learning_material.warning.dirty');
                var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

                ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
                    + '</p><center><b>' + continueStr + '</b></center>'),
                    yesStr, ilios.cm.lm.finishAddLearningMaterialDialogCancel);
            } else {
                ilios.cm.lm.almLearningMaterialModel = null;
                this.cancel();
            }
        };
        var buttonArray = [
            {
                text: "<?php echo $word_done_string; ?>",
                handler: handleCancel,
                isDefault: true
            }
        ];
        var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "840px";
        var dialog = null;
        var displayOnTriggerHandler = null;

        ilios.cm.lm.buildStatusAndOwnerRoleStructures();

        dialog = new YAHOO.widget.Dialog('add_learning_materials_dialog',{
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
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

        if (args['submit_override'] != null) {
            dialog.cfg.setProperty('postmethod', 'manual');
            dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
        }

        // register a listener on some page element to trigger the display of this popup dialog
        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].action == 'alm_dialog_open') {
                dialog.cnumber = handlerArgs[0].container_number;

                dialog.showDialogPane();
            }
        };
        IEvent.subscribe(displayOnTriggerHandler);

        ilios.cm.lm.learningMaterialDialog = dialog;
    }
</script>
