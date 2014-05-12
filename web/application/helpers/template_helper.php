<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file template_helper.php
 *
 * Templating helper functions.
 */

if (! function_exists('ilios_print_daytime_options')) {
    /**
     * Prints out <code>option</code> tags for day times in specified intervals
     * within given boundaries.
     *
     * @param int $start The starting minutes increment index in each hour
     * @param int $end Total count of minutes increments in entire options list, 60 (@ 4/hr) = 15 hours
     */

    function ilios_print_daytime_options ($start = 0, $end = null) {

        //pass in the instance to get the configuration info
        $CI =& get_instance();

        //if there is no value set for $end, get it from the config file...
        if(!isset($end)){
            //check/set it from the config file
            if($CI->config->item('time_selection_total_increments')) {
                $end = $CI->config->item('time_selection_total_increments');
            } else {
                //or set the default of 60
                $end = 60;
            }
        }

        //check for the $hoursOffset override in the config file
        if($CI->config->item('time_selection_hours_offset')) {
            $hoursOffset = $CI->config->item('time_selection_hours_offset');
        } else {
            $hoursOffset = 6;
        }

        //check for the $incrementsPerHour override in the config file
        if($CI->config->item('time_selection_increments_per_hour')) {
            $incrementsPerHour = $CI->config->item('time_selection_increments_per_hour');
        } else {
            //Set default of '4' which would reflect :00, :15, :30, :45...
            $incrementsPerHour = 4;
        }

        for ($i = $start; $i < $end; $i++) {

            $hours = floor($i / $incrementsPerHour) + $hoursOffset;
            //set the increment multiplier based on number of increments in one hour (60 mins)
            $minutes = ($i % $incrementsPerHour) * (60 / $incrementsPerHour);

            if ($hours < 10) {
                $hours = '0' . $hours;
            }

	    if ($minutes < 10) {
                $minutes = '0' . $minutes;
            }

            $string = $hours . ':' . $minutes;

            echo '<option value="' . $string . '">' . $string . '</option>';
        }
    }
}

if ( ! function_exists('generateProgressDivMarkup')) {
    /**
     * Generates markup for displaying status progress messages.
     * @param string|null $divStyleDefinition Inline CSS styles.
     * @param string $message A status message.
     * @return string The generated markup.
     */
    function generateProgressDivMarkup ($divStyleDefinition = null, $message = 'XX') {
        if (is_null($divStyleDefinition)) {
            $divStyleDefinition= "float: right; margin-right: 36px; display: none;";
        }

        $rhett = '<div style="' . $divStyleDefinition . '" id="save_in_progress_div" class="indeterminate_progress_text">
                <div class="indeterminate_progress" style="display: inline-block;"></div>
                <span id="save_in_progress_text">' . htmlentities($message, ENT_COMPAT, 'UTF-8') . '</span>&hellip;
            </div>';

        return $rhett;
    }
}

if (! function_exists('generatePickerMarkupAndScript')) {
    /**
     * This function allows a generalization of HTML and JavaScript generation for pickers of different
     * table-model pairings. This is a walk through the unpretty world of PHP variable substitution
     * into generated javascript code.
     *
     * ASSUMPTIONS:
     *   the controller class cited in controllerURL responds to the method defined in $remoteDataSourceMethod
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
     * @param string $alternativeAutoCompleteFilterer
     * @param string $alternativeAutoCompleteFormatter
     * @param int $maxResultsDisplay
     * @param string $alternativeDisplayHandlerCode
     * @param string $alternativeSelectHandlerCode
     * @param string $alternativeDeselectHandlerCode
     * @param string $alternativeSubmitHandlerCode
     * @param string $initialSelectionFormatter
     * @param string $displayVetoer
     *
     * @todo Rid the world of this abomination. Replace with JS-only solution, e.g. by extending YAHOO.widget.Dialog. [ST 2013/11/24]
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
            <?php echo $alternativeSubmitHandlerCode; ?>
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
}

if (! function_exists('generateCheckboxElementsFromArray')) {
    /**
     * Generates and returns a HTML checkbox element plus corresponding label for each item in a given array.
     *
     * @param array $element_key_and_value_array An associative array, each item's key being used as the checkbox value and each item's value being used as the label text.
     * @return string The generated markup.
     */
    function generateCheckboxElementsFromArray ($element_key_and_value_array) {

        $rhett = '';

        if (!empty($element_key_and_value_array)) {
            foreach ($element_key_and_value_array as $key=>$value) {
                $rhett .= '<input type="checkbox" value="'. htmlentities($key, ENT_COMPAT, 'UTF-8').'" />';
                $rhett .= '<label>' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '</label><br />';
            }
        }

        return $rhett;
    }
}
