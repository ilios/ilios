<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MeSH picker dialog code and markup.
 *
 * DEPENDENCIES:
 *   YUI toolkit
 *   scripts/ilios_dom.js
 *   scripts/ilios_utilities.js
 *   scripts/models/mesh_item_model.js
 *   scripts/mesh_base_framework.js
 *
 * Users of this code should make sure they implement the following methods:
 *             ilios.common.picker.mesh.handleMeSHPickerSave(dialogPanel)
 */
?>
<div class="tabdialog" id="ilios_mesh_picker">
    <div class="hd"><?php echo $mesh_dialog_title; ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <div class="dialog_left">
                <ul id="mesh_descriptors_picked" class="picked"></ul>
            </div>
            <div class="dialog_right">
                <form method="GET" action="#">
                    <div class="yui-navset yui-navset-top" id="tabbed_view_mesh">
                        <ul class="yui-nav">
                            <li class="selected" title="active">
                            <a href="#mesh_results_tab"><em><?php echo $mesh_search_mesh; ?></em></a>
                            </li>
                        </ul>
                        <div class="yui-content">
                            <div id="mesh_results_tab">
                                <input type="text" name="mesh_search_terms" id="mesh_search_terms" style="width: 80%">
                                <button class="tiny radius button" onclick="ilios.common.picker.mesh.handleMeSHSearch(event);"><?php echo $word_search_string; ?></button>
                                <div id="mesh_search_results"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </form>
            </div>
        </div>
        <div class="clear"></div>
        <div id="mesh_search_status" style="position: absolute; left: 12px; bottom: 10px; height: 18px; font-weight: bold; font-size: 9pt; color: #aa3241;"></div>
    </div>
    <div class="ft"></div>
</div>
<div class="clear"></div>
<script type="text/javascript">
    ilios.namespace('common.picker.mesh');

    ilios.common.picker.mesh.buildMeSHPickerDialogDOM = function () {
        var handleSave = function () {
            //because lm mesh terms can be save from within their 'Details' dialog - OR - the
            //standalone mesh picker dialog, we check for the latter and handle it
            if(this.dialog_type == 'learning_material_mesh_only'){
                var lmModel = ilios.mesh.meshInEditReferenceModel;
                lmModel.replaceContentWithModel(ilios.mesh.meshInEditModel, true);
                var cnumber = this.cnumber;
                var lmnumber = this.lmnumber;
                var isCourse = (cnumber == -1);
                var lmDbId = ilios.common.lm.learningMaterialsDetailsModel.getDBId();
                var model = isCourse ? ilios.cm.currentCourseModel
                    : ilios.cm.currentCourseModel.getSessionForContainer(cnumber);
                var courseOrSessionDbId = model.dbId;
                ilios.cm.transaction.updateLearningMaterial(model, lmDbId, isCourse,
                    courseOrSessionDbId, cnumber, lmnumber);
            } else {
                ilios.common.picker.mesh.handleMeSHPickerSave(this);
            }
            this.cancel();
        };

        var handleCancel = function () {
            ilios.mesh.handleMeSHPickerCancel(this);
            this.cancel();
        };

        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var saveStr = ilios_i18nVendor.getI18NString('general.terms.done');
        var buttonArray = [
            {text: saveStr, handler: handleSave, isDefault: true},
            {text: cancelStr, handler: handleCancel}
        ];

        var panelWidth = "730px";
        var dialog = new YAHOO.widget.Dialog('ilios_mesh_picker', {
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
        });

        var displayOnTriggerHandler = null;

        dialog.showDialogPane = function () {
            ilios.mesh.populateMeSHPickerDialog();
            dialog.center();
            dialog.show();
        };

        // Render the Dialog
        dialog.render();

        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].action == 'mesh_picker_dialog_open') {
                ilios.mesh.meshInEditReferenceModel = handlerArgs[0].model_in_edit;
                ilios.mesh.meshInEditModel = ilios.mesh.meshInEditReferenceModel.clone();
                dialog.cnumber = handlerArgs[0].cnumber;
                dialog.lmnumber = handlerArgs[0].lmnumber;
                dialog.dialog_type = handlerArgs[0].dialog_type;
                dialog.showDialogPane();
            }
        };

        ilios.ui.onIliosEvent.subscribe(displayOnTriggerHandler);

        ilios.mesh.meshPickerDialog = dialog;
    };

    // @private
    ilios.common.picker.mesh.handleMeSHSearchFieldInput = function (inputField, event) {
        var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);

        if (charCode == 13) {
            var elem = document.getElementById('mesh_search_terms');
            ilios.mesh.performMeSHSearch(elem.value, true, true);
            event.cancelBubble = true;
            event.returnValue = false;
            return false;
        }
        return true;
    }
    ilios.common.picker.mesh.handleMeSHSearch = function (e) {
            YAHOO.util.Event.preventDefault(e);
            var elem = document.getElementById('mesh_search_terms');
            ilios.mesh.performMeSHSearch(elem.value, true, true);
//            event.cancelBubble = true;
//            event.returnValue = false;
//            return true;
//        return true;
    }

    YAHOO.util.Event.onDOMReady(ilios.common.picker.mesh.buildMeSHPickerDialogDOM);
</script>
