<?php
/**
 * Includes-template.
 *
 * Renders the course search dialog.
 *
 * @see application/views/course/course_manager.php
 * @todo: consolidate with program search widget
 *
 * DEPENDENCIES:
 *     YUI toolkit
 *     scripts/ilios_dom.js
 *     scripts/ilios_utilities.js
 */
?>
<div class="tabdialog" id="course_search_picker">
    <div class="hd"><?php echo $course_search_title; ?></div>
    <div class="bd">
        <span id="course_search_status" class="search-status"></span>
        <div class="dialog_wrap">
            <form method="GET" action="#">
                <fieldset>
                    <legend>Search Term</legend>
                    <input type="text" id="course_search_terms" name="course_search_terms" size="50"
                           onkeypress="return ilios.cm.cs.handleCourseSearchFieldInput(event);" placeholder = "<?php echo $generic_search_hint ?>">
                    <div class="search_icon_button"
                         onclick="ilios.cm.cs.performCourseSearch(document.getElementById('course_search_terms').value);"></div>
                </fieldset>
                <fieldset>
                    <legend>Search Results</legend>
                    <div class="scroll_list clearfix">
                        <ul id="course_search_results_list" class="search-results">
                            <li></li>
                        </ul>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">
    ilios.cm.cs.courseSearchDialog = null;
    ilios.cm.cs.buildCourseSearchDialogDOM = function () {
        var handleCancel = function () {
            this.cancel();
        };
        var handleReset = function(){
            ilios.cm.cs.emptySearchDialogForViewing();
            return false;
        }

        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var resetStr = ilios_i18nVendor.getI18NString('general.phrases.search.clear');

        var buttonArray = [
                {text: cancelStr, handler: handleCancel},
                {text: resetStr, handler: handleReset}
            ];

        var panelWidth = "600px";
        var dialog = new YAHOO.widget.Dialog('course_search_picker', {
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
        });

        dialog.showDialogPane = function () {
            ilios.cm.cs.emptySearchDialogForViewing();
            dialog.center();
            dialog.show();
        };

        dialog.render();
        ilios.cm.cs.courseSearchDialog = dialog;
    };

    // @private
    ilios.cm.cs.handleCourseSearchFieldInput = function (event) {
        var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
        //var element = new YAHOO.util.Element(document.getElementById('search_term_hint_text'));

        //if (element.getStyle('display') != 'none') {
        //    element.setStyle('display', 'none');
        //}

        if (charCode == 13) {
            var elem = document.getElementById('course_search_terms');
            ilios.cm.cs.performCourseSearch(elem.value);
            event.cancelBubble = true;
            event.returnValue = false;
            return false;
        }
        return true;
    }
    YAHOO.util.Event.onDOMReady(ilios.cm.cs.buildCourseSearchDialogDOM);
</script>
