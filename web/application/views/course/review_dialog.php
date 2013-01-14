<?php

/**
 * DEPENDENCIES:
 *      YUI toolkit
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 */

?>

        <div class="tabdialog" id="review_dialog">
            <div class="hd" id="review_dialog_title">XXXX</div>
            <div class="bd" style="position: relative;">
                <div class="dialog_wrap" id="r_dialog_wrap">
                    <form action="no matter">
                        <h2 id="review_heading">
                            Course title or whatever goes here
                        </h2>
                        <p id="course_level"></p>
                        <div id="full_review">
                            <div id="full_review_course_div">
                            </div>
                            <div id="full_review_session_div">
                            </div>
                        </div>

                        <div id="course_review">
                            <div id="course_review_course_div"></div>
                        </div>

                        <div id="session_review">
                            <div id="session_review_session_div"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        ilios.cm.reviewDialog = null;

        // The code which populates the view, showing checkboxes where appropriate, should put each
        //      HTMLElement which in this [what should be an] array
        ilios.cm.reviewDialogObjects = null;

        ilios.cm.assembleReviewDialog = function (use, less, args) {
            var handleSave = function () {
                this.cancel();

                ilios.cm.transaction.continueMixedPublish();
            };

            var handleCancel = function () {
                this.cancel();
            };

            var cancelStr = ilios_i18nVendor.getI18NString('course_management.return_to_course');
            var saveStr = ilios_i18nVendor.getI18NString('general.phrases.publish_now');
            var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                               {text: cancelStr, handler: handleCancel}];
            var panelWidth = "600px";
            var displayOnTriggerHandler = null;
            var dialog = new YAHOO.widget.Dialog('review_dialog',
                                                 { width: panelWidth, modal: true,
                                                     visible: false, constraintoviewport: false,
                                                     buttons: buttonArray });

            dialog.showDialogPane = function () {
                dialog.center();
                dialog.show();
            };

            dialog.render();

            displayOnTriggerHandler = function (type, handlerArgs) {
                if (handlerArgs[0].action == 'review_dialog_open') {
                    var containerNumber = handlerArgs[0].cnumber;

                    switch (handlerArgs[0].review_type) {
                        case 0:         // full
                            ilios.cm.populateReviewForFullReview();
                            break;
                        case 1:         // course only
                            ilios.cm.populateReviewForCourseReview();

                            break;
                        case 2:         // session only
                            ilios.cm.populateReviewForSessionReviewForContainer(containerNumber);

                            break;
                    }
                    dialog.showDialogPane();
                }
            };
            IEvent.subscribe(displayOnTriggerHandler);

            ilios.cm.reviewDialog = dialog;
        }

        </script>
