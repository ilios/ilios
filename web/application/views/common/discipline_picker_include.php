<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes template.
 * Churns out the markup and javascript for a "discipline picker" dialog in the most gnarly fashion possible.
 *
 * @see program/discipline_include.php
 * @see course/discipline_include.php
 *
 * @todo Turducken code. Replace with JS-only solution.
 */
require_once getServerFilePath("views") . "common/picker_dialog_generator.php";

/**
 * The code which includes this file must define the following variables (prior to inclusion, to
 * be safe):
 *
 * $parentModelGetterName
 * $parentModelGetter
 * $localModelGetterInvocation
 * $localModelSetterName
 */

$uniquer = 'disc_';

$dialogDivId = 'discipline_picker_dialog';

$modelName = 'DisciplineModel';

$remoteDataSourceMethod = 'getDisciplineList';
$responseSchema = '{ resultNode: "Result", fields: ["title", "discipline_id"] }';

$hiddenFormElementId = 'discipline_hidden_input';
$listingTextFieldId = 'discipline_picker_selected_text_list';
$selectedItemContainerId = 'discipline_picked';

$autolistContainerId = 'discipline_autolist';
$autoCompleteTextFieldId = 'discipline_name_input';
$i18nKeyForSelectedLabel = 'general.terms.topics';
$i18nKeyForInstructions = 'general.text.discipline_search_instructions';
$autoCompleteTabId = 'discipline_autocomplete_tab';

$dialogDisplayingEventTriggerName = 'discipline_picker_show_dialog';

// custom select handler code
$selectHandlerCode = '
    var model = new ' . $modelName . '();
    model.setDBId(rowSelection.discipline_id);
    model.setTitle(rowSelection.title);

    var listElement = document.getElementById("' . $selectedItemContainerId . '");
    var liElement = document.createElement("li");
    var textNode = document.createTextNode(model.getTitle());
    var selectedModels = ' . $uniquer . 'currentlySelectedModels;

    selectedModels.push(model);

    liElement.appendChild(textNode);
    liElement.iliosModel = model;

    listElement.appendChild(liElement);

    return liElement;
';
// custom deselect handler code
$deselectHandlerCode = '
    var i, n;
    var model;
    var selectedModels = ' . $uniquer . 'currentlySelectedModels;
    var target = ilios.utilities.getEventTarget(event);
    var listElement = document.getElementById("' . $selectedItemContainerId . '");
    if ("li" === target.tagName.toLowerCase()) {
        model = target.iliosModel;
        for (i = 0, n = selectedModels.length; i < n; i++) {
            if (model.getDBId() === selectedModels[i].getDBId()) {
                selectedModels.splice(i, 1);
                break;
            }
        }
    }
';

// custom display handler code
$displayDialogHandlerCode = '
    var i, n;
    var parentModel = ' . $parentModelGetterName . '(dialog);
    var picker = document.getElementById( ' . $uniquer . 'selectedItemContainer);
    var localModels = parentModel.' . $localModelGetterInvocation . ';
    var selectedModels = [];
    ' . $uniquer . 'currentlySelectedModels;
    picker.innerHTML = "";

    selectedModels = null;

    if (localModels != null) {
        var model = null;
        var liElement = null;
        selectedModels = localModels.slice(0);

        // repopulate picker list to reflect the display
        for (i = 0, n = selectedModels.length; i < n; i++) {
            model = selectedModels[i];
            liElement = document.createElement("li");
            liElement.iliosModel = model;
            liElement.innerHTML = model.title;
            picker.appendChild(liElement);
        }
    }
    ' . $uniquer . 'currentlySelectedModels = selectedModels;
    return true;
';

// custom submit handler code
$submitHandlerCode = '
    var textFieldContent = "";
    var modelTitles = [];
    var i, n;
    var containerNumber = this.containerNumber; // "this" should be the Dialog instance
    var inputTextId = containerNumber + "_" + ' . $uniquer . 'listingTextField;
    var parentModel = ' . $parentModelGetterName . '(this);
    var element = null;
    var selectedModels = ' . $uniquer . 'currentlySelectedModels;

    parentModel.' . $localModelSetterName . '(selectedModels);

    for (i = 0, n = selectedModels.length; i < n; i++) {
        modelTitles.push(selectedModels[i].getTitle());
    }
    modelTitles.sort();
    textFieldContent = modelTitles.join(";");

    element = document.getElementById(inputTextId + "_full");
    if (element != null) {
        element.innerHTML = textFieldContent;
        element = document.getElementById(inputTextId);
        element.innerHTML = ilios.lang.ellipsisedOfLength(textFieldContent, 75);
    } else {
        element = document.getElementById(inputTextId);
        element.innerHTML = textFieldContent;
    }
';



// custom display handler function
// $alternativeDisplayHandlerCode = '';

generatePickerMarkupAndScript(
    $uniquer,
    $dialogDivId,
    $modelName,
    null,
    $remoteDataSourceMethod,
    $responseSchema,
    $hiddenFormElementId,
    $listingTextFieldId,
    $selectedItemContainerId,
    $parentModelGetterName,
    $parentModelGetter,
    $localModelGetterInvocation,
    $localModelSetterName,
    null,
    $autolistContainerId,
    $autoCompleteTextFieldId,
    $i18nKeyForSelectedLabel,
    $i18nKeyForInstructions,
    $autoCompleteTabId,
    $dialogDisplayingEventTriggerName,
    'YAHOO.util.XHRDataSource.TYPE_XML',
    null,
    null,
    null,
    150,
    $displayDialogHandlerCode,
    $selectHandlerCode,
    $deselectHandlerCode,
    $submitHandlerCode
    );
