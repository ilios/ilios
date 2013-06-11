/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.cm.loadedCourseModel = null;
ilios.cm.currentCourseModel = null;

ilios.cm.inEditObjectiveModel = null;
ilios.cm.inEditObjectiveContainerTextField = null;
ilios.cm.inEditObjectiveContainerTextEditor = null;
ilios.cm.programCohortObjectives = [];
ilios.cm.adjustingProgramCohortObjectiveDOM = false;

ilios.cm.uiElementsToHideOnLockedView = null;
ilios.cm.uiElementsToDisableOnLockedView = null;

ilios.cm.yuiCalendarInstance = null;

ilios.cm.yuiCalendarModificationTarget = { COURSE_START: 'course start',
                                           COURSE_END: 'course end',
                                           ROLLOVER_START: 'rollover start',
                                           ROLLOVER_END: 'rollover end' };

/**
 * Dirty-state change-listener for course and session models.
 * @property dirtyStateListener
 * @type {Object}
 * @todo break this up into two distinct state listeners - one for session models and another one for course models
 */
ilios.cm.dirtyStateListener = {

    /**
     * Updates the course or session model's display if the model has changed.
     * @method modelChanged
     * @param {CourseModel | SessionModel} model the course or session model
     * @param {Object} obj state-change-listener arguments.
     */
    modelChanged: function (model, obj) {
        var enabled = model.isModelDirty();
        var idStr = null;
        var element = null;
        var containerNumber = -1;
        var Element = YAHOO.util.Element;

        if (model == ilios.cm.currentCourseModel) { // current course model
            var publishability = -1;
            var enable = false;

            element = document.getElementById('draft_button');
            ilios.dom.setElementEnabled(element, enabled);

            element = document.getElementById('publish_button');
            enable = ilios.utilities.modelItemNeedsPublishing(ilios.cm.currentCourseModel);
            if (enable) {
                enable = (ilios.cm.currentCourseModel.getPublishability()
                    != ilios.cm.currentCourseModel.CANNOT_BE_PUBLISHED);
            }
            ilios.dom.setElementEnabled(element, enable);

            //idStr = ilios.cm.generateIdStringForPublishWarning(containerNumber);
            //element = new Element(document.getElementById(idStr));
            publishability = model.getPublishability();

            if ((! enable) || (publishability != model.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS)) {
//                element.setStyle('display', 'none');
                YAHOO.util.Dom.removeClass(element, 'icon-warning');
            } else {
//                element.setStyle('display', 'inline-block');
                YAHOO.util.Dom.addClass(element, 'icon-warning');
            }

            element = document.getElementById('reset_button');
            ilios.dom.setElementEnabled(element, enabled);

            //element = document.getElementById('course_title').parentNode;
            element = YAHOO.util.Dom.getElementsByClassName('level-1', 'div', document.getElementById('course_form'))[0];

            if (enabled) {
                (new Element(element)).addClass('dirty_state');
            } else {
                (new Element(element)).removeClass('dirty_state');
            }

            element = document.getElementById('-1_director_picker_selected_text_list');
            element.innerHTML = model.getDirectorsAsFormattedText();

            element = document.getElementById('-1_mesh_terms_picker_selected_text_list');
            element.innerHTML = model.getMeSHItemsAsFormattedText();

            element = document.getElementById('course_associated_learners');
            element.innerHTML = ilios.cm.currentCourseModel.getAssociatedLearnersAsFormattedText();

            ilios.cm.updateObjectiveMeSHCounts(model, -1);
        } else { // session model
            containerNumber = ilios.cm.currentCourseModel.getContainerForSession(model);
            idStr = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);

            element = document.getElementById(idStr).parentNode;
            if (enabled) {
                (new Element(element)).addClass('dirty_state');
            } else {
                (new Element(element)).removeClass('dirty_state');
            }

            element = document.getElementById('' + containerNumber + '_mesh_terms_picker_selected_text_list');
            element.innerHTML = model.getMeSHItemsAsFormattedText();

            element = document.getElementById('' + containerNumber + '_child_save');
            ilios.dom.setElementEnabled(element, enabled);

            ilios.cm.session.updatePublishButtonForSession(model, containerNumber);
            ilios.cm.updateObjectiveMeSHCounts(model, containerNumber);
        }

        ilios.cm.updatePublishAllUI();
    }

}

/**
 * Dirty-state change-listener for objective models.
 * @property objectiveDirtyStateListener
 * @type {Object}
 */
ilios.cm.objectiveDirtyStateListener = {

    /**
     * Updates the objective model's display if the model has changed.
     * @method modelChanged
     * @param {ObjectiveModel} model the objective model
     * @param {Object} obj state-change-listener arguments. Parameters assumed:
     *     "containerId": DOM element Id for the objective description container corresponding to the given objective model.
     */
    modelChanged: function (model, obj) {
        var container = null;
        var o = YAHOO.lang.isObject(obj) || {};
        if (obj.containerId) {
            container = document.getElementById(obj.containerId);
            if (container) {
                container.innerHTML = model.getTitle();
            }
        }
    }
};

/**
 * Dirty-state change-listener for session models.
 * @property sessionDescriptionDirtyStateListener
 * @type {Object}
 * @todo expand this listener's scope to cover the whole session display
 */
ilios.cm.sessionDescriptionDirtyStateListener = {
    /**
     * Updates the session model's description display if the model has changed.
     * @method modelChanged
     * @param {SessionModel} model the session model
     * @param {Object} obj state-change-listener arguments. Parameters assumed:
     *     "containerId": DOM element Id for the session description container corresponding to the given session model.
     */
    modelChanged: function(model, obj) {
        var container = null;
        var o = YAHOO.lang.isObject(obj) || {};
        if (obj.containerId) {
            container = document.getElementById(obj.containerId);
            if (container) {
                container.innerHTML = model.getDescription();
            }
        }
    }
}


ilios.cm.displayAddNewCourseDialog = function () {
    if ((ilios.cm.currentCourseModel != null)
            && (ilios.cm.currentCourseModel.isModelDirty()
                            || ilios.cm.currentCourseModel.containsDirtySessions())) {
        var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + notification
                + '</p><center><b>' + continueStr + '</b></center>'), yesStr,
               ilios.cm.continueDisplayAddNewCourseDialog);

        return;
    }

    ilios.ui.onIliosEvent.fire({ action: 'default_dialog_open', event: 'add_new_course'});
};

ilios.cm.continueDisplayAddNewCourseDialog = function () {
    this.cancel();      // (alert panel dismiss)

    ilios.ui.onIliosEvent.fire({ action: 'default_dialog_open', event: 'add_new_course' });
};

ilios.cm.windowWillClose = function (e) {
    if ((ilios.cm.currentCourseModel != null)
            && (ilios.cm.currentCourseModel.isModelDirty()
                            || ilios.cm.currentCourseModel.containsDirtySessions())) {
        var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');

        e.returnValue = notification; // most browsers

        return notification; // safari
    }
};

// @private
ilios.cm.calendarSelectionHandler = function (type, args, obj) {
    var selected = null;
    var selectedDate = null;
    var formattedDate = null;
    var element = null;

    // 'this' is the calendar
    if (this.isProgrammaticallySelectingDates) {
        return;
    }

    selected = args[0];
    selectedDate = this.toDate(selected[0]);
    formattedDate = ilios.utilities.dateObjectToMySQLFriendly(selectedDate, false);

    if (this.modificationTarget == ilios.cm.yuiCalendarModificationTarget.COURSE_START) {
        ilios.cm.currentCourseModel.setStartDate(formattedDate);

        element = document.getElementById('course_start_date');
    }
    else if (this.modificationTarget == ilios.cm.yuiCalendarModificationTarget.COURSE_END) {
        ilios.cm.currentCourseModel.setEndDate(formattedDate);

        element = document.getElementById('course_end_date');
    }
    else if (this.modificationTarget == ilios.cm.yuiCalendarModificationTarget.ROLLOVER_START) {
        ilios.cm.rollover.setRolloverStartDate(selectedDate);
    }

    if (element != null) {
        element.innerHTML = selectedDate.format('ddd mmm dd yyyy');
    }

    this.hide();
};

