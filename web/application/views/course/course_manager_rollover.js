/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('cm.rollover');


ilios.cm.rollover.currentWorkflowStep = 0;

ilios.cm.rollover.startDateObject = null;
ilios.cm.rollover.endDateObject = null;
ilios.cm.rollover.startYear = 0;
ilios.cm.rollover.offeringRolloverCheckbox = null;
ilios.cm.rollover.resultingCourseId = -1;
ilios.cm.rollover.userCanViewNewCourse = false;

ilios.cm.rollover.prepareForRolloverWorkflow = function () {
    ilios.cm.rollover.currentWorkflowStep = 0;

    ilios.cm.rollover.startDateObject = null;
    ilios.cm.rollover.endDateObject = null;

    ilios.cm.rollover.startYear = 0;

    ilios.cm.rollover.offeringRolloverCheckbox = null;

    ilios.ui.hackilySetButtonTitleOnYUIDialog(ilios_i18nVendor.getI18NString('general.terms.continue'),
                                              ilios.cm.rolloverDialog, 0);

    ilios.cm.rollover.createInitialDOM();

    ilios.cm.rollover.populateInitialDOM();
};

ilios.cm.rollover.setRolloverStartDate = function (startDate) {
    var currentCourseTimeSpan = null;
    var element = null;
    var newDOW = startDate.getDay();
    var originalDOW = ilios.cm.currentCourseModel.getStartDateAsDateObject().getDay();

    if (newDOW != originalDOW) {
        var msg = ilios_i18nVendor.getI18NString('course_management.rollover.warning.start_date_dow_1')
                        + " " + ilios.global.getI18NStringForDayOfWeek(newDOW)
                        + " "
                        + ilios_i18nVendor.getI18NString('course_management.rollover.warning.start_date_dow_2')
                        + ", " + ilios.global.getI18NStringForDayOfWeek(originalDOW)
                        + ", "
                        + ilios_i18nVendor.getI18NString('course_management.rollover.warning.start_date_dow_3');

        ilios.alert.alert(msg);
        return;
    }

    // we do this just to avoid hinkiness with crossing a DST fallback in the end date (if it
    //      is hour==0 on the startDate, which it is by default arriving here out of the YUI
    //      calendar, then crossing into DST computes one day earlier than it should be at 23:00:00)
    startDate.setHours(6, 0, 0, 0);

    currentCourseTimeSpan = ilios.cm.currentCourseModel.getEndDateAsDateObject().getTime()
                                 - ilios.cm.currentCourseModel.getStartDateAsDateObject().getTime();

    ilios.cm.rollover.startDateObject = startDate;
    ilios.cm.rollover.endDateObject = new Date(startDate.getTime() + currentCourseTimeSpan);

    element = document.getElementById('r1_new_start_date');
    element.innerHTML = ilios.cm.rollover.startDateObject.format('ddd mmm dd yyyy');

    element = document.getElementById('r1_new_end_date');
    element.innerHTML = ilios.cm.rollover.endDateObject.format('ddd mmm dd yyyy');
};

ilios.cm.rollover.progressRolloverWorkflow = function () {
    var element = null;

    ilios.cm.rollover.currentWorkflowStep++;

    switch (ilios.cm.rollover.currentWorkflowStep) {
        case 1:
            element = document.getElementById('r1_academic_year_select');

            ilios.cm.rollover.startYear = parseInt(element.options[element.selectedIndex].value);

            if (! ilios.cm.rollover.dateSettingsAreValid()) {
                ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.rollover.warning.bad_dates'));

                ilios.cm.rollover.startYear = 0;

                ilios.cm.rollover.currentWorkflowStep--;
            }
            else {
                ilios.cm.rollover.createDOMForCourseSummary();
                ilios.cm.rollover.populateDOMForCourseSummary();
            }

            break;
        case 2:
            ilios.cm.rollover.createDOMForServerTransaction();

            setTimeout('ilios.cm.rollover.performRollover()', 1000);

            break;
        case 3:
            ilios.cm.rollover.createDOMForRolloverSuccess();
            break;
    }
};

