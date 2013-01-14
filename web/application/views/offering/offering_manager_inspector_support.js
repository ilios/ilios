/**
 * This file provides javascript functions intended for supporting the inspector pane UI.
 */

/*
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/third_party/date_formatter.js
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */



ilios.namespace('om.inspector');


ilios.om.inspector.presentlyInspectedOfferingModel = null;

/*
 * This method is call via onDomReady. It's responsible for displaying all of the i18n content which
 * 	did not get displayed during initial DOM construction via HTML time.
 *
 * Should be considered @protected
 */
ilios.om.inspector.initializeInspectorPane = function () {
	var i18nStr = ilios_i18nVendor.getI18NString('general.terms.faculty');
	var element = document.getElementById('inspector_pane_instructor_groups_title');

	element.innerHTML = i18nStr + ':';

	i18nStr = ilios_i18nVendor.getI18NString('general.terms.supplemental');
	element = document.getElementById('inspector_pane_offering_attributes_supplemental');
	element.innerHTML = i18nStr;

	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.special_equipment');
	element = document.getElementById('inspector_pane_offering_attributes_equipment');
	element.innerHTML = i18nStr;

	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.professional_attire');
	element = document.getElementById('inspector_pane_offering_attributes_attire');
	element.innerHTML = i18nStr;

	i18nStr = ilios_i18nVendor.getI18NString('general.terms.groups');
	element = document.getElementById('inspector_pane_learners_title');
	element.innerHTML = i18nStr + ':';

	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.activities_materials');
	element = document.getElementById('inspector_pane_learning_materials_title');
	element.innerHTML = i18nStr + ':';

	i18nStr = ilios_i18nVendor.getI18NString('general.terms.vocabulary');
	element = document.getElementById('inspector_pane_vocabulary_title');
	element.innerHTML = i18nStr + ':';

	i18nStr = ilios_i18nVendor.getI18NString('general.terms.objectives');
	element = document.getElementById('inspector_pane_objectives_title');
	element.innerHTML = i18nStr + ':';
};

/*
 * This is a helper method to generate the text string descibing the time range and location of
 * 	this event.
 *
 * Should be considered @private
 */
ilios.om.inspector.generateTimeAndLocationMarkup = function (offeringModel, location) {
	var startDate = offeringModel.getStartDate();

	if (offeringModel.isIndependentLearningEvent()) {
		var ilStr
				= ilios_i18nVendor.getI18NString('course_management.session.independent_learning');
		var dueStr = ilios_i18nVendor.getI18NString('general.phrases.due_by');
		var dueDateStr = startDate.format('dddd dd mmmm, yyyy');

		return ilStr + ' ' + dueStr + ' ' + dueDateStr;
	}
	else {
		var endDate = offeringModel.getEndDate();
		var fromStr = ilios_i18nVendor.getI18NString('general.terms.from');
		var toStr = ilios_i18nVendor.getI18NString('general.terms.to').toLowerCase();
		var startStr = startDate.format('dddd dd mmmm HH:MM');
		var endStr = endDate.format('dddd dd mmmm HH:MM');
		var roomStr = ilios_i18nVendor.getI18NString('general.terms.room');

		return fromStr + ' ' + startStr + ' ' + toStr + ' ' + endStr + '<br/>' + roomStr
					+ ' ' + location;
	}
};

/*
 * This is a helper method to generate the markup listing groups and/or people (used with both the
 * 	instructors and learners sections).
 *
 * Should be considered @private
 */
ilios.om.inspector.generateGroupMarkup = function (groupArray) {
	var model = null;
	var rhett = '';

	for (var key in groupArray) {
		model = groupArray[key];

		if (rhett.length > 0) {
			rhett += '<br/>';
		}

		if (model instanceof UserModel) {
			rhett += model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
		}
		else {
			rhett += ((model.title != null) ? model.title : model.groupTitle);
		}
	}

	return rhett;
};

/*
 * This is a helper method to get the human display text for a session type given the session type's
 * database id.
 * @method getSessionTypeTitleForId
 * @param {Number} sessionTypeId the session type record id
 * @return {String} the session type title, or a blank string if none could be found.
 * @private
 */
ilios.om.inspector.getSessionTypeTitleForId = function (sessionTypeId) {
    var model = ilios.om.loadedSessionTypes[sessionTypeId];
    if (model) {
        return (model.title || '');
    }
    return '';
};