/*
 * Assures that the calendar to display is set with the appropriate year and month.
 *
 * @private
 */
ilios.cm.setCalendarToDate = function (dateObject) {
    if (ilios.cm.yuiCalendarInstance != null) {
        var dateObjectToUse = (dateObject != null) ? dateObject : new Date();

        ilios.cm.yuiCalendarInstance.setYear(dateObjectToUse.getFullYear());
        ilios.cm.yuiCalendarInstance.setMonth(dateObjectToUse.getMonth());

        ilios.cm.yuiCalendarInstance.isProgrammaticallySelectingDates = true;
        ilios.cm.yuiCalendarInstance.select(dateObjectToUse);
        ilios.cm.yuiCalendarInstance.isProgrammaticallySelectingDates = false;

        ilios.cm.yuiCalendarInstance.render();
    }
};

// @private
ilios.cm.moveCalendarToDOMElement = function (element) {
    var xyCoordinates = YAHOO.util.Dom.getXY(element);
    var element = new YAHOO.util.Element(document.getElementById('date_picking_calendar_container'));

    element.setStyle('left', (xyCoordinates[0] + 'px'));
    element.setStyle('top', (xyCoordinates[1] + 'px'));
};

/*
 * Call via onDOMReady
 */
ilios.cm.registerSaveAndPublishAll = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('save_all_dirty_to_draft');
    var saveAllStr = ilios_i18nVendor.getI18NString('course_management.warning.save_all_draft');
    var publishAllStr = ilios_i18nVendor.getI18NString('course_management.warning.publish_all');
    var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

    Event.addListener(element, 'click', function () {
        ilios.alert.inform('<p style="margin-bottom:9px; text-align:justify;">'
            + saveAllStr + ':</p><center><b>' + continueStr
            + '</b></center>',
            yesStr, ilios.cm.transaction.saveAllDirty);
    });

    element = document.getElementById('publish_all');
    Event.addListener(element, 'click', function () {
        ilios.alert.inform('<p style="margin-bottom:9px; text-align:justify;">'
            + publishAllStr + ':</p><center><b>' + continueStr
            + '</b></center>',
            yesStr, ilios.cm.transaction.publishAll);
    });
};

/*
 * Call via onDOMReady
 */
ilios.cm.registerCourseUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('course_title');

    if (ilios.cm.yuiCalendarInstance == null) {
        var titleStr = ilios_i18nVendor.getI18NString('general.phrases.choose_date') + ':';

        ilios.cm.yuiCalendarInstance
                        = new YAHOO.widget.Calendar("yuiCal", "date_picking_calendar_container",
                                                    {title: titleStr, close: true} );
        ilios.cm.yuiCalendarInstance.render();
        ilios.cm.yuiCalendarInstance.hide();

        ilios.cm.yuiCalendarInstance.isProgrammaticallySelectingDates = false;

        ilios.cm.yuiCalendarInstance.selectEvent.subscribe(ilios.cm.calendarSelectionHandler,
                                                           ilios.cm.yuiCalendarInstance,
                                                           true);
    }

    Event.addListener(element, 'keyup', function () {
        if (! document.getElementById('course_level_selector').hasAttribute('disabled')) {
            var newTitle = document.getElementById('course_title').value;
            ilios.cm.currentCourseModel.setTitle(newTitle);
        }
    });


    element = document.getElementById('external_course_id');
    Event.addListener(element, 'keyup', function () {
        var newECId = document.getElementById('external_course_id').value;
        ilios.cm.currentCourseModel.setExternalCourseId(newECId);
    });

    element = document.getElementById('start_date_calendar_button');
    Event.addListener(element, 'click', function () {
        if (! document.getElementById('course_level_selector').hasAttribute('disabled')) {
            ilios.cm.yuiCalendarInstance.modificationTarget = ilios.cm.yuiCalendarModificationTarget.COURSE_START;

            ilios.cm.setCalendarToDate(ilios.cm.currentCourseModel.getStartDateAsDateObject());

            ilios.cm.moveCalendarToDOMElement(this);

            ilios.cm.yuiCalendarInstance.show();
        }
    });

    element = document.getElementById('end_date_calendar_button');
    Event.addListener(element, 'click', function () {
        if (! document.getElementById('course_level_selector').hasAttribute('disabled')) {
            ilios.cm.yuiCalendarInstance.modificationTarget = ilios.cm.yuiCalendarModificationTarget.COURSE_END;
            ilios.cm.setCalendarToDate(ilios.cm.currentCourseModel.getEndDateAsDateObject());
            ilios.cm.moveCalendarToDOMElement(this);
            ilios.cm.yuiCalendarInstance.show();
        }
    });


    element = document.getElementById('course_level_selector');
    Event.addListener(element, 'change', function () {
        ilios.cm.currentCourseModel.setCourseLevel(this.options[this.selectedIndex].value);
    });

    element = document.getElementById('clerkship_type_selector');
    Event.addListener(element, 'change', function () {
        ilios.cm.currentCourseModel.setClerkshipTypeId(this.options[this.selectedIndex].value);
    });

    element = document.getElementById('-1_learning_material_expand_widget');
    Event.addListener(element, 'click', function () {
        ilios.cm.lm.setLearningMaterialDivVisibility(-1, this, true);
    });

    element = document.getElementById('-1_objectives_container_expand_widget');
    Event.addListener(element, 'click', function () {
        ilios.cm.setObjectiveDivVisibility(-1, this, true);
    });

    element = document.getElementById('lm_meta_statuses_selector');
    Event.addListener(element, 'change', function () {
        ilios.cm.transaction.modifyLearningMaterial(this);
    });
};