// @private
ilios.cm.rollover.dateSettingsAreValid = function () {
    var yearDelta = 0;

    if (ilios.cm.rollover.startDateObject == null) {
        return false;
    }

    yearDelta = ilios.cm.rollover.startDateObject.getFullYear() - ilios.cm.rollover.startYear;

    if (yearDelta != 0) {
        if ((yearDelta == 1) && (ilios.cm.rollover.startDateObject.getMonth() < 5)) {
            return true;
        }

        return false;
    }

    return (ilios.cm.rollover.startDateObject.getMonth() >= 5);
};

ilios.cm.rollover.showRolloverFailureWindow = function () {
    var contentContainer = document.getElementById('rollover_dialog_content');

    ilios.ui.hackilySetButtonVisibilityOnYUIDialog(ilios.cm.rolloverDialog, false);
    ilios.ui.hackilySetButtonTitleOnYUIDialog(ilios_i18nVendor.getI18NString('general.terms.ok'),
                                              ilios.cm.rolloverDialog, 1);
    ilios.ui.hackilySetButtonVisibilityOnYUIDialog(ilios.cm.rolloverDialog, true, 1);

    ilios.utilities.removeAllChildren(contentContainer);

    // TODO - more informative junk
    contentContainer.appendChild(document.createTextNode('FAILURE'));
};

// @private
ilios.cm.rollover.createInitialDOM = function () {
    var contentContainer = document.getElementById('rollover_dialog_content');
    var container = null;
    var subContainer = null;
    var element = null;
    var spanElement = null;
    var ulElement, liElement, rowElement, labelElement, dataElement;

    ilios.utilities.removeAllChildren(contentContainer);
    //build a summary-header
    container = document.createElement('div');
    container.setAttribute('class', 'summary-header');
    ulElement = document. createElement('ul');

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.parent_name');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_parent_course_name');
    element.setAttribute('class', 'data');
    liElement.appendChild(element);
    ulElement.appendChild(liElement);

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.external_course_id');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_id');
    element.setAttribute('class', 'data');
    element.innerHTML = '-';
    liElement.appendChild(element);
    ulElement.appendChild(liElement);

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_year');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_year');
    element.setAttribute('class', 'data');
    liElement.appendChild(element);
    ulElement.appendChild(liElement);

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_level');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_level');
    element.setAttribute('class', 'data');
    liElement.appendChild(element);
    ulElement.appendChild(liElement);

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.start_date');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_start_date');
    element.setAttribute('class', 'data');
    liElement.appendChild(element);
    ulElement.appendChild(liElement);

    liElement = document.createElement('li');
    element = document.createElement('span');
    element.setAttribute('class', 'data-type');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.end_date');
    liElement.appendChild(element);
    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_end_date');
    element.setAttribute('class', 'data');
    liElement.appendChild(element);
    ulElement.appendChild(liElement);
    container.appendChild(ulElement);

    contentContainer.appendChild(container);

    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('p');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.choice_instructions');
    rowElement.appendChild(element);
    contentContainer.appendChild(rowElement);

    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.setAttribute('for', 'r1_course_title_and_id');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_name');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.setAttribute('id', 'r1_course_title_and_id');
    element.innerHTML = ' &nbsp;';
    dataElement = ilios.dom.createDataCol(rowElement, element);
    contentContainer.appendChild(rowElement);

    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.setAttribute('for', 'r1_academic_year_select');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.new_year');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('select');
    element.setAttribute('id', 'r1_academic_year_select');
    dataElement = ilios.dom.createDataCol(rowElement, element);
    contentContainer.appendChild(rowElement);

    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.setAttribute('for', 'r1_new_start_date');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.new_start');
    labelElement = ilios.dom.createLabelCol(rowElement,element);


    element = document.createElement('span');
    element.setAttribute('id', 'r1_new_start_date');
    dataElement = ilios.dom.createDataCol(rowElement, element);

    element = document.createElement('span');
    element.setAttribute('class', 'calendar_button');
    YAHOO.util.Event.addListener(element, 'click',
                       function () {ilios.cm.yuiCalendarInstance.modificationTarget
                                              = ilios.cm.yuiCalendarModificationTarget.ROLLOVER_START;

                                     if (ilios.cm.rollover.startDateObject == null) {
                                         var zeroDate = new Date();
                                         var element = document.getElementById('r1_academic_year_select');

                                         zeroDate.setFullYear(parseInt(element.options[element.selectedIndex].value));
                                         zeroDate.setMonth(8, 1);

                                         ilios.cm.setCalendarToDate(zeroDate);
                                     }
                                     else {
                                         ilios.cm.setCalendarToDate(ilios.cm.rollover.startDateObject);
                                     }

                                     ilios.cm.moveCalendarToDOMElement(this);

                                     ilios.cm.yuiCalendarInstance.show();});
    dataElement.appendChild(element);
    contentContainer.appendChild(rowElement);

    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.setAttribute('for', 'r1_new_end_date');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.new_end')
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.setAttribute('id', 'r1_new_end_date');
    dataElement = ilios.dom.createDataCol(rowElement, element);
    element = document.createElement('span');
    element.setAttribute('class', 'note');
    element.innerHTML = ' (' + ilios_i18nVendor.getI18NString('course_management.rollover.auto_calculated') + ')';
    dataElement.appendChild(element);
    contentContainer.appendChild(rowElement);


    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('input');
    element.setAttribute('id', 'r1_all_offerings_checkbox');
    element.setAttribute('type', 'checkbox');
    element.setAttribute('checked', 'checked');
    dataElement = ilios.dom.createDataCol(rowElement, element);
    ilios.cm.rollover.offeringRolloverCheckbox = element;

    element = document.createElement('label');
    element.setAttribute('for', 'r1_all_offerings_checkbox');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.include_offerings');
    dataElement.appendChild(element);
    contentContainer.appendChild(rowElement);
};

