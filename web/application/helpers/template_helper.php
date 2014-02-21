<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file template_helper.php
 *
 * Templating helper functions.
 */

if (! function_exists('ilios_print_daytime_options')) {
    /**
     * Prints out <code>option</code> tags for day times in 15min intervals
     * within given boundaries.
     *
     * @param int $start
     * @param int $end
     * @param int $hoursOffset
     *
     * @todo improve code docs. [ST 2013/11/22]
     */
    function ilios_print_daytime_options ($start = 0, $end = 60, $hoursOffset = 6) {
        for ($i = $start; $i < $end; $i++) {
            $hours = floor($i / 4) + $hoursOffset;
            $minutes = ($i % 4) * 15;

            if ($hours < 10) {
                $hours = '0' . $hours;
            }

            if ($minutes == 0) {
                $minutes = '00';
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

if (! function_exists('createContentContainerMarkup')) {
    /**
     * Markup generator function for "primary content" container elements (i.e. 'program' of the program & program years).
     *
     * @param string $formPrefix Markup prefixing the container element.
     * @param string $addNewEntityLink Markup containing the "add new <entity>" link or button.
     * @param string $searchNewEntityLink Markup containing the "search <entities>" link or button.
     * @param string $entityContainerHeader Markup that goes into the container header.
     * @param string $entityContainerContent Markup that goes into the container body.
     * @param string $addNewSomethingId Id-attribute of the "add new <related entity>" button.
     *      If a falsy value is provided then the button will not be rendered.
     * @param string $addNewSomethingAction JavaScript code. Goes into the inline "onclick" event handler of the
     *      "add new <related entity>" button.
     * @param string $addNewSomethingDisplayText The label of the "add new <related entity>" button.
     * @param string $suffixingContent Markup that goes below the container body.
     * @param string $saveDraftAction JavaScript code. Goes into the inline "onclick" event handler of the
     *      "save as draft" button.
     * @param string $publishAction JavaScript code. Goes into the inline "onclick" event handler of the
     *      "publish" button.
     * @param string $revertAction JavaScript code. Goes into the inline "onclick" event handler of the
     *      "reset form" button.
     * @param boolean $shouldShowSavePublishRevertButtons If TRUE then the "publish", "save draft" and "reset form"
     *      buttons will be rendered, otherwise not.
     * @param boolean $showPublishDraftStatus If TRUE then the generated container markup will contain a section
     *      for displaying publish-status information.
     * @param boolean $showPublishAllButton If TRUE then "publish all" button will be rendered.
     * @param boolean $showSaveAllLink If TRUE then the "save all" button will be rendered.
     * @param string $saveAllString The label-text of the "save all" button.
     * @param string $saveDraftString The label-text of the "save as draft" button.
     * @param string $publishAllString The label-text of the "publish all" button.
     * @param string $publishNowString The label-text of the "publish now" button.
     * @param string $resetFormString The label-text of the "reset form" button.
     * @param boolean $showArchivingLinkDiv If TRUE then the container element for the "archive" button will be rendered.
     * @param boolean $showRolloverLinkDiv If TRUE then the container element for the "rollover" button will be rendered.
     *
     * @todo Junk this god-awful mess. [ST 2013/06/18]
     */
    function createContentContainerMarkup ($formPrefix, $addNewEntityLink, $searchNewEntityLink, $entityContainerHeader,
                                           $entityContainerContent, $addNewSomethingId, $addNewSomethingAction,
                                           $addNewSomethingDisplayText, $suffixingContent, $saveDraftAction,
                                           $publishAction, $revertAction, $shouldShowSavePublishRevertButtons,
                                           $showPublishDraftStatus, $showPublishAllButton,$showSaveAllLink,
                                           $saveAllString, $saveDraftString, $publishAllString,
                                           $publishNowString, $resetFormString, $showArchivingLinkDiv = false,
                                           $showRolloverLinkDiv = false)
    {
        ?>
        <div class="content_container">
            <div class="master_button_container clearfix">
                <ul class="buttons left">
                    <li>
                        <?php
                        if ($searchNewEntityLink) :
                        echo $searchNewEntityLink;
                        ?>
                    </li>
                    <?php
                    endif;
                    if ($addNewEntityLink != '') :
                    ?>
                    <li>
                        <?php
                        echo $addNewEntityLink;
                        ?>
                    </li>
                <?php endif; ?>
                </ul>
                <ul class="buttons right">
                    <?php
                    if ($showRolloverLinkDiv) :
                        ?>
                        <li id="rollover_link_div" class="rollover_link_div"></li>
                    <?php
                    endif;
                    if ($showArchivingLinkDiv) :
                        ?>
                        <li id="archiving_link_div" class="archiving_link_div"></li>
                    <?php
                    endif;
                    ?>
                    <li>
                        <?php
                        if ($showSaveAllLink) :
                            ?>
                            <button id="save_all_dirty_to_draft" class="medium radius button" disabled='disabled'><?php echo $saveAllString ?></button>
                        <?php
                        endif;
                        ?>
                    </li>
                    <?php
                    if ($showPublishAllButton) :
                        ?>
                        <li>
                            <button id="publish_all" class="medium radius button" disabled='disabled'><i class="icon-checkmark"></i><?php echo $publishAllString ?></button>
                        </li>
                    <?php
                    endif;
                    ?>
                </ul>
            </div>
            <?php echo $formPrefix; ?>
            <div class="entity_container level-1">
                <div class="hd clearfix">
                    <div class="toggle">
                        <a href="#" id="show_more_or_less_link"
                           onclick="ilios.utilities.toggle('course_more_or_less_div', this); return false;" >
                            <i class="icon-plus" aria-hidden = "true"> </i>
                        </a>
                    </div>
                    <ul>
                        <?php
                        echo $entityContainerHeader;

                        if ($showPublishDraftStatus) :
                            ?>
                            <li class="publish-status">
                                <span class="data-type">Publish Status</span>
                                <span class="data" id="parent_publish_status_text"></span>
                            </li>

                        <?php
                        endif;
                        ?>

                    </ul>
                </div>
                <div id="course_more_or_less_div" class="bd" style="display:none">


                    <?php
                    echo $entityContainerContent;
                    if ($shouldShowSavePublishRevertButtons) :
                        ?>
                        <div class="buttons bottom">
                            <button id="draft_button" class="medium radius button" disabled="disabled" onClick="<?php echo $saveDraftAction ?>"><?php echo $saveDraftString ?></button>
                            <button id="publish_button" class="medium radius button" disabled="disabled" onClick="<?php echo $publishAction ?>"><?php echo $publishNowString ?></button>
                            <button id="reset_button" class="reset_button small secondary radius button" disabled="disabled" onClick="<?php echo $revertAction ?>"><?php echo $resetFormString ?></button>
                        </div>
                    <?php
                    endif;
                    ?>
                </div><!--close div.bd-->
            </div><!-- entity_container close -->
            </form>
            <?php
            if ($addNewSomethingId != '') :
                ?>
                <button class="small secondary radius button" disabled="disabled" id="<?php echo $addNewSomethingId; ?>" onClick="<?php echo $addNewSomethingAction; ?>"><?php echo $addNewSomethingDisplayText; ?></button>

            <?php
            endif;
            echo $suffixingContent;
            ?>
        </div><!-- content_container close -->
    <?php
    } // end function
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