// @private
ilios.cm.courseLoader = function (courseModelStub) {
    var Element = YAHOO.util.Element;
    var loadedCourseDetails = null;
    var element = null;
    var publishability = -1;
    var enable = false;

    // we don't want to AJAX this one lest the user be left with a half-state waiting on a tardy
    //          asynchronous reply
    loadedCourseDetails = ilios.course_model.loadCourseTreeSynchronously(courseModelStub.getDBId(), false, null, 'getCourseTree');

    ilios.cm.populateCourseAndSetEnable(courseModelStub.getTitle(), courseModelStub.getStartDate(),
                                        courseModelStub.getEndDate(), courseModelStub.getYear(),
                                        courseModelStub.getDBId(),
                                        courseModelStub.getExternalCourseId(),
                                        courseModelStub.getUniqueId(),
                                        courseModelStub.getCourseLevel(),
                                        courseModelStub.getPublishEventId(),
                                        loadedCourseDetails.cohorts,
                                        loadedCourseDetails.competencies,
                                        loadedCourseDetails.disciplines,
                                        loadedCourseDetails.directors,
                                        loadedCourseDetails.meshTerms,
                                        loadedCourseDetails.objectives,
                                        loadedCourseDetails.learningMaterials,
                                        loadedCourseDetails.sessions,
                                        loadedCourseDetails.learners,
                                        courseModelStub.isLocked(),
                                        courseModelStub.isPublishedAsTBD(),
                                        loadedCourseDetails.clerkship_type_id);

    element = new Element(document.getElementById('course_more_or_less_div'));
    if (element.getStyle('display') != 'none') {
        ilios.utilities.toggleShowMoreOrLess('course_more_or_less_div',
                                             document.getElementById('show_more_or_less_link'));
    }

    element = document.getElementById('save_all_dirty_to_draft');
    ilios.dom.setElementEnabled(element, true);

    ilios.cm.updatePublishAllUI();

    element = document.getElementById('publish_button');
    publishability = ilios.cm.currentCourseModel.getPublishability();
    enable = ilios.utilities.modelItemNeedsPublishing(ilios.cm.currentCourseModel);
    if (enable) {
        enable = (publishability != ilios.cm.currentCourseModel.CANNOT_BE_PUBLISHED);
    }
    ilios.dom.setElementEnabled(element, enable);
    //element = new Element(document.getElementById(ilios.cm.generateIdStringForPublishWarning("-1")));
    if ((! enable)
         || (publishability != ilios.cm.currentCourseModel.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS)) {
    //    element.setStyle('display', 'none');
        YAHOO.util.Dom.removeClass(element, 'icon-warning');
    }
    else {
    //    element.setStyle('display', 'inline-block');
        YAHOO.util.Dom.addClass(element, 'icon-warning');
    }

    ilios.cm.handleArchivingLinkVisibility();
    ilios.cm.handleRolloverLinkVisibility();

    ilios.cm.session.collapseOrExpandSessions(true);

    if (! courseModelStub.isLocked()) { // only unlock the course title if the course itself is not locked
        element = document.getElementById('course_title');
        element.removeAttribute('disabled');
    }
    ilios.cm.cs.courseSearchDialog.cancel();
};

ilios.cm.updatePublishAllUI = function () {
    var publishability = ilios.cm.currentCourseModel.getTreePublishability();
    var element = document.getElementById('publish_all');
    var enable = ((publishability != ilios.cm.currentCourseModel.CANNOT_BE_PUBLISHED)
                                        && (! ilios.cm.currentCourseModel.treeIsFullyPublished()));

    ilios.dom.setElementEnabled(element, enable);

    //element = new YAHOO.util.Element(document.getElementById('publish_all_warning'));
    if ((! enable)
         || (publishability != ilios.cm.currentCourseModel.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS)) {
        //element.setStyle('display', 'none');
        YAHOO.util.Dom.removeClass(element,'icon-warning');
    }
    else {
        //element.setStyle('display', 'inline-block');
        YAHOO.util.Dom.addClass(element,'icon-warning');
    }
};

ilios.cm.handleArchivingLinkVisibility = function () {
    var element = document.getElementById('archiving_link_div');
    var showArchiveLink = (ilios.global.preferencesModel.showCourseArchiving()
                                    && ilios.cm.currentCourseModel.isPublished());

    if (showArchiveLink) {
        var sessions = ilios.cm.currentCourseModel.getSessions();
        var allPublished = true;

        for (var key in sessions) {
            if (! sessions[key].isPublished()) {
                allPublished = false;

                break;
            }
        }

        showArchiveLink = allPublished;
    }

    if (showArchiveLink) {
        var linkStr = (ilios.cm.currentCourseModel.isLocked()
                                ? ilios_i18nVendor.getI18NString('general.terms.file')
                                : ilios_i18nVendor.getI18NString('general.terms.archive'));

        ilios.cm.populateDialogOpeningLink(element, linkStr, 'archive_dialog_open');

        (new YAHOO.util.Element(element)).setStyle('display', '');
    }
    else {
        (new YAHOO.util.Element(element)).setStyle('display', 'none');
    }
};

ilios.cm.handleRolloverLinkVisibility = function () {
    var element = document.getElementById('rollover_link_div');
    var showRolloverLink = ilios.global.preferencesModel.showCourseRollover();

    if (showRolloverLink) {
        showRolloverLink = ilios.cm.currentCourseModel.isPublished();
    }

    if (showRolloverLink) {
        var linkStr = ilios_i18nVendor.getI18NString('course_management.rollover.link');

        ilios.cm.populateDialogOpeningLink(element, linkStr, 'rollover_dialog_open');

        (new YAHOO.util.Element(element)).setStyle('display', '');
    }
    else {
        (new YAHOO.util.Element(element)).setStyle('display', 'none');
    }
};

ilios.cm.populateDialogOpeningLink = function (linkDiv, linkStr, dialogOpenAction) {
    var aElement = document.createElement('a');

    aElement.setAttribute('class', 'small secondary radius button');
    aElement.setAttribute('href', '');
    aElement.setAttribute('onclick', 'return false;');
    YAHOO.util.Event.addListener(aElement, 'click', function (e) {
        IEvent.fire({
            action: dialogOpenAction
        });
    });
    aElement.innerHTML = linkStr;

    ilios.utilities.removeAllChildren(linkDiv);
    linkDiv.appendChild(aElement);
};

// called on DOM ready
ilios.cm.loadCourseIfAppropriate = function () {
    if (ilios.cm.preloadedCourseModelStub != null) {
        ilios.cm.courseLoader(ilios.cm.preloadedCourseModelStub);
    }
};

