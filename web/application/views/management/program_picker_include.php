<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-script that rigs the "program picker" dialog to the user management page.
 */
$dialogDivId = 'program_picker_dialog';

$modelName = 'SimpleProgramModel';
$modelSpecification = 'function ' . $modelName . ' () {
    AbstractJavaScriptModelForm.apply(this, arguments);
};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    ' . $modelName . '.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}';


$remoteDataSourceMethod = 'getProgramList';
$responseSchema = '{
    resultNode: "Result",
    fields: ["title", "program_id"]
}';

$hiddenFormElementId = 'program_hidden_input';
$listingTextFieldId = 'program_picker_selected_text_list';
$selectedItemContainerId = 'program_picked';

$modelSettingCode = '
    model.setDBId(rowSelection.program_id);
    model.setTitle(rowSelection.title);

';


$autolistContainerId = 'program_autolist';
$autoCompleteTextFieldId = 'program_name_input';
$i18nKeyForSelectedLabel = 'general.terms.programs';
$i18nKeyForInstructions = 'general.text.program_search_instructions';
$autoCompleteTabId = 'program_autocomplete_tab';

$dialogDisplayingEventTriggerName = 'program_picker_show_dialog';

$uniquer = 'prog_';

$selectHandlerCode = '
var model = new ' . $modelName . '();
var listElement = document.getElementById("' . $selectedItemContainerId . '");
var element = document.createElement("li");

' . $modelSettingCode . '

' . $uniquer . 'currentlySelectedModels.length++;
' . $uniquer . 'currentlySelectedModels[model.dbId] = model;

element.innerHTML = rowSelection.title;
element.iliosModel = model;
listElement.appendChild(element);
';

$deselectHandlerCode = '
var target = ilios.utilities.getEventTarget(event);
var model = target.iliosModel;

if (' . $uniquer . 'currentlySelectedModels[model.dbId] != null) {
    delete ' . $uniquer . 'currentlySelectedModels[model.dbId];
    ' . $uniquer . 'currentlySelectedModels.length--;
}
';

$parentModelGetterName = 'ilios.management.getSelectedProgramModelsHolder';
$parentModelGetter = '';


$localModelGetterInvocation = 'management.getSelectedProgramModels()';
$localModelSetterName = '';

$submitHandlerCode = '
ilios.management.preDialogSelectedProgramModels
    = ilios.utilities.cloneAssociativeArray(ilios.management.permissions.selectedPermissionPrograms);

ilios.management.permissions.selectedPermissionPrograms = [];

for (var key in ' . $uniquer . 'currentlySelectedModels) {
    ilios.management.permissions.selectedPermissionPrograms.push(' . $uniquer . 'currentlySelectedModels[key]);
}

ilios.management.transaction.setPermissionsForSelectedPrograms();
';

$displayVetoer = 'ilios.management.programPickerDisplayVetoer';

generatePickerMarkupAndScript($uniquer, $dialogDivId, $modelName, $modelSpecification,
    $remoteDataSourceMethod, $responseSchema, $hiddenFormElementId, $listingTextFieldId,
    $selectedItemContainerId, $parentModelGetterName, $parentModelGetter,
    $localModelGetterInvocation, $localModelSetterName, $modelSettingCode,
    $autolistContainerId, $autoCompleteTextFieldId, $i18nKeyForSelectedLabel,
    $i18nKeyForInstructions, $autoCompleteTabId, $dialogDisplayingEventTriggerName,
    'YAHOO.util.XHRDataSource.TYPE_XML', null, null, null, 75, null,
    $selectHandlerCode, $deselectHandlerCode, $submitHandlerCode, null, $displayVetoer);
?>

<script type="text/javascript">
    ilios.management.preDialogSelectedProgramModels = null;

    ilios.management.getSelectedProgramModelsHolder = function () {
        return ilios;
    };

    ilios.management.programPickerDisplayVetoer = function (dialog) {
        // Hacky temporary code until this functionality is functioning correctly
        if ((ilios.management.permissions.selectedPermissionPrograms.length > 0)
            && (ilios.management.permissions.selectedUsers.length > 1)) {
            ilios.alert.alert('Removing programs just selected in this session presently leads to unpredictable'
                + ' behaviour. Click the "Finished" button and alter individual users to'
                + ' remove programs as necessary.');

            return true;
        }

        return false;
    };

    ilios.management.getSelectedProgramModels = function () {
        return ilios.management.permissions.selectedPermissionPrograms;
    };
</script>
