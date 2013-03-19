<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */
?>
<div class="tabdialog" id="learner_view_dialog">
    <div class="hd"></div>
    <div class="bd">
        <div class="dialog_wrap">
            <form method='get' action='#'>
                <div id="learner_view_content_div"></div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">
    ilios.namespace('om.learner');

    ilios.om.learner.assembleLearnerViewDialog = function (use, less, args) {
        var handleCancel = function () {
            this.cancel();
        };
        var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');
        var buttonArray = [
            {text: doneStr, handler: handleCancel, isDefault: true}
        ];
        var panelWidth = '510px';
        var displayOnTriggerHandler = null;
        var element = null;
        var dialog = new YAHOO.widget.Dialog('learner_view_dialog', {
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
        });
        var title = ilios_i18nVendor.getI18NString("offering_management.learner_view_dialog_title");

        dialog.setHeader(title);

        dialog.showDialogPane = function () {
            dialog.center();
            dialog.show();
        };

        // Render the Dialog
        dialog.render();

        // register a listener on some page element to trigger the display of this popup dialog
        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].action == 'lv_dialog_open') {
                var model = handlerArgs[0].model;
                var container = document.getElementById('learner_view_content_div');

                ilios.ui.renderIndeterminateInView(container);

                dialog.showDialogPane();

                if ((model == null) || (typeof model == 'undefined')) {
                    model = ilios.om.calendar.currentlySelectedIliosModel;
                }

                if (model.isIndependentLearningEvent()) {
                    ilios.learner_view.loadSILMDetailsIntoDivContainer(model.getSessionId(), container, false);
                } else {
                    ilios.learner_view.loadOfferingDetailsIntoDivContainer(model.getDBId(), container, false);
                }
            }
        };

        IEvent.subscribe(displayOnTriggerHandler);

        ilios.learner_view.learnerViewDialog = dialog;

        element = document.getElementById('inspector_pane_open_learner_view');

        if (element != null) {
            element = new YAHOO.util.Element(element);
            element.addListener('click', function (e) {
                IEvent.fire({action: 'lv_dialog_open'});
            }, null, this);
        }
    };
</script>
