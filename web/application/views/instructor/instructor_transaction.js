/**
 * DEPENDENCIES:
 * 		YUI lib (at least YAHOO.widget and its dependencies)
 *		scripts/third_party/json2.js
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_utilities.js
 * 		instructor_dom.js
 */

ilios.igm.deleteGroup = function (event) {
	var target = ilios.utilities.getEventTarget(event);
	var containerNumber = target.getAttribute('cnumber');
	var deletePrefix = ilios_i18nVendor.getI18NString('general.warning.delete_prefix');
	var groupString = ilios_i18nVendor.getI18NString('general.terms.group').toLowerCase();
	var yesString = ilios_i18nVendor.getI18NString('general.terms.yes');

	ilios.alert.inform('<center><b>' + deletePrefix + ' ' + groupString + '?</b></center>', yesString,
			ilios.igm.continueDeleteGroup, {"cnumber": containerNumber});
};

/**
 * Button-click event handler function. Tied to the "ok" button of the "delete group" dialog.
 * Deletes a given group.
 * @method continueDeleteGroup
 * @param {Event} event the click event
 * @param {Object} args event-handler argument object, expects to contain the following:
 *    "cnumber" .. the group's container number
 * @private
*/
ilios.igm.continueDeleteGroup = function (event, args) {
	var containerNumber = args.cnumber;
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var url = controllerURL + "deleteGroup";
	var method = "POST";
	var paramString = "instructor_group_id=" + groupModel.getDBId() + "&container_number="
																				+ containerNumber;
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var cNumber = null;
				var collapserId = null;
				var groupContainer = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var msg
					    = ilios_i18nVendor.getI18NString('instructor_groups.error.group_deletion');

					ilios.alert.alert(msg + ": " + parsedObject.error);

					return;
				}

				cNumber = parsedObject.container_number;

				// delete from DOM
				collapserId
						= ilios.dom.childCollapsingContainerIdForContainerNumber(cNumber);
				groupContainer = document.getElementById(collapserId).parentNode;
				groupContainer.parentNode.removeChild(groupContainer);

				delete ilios.igm.instructorGroupModels[cNumber];
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	this.hide();

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.igm.loadAssociatedCoursesForContainerNumber = function (containerNumber) {
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var url = controllerURL + 'getAssociatedCourses';
	var method = "POST";
	var paramString = "instructor_group_id=" + groupModel.getDBId();
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var idStr = null;
				var containerElement = null;
				var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
					ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
					   									   'instructor_groups.loading_associated_courses');
					ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var msg = ilios_i18nVendor.getI18NString(
												'instructor_groups.error.associated_courses_load');

					ilios.alert.alert(msg + ": " + parsedObject.error);

					ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
					   									   'instructor_groups.loading_associated_courses');
					ilios.alert.updateServerInteractionProgress();

					return;
				}

				idStr = ilios.igm.generateIdForAssociatedCoursesDiv(containerNumber);
				containerElement = document.getElementById(idStr);

				if (parsedObject.courses.length == 0) {
					var str = ilios_i18nVendor.getI18NString(
														'instructor_groups.no_associated_courses');

					element = document.createElement('span');
					element.setAttribute('class', 'read_only_data');
					//element.setAttribute('style', 'font-style: italic;');
					element.innerHTML = str;

					containerElement.appendChild(element);
				}
				else {
					var model = null;
					var yearInt = 0;


					for (var key in parsedObject.courses) {
						model = parsedObject.courses[key];

						yearInt = parseInt(model.year);
						yearInt -= 1999;

						element = document.createElement('a');
						element.setAttribute('href',
											 courseControllerURL + '?course_id=' + model.course_id);
						element.innerHTML = model.title + ' - ' + model.year + '-' + yearInt;

						containerElement.appendChild(element);
						containerElement.appendChild(document.createElement('br'));
					}
				}

				containerElement.hasLoaded = true;

				ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
				   									   'instructor_groups.loading_associated_courses');
				ilios.alert.updateServerInteractionProgress();
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	ilios.alert.networkActivityI18NStrings.push('instructor_groups.loading_associated_courses');
	ilios.alert.updateServerInteractionProgress();

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.igm.handleSaveForContainerNumber = function (containerNumber) {
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var url = controllerURL + 'saveGroup';
	var method = "POST";
	var paramString = "instructor_group_id=" + groupModel.getDBId()
								+ "&container_number=" + containerNumber
								+ "&title=" + encodeURIComponent(groupModel.getTitle());
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var group = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var msg = ilios_i18nVendor.getI18NString('instructor_groups.error.group_save');

					ilios.alert.alert(msg + ": " + parsedObject.error);

					return;
				}

				group = ilios.igm.instructorGroupModels[parsedObject.container_number];
				group.clearDirtyState();

				//hack to get the title to refresh on save
				//should also resort the full list
				ilios.dom.collapseChildForContainerNumber(containerNumber,1,ilios.igm.handleGroupDivCollapse);
				ilios.dom.collapseChildForContainerNumber(containerNumber,2,ilios.igm.handleGroupDivCollapse);
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	paramString += '&users=' + JSON.stringify(groupModel.getUsersAsArrayOfUserIds());

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.igm.handleAddMemberUploadClick = function (uploadButtonElement) {
	var containerNumber = uploadButtonElement.getAttribute('cnumber');
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var element = document.getElementById('em_instructor_group_id');
	var url = controllerURL + 'uploadInstructorListCSVFile';
	var method = "POST";
	var uploadCallback = {
			upload: function (resultObject) {
				var parsedObject
					= ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(
																		resultObject.responseText);
				var users = null;
				var group = null;
				var i = 0;
				var len = 0;
				var userModel = null;
				var msg = null;

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
                                    msg = parsedObject.error;
                                    if(parsedObject.rowErrors != null){
                                        var table = '<table>'
                                                + '<thead><tr>'
                                                + '</tr></thead><tbody>';
                                        for (var key in parsedObject.rowErrors) {
                                            var row = parsedObject.rowErrors[key];
                                            var span = row.length +1;
                                            table += '<tr style="border-bottom: 1px black solid"><td  style="white-space: nowrap; padding-right: 1em; font-weight:bold" rowspan="' + span + '">' 
                                                + ilios_i18nVendor.getI18NString('general.terms.entry') + ' '
                                                + key + '</td></tr>';
                                            for (var i = 0; i < row.length; i++) {
                                                if(i == row.length-1){
                                                    table += '<tr style="border-bottom: 1px black solid">';
                                                } else {
                                                    table += '<tr>';
                                                }
                                                table += '<td>' + row[i] + '</td></tr>';
                                            }
                                        }
                                        table += '</tbody></table>';
                                        msg += table;
                                    }
                                    ilios.alert.alert(msg);
                                    
                                    return;
				}

				group = ilios.igm.instructorGroupModels[parsedObject.container_number];
				users = parsedObject.users;
				len = users.length;
				for (; i < len; i++) {
					userModel = new UserModel(users[i]);

					group.addUser(userModel);
				}

				group.clearDirtyState();

				msg = ilios_i18nVendor.getI18NString('instructor_groups.notification.instructors_added');
				document.getElementById('em_transaction_status').innerHTML
							  					   = "" + len + " " + msg.toLowerCase();
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	element.value = groupModel.getDBId();

	element = document.getElementById('em_container_number');
	element.value = containerNumber;

	element = document.getElementById('em_filepath_value');
	element.value = document.getElementById('em_csv_file_field').value;

	YAHOO.util.Connect.setForm('em_upload_form', true);
	YAHOO.util.Connect.asyncRequest(method, url, uploadCallback, '');
};

ilios.igm.handleManualUserAdd = function () {
	var element = document.getElementById('em_last_name');
	var containerNumber = element.getAttribute('cnumber');
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var url = controllerURL + 'addNewUserToGroup';
	var method = "POST";
	var paramString = "instructor_group_id=" + groupModel.getDBId()
														+ "&container_number=" + containerNumber;
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var userModel = null;
				var group = null;
				var cNumber = 0;
				var msg = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					msg = ilios_i18nVendor.getI18NString('instructor_groups.error.instructors_add');

					ilios.alert.alert(msg + ": " + parsedObject.error);

					return;
				}

				cNumber = parsedObject.container_number;

				group = ilios.igm.instructorGroupModels[cNumber];

				userModel = new UserModel(parsedObject.user);
				group.addUser(userModel);

				document.getElementById(ilios.igm.generateIdForMemberCount(cNumber)).innerHTML
																		   = group.getMemberCount();

				group.clearDirtyState();

				msg = ilios_i18nVendor
								  .getI18NString('instructor_groups.notification.instructor_added');
				document.getElementById('em_transaction_status').innerHTML = msg + ": "
																			  + userModel.getDBId();
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	// todo entities-ing
	paramString += '&last_name=' + encodeURIComponent(element.value);

	element = document.getElementById('em_first_name');
	paramString += '&first_name=' + encodeURIComponent(element.value);

	element = document.getElementById('em_middle_name');
	paramString += '&middle_name=' + encodeURIComponent(element.value);

	element = document.getElementById('em_phone');
	paramString += '&phone=' + encodeURIComponent(element.value);

	element = document.getElementById('em_email');
	paramString += '&email=' + encodeURIComponent(element.value);

	element = document.getElementById('em_uc_id');
	paramString += '&uc_uid=' + encodeURIComponent(element.value);

	element = document.getElementById('em_other_id');
	paramString += '&other_id=' + encodeURIComponent(element.value);

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.igm.handleManualGroupAdd = function () {
	var url = controllerURL + 'addNewEmptyGroup';
	var method = "POST";
	var paramString = "next_container=" + ilios.igm.nextContainerNumber;
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var igModel = null;
                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var msg = ilios_i18nVendor.getI18NString('instructor_groups.error.group_add');

					ilios.alert.alert(msg + ": " + parsedObject.error);

					return;
				}

				igModel = new InstructorGroupModel();
				igModel.setDBId(parsedObject.instructor_group_id);
				igModel.setTitle(parsedObject.title);

				igModel.clearDirtyState();
				igModel.addStateChangeListener(ilios.igm.dirtyStateListener, null);

				ilios.igm.instructorGroupModels[parsedObject.container_number] = igModel;

				ilios.igm.createGroupUI(document.getElementById('group_container'),
										parsedObject.container_number);
				ilios.utilities.scrollElementIntoView(document.getElementById(parsedObject.container_number + '_collapser'));
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	ilios.igm.nextContainerNumber++;

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
