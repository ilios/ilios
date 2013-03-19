<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 *
 * Users of this code should make sure they implement a method:
 *          ilios.common.lm.populateLearningMaterialMetadataDialog(containerNumber, learningMaterialId)
 *
 * Also, the CodeIgniter controller must make sure to populate the $data associate array with
 *      the key 'learning_material_statuses' -- see the course_management controller for an
 *      example.
 *
 * TODO there is still dependency on CM in here (temporarily fine since no other code outside CM
 *              uses this)
 */

?>

        <div class="tabdialog" id="ilios_learning_material_lightbox">

            <div class="hd"><?php echo $learning_materials_metadata_title ?></div>
            <div class="bd">
                <div class="dialog_wrap" id="ilios_learning_material_lightbox_wrap">
                    <form method='GET' action="#">
                        <fieldset>
                            <legend>Learning Material Details</legend>
                            <div class="row no-action">
                                <div class="column label">
                                    <label>
                                        <?php echo $learning_materials_asset_title ?>:
                                    </label>
                                </div>
                                <div class="column data">
                                    <span id="ilios_lm_display_name" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $word_status_string ?>:</label>
                                </div>
                                <div class="column data">
                                    <select id="lm_meta_statuses_selector">
    <?php
                foreach ($learning_material_statuses as $status) {
    ?>
                                        <option value="<?php echo $status['learning_material_status_id'] ?>"><?php echo $status['title'] ?></option>
    <?php
                }
    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $word_owner_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_owner_name" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $phrase_owner_role_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_owner_role" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $learning_materials_asset_creator ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_creator" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action alm_file_div">
                                <div class="column label">
                                    <label for=""><?php echo $word_filename_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_file_name" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action alm_file_div">
                                <div class="column label">
                                    <label for=""><?php echo $phrase_file_size_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_file_size" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action alm_file_div">
                                <div class="column label">
                                    <label for=""><?php echo $phrase_file_type_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_file_type" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action alm_link_div">
                                <div class="column label">
                                    <label for=""><?php echo $word_link_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <a href='' target=_new id="ilios_lm_link_value"></a>
                                </div>
                            </div>
                            <div class="row no-action alm_citation_div">
                                <div class="column label">
                                    <label for=""><?php echo $word_citation_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <div id="ilios_lm_citation_value" class="read_only_data scroll_list"></div>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $phrase_upload_date_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <span id="ilios_lm_upload_date" class="read_only_data"></span>
                                </div>
                            </div>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for=""><?php echo $word_description_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <div id="ilios_lm_description" class="read_only_data scroll_list"></div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Usage Details</legend>
                            <div class="row no-action">
                                <div class="column label">
                                    <label for="ilios_lm_required_checkbox"><?php echo $word_required_string ?></label>
                                </div>
                                <div class="column data">
                                   <input type='checkbox' id="ilios_lm_required_checkbox">
                                </div>
                            </div>
                            <div class="row">
                                <div class="column label">
                                    <label for=""><?php echo $phrase_mesh_terms_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <div id="ilios_lm_mesh" class="read_only_data scroll_list"></div>
                                </div>
                                <div class="action">
                                   <button id="ilios_lm_mesh_link" class="tiny secondary radius button" onclick="return false;"><?php echo $word_edit_string  ?></button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="column label">
                                    <label for=""><?php echo $word_notes_string ?>:</label>
                                </div>
                                <div class="column data">
                                   <div id="ilios_lm_notes" class="read_only_data scroll_list"></div>
                                </div>
                                <div class="column actions">
                                   <button id="ilios_lm_notes_link" class="tiny secondary radius button" onclick="return false;"><?php echo $word_edit_string  ?></button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="ft"></div>
        </div>

        <script type="text/javascript">
            ilios.namespace('common.lm');

            ilios.common.lm.learningMaterialsDetailsDialog = null;
            ilios.common.lm.learningMaterialsDetailsModel = null;

            // @private
            ilios.common.lm.buildLearningMaterialLightboxDOM = function () {
                var Event = YAHOO.util.Event;
                var element = null;

                var handleCancel = function () {
                    this.cancel();
                };

                var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');

/*
 * There is a save modification case, when the LM author is the present authenticated user -- TODO

                var handleSave = function () {
                    validateLightboxSave(this);
                };
                var saveStr = ilios_i18nVendor.getI18NString('general.terms.save');
                var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                                   {text: doneStr, handler: handleCancel}];
*/

                var buttonArray = [{text: doneStr, handler: handleCancel}];

                var panelWidth = "620px";
                var dialog = new YAHOO.widget.Dialog('ilios_learning_material_lightbox',
                                                     {width: panelWidth, modal: true, visible: false,
                                                      constraintoviewport: false, buttons: buttonArray});
                var displayOnTriggerHandler = null;

                dialog.showDialogPane = function () {
                    ilios.common.lm.populateLearningMaterialMetadataDialog(
                                                    ilios.common.lm.learningMaterialsDetailsModel);

                    dialog.center();
                    dialog.show();
                };

                // Render the Dialog
                dialog.render();

                displayOnTriggerHandler = function (type, handlerArgs) {
                    if (handlerArgs[0].action == 'lm_metadata_dialog_open') {
                        dialog.cnumber = handlerArgs[0].container_number;

                        dialog.showDialogPane();
                    }
                };
                IEvent.subscribe(displayOnTriggerHandler);

                ilios.common.lm.learningMaterialsDetailsDialog = dialog;

                element = document.getElementById('ilios_lm_required_checkbox');
                Event.addListener(element, 'click', function (e) {
                    var toggle = (! ilios.common.lm.learningMaterialsDetailsModel.isRequired());
                    var containerNumber = ilios.common.lm.learningMaterialsDetailsDialog.cnumber;
                    var model = null;

                    if (containerNumber == -1) {
                        model = ilios.cm.currentCourseModel;
                    } else {
                        model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
                    }

                    ilios.common.lm.learningMaterialsDetailsModel.setRequired(toggle);

                    model.setDirtyAndNotify();
                });

                element = document.getElementById('ilios_lm_mesh_link');
                Event.addListener(element, 'click', function (e) {
                    IEvent.fire({
                        action: 'mesh_picker_dialog_open',
                        model_in_edit: ilios.common.lm.learningMaterialsDetailsModel
                    });
                    return false;
                });

                element = document.getElementById('ilios_lm_notes_link');
                Event.addListener(element, 'click', function (e) {
                    IEvent.fire({
                        action: 'elmn_dialog_open'
                    });
                    return false;
                });
            };

            YAHOO.util.Event.onDOMReady(ilios.common.lm.buildLearningMaterialLightboxDOM);

        </script>
