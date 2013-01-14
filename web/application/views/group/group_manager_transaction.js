/**
 * DEPENDENCIES:
 * 		YUI lib (at least YAHOO.widget and its dependencies)
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_utilities.js
 * 		group_manager_dom.js
 */

ilios.namespace('gm.transaction');


ilios.gm.transaction.autogenerateSubgroups = function (event) {
	var containerNumber = this.cnumber;
	var numGroups = document.getElementById(ilios.gm.generateIdForAutogenerateTextfield(containerNumber))
																							.value;
	var url = controllerURL + "autogenerateSubGroups";
	var method = "POST";
	var paramString = "num_groups=" + numGroups;
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var model = null;
				var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				/*
				 * The usage of the variable containerNumber in here, while legal, is troubling
				 *		from a scope perspective. Do i trust the javascript engine to eventually
				 *		clean it up? Sure, probably... i'm more ok with the notion that the number
				 *		of times this method would be called during the lifespan of the page is
				 *		vanishingly small.
				 */
				ilios.gm.enableGroupGenerationDiv(containerNumber, false);

				// since, by design, we can only autogenerate top level groups, we know from
				//		which group to request the subgroup
				model = ilios.gm.currentModel.getRootGroup()
													.getSubgroupForContainerNumber(containerNumber);

				ilios.gm.handleSubgroupLoad(parsedObject.XHRDS, model);

				element = document.getElementById(
									   ilios.gm.generateIdForManageSubgroupsCount(containerNumber));
				element.innerHTML = " (" + model.getSubgroupCount() + ")";

				ilios.gm.subgroup.indexPathsNeedRebuilding = true;
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
   			}};
	var groupModel = ilios.gm.currentModel.getRootGroup()
													.getSubgroupForContainerNumber(containerNumber);

	paramString += "&cohort_id=" + ilios.gm.currentModel.getCohortId();
	paramString += "&group_id=" + groupModel.getDBId();

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.gm.transaction.saveGroupModel = function (event) {
	var rootModel = ilios.gm.currentModel.getRootGroup();
	var url = controllerURL + 'saveGroupModelTree';
	var method = "POST";
	var paramString = "whole_model_glom=" + escape(rootModel.getAsJSONString());
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var rootGroup = null;
				var subGroups = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var i18nStr = ilios_i18nVendor.getI18NString('groups.error.group_save');

					ilios.alert.alert(i18nStr + ': ' + parsedObject.error);

					return;
				}

				rootGroup = ilios.gm.currentModel.getRootGroup();
				subGroups = rootGroup.getSubgroups();
				for (var key in subGroups) {
					subGroups[key].clearDirtyState();
				}
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.gm.transaction.deleteGroup = function (event) {
	var target = ilios.utilities.getEventTarget(event);
	var containerNumber = target.getAttribute('cnumber');
	var continuedAction = function () {
		var deletePrefix = ilios_i18nVendor.getI18NString('general.warning.delete_prefix');
		var groupWord = ilios_i18nVendor.getI18NString('general.terms.group').toLowerCase();

		// todo *TECHNICALLY* this save is asynchronous ultimately and so could still go rancid
		if (ilios.gm.currentModel.getRootGroup().isModelDirty()) {
			ilios.gm.transaction.saveGroupModel(null);
		}

		if (this.cancelEvent != null) {
			this.hide();
		}

		ilios.alert.inform('<center><b>' + deletePrefix + ' ' + groupWord + '?</b></center>',
			ilios_i18nVendor.getI18NString('general.terms.yes'),
			ilios.gm.transaction.continueDeleteGroup, {"cnumber": containerNumber});
	};

	if (ilios.gm.currentModel.getRootGroup().isModelDirty()) {
		var dirtyStr = ilios_i18nVendor.getI18NString('general.warning.dirty_state_save_changes');
		var saveQueryStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_save');
		var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

		ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
					+ '</p><center><b>' + saveQueryStr + '</b></center>'), yesStr, continuedAction);
	}
	else {
		continuedAction();
	}
};

/**
 * Button-click event handler function. Tied to the "ok" button of the "delete group" dialog.
 * Deletes a given group.
 * @method continueDeleteGroup
 * @param {Event} event the click event
 * @param {Object} args event-handler argument object, expects to contain the following:
 *     "cnumber" .. the group's container number
 * @private
 */
