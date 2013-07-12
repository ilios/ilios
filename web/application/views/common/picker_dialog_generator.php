<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * JAVASCRIPT DEPENDENCIES:
 *         YUI libraries
 *         scripts/abstract_js_model_form.js
 *         scripts/ilios_ui.js
 *         scripts/ilios_utilities.js
 *
 * ASSUMPTIONS:
 *         . the controller class cited in controllerURL responds to the method defined in
 *                 $remoteDataSourceMethod
 */
/**
 * This function allows a generalization of HTML and JavaScript generation for pickers of different
 * table-model pairings. This is a walk through the unpretty world of PHP variable substitution
 * into generated javascript code.
 *
 * @param string $uniquer
 * @param string $dialogDivId
 * @param string $modelName
 * @param string $modelSpecification
 * @param string $remoteDataSourceMethod
 * @param string $responseSchema little known, documented who knows where, the first column name listed
 *     in the value for the 'fields' key here will have its
 *     corresponding value as the 'resultMatch' (third) variable in
 *     the autoCompleter's formatResult function signature..
 * @param string $hiddenFormElementId
 * @param string $listingTextFieldId
 * @param string $selectedItemContainerId
 * @param string $parentModelGetterName
 * @param string $parentModelGetter
 * @param string $localModelGetterInvocation
 * @param string $localModelSetterName
 * @param string $modelSettingCode not used if $alternativeSelectHandlerCode is non-null
 * @param string $autolistContainerId
 * @param string $autoCompleteTextFieldId
 * @param string $i18nKeyForSelectedLabel
 * @param string $i18nKeyForInstructions
 * @param string $autoCompleteTabId
 * @param string $dialogDisplayingEventTriggerName
 * @param string $dataResponseType
 * @param string $alternativeAutoCompleteRequestGenerator
 * @param string $alternativeAutoCompleteFilterer
 * @param string $alternativeAutoCompleteFormatter
 * @param string $maxResultsDisplay
 * @param string $alternativeDisplayHandlerCode
 * @param string $alternativeSelectHandlerCode
 * @param string $alternativeDeselectHandlerCode
 * @param string $alternativeSubmitHandlerCode
 * @param string $initialSelectionFormatter
 * @param string $displayVetoer
 *
 * @todo Rid the world of this abomination. Replace with JS-only solution, e.g. by extending YAHOO.widget.Dialog.
 */
