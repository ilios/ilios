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
 * TODO there is a lot of commonality between this and IGM's faculty picker
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
                              'YAHOO.util.XHRDataSource.TYPE_XML',
                              $autoCompleteFilterer, $autoCompleteFormatter, 500,
                              $displayHandlerCode, $selectHandlerCode,
                              $deselectHandlerCode, $submitHandlerCode);

?>

<script src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_common_picker_director.js"); ?>"></script>
