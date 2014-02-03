<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-script that rigs the "course picker" dialog to the user management page.
 */

$dialogDivId = 'course_picker_dialog';
$modelName = 'SimpleCourseModel';
$modelSpecification = 'function ' . $modelName . ' () {
    AbstractJavaScriptModelForm.apply(this, arguments);

    this.startDate = null;
    this.endDate = null;
    this.year = null;
};


for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    ' . $modelName . '.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};';

$remoteDataSourceMethod = 'getCourseList';
$responseSchema = '{
    resultNode: "Result",
    fields: ["title", "course_id", "external_id", "start_date",
        "end_date", "year", "course_level", "publish_event_id",
        "locked", "published_as_tbd"]
}';


$hiddenFormElementId = 'course_hidden_input';
$listingTextFieldId = 'course_picker_selected_text_list';
$selectedItemContainerId = 'course_picked';

$modelSettingCode = '
    var dates = ilios.management.getStartAndEndDateObjectsFromPermissionCourseDBObject(rowSelection);

    model.setDBId(rowSelection.course_id);
    model.setTitle(rowSelection.title);
    model.year = parseInt(rowSelection.year);

    model.startDate = dates[0];
    model.endDate = dates[1];
';


$autolistContainerId = 'course_autolist';
$autoCompleteTextFieldId = 'course_name_input';
$i18nKeyForSelectedLabel = 'general.terms.courses';
$i18nKeyForInstructions = 'general.text.course_search_instructions';
$autoCompleteTabId = 'course_autocomplete_tab';

$dialogDisplayingEventTriggerName = 'course_picker_show_dialog';

$uniquer = 'cour_';

$selectHandlerCode = '
    var model = new ' . $modelName . '();
    var listElement = document.getElementById("' . $selectedItemContainerId . '");
    var element = document.createElement("li");

    ' . $modelSettingCode . '

    ' . $uniquer . 'currentlySelectedModels.length++;
    ' . $uniquer . 'currentlySelectedModels[model.dbId] = model;

    element.innerHTML = ilios.management.getDisplayHTMLForSimpleCourseModel(model);
    element.iliosModel = model;
    listElement.appendChild(element);
';

$deselectHandlerCode = '
    var target = ilios.utilities.getEventTarget(event);
    var model = (target.nodeName == "SPAN") ? target.parentNode.iliosModel
                                            : target.iliosModel;

    if (' . $uniquer . 'currentlySelectedModels[model.dbId] != null) {
        delete ' . $uniquer . 'currentlySelectedModels[model.dbId];

        ' . $uniquer . 'currentlySelectedModels.length--;
    }
';

$initialDisplayFormatter = 'ilios.management.getDisplayHTMLForSimpleCourseModel';
$resultsFormatter = 'ilios.management.autocompleteResultFormatter';

$parentModelGetterName = 'ilios.management.getSelectedCourseModelsHolder';
$parentModelGetter = '';


$localModelGetterInvocation = 'management.getSelectedCourseModels()';
$localModelSetterName = '';

$submitHandlerCode = '
    ilios.management.preDialogSelectedCourseModels
         = ilios.utilities.cloneAssociativeArray(ilios.management.selectedPermissionCourses);

    ilios.management.selectedPermissionCourses = [];

    for (var key in ' . $uniquer . 'currentlySelectedModels) {
        ilios.management.selectedPermissionCourses.push(' . $uniquer . 'currentlySelectedModels[key]);
    }

    ilios.management.transaction.setPermissionsForSelectedCourses();
';

            $displayVetoer = 'ilios.management.coursePickerDisplayVetoer';

generatePickerMarkupAndScript($uniquer, $dialogDivId, $modelName, $modelSpecification,
    $remoteDataSourceMethod, $responseSchema, $hiddenFormElementId, $listingTextFieldId,
    $selectedItemContainerId, $parentModelGetterName, $parentModelGetter, $localModelGetterInvocation,
    $localModelSetterName, $modelSettingCode, $autolistContainerId, $autoCompleteTextFieldId,
    $i18nKeyForSelectedLabel, $i18nKeyForInstructions, $autoCompleteTabId, $dialogDisplayingEventTriggerName,
    'YAHOO.util.XHRDataSource.TYPE_XML', null, $resultsFormatter, 125, null, $selectHandlerCode, $deselectHandlerCode,
    $submitHandlerCode, $initialDisplayFormatter, $displayVetoer);
?>

<script type="text/javascript">

    ilios.management.selectedPermissionCourses = null;
    ilios.management.preDialogSelectedCourseModels = null;

    ilios.management.getSelectedCourseModelsHolder = function () {
        return ilios;
    };

    ilios.management.getDisplayHTMLForSimpleCourseModel = function (model) {
        return ilios.management.getDisplayHTMLForCourseAttributes(model.title, model.year, model.startDate, model.endDate);
    };

    ilios.management.autocompleteResultFormatter = function (resultDataObject, queryString, resultMatch, autoCompleter) {
        var dates = ilios.management.getStartAndEndDateObjectsFromPermissionCourseDBObject(resultDataObject);

        return ilios.management.getDisplayHTMLForCourseAttributes(resultDataObject.title,
            parseInt(resultDataObject.year), dates[0], dates[1]);
    };

    ilios.management.getDisplayHTMLForCourseAttributes = function (title, intYear, startDate,
                                                                   endDate) {
        var rhett = title + ' ' + intYear + '-' + (intYear + 1);

        rhett += ' <span style="font-size: 8pt; color: #a1a3a3;">';
        rhett += startDate.format('m/d/yyyy') + '-' + endDate.format('m/d/yyyy');
        rhett += '</span>';

        return rhett;
    };

    ilios.management.getStartAndEndDateObjectsFromPermissionCourseDBObject = function (dbObject) {
        var rhett = [];

        rhett.push(ilios.utilities.mySQLTimelessDateToDateObject(dbObject.start_date));
        rhett.push(ilios.utilities.mySQLTimelessDateToDateObject(dbObject.end_date));

        return rhett;
    };

    ilios.management.coursePickerDisplayVetoer = function (dialog) {
        // Hacky temporary code until this functionality is functioning correctly
        if ((ilios.management.selectedPermissionCourses.length > 0)
            && (ilios.management.permissions.selectedUsers.length > 1)) {
            ilios.alert.alert('Removing courses just selected in this session presently leads to unpredictable'
                + ' behaviour. Click the "Finished" button and alter individual users to'
                + ' remove courses as necessary.');

            return true;
        }

        return false;
    };

    ilios.management.getSelectedCourseModels = function () {
        return ilios.management.selectedPermissionCourses;
    };
</script>