// @private
ilios.cm.rollover.populateInitialDOM = function () {
    var element = document.getElementById('r1_parent_course_name');
    var yearStart = parseInt(ilios.cm.currentCourseModel.getYear());
    var optionElement = null;
    var courseID = ilios.cm.currentCourseModel.getExternalCourseId();

    element.innerHTML = ilios.cm.currentCourseModel.getTitle();

    element = document.getElementById('r1_course_id');
    if (courseID === '' || courseID == 'null'){
        courseID = '-';
    }
    element.innerHTML = courseID;

    element = document.getElementById('r1_course_year');
    element.innerHTML = "" + yearStart + "-" + (yearStart + 1);

    element = document.getElementById('r1_course_level');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.level') + ' ' + ilios.cm.currentCourseModel.getCourseLevel();

    element = document.getElementById('r1_course_start_date');
    element.innerHTML
        = ilios.cm.currentCourseModel.getStartDateAsDateObject().format('ddd mm-dd-yyyy', false, true);

    element = document.getElementById('r1_course_end_date');
    element.innerHTML
        = ilios.cm.currentCourseModel.getEndDateAsDateObject().format('ddd mm-dd-yyyy', false, true);


    element = document.getElementById('r1_course_title_and_id');
    element.innerHTML = ilios.cm.currentCourseModel.getTitle() + " &nbsp; &nbsp; &nbsp; "
                            + ilios.cm.currentCourseModel.getExternalCourseId();

    element = document.getElementById('r1_academic_year_select');
    ilios.utilities.removeAllChildren(element);
    for (var i = 0; i < 6; i++) {
        optionElement = document.createElement('option');
        optionElement.setAttribute('value', (yearStart + i));
        if (i == 1) {
            optionElement.setAttribute('selected', 'true');
        }

        optionElement.appendChild(document.createTextNode("" + (yearStart + i) + "-"
                                                             + (yearStart + i + 1)));

        element.appendChild(optionElement);
    }
};