ilios.cm.populateCourseAndSetEnable = function (title, startDate, endDate, yearStart, courseId,
                                                externalId, uniqueId, courseLevel, publishEventId, cohorts,
                                                competencies, disciplines, directors, meshTerms,
                                                objectives, learningMaterials, sessions, learners,
                                                courseIsLocked, publishedAsTBD, clerkshipTypeId) {
    var Element = YAHOO.util.Element,
        element = document.getElementById('session_container'),
        key = null,
        dateObject = null,
        i = 0,
        n = 0;

    ilios.utilities.removeAllChildren(element);
    ilios.cm.session.sessionContainerCount = 0;
    ilios.cm.uiElementsToHideOnLockedView = [];
    ilios.cm.uiElementsToDisableOnLockedView = [];

    ilios.cm.currentCourseModel = new CourseModel();

    ilios.cm.currentCourseModel.setDBId(courseId);
    ilios.cm.currentCourseModel.setTitle(title);
    ilios.cm.currentCourseModel.setExternalCourseId(externalId);
    ilios.cm.currentCourseModel.setStartDate(startDate);
    ilios.cm.currentCourseModel.setEndDate(endDate);
    ilios.cm.currentCourseModel.setYear(yearStart);
    ilios.cm.currentCourseModel.setCourseLevel(courseLevel);
    ilios.cm.currentCourseModel.setLocked(courseIsLocked);

    if (clerkshipTypeId) {
        ilios.cm.currentCourseModel.setClerkshipTypeId(clerkshipTypeId);
    }

    if (publishEventId != null) {
        ilios.cm.currentCourseModel.setPublishEventId(publishEventId);
        ilios.cm.currentCourseModel.setPublishedAsTBD(publishedAsTBD);
    }

    element = document.getElementById('course_title');
    element.value = title;

    element = document.getElementById('summary-course-title');
    element.innerHTML = title;

    element = document.getElementById('summary-course-id');
    element.innerHTML = externalId;

    element = document.getElementById('summary-course-year');
    element.innerHTML = "" + yearStart + "-" + (parseInt(yearStart) + 1);

    element = document.getElementById('summary-course-level');
    element.innerHTML = "Level" + courseLevel;

    element = document.getElementById('external_course_id');
    element.value = externalId;

    element = document.getElementById('course_year_start');
    element.innerHTML = "" + yearStart + "-" + (parseInt(yearStart) + 1);

    element = document.getElementById('course_start_date');
    dateObject = ilios.utilities.mySQLTimelessDateToDateObject(startDate);
    element.innerHTML = dateObject.format('ddd mmm dd yyyy');

    element = document.getElementById('course_end_date');
    dateObject = ilios.utilities.mySQLTimelessDateToDateObject(endDate);
    element.innerHTML = dateObject.format('ddd mmm dd yyyy');

    element = document.getElementById('course_title').parentNode;
    (new Element(element)).removeClass('dirty_state');

    element = document.getElementById('course_level_selector');
    ilios.utilities.selectOptionWithValue(element, courseLevel);

    element = document.getElementById('clerkship_type_selector');
    if (clerkshipTypeId) {
        ilios.utilities.selectOptionWithValue(element, clerkshipTypeId);
    }

    element = document.getElementById('course_unique_id');
    element.value = uniqueId;

    element = document.getElementById('parent_publish_status_text');
    ilios.cm.setPublishIdAndUpdateText(ilios.cm.currentCourseModel, element, publishEventId);

    if (cohorts != null) {
        ilios.cm.currentCourseModel.setCohorts(cohorts);
    }

    if (competencies != null) {
        ilios.cm.currentCourseModel.setCompetencies(competencies);
        ilios.cm.repopulateListedCourseCompetencies(true);
    }
    else {
        document.getElementById('-1_competency_picker_selected_text_list').innerHTML = '';
    }

    element = document.getElementById('-1_discipline_picker_selected_text_list');
    if (disciplines != null) {
        ilios.cm.currentCourseModel.setDisciplines(disciplines);
        element.innerHTML = ilios.utilities.delimitedStringOfTitledObjects(
                                                ilios.cm.currentCourseModel.getDisciplines(), ';');
    }
    else {
        element.innerHTML = '';
    }

    element = document.getElementById('-1_director_picker_selected_text_list');
    if (directors != null) {
        ilios.cm.currentCourseModel.setDirectors(directors);
        element.innerHTML = ilios.cm.currentCourseModel.getDirectorsAsFormattedText();
    }
    else {
        element.innerHTML = '';
    }

    element = document.getElementById('-1_mesh_terms_picker_selected_text_list');
    if (meshTerms != null) {
        for (key in meshTerms) {
            ilios.cm.currentCourseModel.addMeSHItem(meshTerms[key]);
        }
        element.innerHTML = ilios.cm.currentCourseModel.getMeSHItemsAsFormattedText();
    }
    else {
        element.innerHTML = '';
    }

    element = document.getElementById(ilios.cm.generateIdStringForObjectivesContainer(-1));
    ilios.utilities.removeAllChildren(element);
    if (objectives != null) {
        var objectiveModel = null;
        var objectiveNumber = 0;

        for (key in objectives) {
            objectiveModel = objectives[key];

            objectiveNumber = ilios.cm.currentCourseModel.getNextObjectiveNumber();

            ilios.cm.buildAndPopulateObjective(-1, objectiveNumber, ilios.cm.currentCourseModel,
                                               objectiveModel, element, courseIsLocked);
        }
    }
    ilios.cm.updateObjectiveCountText(-1);

    if (learningMaterials != null) {
        for (key in learningMaterials) {
            ilios.cm.currentCourseModel.addLearningMaterial(learningMaterials[key]);
        }
    }

    if (sessions != null) {
        var sessionModel = null;
        var containerNumber = 0;
        for (i = 0, n = sessions.length; i < n; i++) {
            sessionModel = sessions[i];
            sessionModel.addStateChangeListener(ilios.cm.dirtyStateListener, null);
            containerNumber = ++ilios.cm.session.sessionContainerCount;
            ilios.cm.session.buildAndPopulateSession(containerNumber, ilios.cm.currentCourseModel,
                                                     sessionModel, courseIsLocked);
            ilios.cm.transaction.loadOfferingsForSession(sessionModel.getDBId());
        }
    }

    element = document.getElementById('course_associated_learners');
    if (learners != null) {
        ilios.cm.currentCourseModel.setAssociatedLearners(learners);
        element.innerHTML = ilios.cm.currentCourseModel.getAssociatedLearnersAsFormattedText();
    }
    else {
        element.innerHTML = '';
    }

    ilios.cm.setEnabledStateForCourseContainerUI(true);

    ilios.cm.transaction.loadStudentGroupTree();
    ilios.cm.transaction.loadInstructorsModel();

    ilios.cm.currentCourseModel.clearDirtyState();
    ilios.cm.loadedCourseModel = ilios.cm.currentCourseModel.clone();

    element = document.getElementById('course_title').parentNode;
    (new Element(element)).removeClass('dirty_state');

    ilios.cm.currentCourseModel.addStateChangeListener(ilios.cm.dirtyStateListener, null);

    ilios.cm.lm.populateLearningMaterialList(-1);

    ilios.cm.refreshCohortData();

    ilios.cm.session.setSessionsSummaryText();

    if (courseIsLocked) {
        ilios.cm.setDisplayAsLocked();
    }
};

ilios.cm.repopulateListedCourseCompetencies = function (initialPopulation) {
    var element = document.getElementById('-1_competency_picker_selected_text_list');
    var objectives = initialPopulation ? null : ilios.cm.currentCourseModel.getObjectives();
    var boundingObjectives = initialPopulation ? null : ilios.cm.programCohortObjectives;

    element.innerHTML = ilios.competencies.generateListHTMLForSelectedCompetencies(
        ilios.cm.currentCourseModel.getCompetencies(), objectives, boundingObjectives);
};

ilios.cm.setPublishIdAndUpdateText = function (model, element, publishEventId) {
    model.setPublishEventId((publishEventId > 0) ? publishEventId : -1);

    ilios.cm.updatePublishStateText(model, element);
};

ilios.cm.updatePublishStateText = function (model, element) {
    if (model.getPublishEventId() > 0){
        var stateText = '<span class="status is-published">' + ilios_i18nVendor.getI18NString('general.terms.published') + '</span>';

        if (model.isPublishedAsTBD()) {
            stateText = '<span class="status is-published-tbd">' + ilios_i18nVendor.getI18NString('general.terms.published_scheduled') + '</span>';
        }

        element.innerHTML = stateText;
    }
    else {
        element.innerHTML = '<span class="status is-draft">' + ilios_i18nVendor.getI18NString('general.terms.draft') + '</span>';
    }
};

ilios.cm.generateIdStringForObjectivesContainer = function (containerNumber) {
    return '' + containerNumber + '_objectives_container';
};

ilios.cm.generateIdStringForObjectivesContainerLabel = function (containerNumber) {
    return '' + containerNumber + '_objectives_container_label';
};

ilios.cm.generateIdStringForObjectivesContainerExpandWidget = function (containerNumber) {
    return '' + containerNumber + '_objectives_container_expand_widget';
};

ilios.cm.generateIdStringForObjectiveTextArea = function (containerNumber, objectiveNumber) {
    return '' + containerNumber + '_' + objectiveNumber + '_objectives_container';
};

ilios.cm.generateIdStringForObjectiveMeSHLink = function (containerNumber, objectiveNumber) {
    return '' + containerNumber + '_' + objectiveNumber + '_objectives_mesh_link';
};

ilios.cm.generateIdStringForPublishWarning = function (containerNumber) {
    return '' + containerNumber + '_publish_warning';
};

ilios.cm.appendAttributeDisplayBlock = function (parentElement, containerNumber, labelString,
                                                 searchEventName, uiTextFieldSuffix, stackVertical,
                                                 textFieldContent) {
    var clickFunction = function (e) {IEvent.fire({action: 'default_dialog_open',
                                                    event: searchEventName,
                                                    container_number: containerNumber});};

    return ilios.cm.createAttributeDisplayBlock(parentElement, containerNumber, labelString,
                                                clickFunction, uiTextFieldSuffix, stackVertical,
                                                textFieldContent);
};