ilios.gm.transaction.continueDeleteGroup = function (event, args) {
	var containerNumber = args.cnumber;
	var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
	var url = controllerURL + "deleteGroup";
	var method = "POST";
	var paramString = "group_id=" + groupModel.getDBId() + "&container_number=" + containerNumber;
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
					var i18nStr = ilios_i18nVendor.getI18NString('groups.error.group_delete');

					ilios.alert.alert(i18nStr + ": " + parsedObject.error);

					return;
				}

				cNumber = parsedObject.container_number;

				/*
				 * TODO if root view group is not the root group then add all of the deleted models
				 * 			users (and its subgroups users and ...) to the parent group
				 */
				if (ilios.gm.currentRootViewGroup != ilios.gm.currentModel.getRootGroup()) {
					var deletedModel
							= ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(cNumber);
					var allUsers = deletedModel.getAllUsers();

					for (var key in allUsers) {
						ilios.gm.currentRootViewGroup.addUser(allUsers[key], true);
					}
				}

				// delete from model
				ilios.gm.currentRootViewGroup.removeSubgroupForContainerNumber(cNumber);
				ilios.gm.currentRootViewGroup.clearDirtyState();

				// delete from DOM
				collapserId
						= ilios.dom.childCollapsingContainerIdForContainerNumber(cNumber);
				groupContainer = document.getElementById(collapserId).parentNode;
				groupContainer.parentNode.removeChild(groupContainer);

				ilios.gm.subgroup.indexPathsNeedRebuilding = true;
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	this.hide();

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.gm.transaction.handleAddMemberUploadClick = function (uploadLinkDOMElement) {
	var url = controllerURL + 'uploadStudentListCSVFile';
	var method = "POST";
	var element = document.getElementById('em_cohort_id');
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
				var i18nStr = null;

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var msg = ilios_i18nVendor.getI18NString('groups.error.user_add_csv');

					msg += ": " + parsedObject.error;

					if (parsedObject.duplicates != null) {
						var first = true;

						msg += '<br/>';

						for (var key in parsedObject.duplicates) {
							if (first) {
								first = false;
							}
							else {
								msg += '; ';
							}

							msg += parsedObject.duplicates[key];
						}
					}

					ilios.alert.alert(msg);

					return;
				}

				group = ilios.gm.currentRootViewGroup;

				users = parsedObject.users;
				len = users.length;
				for (; i < len; i++) {
					userModel = new StudentModel(users[i]);

					group.addUser(userModel);
				}

				group.clearDirtyState();

				document.getElementById('current_enrollment').innerHTML = group.getUserCount();

				i18nStr = ilios_i18nVendor.getI18NString('groups.notification.users_added');
				document.getElementById('em_transaction_status').innerHTML = "" + len + " "
																			+ i18nStr.toLowerCase();
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	element.value = ilios.gm.currentModel.getCohortId();

	element = document.getElementById('em_filepath_value');
	element.value = document.getElementById('em_csv_file_field').value;

	YAHOO.util.Connect.setForm('em_upload_form', true);
	YAHOO.util.Connect.asyncRequest(method, url, uploadCallback, '');
};

ilios.gm.transaction.handleManualUserAdd = function () {
	var element = document.getElementById('em_last_name');
	var url = controllerURL + 'addNewUserToGroup';
	var method = "POST";
	var paramString = "container_number=-1&cohort_id=" + ilios.gm.currentModel.getCohortId();
	var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var userModel = null;
				var group = null;
				var i18nStr = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					i18nStr = ilios_i18nVendor.getI18NString('groups.error.user_add');

					ilios.alert.alert(i18nStr + ": " + parsedObject.error);

					return;
				}

				group = ilios.gm.currentModel.getRootGroup();

				userModel = new StudentModel(parsedObject.user);
				group.addUser(userModel);

				document.getElementById('current_enrollment').innerHTML = group.getUserCount();

				group.clearDirtyState();

				i18nStr = ilios_i18nVendor.getI18NString('groups.notification.user_added');
				document.getElementById('em_transaction_status').innerHTML
										= i18nStr + ": " + userModel.getDBId();
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	// todo entities-ing
	paramString += '&last_name=' + element.value;

	element = document.getElementById('em_first_name');
	paramString += '&first_name=' + element.value;

	element = document.getElementById('em_middle_name');
	paramString += '&middle_name=' + element.value;

	element = document.getElementById('em_phone');
	paramString += '&phone=' + element.value;

	element = document.getElementById('em_email');
	paramString += '&email=' + element.value;

	element = document.getElementById('em_uc_id');
	paramString += '&uc_uid=' + element.value;

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.gm.transaction.handleManualGroupAdd = function () {
	var rootGroup = null;
	var url = null;
	var method = null;
	var paramString = null;
	var ajaxCallback = null;

	// MAY RETURN THIS BLOCK
	if (ilios.gm.currentModel == null) {
		return;
	}

	rootGroup = ilios.gm.currentRootViewGroup;
	url = controllerURL + 'addNewGroup';
	method = "POST";

	paramString = "cohort_id=" + ilios.gm.currentModel.getCohortId() + "&group_id=";
	if (rootGroup.getParentGroup() == null) {
		paramString += "-1";
	}
	else {
		paramString += rootGroup.getDBId();
	}
	paramString += "&next_container=" + rootGroup.getNextContainerNumber();
	ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var viewRoot = ilios.gm.currentRootViewGroup;
				var newGroupModel = null;
				var containerNumber = -1;
				var dummyIndexPath = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

				// MAY RETURN THIS BLOCK
				if (parsedObject.error != null) {
					var i18nStr = ilios_i18nVendor.getI18NString('groups.error.group_add');

					ilios.alert.alert(i18nStr + ": " + parsedObject.error);

					return;
				}

				if (ilios.gm.subgroup.isPresentlyCollapsed) {
					dummyIndexPath = viewRoot.indexPath.concat();
					dummyIndexPath[viewRoot.indexPath.length] = 1;
				}

				newGroupModel = ilios.gm.createSubgroupModelAndUI(document.getElementById('group_container'),
														 		  viewRoot, parsedObject.group_id,
														 		  parsedObject.title, null, '', '',
														 		  null, null, dummyIndexPath);

				containerNumber = viewRoot.getContainerNumberForSubgroup(newGroupModel);

				// this functionality was done on the db side during the successful group add; we're
				//		sparing a server transaction here
				if (viewRoot.getParentGroup() == null) {
					var users = viewRoot.getUsers();
					var element = null;

					for (var key in users) {
						newGroupModel.addUser(users[key], true);
					}

					element
					   = document.getElementById(ilios.gm.generateIdForEnrollment(containerNumber));
					element.innerHTML = newGroupModel.getUserCount();
				}

				if (ilios.gm.subgroup.isPresentlyCollapsed) {
					ilios.gm.subgroup.updateSubgroupCount(viewRoot);
				}

				ilios.gm.enableGroupGenerationDiv(containerNumber, (viewRoot.getParentGroup() == null));

				ilios.gm.subgroup.indexPathsNeedRebuilding = true;
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);
    		}};

	YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