// @private
ilios.cm.rollover.createDOMForCourseSummary = function () {
    var contentContainer = document.getElementById('rollover_dialog_content');
    var academicYearString = ilios.cm.rollover.startYear
                            + '-' + (ilios.cm.rollover.startYear + 1);
    var container = null;
    var subContainer = null;
    var element = null;
    var subElement = null;

    ilios.utilities.removeAllChildren(contentContainer);


    container = document.createElement('div');
    element = document.createElement('p');
    element.setAttribute('class', 'warning margin-b0');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.warning.summary_desc_1')
                            + ' "'
                            + ilios.cm.currentCourseModel.getTitle()
                            + '" '
                            + ilios_i18nVendor.getI18NString('course_management.rollover.warning.summary_desc_2')
                            + ' ' + academicYearString
                            + ' '
                            + ilios_i18nVendor.getI18NString('course_management.rollover.warning.summary_desc_3')
                            + '.';
    container.appendChild(element);
    element = document.createElement('p');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.warning.summary_desc_4')
                        + ' <em>'
                        + ilios_i18nVendor.getI18NString('course_management.rollover.warning.summary_desc_5') + '</em>';
    container.appendChild(element);
    contentContainer.appendChild(container);

    subContainer = document.createElement('div');
    subContainer.setAttribute('class', 'scroll_list');
//course name
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_name');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.currentCourseModel.getTitle();
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//course year
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.course_year');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.appendChild(document.createTextNode(academicYearString));
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//course ID
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.id');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.currentCourseModel.getExternalCourseId();
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//course level
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.level');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.currentCourseModel.getCourseLevel();
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//course start date
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.start_date');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.rollover.startDateObject.format('mm/dd/yyyy');
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//course end date
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.end_date');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.rollover.endDateObject.format('mm/dd/yyyy');
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//rollover from
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.from');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.innerHTML = ilios.cm.currentCourseModel.getTitle() + ' '
                            + ilios.cm.currentCourseModel.getYear() + '-'
                            + (parseInt(ilios.cm.currentCourseModel.getYear()) + 1);
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//total sessions
    rowElement = ilios.dom.createEntityContainerInputRow();
    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.total_sessions');
    labelElement = ilios.dom.createLabelCol(rowElement,element);

    element = document.createElement('span');
    element.setAttribute('id', 'r2_session_count');
    element.innerHTML = '&nbsp;';
    dataElement = ilios.dom.createDataCol(rowElement, element);
    subContainer.appendChild(rowElement);

//everything else
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.directors'),
                                   'r2_director_div', 'r2_director_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.topics'),
                                   'r2_discipline_div', 'r2_discipline_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.program_cohorts'),
                                   'r2_cohort_div', 'r2_cohort_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.competencies'),
                                   'r2_competency_div', 'r2_competency_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.objectives'),
                                   'r2_objective_div', 'r2_objective_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.mesh_terms'),
                                   'r2_mesh_div', 'r2_mesh_content');
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.learning_materials'),
                                   'r2_lm_div', 'r2_lm_content');

    contentContainer.appendChild(subContainer);

    ilios.ui.hackilySetButtonTitleOnYUIDialog(ilios_i18nVendor.getI18NString('course_management.rollover.create_course'),
                                              ilios.cm.rolloverDialog, 0);
};