ilios.cm.createAttributeDisplayBlock = function (parentElement, containerNumber, labelString,
                                                 searchClickFunction, uiTextFieldSuffix,
                                                 stackVertical, textFieldContent) {
    var Element = YAHOO.util.Element;
    var i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    var scratchElement = null;
    var scratchContainer = null;
    var scratchInput = null;
    var text = null;
    var rhett = null;
    var rowElement, labelCol, dataCol, actionCol;
	//label column
    rowElement = ilios.dom.createEntityContainerInputRow();
    text = document.createTextNode(labelString);
    scratchLabel = document.createElement('label');
    scratchLabel.appendChild(text);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    //data column
    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id', ('' + containerNumber + '_' + uiTextFieldSuffix));
    scratchInput.setAttribute('class', 'read_only_data scroll_list');
    scratchInput.innerHTML = textFieldContent;
    if (! stackVertical) {
    }
    else {
       scratchInput.setAttribute('class', 'stack');
    }
    dataCol = ilios.dom.createDataCol(rowElement,scratchInput);

    //action column
    scratchInput = new Element(document.createElement('a'), {href: ''});
    scratchInput.get('element').setAttribute('onclick', 'return false;');
    scratchInput.addClass('tiny radius button');
    scratchInput.addListener('click', searchClickFunction, null, this);
    text = document.createTextNode(i18nStr);
    scratchInput.appendChild(text);
    rhett = scratchInput;
    actionCol = ilios.dom.createActionCol(rowElement,scratchInput.get('element'));

    parentElement.appendChild(rowElement);
    return rhett;
};

// @private
ilios.cm.deleteObjective = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var deleteObjectiveStr = ilios_i18nVendor.getI18NString("general.warning.delete_objective");
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
    var args = {
        "cnumber": target.getAttribute("cnumber"),
        "onumber": target.getAttribute("onumber")
    }
    ilios.alert.inform(deleteObjectiveStr, yesStr, ilios.cm.continueDeletingObjective, args);
};

/**
 * "Click" event handler function for the "delete objective" confirmation dialog's "OK" button.
 * @method ilios.cm.continueDeletingObjective
 * @param {Event} event
 * @param {Object} obj handler arguments, expected attributes are the corresponding container id ("cnumber")
 *    and the id of the objective to delete ("onumber").
 */
ilios.cm.continueDeletingObjective = function(event, obj) {
    var containerNumber = obj.cnumber;
    var objectiveNumber = obj.onumber;
    var isCourse = (containerNumber == -1);
    var objectiveDescriptionContainerId = ilios.cm.generateIdStringForObjectiveTextArea(containerNumber, objectiveNumber);
    var model = isCourse ? ilios.cm.currentCourseModel : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var element = document.getElementById(objectiveDescriptionContainerId).parentNode;

    element.parentNode.removeChild(element);
    model.removeObjectiveForContainer(objectiveNumber);
    ilios.cm.updateObjectiveCountText(containerNumber);
    this.hide();
}

/**
 * Initiates the addition of a new objective to a course by firing up the "session objective" dialog.
 * @method addNewCourseObjective
 * @param {String} containerNumber the course objectives display container id
 */
ilios.cm.addNewCourseObjective = function (containerNumber) {
    ilios.cm.inEditObjectiveModel = null;
    IEvent.fire({
        action: 'eco_dialog_open'
    });
};

/**
 * Initiates the addition of a new objective to a session by firing up the "session objective" dialog.
 * @method addSessionObjective
 * @param {String} containerNumber the session objectives display container id
 */
ilios.cm.addNewSessionObjective = function (containerNumber) {
    ilios.cm.inEditObjectiveModel = null;
    IEvent.fire({
        action: 'eso_dialog_open',
        cnumber: containerNumber
    });
};

ilios.cm.addNewCourseObjectiveToDom = function (objectiveModel) {
    if (ilios.cm.currentCourseModel != null) {
        var model = ilios.cm.currentCourseModel;
        var objectiveNumber = model.getNextObjectiveNumber();
        var containerId = ilios.cm.generateIdStringForObjectivesContainer(-1);
        var container = document.getElementById(containerId);
        ilios.cm.buildAndPopulateObjective(-1, objectiveNumber, model, objectiveModel, container);
        ilios.cm.setObjectiveDivVisibility(-1, null, false);
    }
};

ilios.cm.addNewSessionObjectiveToDom = function (containerNumber, objectiveModel) {
    if (ilios.cm.currentCourseModel != null) {
        var model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
        var objectiveNumber = model.getNextObjectiveNumber();
        var containerId = ilios.cm.generateIdStringForObjectivesContainer(containerNumber);
        var container = document.getElementById(containerId);
        ilios.cm.buildAndPopulateObjective(containerNumber, objectiveNumber, model, objectiveModel, container);
        ilios.cm.setObjectiveDivVisibility(containerNumber, null, false);
    }
};

ilios.cm.resetEditCourseObjectiveDialog = function (dialog) {
    var objectivesDiv = document.getElementById('eco_parent_objectives_div');
    var pcObjective = null;
    var element = null;
    var parentObjectives = [];

    ilios.utilities.removeAllChildren(objectivesDiv);

    if (ilios.cm.inEditObjectiveModel) {
        parentObjectives = ilios.cm.inEditObjectiveModel.getParentObjectives();
        document.getElementById("eco_textarea").value = ilios.cm.inEditObjectiveModel.getDescription();
        dialog.ecoEditor.setEditorHTML(ilios.cm.inEditObjectiveModel.getDescription());
    } else {
        document.getElementById("eco_textarea").value = "";
        dialog.ecoEditor.setEditorHTML("");
    }

    for (var i = 0; i < ilios.cm.programCohortObjectives.length; i++) {
        pcObjective = ilios.cm.programCohortObjectives[i];
        if (pcObjective.objectives.length > 0) {
            objectivesDiv.appendChild(ilios.cm.domForEditCourseProgramCohortObjective(
                pcObjective, parentObjectives, (! pcObjective.belongsToActiveSchool)));
        }
    }
};

// @private
ilios.cm.domForEditCourseProgramCohortObjective = function (pcObjectiveModel, parentObjectives, readOnly) {
    var rhett = document.createElement('div');
    var titleDiv = document.createElement('div');
    var objectivesContainer = document.createElement('div');
    var objectiveElement = null;
    var objectiveModel = null;
    var selectedId = null;
    var i = 0;

    for (i = 0; i < pcObjectiveModel.objectives.length; i++) {
        objectiveModel = pcObjectiveModel.objectives[i];

        if (ilios.utilities.arrayContains(parentObjectives, objectiveModel.getDBId())) {
            selectedId = objectiveModel.getDBId();

            break;
        }
    }

    rhett.setAttribute('style', 'margin-top: 9px');

    titleDiv.setAttribute('class', 'read_only_data');
    titleDiv.innerHTML = pcObjectiveModel.title;

    objectivesContainer.setAttribute('style',
                                     'margin: 3px 15px 0 6px; border: 1px solid #111111; '
                                        + 'height: 99px; overflow: auto; position: relative; '
                                        + 'background-color: #FFFFFF;');
    for (i = 0; i < pcObjectiveModel.objectives.length; i++) {
        objectiveModel = pcObjectiveModel.objectives[i];

        objectiveElement = ilios.cm.domForEditCourseParentObjectiveElement(objectiveModel, selectedId, readOnly);

        objectivesContainer.appendChild(objectiveElement);
    }

    rhett.appendChild(titleDiv);
    rhett.appendChild(objectivesContainer);

    return rhett;
};

