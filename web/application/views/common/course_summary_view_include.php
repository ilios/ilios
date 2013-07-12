<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 *      scripts/public_course_summary_base_framework.js
 */
?>
<div class="tabdialog" id="course_summary_view_dialog">
    <div class="hd">&nbsp;</div>
    <div class="bd">
        <div class="dialog_wrap">
            <form method="get" action="#">
                <div id="course_summary_view_content_div"></div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">
    ilios.course_summary.courseSummaryViewDialog = null;

    ilios.course_summary.assembleCourseSummaryViewDialog = function (use, less, args) {
        var handleCancel = function () {
            this.cancel();
        };
        var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');
        var buttonArray = [
            {text: doneStr, handler: handleCancel, isDefault: true}
        ];
        var dialog = new YAHOO.widget.Dialog('course_summary_view_dialog', {
            width: "538px",
            y: 0,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray,
            zIndex: 5
        });

        dialog.showDialogPane = function () {
            this.cfg.setProperty("x", Math.floor((YAHOO.util.Dom.getViewportWidth() - 538) / 2));
            this.show();
        };

        // Render the Dialog
        dialog.render();

        ilios.course_summary.courseSummaryViewDialog = dialog;
    };

    YAHOO.util.Event.onDOMReady(ilios.course_summary.assembleCourseSummaryViewDialog, {});
</script>
