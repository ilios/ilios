<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The code relies upon the following notions:
 *  . the Javascript model which is produced by the PHP that includes this file must feature the
 *          implementation of a method:
 *                      ilios.common.picker.director.getOwningModelForDirectorPicker(dialog)
 *          which returns the underlying model (ProgramYearModel, for example) associated to a
 *          given dialog
 *     . the underlying model (ProgramYearModel, for example) implements the following methods:
 *                 addDirector(UserModel)
 *                 removeDirector(UserModel)
 *                 getDirectors()
 *                 replaceContentWithModel(<underlyingModelType>, bool:forceReplace)
 *                 compareTo(<underlyingModelType>)
 *                 clone()
 *
 * See program/director_include.php for a usage example
 *
 * TODO there is a lot of commonality between this an IGM's faculty picker
 */

$dialogDivId = 'director_picker_dialog';


$modelName = '';
$modelSpecification = '';


$remoteDataSourceMethod = 'getDirectorList';

$autoCompleteFilterer = 'ilios.common.picker.director. directorAutoCompleteFilterer';

$autoCompleteFormatter = 'ilios.common.picker.director.directorAutoCompleteFormatter';

$responseSchema = '{ resultNode: "Result", fields: ["last_name", "user_id", "first_name", '
                        . '"middle_name", "phone", "email", '
                        . '"uc_uid", "other_id"] }';

$hiddenFormElementId = 'director_hidden_input';
$listingTextFieldId = 'director_picker_selected_text_list';
$selectedItemContainerId = 'director_picked';


$parentModelGetterName = '';
$parentModelGetter = '';
$localModelGetterInvocation = '';
$localModelSetterName = '';
$modelSettingCode = '';


$autolistContainerId = 'director_autolist';
$autoCompleteTextFieldId = 'director_name_input';
$i18nKeyForSelectedLabel = 'general.terms.directors';
$i18nKeyForInstructions = 'general.text.director_search_instructions';
$autoCompleteTabId = 'director_autocomplete_tab';

$dialogDisplayingEventTriggerName = 'director_picker_show_dialog';

$displayHandlerCode = '
    var owningModel = ilios.common.picker.director.getOwningModelForDirectorPicker(dialog);
    var users = owningModel.getDirectors();
    var userModel = null;

    ilios.common.picker.director.idUserMap = new Array();
    ilios.common.picker.director.directorPickerModel = owningModel.clone();
    ilios.common.picker.director.directorPickerModel.setDBId(owningModel.getDBId());

    if (ilios.common.picker.director.directorSelectListElement == null) {
        ilios.common.picker.director.directorSelectListElement
                                        = document.getElementById(dialog.selectListId);
    }
    ilios.common.picker.director.directorSelectListElement.iliosModel = owningModel;

    ilios.utilities.removeAllChildren(ilios.common.picker.director.directorSelectListElement);

    for (var key in users) {
        userModel = users[key];

        ilios.common.picker.director.directorSelectListElement.appendChild(
                                         ilios.common.picker.director
                                              .createSelectedElementForUserModel(userModel));
    }

    return true;
';

$selectHandlerCode = '
    var userModel = ilios.common.picker.director.idUserMap[rowSelection.user_id];

    ilios.common.picker.director.directorSelectListElement.appendChild(
                                          ilios.common.picker.director
                                              .createSelectedElementForUserModel(userModel));
    ilios.common.picker.director.directorPickerModel.addDirector(userModel);
';

$deselectHandlerCode = '
    var target = ilios.utilities.getEventTarget(event);

    if (target.tagName.toLowerCase() === "li") {
        ilios.common.picker.director.directorPickerModel.removeDirector(target.iliosModel);
    }
';

$submitHandlerCode = '
    var owningModel = ilios.common.picker.director.directorSelectListElement.iliosModel;

    if (owningModel.compareTo(ilios.common.picker.director.directorPickerModel) != 0) {
        owningModel.replaceContentWithModel(ilios.common.picker.director.directorPickerModel,
                                            true);
    }

    ilios.common.picker.director.idUserMap = null;
    ilios.common.picker.director.directorPickerModel = null;
';


generatePickerMarkupAndScript("dire_", $dialogDivId, $modelName, $modelSpecification,
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
                              'YAHOO.util.XHRDataSource.TYPE_XML', null,
                              $autoCompleteFilterer, $autoCompleteFormatter, 500,
                              $displayHandlerCode, $selectHandlerCode,
                              $deselectHandlerCode, $submitHandlerCode);

?>

<script type="text/javascript">

    ilios.namespace('common.picker.director');


    ilios.common.picker.director.idUserMap = null;
    ilios.common.picker.director.directorPickerModel = null;
    ilios.common.picker.director.directorSelectListElement = null;

    ilios.common.picker.director.createSelectedElementForUserModel = function (userModel) {
        var element = document.createElement('li');
        var textNode = document.createTextNode(userModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST));

        element.iliosModel = userModel;
        element.appendChild(textNode);

        ilios.utilities.setToolTipForElement(element, userModel.getEmailAddress());

        return element;
    };

    ilios.common.picker.director.directorAutoCompleteFilterer = function (queryString, fullResponse,
                                                                          parsedResponse, callback,
                                                                          autoCompleter) {
        var len = parsedResponse.results.length;
        var selectedList = document.getElementById(autoCompleter.target);
        var filteredResults = new Array();
        var i = 0;
        var userModel = null;
        var populateUserMap = (ilios.common.picker.director.idUserMap.length == 0);

        for (; i < len; i++) {
            userModel = new UserModel(parsedResponse.results[i]);

             userModel.setLastName(parsedResponse.results[i].last_name);
             userModel.clearDirtyState();

            if (populateUserMap) {
                ilios.common.picker.director.idUserMap[userModel.getDBId()] = userModel;
            }

            if (! ilios.utilities.searchListElementForModel(selectedList, userModel)) {
                filteredResults.push(parsedResponse.results[i]);
            }
        }

        parsedResponse.results = filteredResults;

        return parsedResponse;
    };

    ilios.common.picker.director.directorAutoCompleteFormatter = function (resultDataObject,
                                                                           queryString, resultMatch,
                                                                             autoCompleter) {
        var rhett = '<span uid="' + resultDataObject.user_id + '" title="'
                        + resultDataObject.email + '">';

        rhett += ilios.utilities.createFormattedUserName(resultDataObject.first_name,
                                                          resultDataObject.middle_name,
                                                          resultDataObject.last_name, 0);

        rhett += '</span>';

        return rhett;
    };
</script>