//@private
ilios.cm.domForEditCourseParentObjectiveElement = function (objectiveModel, selectedId, readOnly) {
    var rhett = document.createElement('div');
    var textDiv = document.createElement('div');
    var checkboxDiv = document.createElement('div');
    var checkbox = document.createElement('input');
    var competencyModel = ilios.competencies.getCompetency(objectiveModel.getCompetencyId());
    var textTitle = null;
    var html = '';
    var bgColor = null;
    var color = null;
    var selected = false;

    if (false !== competencyModel) {
        textTitle = competencyModel.getCompetencyTitle();
    }

    if ((selectedId == null) || (selectedId == objectiveModel.getDBId())) {
        bgColor = '#FFFFFF';
        color = '#000000';
        selected = (selectedId != null);
    } else {
        bgColor = '#E4E4E4';
        color = '#97909B';
    }

    rhett.setAttribute('style',
                       'position: relative; background-color: ' + bgColor + '; '
                            + 'border-bottom: 1px solid #111111; color: ' + color + ';');

    textDiv.setAttribute('style',
                         'float: left; padding: 3px 6px; width: 85%; margin: 0; '
                            + 'border-right: 1px solid #111111');
    checkboxDiv.setAttribute('style', 'float: right; margin-top: 8px; width: 12%;');
    checkboxDiv.setAttribute('class', 'objective_checkbox_div');

    if (textTitle != null) {
        html += '(' + textTitle + ')<br/>';
    }
    html += '<span style="font-size: 8pt; font-weight: bold;">' + objectiveModel.getDescription()
                + '</span>';
    textDiv.innerHTML = html;

    checkbox.setAttribute('type', 'checkbox');
    if (selected) {
        checkbox.setAttribute('checked', 'yes');
    }
    checkbox.iliosModel = objectiveModel;
    if (readOnly) {
        checkbox.setAttribute("disabled", "disabled");
    } else {
        YAHOO.util.Event.addListener(checkbox, 'click', function () {
            ilios.cm.programCohortObjectiveSelected(this);
        });
    }
    checkboxDiv.appendChild(checkbox);

    rhett.appendChild(textDiv);
    rhett.appendChild(checkboxDiv);
    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

ilios.cm.getArrayOfCurrentlySelectedParentObjectives = function () {
    var rhett = new Array();
    var objectivesDiv = document.getElementById('eco_parent_objectives_div');
    var selectedObjectiveModel = null
    var children = objectivesDiv.children;

    for (var i = 0; i < children.length; i++) {
        selectedObjectiveModel
                    = ilios.cm.getSelectedObjectiveModelForProgramCohortObjectiveDiv(children[i]);

        if (selectedObjectiveModel != null) {
            rhett.push(selectedObjectiveModel.getDBId());
        }
    }

    return rhett;
};

//@private
ilios.cm.getSelectedObjectiveModelForProgramCohortObjectiveDiv = function (objectiveDiv) {
    var objectivesDiv = objectiveDiv.children[1];
    var objective = null;
    var checkBoxDiv = null;
    var checkbox = null;

    for (var i = 0; i < objectivesDiv.children.length; i++) {
        objective = new YAHOO.util.Element(objectivesDiv.children[i]);
        checkboxDiv = objective.getElementsByClassName('objective_checkbox_div');
        checkbox = checkboxDiv[0].children[0];

        if (checkbox.checked) {
            return checkbox.iliosModel;
        }
    }

    return null;
};

//@private
ilios.cm.programCohortObjectiveSelected = function (checkbox) {
    var Element = YAHOO.util.Element;
    var objectiveDiv = null;
    var objectivesContainer = null;
    var peerObjectives = null;
    var peerDiv = null;
    var checkboxDiv = null;
    var i = 0;

    if (ilios.cm.adjustingProgramCohortObjectiveDOM) {
        return;
    }

    ilios.cm.adjustingProgramCohortObjectiveDOM = true;

    objectiveDiv = checkbox.parentNode.parentNode;
    objectivesContainer = objectiveDiv.parentNode;
    peerObjectives = objectivesContainer.children;

    if (checkbox.checked) {
        for (i = 0; i < peerObjectives.length; i++) {
            peerDiv = new Element(peerObjectives[i]);
            peerDiv.setStyle('color', '#97909B');
            peerDiv.setStyle('background-color', '#E4E4E4');

            checkboxDiv = peerDiv.getElementsByClassName('objective_checkbox_div');
            checkboxDiv[0].children[0].checked = false;
        }

        peerDiv = new Element(objectiveDiv);
        peerDiv.setStyle('color', '#000000');
        peerDiv.setStyle('background-color', '#FFFFFF');

        checkbox.checked = true;
    }
    else {
        for (i = 0; i < peerObjectives.length; i++) {
            peerDiv = new Element(peerObjectives[i]);
            peerDiv.setStyle('color', '#000000');
            peerDiv.setStyle('background-color', '#FFFFFF');
        }
    }

    ilios.cm.adjustingProgramCohortObjectiveDOM = false;
};

ilios.cm.resetEditLearningMaterialNotesDialog = function (dialog) {
    var element = document.getElementById('elmn_textarea');

    dialog.elmnEditor.setEditorHTML(ilios.common.lm.learningMaterialsDetailsModel.getNotes());

    element = document.getElementById('learning_material_notes_viewable_checkbox');
    element.checked = (! ilios.common.lm.learningMaterialsDetailsModel.isNotesPubliclyViewable());
};

/*
 * @param model if non-null, the objectiveModel will be added to it
 */
ilios.cm.buildAndPopulateObjective = function (containerNumber, objectiveNumber, model,
                                               objectiveModel, container, isLocked) {
    var newObjectiveContainer = document.createElement('div');
    var scratchElement = null;
    var scratchString = null;
    var scratchInput = null;
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;
    var isLocked = isLocked || false;

    newObjectiveContainer.setAttribute('class', 'objective_container');
    newObjectiveContainer.setAttribute('cnumber', containerNumber);
    newObjectiveContainer.setAttribute('onumber', objectiveNumber);


    // Delete widget
    scratchElement = new Element(document.createElement('div'));
    scratchElement.addClass('delete_widget icon-cancel');
    scratchElement.get('element').setAttribute('title', ilios_i18nVendor.getI18NString("general.phrases.delete_objective"));
    scratchElement.get('element').setAttribute('cnumber', containerNumber);
    scratchElement.get('element').setAttribute('onumber', objectiveNumber);
    if (! isLocked) {
        scratchElement.addListener('click', ilios.cm.deleteObjective, null, this);
    }
    newObjectiveContainer.appendChild(scratchElement.get('element'));
    ilios.cm.uiElementsToHideOnLockedView.push(scratchElement);

    // objective description container
    scratchElement = document.createElement('div');
    scratchString = ilios.cm.generateIdStringForObjectiveTextArea(containerNumber, objectiveNumber);
    scratchElement.setAttribute('class', 'objective_description_container');
    scratchElement.setAttribute('id', scratchString);
    scratchElement.innerHTML = objectiveModel.getTitle();
    newObjectiveContainer.appendChild(scratchElement);

    if (! isLocked) {
        if (-1 === containerNumber) {  // course objective
            // register click event handler on objective description container
            Event.addListener(scratchElement, "click", function (e) { // pop up the "edit objective" dialog
                ilios.cm.inEditObjectiveModel = objectiveModel;
                IEvent.fire({
                    action: 'eco_dialog_open'
                });
            });
        } else { // session objective
                    // register click event handler on objective description container
            Event.addListener(scratchElement, "click", function (e) { // pop up the "edit objective" dialog
                ilios.cm.inEditObjectiveModel = objectiveModel;
                IEvent.fire({
                    action: 'eso_dialog_open',
                    cnumber: containerNumber
                });
            });
        }
        objectiveModel.addStateChangeListener(ilios.cm.objectiveDirtyStateListener, {containerId : scratchString});
    }
    scratchString = ilios.cm.generateIdStringForObjectiveMeSHLink(containerNumber, objectiveNumber);
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('id', scratchString);
    scratchInput.setAttribute('class', 'mesh_btn tiny secondary radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick', 'return false;');
    if (! isLocked) {
        Event.addListener(scratchInput, 'click', function (e) {
            IEvent.fire({
                action: 'mesh_picker_dialog_open',
                model_in_edit: objectiveModel
            });
            return false;
        });
    }

    scratchInput.innerHTML = ilios.cm.meshLinkText(objectiveModel);
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(scratchInput));
    newObjectiveContainer.appendChild(scratchInput);

    ilios.utilities.appendClearingDivToContainer(newObjectiveContainer);
    container.appendChild(newObjectiveContainer);

    if (model != null) {
        model.addObjectiveForContainer(objectiveModel, objectiveNumber);
    }

    ilios.cm.updateObjectiveCountText(containerNumber);
};

