<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-script that rigs the "school picker" dialog to the user management page.
 */

$dialogDivId = 'school_picker_dialog';


$modelName = 'SimpleSchoolModel';
$modelSpecification = 'function ' . $modelName . ' () {
    AbstractJavaScriptModelForm.apply(this, arguments);
};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    ' . $modelName . '.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}';


$remoteDataSourceMethod = 'getSchoolList';
$responseSchema = '{ resultNode: "Result", fields: ["title", "school_id"] }';


$hiddenFormElementId = 'school_hidden_input';
$listingTextFieldId = 'school_picker_selected_text_list';
$selectedItemContainerId = 'school_picked';


$modelSettingCode = '
    model.setDBId(rowSelection.school_id);
 model.setTitle(rowSelection.title);
';


$autolistContainerId = 'school_autolist';
$autoCompleteTextFieldId = 'school_name_input';
$i18nKeyForSelectedLabel = 'general.terms.schools';
$i18nKeyForInstructions = 'general.text.school_search_instructions';
$autoCompleteTabId = 'school_autocomplete_tab';

$dialogDisplayingEventTriggerName = 'school_picker_show_dialog';

$uniquer = 'scho_';

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

$parentModelGetterName = 'ilios.management.getSelectedSchoolModelsHolder';
$parentModelGetter = '';


$localModelGetterInvocation = 'management.getSelectedSchoolModels()';
$localModelSetterName = '';

$submitHandlerCode = '
    ilios.management.preDialogSelectedSchoolModels
          = ilios.utilities.cloneObject(ilios.management.permissions.selectedPermissionSchools);

    ilios.management.permissions.selectedPermissionSchools = new Array();

    for (var key in ' . $uniquer . 'currentlySelectedModels) {
        ilios.management.permissions.selectedPermissionSchools.push(' . $uniquer . 'currentlySelectedModels[key]);
    }

    ilios.management.transaction.setPermissionsForSelectedSchools();
            ';

$displayVetoer = 'ilios.management.schoolPickerDisplayVetoer';

generatePickerMarkupAndScript($uniquer, $dialogDivId, $modelName, $modelSpecification,
                              $remoteDataSourceMethod, $responseSchema,
                              $hiddenFormElementId, $listingTextFieldId,
                              $selectedItemContainerId, $parentModelGetterName,
                              $parentModelGetter, $localModelGetterInvocation,
                              $localModelSetterName, $modelSettingCode,
                              $autolistContainerId, $autoCompleteTextFieldId,
                              $i18nKeyForSelectedLabel,
                              $i18nKeyForInstructions,
                              $autoCompleteTabId,
                              $dialogDisplayingEventTriggerName,
                              'YAHOO.util.XHRDataSource.TYPE_XML',
                              null, null, null, 75, null,
                              $selectHandlerCode, $deselectHandlerCode,
                              $submitHandlerCode, null,
                              $displayVetoer);


?>

<script type="text/javascript">

    ilios.management.preDialogSelectedSchoolModels = null;

    ilios.management.getSelectedSchoolModelsHolder = function () {
        return ilios;
    };

    ilios.management.schoolPickerDisplayVetoer = function (dialog) {
        // Hacky temporary code until this functionality is functioning correctly
        if ((ilios.management.permissions.selectedPermissionSchools.length > 0)
                                && (ilios.management.permissions.selectedUsers.length > 1)) {
            ilios.alert.alert('Removing schools just selected in this session presently leads to unpredictable'
                    + ' behaviour. Click the "Finished" button and alter individual users to'
                    + ' remove schools as necessary.');

            return true;
        }

        return false;
    };

    ilios.management.getSelectedSchoolModels = function () {
        return ilios.management.permissions.selectedPermissionSchools;
    };

</script>