/**
 * Given an offeringModel instance, populate the inspector pane UI with all of the data held by
 * 	that instance.
 */
ilios.om.inspector.populateInspectorForModel = function (offeringModel) {
	var element = document.getElementById('inspector_pane_title_bar');
	var multipleStr = ilios_i18nVendor.getI18NString('course_management.multiple_offerings');
	var location = (offeringModel.isAggregatePlaceholder == null) ? offeringModel.getRoom()
																  : multipleStr;
	var allowLearnerView = (offeringModel.isAggregatePlaceholder == null);

	element.setAttribute('class', ('om_inspector_title_bar ilios_event_color_'
															+ offeringModel.getSessionTypeId()));

	element = document.getElementById('inspector_pane_session_type_title');
	element.innerHTML = ilios.om.inspector.getSessionTypeTitleForId(offeringModel.getSessionTypeId());

	element = document.getElementById('inspector_pane_time_and_location');
	element.innerHTML = ilios.om.inspector.generateTimeAndLocationMarkup(offeringModel, location);

	element = document.getElementById('inspector_pane_instructor_groups_content');
	element.innerHTML = (offeringModel.isAggregatePlaceholder == null)
								? ilios.om.inspector.generateGroupMarkup(offeringModel.getInstructors())
								: multipleStr;

	element = document.getElementById('inspector_pane_learners_content');
	element.innerHTML = (offeringModel.isAggregatePlaceholder == null)
								? ilios.om.inspector.generateGroupMarkup(offeringModel.getStudentGroups())
								: multipleStr;

	element = document.getElementById('inspector_pane_title_bar_title');
	if (offeringModel.getOwningSessionTitle() != null) {
		var url = controllerURL + 'getExtraInspectorContentsForSession';
		var method = "POST";
		var paramString = "session_id=" + offeringModel.getSessionId();
		var ajaxCallback = {
				success: function (resultObject) {
					var parsedObject = null;
					var el = null;
					var noStr = null;
					var yesStr = null;
					var modelArray = null;
					var model = null;
					var li = null;
					var textNode = null;
                    var key = null;

                    try {
                        parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                    }
                    catch (e) {
						ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
															   'offering_management.offering_inspection');
						ilios.alert.updateServerInteractionProgress();

                        ilios.global.defaultAJAXFailureHandler(null, e);

                        return;
                    }

					// MAY RETURN THIS BLOCK
					if (parsedObject.error != null) {
						var msg
							= ilios_i18nVendor
									.getI18NString('offering_management.error.failed_inspection');

						ilios.alert.alert(msg + ": " + parsedObject.error);

						ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
															   'offering_management.offering_inspection');
						ilios.alert.updateServerInteractionProgress();

						return;
					}

					noStr = ilios_i18nVendor.getI18NString('general.terms.no');
					yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

					el = document
						   .getElementById('inspector_pane_offering_attributes_supplemental_value');
					el.innerHTML = ((parsedObject.supplemental == '1') ? yesStr : noStr);

					el = document
							.getElementById('inspector_pane_offering_attributes_equipment_value');
					el.innerHTML = ((parsedObject.special_equipment == '1') ? yesStr : noStr);

					el = document.getElementById('inspector_pane_offering_attributes_attire_value');
					el.innerHTML = ((parsedObject.attire == '1') ? yesStr : noStr);

					el = document.getElementById('inspector_pane_vocabulary_content');
					modelArray = parsedObject.mesh_terms;
					textNode = "";
					for (key in modelArray) {
						if (textNode.length > 0) {
							textNode += "; ";
						}

						textNode += modelArray[key];
					}
					el.innerHTML = textNode;

					el = document.getElementById('inspector_pane_objectives_content');
					ilios.utilities.removeAllChildren(el);
					modelArray = parsedObject.objectives;
					for (key in modelArray) {
						model = modelArray[key];

						li = document.createElement('li');
						li.setAttribute('style', 'margin-bottom: 12px;');

						textNode = document.createTextNode(model);
						li.appendChild(textNode);

						el.appendChild(li);
					}

					el = document.getElementById('inspector_pane_learning_materials_content');
					ilios.utilities.removeAllChildren(el);
					modelArray = parsedObject.learning_materials;
					for (key in modelArray) {
						model = new LearningMaterialModel(modelArray[key]);

						el.appendChild(ilios.om.inspector.createLIElementForLearningMaterial(model));
					}

					ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
														   'offering_management.offering_inspection');
					ilios.alert.updateServerInteractionProgress();
				},

				failure: function (resultObject) {
    				ilios.global.defaultAJAXFailureHandler(resultObject);
        		}};

		ilios.alert.networkActivityI18NStrings.push('offering_management.offering_inspection');
		ilios.alert.updateServerInteractionProgress();

		element.innerHTML = '<a href="' + courseControllerURL + '?course_id=' + parentCourseId
								+ '&session_id=' + offeringModel.getSessionId() + '">'
								+ offeringModel.getOwningSessionTitle() + '</a>';

		element = document.getElementById('inspector_pane_offering_attributes_supplemental_value');
		element.innerHTML = 'XXXX';

		element = document.getElementById('inspector_pane_offering_attributes_equipment_value');
		element.innerHTML = 'XXXX';

		element = document.getElementById('inspector_pane_offering_attributes_attire_value');
		element.innerHTML = 'XXXX';

		element = document.getElementById('inspector_pane_vocabulary_content');
		element.innerHTML = 'XXXX';

		element = document.getElementById('inspector_pane_objectives_content');
		ilios.utilities.removeAllChildren(element);

		YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
	}
	else {
		var noStr = ilios_i18nVendor.getI18NString('general.terms.no');
		var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
		var li = null;
		var textNode = null;
		var modelArray = null;
		var model = null;
        var key = null;

		element.innerHTML = sessionModel.getTitle();

		element = document.getElementById('inspector_pane_offering_attributes_supplemental_value');
		element.innerHTML = (sessionModel.isSupplemental() ? yesStr : noStr);

		element = document.getElementById('inspector_pane_offering_attributes_equipment_value');
		element.innerHTML = (sessionModel.isEquipmentRequired() ? yesStr : noStr);

		element = document.getElementById('inspector_pane_offering_attributes_attire_value');
		element.innerHTML = (sessionModel.isAttireRequired() ? yesStr : noStr);

		element = document.getElementById('inspector_pane_vocabulary_content');
		element.innerHTML = sessionModel.getMeSHItemsAsFormattedText();

		element = document.getElementById('inspector_pane_objectives_content');
		ilios.utilities.removeAllChildren(element);
		modelArray = sessionModel.getObjectives();
		for (key in modelArray) {
			model = modelArray[key];

			li = document.createElement('li');
			li.setAttribute('style', 'margin-bottom: 12px;');

			textNode = document.createTextNode(model.getDescription());
			li.appendChild(textNode);

			element.appendChild(li);
		}

		element = document.getElementById('inspector_pane_learning_materials_content');
		ilios.utilities.removeAllChildren(element);
		modelArray = sessionModel.getLearningMaterials();
		for (key in modelArray) {
			model = modelArray[key];

			element.appendChild(ilios.om.inspector.createLIElementForLearningMaterial(model));
		}
	}

	element = new YAHOO.util.Element(document.getElementById('inspector_pane'));
	element.setStyle('display', 'block');

	element = document.getElementById('inspector_pane_open_learner_view');
	ilios.dom.setEnableForAElement(element, allowLearnerView);

	ilios.om.inspector.presentlyInspectedOfferingModel = offeringModel;
};