ilios.cm.updateObjectiveCountText = function (containerNumber) {
    var isCourse = (containerNumber == -1);
    var model = isCourse ? ilios.cm.currentCourseModel
                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var idString = ilios.cm.generateIdStringForObjectivesContainerLabel(containerNumber);
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.learning_objectives');
    var element = document.getElementById(idString);

    element.innerHTML = i18nStr + ' (' + ilios.utilities.arraySize(model.getObjectives()) + ')';
};

// @private
ilios.cm.updateObjectiveMeSHCounts = function (model, containerNumber) {
    var objectives = model.getObjectives();
    var idString = null;

    for (var key in objectives) {
        idString = ilios.cm.generateIdStringForObjectiveMeSHLink(containerNumber, key);
        document.getElementById(idString).innerHTML = ilios.cm.meshLinkText(objectives[key]);
    }
};

/*
 * @param shouldToggle if true, the current visibility will be toggled; if false, the div will be
 *                          made visible
 */
ilios.cm.setObjectiveDivVisibility = function (containerNumber, widgetDiv, shouldToggle) {
    var Element = YAHOO.util.Element;
    var element = null;
    var idString = null;
    var div = null;

    if (ilios.cm.currentCourseModel == null) {
        return;
    }

    if (widgetDiv == null) {
        idString = ilios.cm.generateIdStringForObjectivesContainerExpandWidget(containerNumber);
        widgetDiv = document.getElementById(idString);
    }

    element = new Element(widgetDiv);
    idString = ilios.cm.generateIdStringForObjectivesContainer(containerNumber);
    div = new Element(document.getElementById(idString));
    if ((div.getStyle('display') != 'none') && shouldToggle) {
        element.removeClass('expanded_widget');
        element.addClass('collapsed_widget');
        div.setStyle('display', 'none');
    }
    else {
        element.removeClass('collapsed_widget');
        element.addClass('expanded_widget');
        div.setStyle('display', 'block');
    }
};

