<?php
/**
 * Includes-template.
 *
 * Renders the program search dialog.
 *
 * @see application/views/program/program_manager.php
 *
 * DEPENDENCIES:
 *     YUI toolkit
 *     scripts/ilios_dom.js
 *     scripts/ilios_utilities.js
 *     program/program_search_support.js
 */
?>
<div class="tabdialog" id="program_search_picker">
    <div class="hd"><?php echo $program_search_title; ?></div>
    <div class="bd">
        <span id="program_search_status" class="search-status"></span>
        <div class="dialog_wrap">
            <form method="GET" action="#">
                <fieldset>
                    <legend>Search Term</legend>
                    <input type="text" id="program_search_terms" name="program_search_terms" size="50"
                        onkeypress="return ilios.pm.cs.handleProgramSearchFieldInput(event);" placeholder = "<?php echo $generic_search_hint ?>">
                    <span class="search_icon_button"
                        onclick="ilios.pm.cs.performProgramSearch(document.getElementById('program_search_terms').value);">
                    </span>
                </fieldset>
                <fieldset>
                    <legend>Search Results</legend>
                    <div class="scroll_list clearfix">
                        <ul id="program_search_results_list" class="search-results">
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
    ilios.pm.cs.programSearchDialog = null;
    ilios.pm.cs.buildProgramSearchDialogDOM = function () {
        var handleCancel = function () {
            this.cancel();
        };
        var handleReset = function(){
            ilios.pm.cs.emptySearchDialogForViewing();
            return false;
        }
        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var resetStr = ilios_i18nVendor.getI18NString('general.phrases.search.clear');

        var buttonArray = [
                {text: cancelStr, handler: handleCancel},
                {text: resetStr, handler: handleReset}
            ];

        var panelWidth = "600px";
        var dialog = new YAHOO.widget.Dialog('program_search_picker', {
            width: panelWidth,
            modal: true,
            visible: false,
            constraintoviewport: false,
            buttons: buttonArray
        });

        dialog.showDialogPane = function () {
            ilios.pm.cs.emptySearchDialogForViewing();
            dialog.center();
            dialog.show();
        };

        dialog.render();
        ilios.pm.cs.programSearchDialog = dialog;
    };

    // @private
    ilios.pm.cs.handleProgramSearchFieldInput = function (event) {
        var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
        if (charCode == 13) {
            var elem = document.getElementById('program_search_terms');
            ilios.pm.cs.performProgramSearch(elem.value);
            event.cancelBubble = true;
            event.returnValue = false;
            return false;
        }
        return true;
    };

    YAHOO.util.Event.onDOMReady(ilios.pm.cs.buildProgramSearchDialogDOM);
</script>