ilios.om.inspector.createLIElementForLearningMaterial = function (model) {
	var rhett = document.createElement('li');
	var titleElement = document.createElement('span');
	var downloadURL = learningMaterialsControllerURL
							+ "getLearningMaterialWithId?learning_material_id=" + model.getDBId();
	var isLink = (model.getMimeType() == 'link');
	var isCitation = (model.getMimeType() == 'citation');
	var downloadWidget = document.createElement('div');
	var buttonWidgetDiv = document.createElement('div');

	rhett.setAttribute('class', ilios.utilities.convertMimeTypeToCSSClassName(model.getMimeType()));
	rhett.setAttribute('style', 'position: relative;');

	titleElement.innerHTML = model.getTitle();
	titleElement.setAttribute('class', 'title');

	rhett.appendChild(titleElement);

	buttonWidgetDiv.setAttribute('class', 'buttonset');

	downloadWidget.setAttribute('class', 'download_widget');
	//downloadWidget.setAttribute('style', 'margin: 0;');

	if ((! isLink) && (! isCitation)) {
		buttonWidgetDiv.appendChild(downloadWidget);
	}

	rhett.appendChild(buttonWidgetDiv);

	YAHOO.util.Event.addListener(downloadWidget, 'click', function (e) {
        window.location.href = downloadURL;
    });
	return rhett;
};