ilios.cm.setDisplayAsLocked = function () {
    var Element = YAHOO.util.Element,
        Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;
    var container = new Element(document.getElementById('course_title').parentNode);
    var element = document.getElementById('parent_publish_status_text');
    var key = null;
    var elements = null;
    var i, n;

    container.setStyle('background-color', '#C5C5C5');

    element.innerHTML = '<span class="status is-locked">' + ilios_i18nVendor.getI18NString('general.terms.locked') + '</span>';

    element = document.getElementById('course_title');
    element.disabled = 'disabled';

    element = document.getElementById('external_course_id');
    element.disabled = 'disabled';

    element = document.getElementById('course_level_selector');
    element.disabled = 'disabled';

    element = document.getElementById('clerkship_type_selector');
    element.disabled = 'disabled';

    element = document.getElementById('archiving_link_div');
    ilios.cm.populateDialogOpeningLink(element, ilios_i18nVendor.getI18NString('general.terms.file'), 'archive_dialog_open');


    element = new Element(document.getElementById('select_cohorts_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('start_date_calendar_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('end_date_calendar_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('disciplines_search_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('directors_search_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('mesh_search_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('course_learning_material_search_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('add_objective_link'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('save_all_dirty_to_draft'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('publish_all'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('draft_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('publish_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('reset_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('add_new_session_link'));
    element.setStyle('display', 'none');

    elements = ilios.cm.currentCourseModel.getSessions();
    for (key in elements) {
        ilios.cm.session.alterSessionUIToReflectLockedState(elements[key]);
    }

    for (key in ilios.cm.uiElementsToHideOnLockedView) {
        element = ilios.cm.uiElementsToHideOnLockedView[key];
        element.setStyle('display', 'none');
    }

    for (key in ilios.cm.uiElementsToDisableOnLockedView) {
        element = ilios.cm.uiElementsToDisableOnLockedView[key];
        element.get('element').setAttribute('disabled', 'disabled');
    }

    container = new Element(document.getElementById('cohort_level_table_div'));
    elements = container.getElementsByClassName('yui-dt-odd');
    for (key in elements) {
        element = new Element(elements[key]);
        element.setStyle('background-color', '#D1D1D1');
    }
    elements = container.getElementsByClassName('yui-dt-even');
    for (key in elements) {
        element = new Element(elements[key]);
        element.setStyle('background-color', '#DFDFDF');
    }

    // get all objective containers and remove any click event handlers from them.
    elements = YAHOO.util.Dom.getElementsByClassName('objective_description_container');
    Event.removeListener(elements, 'click');

    // remove all "remove" widgets
    // this affects session contrainers, session/course learning materials
    elements = YAHOO.util.Dom.getElementsByClassName("remove_widget");
    Event.removeListener(elements, "click");
    Dom.setStyle(elements, "display", "none");
};

// @private
ilios.cm.meshLinkText = function (model) {
    var rhett = ilios_i18nVendor.getI18NString('general.phrases.add_mesh');

    rhett += " (" + model.getMeSHItems().length + ")";

    return rhett;
};

ilios.cm.displayMeSHDialogForCourse = function () {
    IEvent.fire({action: 'mesh_picker_dialog_open', model_in_edit: ilios.cm.currentCourseModel});
};

ilios.common.picker.mesh.handleMeSHPickerSave = function (dialogPanel) {
    if (ilios.mesh.meshInEditModel.compareTo(ilios.mesh.meshInEditReferenceModel) != 0) {
        var element = null;

        ilios.mesh.meshInEditReferenceModel.replaceContentWithModel(ilios.mesh.meshInEditModel,
                                                                    true);

        if (ilios.mesh.meshInEditReferenceModel instanceof LearningMaterialModel) {
            var containerNumber = ilios.common.lm.learningMaterialsDetailsDialog.cnumber;
            var model = null;

            element = document.getElementById('ilios_lm_mesh');
            if (element != null) {
                element.innerHTML
                                = ilios.mesh.meshInEditReferenceModel.getMeSHItemsAsFormattedText();
            }

            if (containerNumber == -1) {
                model = ilios.cm.currentCourseModel;
            }
            else {
                model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
            }
            model.setDirtyAndNotify();
        }
    }

    if ((ilios.mesh.currentEditQueryUIDPairSelections != null)
                                    && (ilios.mesh.currentEditQueryUIDPairSelections.length > 0)) {
        ilios.mesh.saveMeSHSearchSelections();
    }

    ilios.mesh.meshInEditModel = null;
    ilios.mesh.meshInEditReferenceModel = null;

    ilios.mesh.lastMeSHSearchQuery = null;
    ilios.mesh.currentEditQueryUIDPairSelections = null;
};

ilios.cm.populateReviewForFullReview = function () {
    var container;
    var heading = document.getElementById('review_heading');
    var level = document.getElementById('course_level');
    var childNodes = null;
    var div = null;
    var containerNumber = -1;
    var model = null;
    var element,courseCheckList, sessionChecklist, sessionEl, subList;

    heading.innerHTML = ilios.cm.currentCourseModel.getTitle() + ' - '
                        + ilios.cm.currentCourseModel.getYear() + '-'
                        + (parseInt(ilios.cm.currentCourseModel.getYear()) + 1);
    level.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_level')
                        + ':  <span>' + ilios.cm.currentCourseModel.getCourseLevel() + '</span>';

    element = document.getElementById('review_dialog_title');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.review.course_title');

    element = new YAHOO.util.Element(document.getElementById('course_review'));
    element.setStyle('display', 'none');

    element = new YAHOO.util.Element(document.getElementById('session_review'));
    element.setStyle('display', 'none');

    ilios.cm.reviewDialogObjects = {
        sessionReviewObjects: [],
        courseReviewObject: null
    };

    element = document.getElementById('full_review_course_div');
    ilios.utilities.removeAllChildren(element);
    courseCheckList = document.createElement('ul');
    courseCheckList.setAttribute('class', 'auditList');
    element.appendChild(courseCheckList);

    ilios.cm.populateReviewDetailsInContainer(courseCheckList, ilios.cm.currentCourseModel);

    container = document.getElementById('full_review_session_div');
    ilios.utilities.removeAllChildren(container);

    //create an empty list to hold all of the sessions
    sessionCheckList = document.createElement('ul');
    sessionCheckList.setAttribute('class', 'sessionsList');
    container.appendChild(sessionCheckList);

    childNodes = document.getElementById('session_container').childNodes;
    if (childNodes.length > 0){
        element = document.createElement('h3');
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.sessions') + ':';
        element.setAttribute('class', 'margin-0');
        YAHOO.util.Dom.insertBefore(element,sessionCheckList);
    }
    for (var i = 0; i < childNodes.length; i++) {
        //for each session, gather the audit items in a sublist
        subList = document.createElement('ul');
        subList.setAttribute('class', 'auditList');

        div = childNodes[i];
        containerNumber = div.getAttribute('cnumber');
        model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

        //populate the subList
        ilios.cm.populateReviewDetailsInContainer(subList, model);

        //for each session, we'll create a list item
        sessionEl = new YAHOO.util.Element(document.createElement('li'));
        if ((i % 2) == 1) {
            sessionEl.addClass('alt');
        }
        sessionEl.appendChild(subList);
        sessionCheckList.appendChild(sessionEl.get('element'));
    }

    container = new YAHOO.util.Element(document.getElementById('full_review'));
    container.setStyle('display', 'block');

//    element = new Element(document.getElementById('r_dialog_wrap'));
//    element.setStyle('height', '626px');
};

ilios.cm.populateReviewForCourseReview = function () {
    var heading = document.getElementById('review_heading');
    var level = document.getElementById('course_level');
    var element,courseCheckList, sessionChecklist, sessionEl, subList;

    heading.innerHTML = ilios.cm.currentCourseModel.getTitle() + ' - '
                        + ilios.cm.currentCourseModel.getYear() + '-'
                        + (parseInt(ilios.cm.currentCourseModel.getYear()) + 1);
    level.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_level')
                        + ':  <span>' + ilios.cm.currentCourseModel.getCourseLevel() + '</span>';

    element = document.getElementById('review_dialog_title');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.review.course_title');

    element = new YAHOO.util.Element(document.getElementById('full_review'));
    element.setStyle('display', 'none');

    element = new YAHOO.util.Element(document.getElementById('session_review'));
    element.setStyle('display', 'none');

    ilios.cm.reviewDialogObjects = {
        sessionReviewObjects: [],
        courseReviewObject: null
    };

    element = document.getElementById('course_review_course_div');
    ilios.utilities.removeAllChildren(element);

    courseCheckList = document.createElement('ul');
    courseCheckList.setAttribute('class', 'auditList');
    element.appendChild(courseCheckList);

    ilios.cm.populateReviewDetailsInContainer(courseCheckList, ilios.cm.currentCourseModel);

    element = new YAHOO.util.Element(document.getElementById('course_review'));
    element.setStyle('display', 'block');
};

ilios.cm.populateReviewForSessionReviewForContainer = function (containerNumber) {
    var Element = YAHOO.util.Element;
    var model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var heading = document.getElementById('review_heading');
    var level = document.getElementById('course_level');
    var element, sessionChecklist;

    heading.innerHTML = ilios.cm.currentCourseModel.getTitle() + ' - '
                        + ilios.cm.currentCourseModel.getYear() + '-'
                        + (parseInt(ilios.cm.currentCourseModel.getYear()) + 1);
    level.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_level')
                        + ':  <span>' + ilios.cm.currentCourseModel.getCourseLevel() + '</span>';

    element = document.getElementById('review_dialog_title');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.review.session_title');

    element = new Element(document.getElementById('full_review'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('course_review'));
    element.setStyle('display', 'none');

    ilios.cm.reviewDialogObjects = {
        sessionReviewObjects: [],
        courseReviewObject: null
    };

    element = document.getElementById('session_review_session_div');
    ilios.utilities.removeAllChildren(element);

    sessionCheckList = document.createElement('ul');
    sessionCheckList.setAttribute('class', 'auditList');
    element.appendChild(sessionCheckList);

    ilios.cm.populateReviewDetailsInContainer(sessionCheckList, model);

    element = new Element(document.getElementById('session_review'));
    element.setStyle('display', 'block');
};

// @private
ilios.cm.populateReviewDetailsInContainer = function (container, model) {
    var reviewArray = model.getReviewArray();
    var isCourse = (model instanceof CourseModel);
    var reviewObject = {};
    var element = null;
    var listItem, i, n;

    reviewObject.iliosModel = model;

    if (reviewArray[0] != true) {
        listItem = document.createElement('li');
        listItem.setAttribute('class', 'override');
        element = document.createElement('input');
        element.setAttribute('type', 'checkbox');
        listItem.appendChild(element);

        reviewObject.checkbox = element;

        element = document.createElement('span');
        element.setAttribute('class', 'warning');
        element.innerHTML = isCourse ? ilios_i18nVendor.getI18NString('course_management.review.override_course')
            : ilios_i18nVendor.getI18NString('course_management.review.override_session');
        listItem.appendChild(element);
        container.appendChild(listItem);
    }

    for (i = 1, n = reviewArray.length; i < n; i++) {
        reviewObject = reviewArray[i];
        listItem = document.createElement('li');
        if (reviewObject.passed) {
            listItem.setAttribute('class', 'pass icon-checkmark');
        } else {
            listItem.setAttribute('class','fail icon-warning');
        }
        element = document.createElement('label');
        element.innerHTML = reviewObject.displayLabel + ': ';
        listItem.appendChild(element);

        element = document.createElement('span');
        if (reviewObject.passed) {
            element.setAttribute('class', 'data');
        } else {
            element.setAttribute('class', 'data');
        }
        element.innerHTML = reviewObject.displayValue;
        listItem.appendChild(element);
        container.appendChild(listItem);
    }

    if (isCourse) {
        ilios.cm.reviewDialogObjects.courseReviewObject = reviewObject;
    } else {
        ilios.cm.reviewDialogObjects.sessionReviewObjects.push(reviewObject);
    }
};
