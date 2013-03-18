<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(getServerFilePath('views') . 'common/picker_dialog_generator.php');

/**
 * This file is meant to be included from program_manager.php and is responsible for generating
 * 	the HTML and JavaScript (via the function call to the method defined in
 * 	common/picker_dialog_generator).
 */

			$dialogDivId = 'faculty_picker_dialog';


			$modelName = '';
			$modelSpecification = '';


			$remoteDataSourceMethod = 'getFacultyList';

			$autoCompleteFilterer = 'facultyAutoCompleteFilterer';

			$autoCompleteFormatter = 'facultyAutoCompleteFormatter';

			$responseSchema = '{ resultNode: "Result", fields: ["last_name", "user_id", "first_name", '
									. '"middle_name", "phone", "email", '
									. '"uc_uid", "other_id", "cohort_id"] }';

			$hiddenFormElementId = 'faculty_hidden_input';
			$listingTextFieldId = 'faculty_picker_selected_text_list';
			$selectedItemContainerId = 'faculty_picked';


			$parentModelGetterName = '';
			$parentModelGetter = '';
			$localModelGetterInvocation = '';
			$localModelSetterName = '';
			$modelSettingCode = '';


			$autolistContainerId = 'faculty_autolist';
			$autoCompleteTextFieldId = 'faculty_name_input';
			$i18nKeyForSelectedLabel = 'general.terms.instructors';
			$i18nKeyForInstructions = 'general.text.instructor_search_instructions';
			$autoCompleteTabId = 'faculty_autocomplete_tab';

			$dialogDisplayingEventTriggerName = 'faculty_picker_show_dialog';

			$displayHandlerCode = '
				var containerNumber = dialog.containerNumber;
				var groupModel = ilios.igm.instructorGroupModels[containerNumber];
				var users = groupModel.getUsers();
				var userModel = null;

				idUserMap = new Array();
				facultyPickerModel = groupModel.clone();

				if (facultySelectListElement == null) {
					facultySelectListElement = document.getElementById(dialog.selectListId);
				}
				facultySelectListElement.iliosModel = groupModel;

				ilios.utilities.removeAllChildren(facultySelectListElement);

				for (var key in users) {
					userModel = users[key];

					facultySelectListElement.appendChild(createSelectedElementForUserModel(userModel));
				}

                return true;
			';

			$selectHandlerCode = '
				var userModel = idUserMap[rowSelection.user_id];

				facultySelectListElement.appendChild(createSelectedElementForUserModel(userModel));
				facultyPickerModel.addUser(userModel);
			';

			$deselectHandlerCode = '
				var target = ilios.utilities.getEventTarget(event);

				if (target.tagName.toLowerCase() === "li") {
					facultyPickerModel.removeUser(target.iliosModel);
				}
			';

			$submitHandlerCode = '
				var groupModel = facultySelectListElement.iliosModel;

				if (groupModel.compareTo(facultyPickerModel) != 0) {
					groupModel.replaceContentWithModel(facultyPickerModel, true);
				}

				idUserMap = null;
				facultyPickerModel = null;
			';

			generatePickerMarkupAndScript("facu_", $dialogDivId, $modelName, $modelSpecification,
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
										  $autoCompleteFilterer, $autoCompleteFormatter, 2500,
										  $displayHandlerCode, $selectHandlerCode,
										  $deselectHandlerCode, $submitHandlerCode);

?>

<script type="text/javascript">

	var idUserMap = null;
	var facultyPickerModel = null;
	var facultySelectListElement = null;

	function createSelectedElementForUserModel (userModel) {
		var element = document.createElement('li');
		var textNode = document.createTextNode(userModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST));

		element.iliosModel = userModel;
		element.appendChild(textNode);

		ilios.utilities.setToolTipForElement(element, userModel.getEmailAddress());

		return element;
	}

	var facultyAutoCompleteFilterer = function (queryString, fullResponse, parsedResponse,
										   		callback, autoCompleter) {
		var len = parsedResponse.results.length;
		var selectedList = document.getElementById(autoCompleter.target);
		var filteredResults = new Array();
		var i = 0;
		var userModel = null;
		var populateUserMap = (idUserMap.length == 0);

		for (; i < len; i++) {
			userModel = new UserModel(parsedResponse.results[i]);

			 userModel.setLastName(parsedResponse.results[i].last_name);
			 userModel.clearDirtyState();

			if (populateUserMap) {
				idUserMap[userModel.getDBId()] = userModel;
			}

			if (! ilios.utilities.searchListElementForModel(selectedList, userModel)) {
				filteredResults.push(parsedResponse.results[i]);
			}
		}

		parsedResponse.results = filteredResults;

		return parsedResponse;
	};

	var facultyAutoCompleteFormatter = function (resultDataObject, queryString, resultMatch,
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