// @private
ilios.cm.rollover.populateDOMForCourseSummary = function () {
    var Element = YAHOO.util.Element;
    var xmlHTTPRequest = ilios.utilities.getXMLHttpRequest();
    var url = null;
    var method = null;
    var paramString = null;
    var parsedObject = null;
    var element = null;
    var key = null;
    var parsedModel = null;
    var model = null;

    if (xmlHTTPRequest == null) {
        msg = ilios_i18nVendor.getI18NString('course_management.error.failed_connection');

        // If we throw up our custom YUI alert dialog now, the return which triggers the
        //      collapse of the autocomplete div also has some behind the scenes YUI stupidity
        //      to clear out the dialog.
        setTimeout("ilios.alert.alert('" + msg + "')", 500);

        return;
    }

    url = controllerURL + 'getRolloverSummaryViewForCourseIdInAcademicYear';
    method = 'GET';
    paramString = 'course_id=' + ilios.cm.currentCourseModel.getDBId()
        + '&year=' + ilios.cm.rollover.startYear
        + '&rnd=' + new Date().getTime();

    xmlHTTPRequest.open(method, url + '?' + paramString, false);
    if ('undefined' !== typeof xmlHTTPRequest.overrideMimeType) {
        xmlHTTPRequest.overrideMimeType("text/plain");
    }
    xmlHTTPRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlHTTPRequest.send(null);

    try {
        parsedObject = YAHOO.lang.JSON.parse(xmlHTTPRequest.responseText);
    }
    catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);

        return;
    }

    if (parsedObject.error != null) {
        var msg = ilios_i18nVendor.getI18NString('course_management.error.course_load');

        // If we throw up our custom YUI alert dialog now, the return which triggers the
        //      collapse of the autocomplete div also has some behind the scenes YUI stupidity
        //      to clear out the dialog.
        setTimeout("ilios.alert.alert('" + msg + ': ' + parsedObject.error + "')", 500);

        return;
    }

    element = document.getElementById('r2_session_count');
    element.innerHTML = parsedObject.session_count;

    element = document.getElementById('r2_director_content');
    element.innerHTML = '';
    for (key in parsedObject.directors) {
        parsedModel = parsedObject.directors[key];

        model = new UserModel(parsedModel);

        if (element.innerHTML.length > 0) {
            element.innerHTML += '<br/>';
        }
        element.innerHTML += model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
    }

    element = document.getElementById('r2_discipline_content');
    element.innerHTML = '';
    for (key in parsedObject.disciplines) {
        parsedModel = parsedObject.disciplines[key];

        if (element.innerHTML.length > 0) {
            element.innerHTML += ', ';
        }
        element.innerHTML += parsedModel.title;
    }

    if (parsedObject.different_year == 'true') {
        element = new Element(document.getElementById('r2_cohort_div'));
        element.setStyle('display', 'none');

        element = new Element(document.getElementById('r2_competency_div'));
        element.setStyle('display', 'none');
    }
    else {
        element = document.getElementById('r2_cohort_content');
        element.innerHTML = '';
        for (key in parsedObject.cohorts) {
            parsedModel = parsedObject.cohorts[key];
            model = new ProgramCohortModel(parsedModel.program_title, parsedModel.cohort_title,
                                           parsedModel.cohort_id, -1, parsedModel.start_year, null);

            if (element.innerHTML.length > 0) {
                element.innerHTML += '<br/>';
            }
            element.innerHTML += model.getDisplayText();
        }

        element = document.getElementById('r2_competency_content');
        element.innerHTML = document.getElementById('-1_competency_picker_selected_text_list').innerHTML;
    }

    element = document.getElementById('r2_objective_content');
    element.innerHTML = '';
    for (key in parsedObject.objectives) {
        parsedModel = parsedObject.objectives[key];
        model = new ObjectiveModel(parsedModel);

        if (element.innerHTML.length > 0) {
            element.innerHTML += '<br/>';
        }
        element.innerHTML += '&middot; ' + model.getDescription();
    }

    element = document.getElementById('r2_mesh_content');
    element.innerHTML = '';
    for (key in parsedObject.mesh) {
        parsedModel = parsedObject.mesh[key];

        if (element.innerHTML.length > 0) {
            element.innerHTML += ', ';
        }
        element.innerHTML += parsedModel.name;
    }

    element = document.getElementById('r2_lm_content');
    element.innerHTML = '';
    for (key in parsedObject.learning_materials) {
        parsedModel = parsedObject.learning_materials[key];

        if (element.innerHTML.length > 0) {
            element.innerHTML += '<br/>';
        }
        element.innerHTML += parsedModel.title;
    }
};