function generatePickerMarkupAndScript (
    $uniquer,
    $dialogDivId,
    $modelName,
    $modelSpecification,
    $remoteDataSourceMethod,
    $responseSchema,
    $hiddenFormElementId,
    $listingTextFieldId,
    $selectedItemContainerId,
    $parentModelGetterName,
    $parentModelGetter,
    $localModelGetterInvocation,
    $localModelSetterName,
    $modelSettingCode,
    $autolistContainerId,
    $autoCompleteTextFieldId,
    $i18nKeyForSelectedLabel,
    $i18nKeyForInstructions,
    $autoCompleteTabId,
    $dialogDisplayingEventTriggerName,
    $dataResponseType = 'YAHOO.util.XHRDataSource.TYPE_XML',
    $alternativeAutoCompleteRequestGenerator = null,
    $alternativeAutoCompleteFilterer = null,
    $alternativeAutoCompleteFormatter = null,
    $maxResultsDisplay = 500,
    $alternativeDisplayHandlerCode = null,
    $alternativeSelectHandlerCode = null,
    $alternativeDeselectHandlerCode = null,
    $alternativeSubmitHandlerCode = null,
    $initialSelectionFormatter = null,
    $displayVetoer = null)
{
?>
<div class="tabdialog" id="<?php echo $dialogDivId; ?>"></div>

<script type="text/javascript">
    <?php echo $modelSpecification ?>;
    var <?php echo $uniquer; ?>currentlySelectedModels = [];
    var <?php echo $uniquer; ?>dataSource = new YAHOO.util.XHRDataSource(controllerURL + '<?php echo $remoteDataSourceMethod; ?>');
    var <?php echo $uniquer; ?>hiddenFormElement = "<?php echo $hiddenFormElementId; ?>";
    var <?php echo $uniquer; ?>listingTextField = "<?php echo $listingTextFieldId; ?>";
    var <?php echo $uniquer; ?>selectedItemContainer = "<?php echo $selectedItemContainerId; ?>";


    <?php echo $uniquer; ?>dataSource.responseType = <?php echo $dataResponseType; ?>;
    <?php echo $uniquer; ?>dataSource.responseSchema = <?php echo $responseSchema; ?>;


    <?php echo $parentModelGetter ?>;

    /*
     * This will get messaged just prior to the dialog being displayed to the user.
     *
     * @param dialog a handle to the actual dialog instance which is about to be
     *            displayed
     * @see ilios.dom.buildDialogPanel
     */
    var <?php echo $uniquer; ?>handleDialogDisplay = function (dialog) {
<?php
    if ($alternativeDisplayHandlerCode != null) :
        echo $alternativeDisplayHandlerCode;
    else :
?>
        var parentModel = <?php echo $parentModelGetterName; ?>(dialog);
        var picker = document.getElementById(<?php echo $uniquer; ?>selectedItemContainer);
        var localModels = parentModel.<?php echo $localModelGetterInvocation; ?>;
        var vetoDisplay = false;

<?php
        if ($displayVetoer != null) :
            echo "vetoDisplay = " . $displayVetoer . "(dialog);\n";
        endif;
?>
        if (vetoDisplay) {
            return false;
        }

        picker.innerHTML = '';

        <?php echo $uniquer; ?>currentlySelectedModels = null;

        if (localModels == null) {
            <?php echo $uniquer; ?>currentlySelectedModels = [];
        } else {
            var model = null;
            var liElement = null;
            <?php echo $uniquer; ?>currentlySelectedModels = ilios.utilities.cloneAssociativeArray(localModels);

        // repopulate picker list to reflect the display
            for (var key2 in <?php echo $uniquer; ?>currentlySelectedModels) {
                model = <?php echo $uniquer; ?>currentlySelectedModels[key2];
                liElement = document.createElement('li');
                liElement.iliosModel = model;
                liElement.innerHTML =
<?php
        if ($initialSelectionFormatter != null) :
            echo $initialSelectionFormatter . '(model);';
        else :
            echo 'model.title;';
        endif;
?>
                picker.appendChild(liElement);
            }
        }
    return true;
<?php
    endif;
?>
    }; // end function


    /*
     * This will get messaged when the user clicks the submit button on the dialog (this
     *        button is currently display-text'd as "Done")
     *
     * @see ilios.dom.buildDialogPanel
     */
    var <?php echo $uniquer; ?>submitMethod = function () {
<?php
    if ($alternativeSubmitHandlerCode != null) :
        echo $alternativeSubmitHandlerCode;
    else :
?>
        var textFieldContent = '';
        var containerNumber = this.containerNumber; // 'this' should be the Dialog instance
        var inputTextId = containerNumber + '_' + <?php echo $uniquer; ?>listingTextField;
        var parentModel = <?php echo $parentModelGetterName; ?>(this);
        var element = null;

        parentModel.<?php echo $localModelSetterName; ?>(<?php echo $uniquer; ?>currentlySelectedModels);

        textFieldContent = ilios.utilities.delimitedStringOfTitledObjects(
            <?php echo $uniquer ?>currentlySelectedModels, ';');

        element = document.getElementById(inputTextId + "_full");
        if (element != null) {
            element.innerHTML = textFieldContent;
            element = document.getElementById(inputTextId);
            element.innerHTML = ilios.lang.ellipsisedOfLength(textFieldContent, 75);
        } else {
            element = document.getElementById(inputTextId);
            element.innerHTML = textFieldContent;
        }
<?php
    endif;
?>
    }; // end function


    /*
     * We'll receive notification via this when the user click-deletes an item from the
     *    selected list.
     *
     * @param event actual click event
     * @see ilios.dom.generateAutoCompleteDialogMarkup
     */
    var <?php echo $uniquer; ?>handleDeselect = function (event) {
<?php
    if ($alternativeDeselectHandlerCode != null) :
        echo $alternativeDeselectHandlerCode;
    else :
?>
        var target = ilios.utilities.getEventTarget(event);
        var title = ilios.utilities.htmlEntitiesDecode(target.innerHTML);

        if (<?php echo $uniquer; ?>currentlySelectedModels[title] != null) {
            delete <?php echo $uniquer; ?>currentlySelectedModels[title];
            <?php echo $uniquer; ?>currentlySelectedModels.length--;
        }
<?php
    endif;
?>
    }; // end function


    /*
     * We'll receive notification via this when the user click-selects an item from the
     *    candidate list.
     *
     * @param rowSelection a map of the db row data representation for the user's selection
     * @see ilios.ui.setupDialogAutoComplete
     */
    var <?php echo $uniquer ?>handleSelect = function (rowSelection) {
<?php
    if ($alternativeSelectHandlerCode != null) :
        echo $alternativeSelectHandlerCode;
    else :
?>
        var model = new <?php echo $modelName; ?>();

        <?php echo $modelSettingCode; ?>


        <?php echo $uniquer; ?>currentlySelectedModels.length++;
        <?php echo $uniquer; ?>currentlySelectedModels[rowSelection.title] = model;
<?php
    endif;
?>
    }; // end function

    /**
     * @method initDialog()
     * Initializes the dialog and rigs event handling up to it.
     */
    var <?php echo $uniquer ?>initDialog = function (who, knows, args) {
        var autolistContainer = "<?php echo $autolistContainerId; ?>";
        var textInputFieldForAutoComplete = "<?php echo $autoCompleteTextFieldId; ?>";

        ilios.dom.generateAutoCompleteDialogMarkup({
            deselect_handler: <?php echo $uniquer; ?>handleDeselect,
            selected_label: "<?php echo $i18nKeyForSelectedLabel; ?>",
            instructions: "<?php echo $i18nKeyForInstructions; ?>",
            container: args['container'],
            hidden: <?php echo $uniquer; ?>hiddenFormElement,
            tabs: {autocomplete: "<?php echo $autoCompleteTabId; ?>"},
            acinput: textInputFieldForAutoComplete,
            aclist: autolistContainer,
            picked: <?php echo $uniquer; ?>selectedItemContainer
        });

        ilios.dom.buildDialogPanel({}, {}, {
            trigger: args['trigger'],
            target: <?php echo $uniquer; ?>selectedItemContainer,
            hidden: <?php echo $uniquer; ?>hiddenFormElement,
            input: <?php echo $uniquer; ?>listingTextField,
            submit_override: <?php echo $uniquer; ?>submitMethod,
            display_handler: <?php echo $uniquer; ?>handleDialogDisplay,
            container: args['container']
        });

        ilios.ui.setupDialogAutoComplete({
            target: <?php echo $uniquer; ?>selectedItemContainer,
            input: textInputFieldForAutoComplete,
            container: autolistContainer,
            remote_data: <?php echo $uniquer; ?>dataSource,
            select_handler: <?php echo $uniquer; ?>handleSelect,
<?php
    if ($alternativeAutoCompleteRequestGenerator != null) :
        echo "request_generator: " . $alternativeAutoCompleteRequestGenerator . ",\n";
    endif;

    if ($alternativeAutoCompleteFilterer != null) :
        echo "filter_results_handler: " . $alternativeAutoCompleteFilterer . ",\n";
    endif;

    if ($alternativeAutoCompleteFormatter != null) :
        echo "format_result_handler: " . $alternativeAutoCompleteFormatter . ",\n";
    endif;
?>
            max_displayed_results: <?php echo $maxResultsDisplay; ?>
        });
    }; // end function


    YAHOO.util.Event.onDOMReady(<?php echo $uniquer ?>initDialog, {
        // unique event that triggers opening of the dialog fired
        // from search link near course mesh form element
        trigger: "<?php echo $dialogDisplayingEventTriggerName; ?>",
        // unique id of the div where the dialog xhtml can be
        // generated (once)
        container: "<?php echo $dialogDivId; ?>"
    });
</script>
<?php
}