// @private
ilios.cm.rollover.createDOMForRolloverSuccess = function () {
    var contentContainer = document.getElementById('rollover_dialog_content');
    var permissionStr = ilios.cm.rollover.userCanViewNewCourse
                            ? ""
                            : " " + ilios_i18nVendor.getI18NString('course_management.rollover.warning.no_privs')
                                    + "<br/>"
                                    + ilios_i18nVendor.getI18NString('general.notification.contact_admin');
    var academicYearString = ' ' + ilios.cm.rollover.startYear
                                + '-' + (ilios.cm.rollover.startYear + 1);
    var container = null;
    var subContainer = null;
    var element = null;

    ilios.utilities.removeAllChildren(contentContainer);

    element = document.createElement('h3');
    element.setAttribute('class', 'align-c success icon-checkmark');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.success_msg_1');
    contentContainer.appendChild(element);

    element = document.createElement('p');
    element.setAttribute('class', 'align-c');
    element.innerHTML = ilios.cm.currentCourseModel.getTitle() + ' '
                        + academicYearString
                        + ' '
                        + ilios_i18nVendor.getI18NString('course_management.rollover.success_msg_2');
    contentContainer.appendChild(element);

    element = document.createElement('p');
    element.setAttribute('class', 'align-c');
    element.innerHTML = '<em>' + permissionStr + '</em>';
    contentContainer.appendChild(element);

    if (ilios.cm.rollover.userCanViewNewCourse) {

        element = document.createElement('p');
        element.setAttribute('class', 'align-c');
        subElement = document.createElement('a');
        subElement.setAttribute('href',
                                controllerURL + '?course_id=' + ilios.cm.rollover.resultingCourseId);
        subElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.open')
                                    + ' ' + ilios.cm.currentCourseModel.getTitle()
                                    + ' ' + academicYearString;
        element.appendChild(subElement);
        contentContainer.appendChild(element);

        element = document.createElement('p');
        element.setAttribute('class', 'align-c');
        subElement = document.createElement('a');
        subElement.setAttribute('href', '');
        subElement.setAttribute('onclick', 'ilios.cm.rolloverDialog.cancel(); return false;');
        subElement.innerHTML = ilios_i18nVendor.getI18NString('course_management.rollover.return_to_parent');
        element.appendChild(subElement);

        contentContainer.appendChild(element);
    }

//    contentContainer.appendChild(container);

    ilios.ui.hackilySetButtonTitleOnYUIDialog(ilios_i18nVendor.getI18NString('general.terms.close'),
                                              ilios.cm.rolloverDialog, 1);
    ilios.ui.hackilySetButtonVisibilityOnYUIDialog(ilios.cm.rolloverDialog, true, 1);
};

// @private
ilios.cm.rollover.createDOMForServerTransaction = function () {
    var contentContainer = document.getElementById('rollover_dialog_content');
    var container = document.createElement('div');
    var element = null;

    ilios.ui.hackilySetButtonVisibilityOnYUIDialog(ilios.cm.rolloverDialog, false);

    ilios.utilities.removeAllChildren(contentContainer);

    container.setAttribute('class', 'indeterminate_progress_text');

    element = document.createElement('div');
    element.setAttribute('class', 'indeterminate_progress');
    element.setAttribute('style', 'display: inline-block;');

    container.appendChild(element);
    element = document.createTextNode(' ' + ilios_i18nVendor.getI18NString('course_management.rollover.wait'));
    container.appendChild(element);

    contentContainer.appendChild(container);
}

// @private
ilios.cm.rollover.performRollover = function () {
    var url = controllerURL + 'rolloverCourse';
    var method = "POST";
    var paramString = "course_id=" + ilios.cm.currentCourseModel.getDBId()
                        + '&year=' + ilios.cm.rollover.startYear
                        + '&offerings='
                        + (ilios.cm.rollover.offeringRolloverCheckbox.checked ? 'true' : 'false')
                        + '&start_date='
                        + ilios.utilities.dateObjectToMySQLFriendly(ilios.cm.rollover.startDateObject,
                                                                    false)
                        + '&end_date='
                        + ilios.utilities.dateObjectToMySQLFriendly(ilios.cm.rollover.endDateObject,
                                                                    false);
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    // TODO close dialog ?

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('course_management.error.rollover');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    // TODO close dialog ?

                    return;
                }

                ilios.cm.rollover.resultingCourseId = parsedObject.new_cid;
                ilios.cm.rollover.userCanViewNewCourse = parsedObject.user_can_view;

                ilios.cm.rollover.progressRolloverWorkflow();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.cm.rollover.resultingCourseId = -1;
    ilios.cm.rollover.userCanViewNewCourse = false;

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
