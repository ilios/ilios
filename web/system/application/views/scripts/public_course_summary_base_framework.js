/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('course_summary');

// @private
ilios.course_summary.loadedCourseModel = null;
ilios.course_summary.allCollapsibleDivIds = null;

ilios.course_summary.showCourseSummary = function (courseModel) {
    var container = document.getElementById('course_summary_view_content_div');

    ilios.ui.renderIndeterminateInView(container);
    ilios.course_summary.courseSummaryViewDialog.showDialogPane();
    ilios.course_summary.populateCourseSummaryForCourse(container, courseModel);
    ilios.course_summary.courseSummaryViewDialog.center();

};

// @private
ilios.course_summary.createDOMForCourseSummary = function () {
    var contentContainer = document.createElement('fieldset');
    var container = null;
    var subContainer = null;
    var element = null;
    var subElement = null;
    var spanElement = null;
    var collapsibleDivId = 'summary_course_content_div';
    var idString = null;
    var outerContainer = document.getElementById('course_summary_view_content_div');
    var printBar = document.createElement('div');

    subElement = document.createElement('button');
    subElement.setAttribute('class', 'tiny radius button');
    subElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.print');
    YAHOO.util.Event.addListener(subElement, 'click', function (e) {ilios.course_summary.printCourseSummary();});

    element = document.createElement('li');
    element.appendChild(subElement);
    listElement = document.createElement('ul');
    listElement.setAttribute('class', 'buttons right');
    listElement.appendChild(element);
    printBar.setAttribute('class', 'clearfix');
    printBar.appendChild(listElement);
    outerContainer.appendChild(printBar);

    subContainer = document.createElement('ul');
    subContainer.setAttribute('class', 'summary clearfix');

    subElement = document.createElement('li');
    subElement.setAttribute('id', 'summary_title_div');
    subElement.innerHTML = '&nbsp;';
    subContainer.appendChild(subElement);

    subElement = document.createElement('li');
    spanElement = document.createElement('span');
    spanElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.id') + ': ';
    subElement.appendChild(spanElement);
    spanElement = document.createElement('span');
    spanElement.setAttribute('id', 'summary_course_id');
    spanElement.innerHTML = '&nbsp;';
    subElement.appendChild(spanElement);
    subContainer.appendChild(subElement);

    subElement = document.createElement('li');
    spanElement = document.createElement('span');
    spanElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.level') + ' ';
    subElement.appendChild(spanElement);
    spanElement = document.createElement('span');
    spanElement.setAttribute('id', 'summary_course_level');
    spanElement.innerHTML = '&nbsp;';
    subElement.appendChild(spanElement);
    subContainer.appendChild(subElement);

    subElement = document.createElement('li');
    spanElement = document.createElement('span');
    spanElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.start_date') + ': ';
    subElement.appendChild(spanElement);
    spanElement = document.createElement('span');
    spanElement.setAttribute('id', 'summary_course_start_date');
    spanElement.innerHTML = '&nbsp;';
    subElement.appendChild(spanElement);
    subContainer.appendChild(subElement);

    subElement = document.createElement('li');
    spanElement = document.createElement('span');
    spanElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.end_date') + ': ';
    subElement.appendChild(spanElement);
    spanElement = document.createElement('span');
    spanElement.setAttribute('id', 'summary_course_end_date');
    spanElement.innerHTML = '&nbsp;';
    subElement.appendChild(spanElement);
    subContainer.appendChild(subElement);

    ilios.course_summary.createCollapsingBlock(subContainer, collapsibleDivId);

    contentContainer.appendChild(subContainer);

    subContainer = document.createElement('div');
    subContainer.setAttribute('id', collapsibleDivId);

    idString = 'summary_course_director_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.directors'),
                                   'summary_course_director_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_course_competency_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.competencies'),
                                   'summary_course_competency_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_course_discipline_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.topics'),
                                   'summary_course_discipline_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_course_objective_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.objectives'),
                                   'summary_course_objective_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_course_lm_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.learning_materials'),
                                   'summary_course_lm_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_course_mesh_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.mesh_terms'),
                                   'summary_course_mesh_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    contentContainer.appendChild(subContainer);
    return contentContainer;
};

ilios.course_summary.createCollapsingBlock = function (parentContainer, contentDivId) {
    var element = document.createElement('div');

    element.setAttribute('style', 'position: absolute; cursor: pointer; top: 9px; right: 3px;');
    element.innerHTML = "+ / -";
    element.divIsCollapsed = false;
    YAHOO.util.Event.addListener(element, 'click',
                       function () {ilios.ui.toggleCollapsedVisibility(this,   // TODO closure
                                                                       contentDivId, false);});
    parentContainer.appendChild(element);

    ilios.course_summary.allCollapsibleDivIds.push(contentDivId)
};


/**
 * Returns markup for printing out the "no published session info available" message
 * in the course summary widget view.
 * @private
 * @return {HTMLElement}
 */
ilios.course_summary._createDOMForNoPublishedSessionInfoInCourseSummary = function() {
	var outerElem, innerElem = null,
		msg = ilios_i18nVendor.getI18NString('general.phrases.no_published_sessions'),
		Dom = YAHOO.util.Dom;
	outerElem = document.createElement('fieldset');
	innerElem = document.createElement('p');
	innerElem.innerHTML = msg;
	outerElem.appendChild(innerElem);
	return outerElem;
}

// @private
ilios.course_summary.createDOMForSessionSummary = function (sessionModel, sessionCounter) {
    var contentContainer = document.createElement('fieldset');
    var container = null;
    var subContainer = null;
    var element = null;
    var collapsibleDivId = 'summary_session_' + sessionCounter + '_content_div';
    var idString = null;
    var sessionTitle = null;
    var rowEl, labelEl, dataEl;
    var ilm = sessionModel.getIndependentLearningModel();

    rowEl = ilios.dom.createEntityContainerInputRow();

    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.session') + ':';
    labelEl = ilios.dom.createLabelCol(rowEl, element);

    element = document.createElement('span');
    idString = 'summary_session_' + sessionCounter + '_title_string_div';
    element.setAttribute('id', idString);
    dataEl = ilios.dom.createDataCol(rowEl, element);
    dataEl.setAttribute('id', idString);

    ilios.course_summary.createCollapsingBlock(rowEl, collapsibleDivId);
    contentContainer.appendChild(rowEl);

    subContainer = document.createElement('div');
    subContainer.setAttribute('id', collapsibleDivId);

    rowEl = ilios.dom.createEntityContainerInputRow();

    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.session.session_type');
    labelEl = ilios.dom.createLabelCol(rowEl, element);

    element = document.createElement('span');
    idString = 'summary_session_' + sessionCounter + '_session_type';
    element.setAttribute('id', idString);
    dataEl = ilios.dom.createDataCol(rowEl, element);

    subContainer.appendChild(rowEl);

    rowEl = ilios.dom.createEntityContainerInputRow();

    element = document.createElement('label');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.curriculum_type');
    labelEl = ilios.dom.createLabelCol(rowEl, element);

    element = document.createElement('span');
    idString = 'summary_session_' + sessionCounter + '_type';
    element.setAttribute('id', idString);
    dataEl = ilios.dom.createDataCol(rowEl, element);

    subContainer.appendChild(rowEl);

    idString = 'summary_session_' + sessionCounter + '_offered_content';

    if (ilm) {
        rowEl = ilios.dom.createEntityContainerInputRow();

        element = document.createElement('label');
        element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.due_by');
        labelEl = ilios.dom.createLabelCol(rowEl, element);

        element = document.createElement('span');
        element.setAttribute('id', idString);
        dataEl = ilios.dom.createDataCol(rowEl, element);
        subContainer.appendChild(rowEl);
    } else {
        ilios.ui.createCollapsingBlock(subContainer, ilios_i18nVendor.getI18NString('general.terms.offered'),
            'summary_session_' + sessionCounter + '_offered_div', idString);
        ilios.course_summary.allCollapsibleDivIds.push(idString);
    }

    idString = 'summary_session_' + sessionCounter + '_description_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.description'),
                                   'summary_session_' + sessionCounter + '_description_div',
                                   idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_session_' + sessionCounter + '_objective_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.objectives'),
                                   'summary_session_' + sessionCounter + '_objective_div',
                                   idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_session_' + sessionCounter + '_lm_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.learning_materials'),
                                   'summary_session_' + sessionCounter + '_lm_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_session_' + sessionCounter + '_discipline_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.terms.topics'),
                                   'summary_session_' + sessionCounter + '_discipline_div',
                                   idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    idString = 'summary_session_' + sessionCounter + '_mesh_content';
    ilios.ui.createCollapsingBlock(subContainer,
                                   ilios_i18nVendor.getI18NString('general.phrases.mesh_terms'),
                                   'summary_session_' + sessionCounter + '_mesh_div', idString);
    ilios.course_summary.allCollapsibleDivIds.push(idString);

    contentContainer.appendChild(subContainer);

    return contentContainer;
};

// @private
ilios.course_summary.printCourseSummary = function () {
    var summaryDiv = document.getElementById('course_summary_view_content_div');
    var newWindow = window.open('', 'Ilios_Print_Window',
                                'width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,'
                                        + 'status=no,resizable=yes');
    var length = ilios.course_summary.allCollapsibleDivIds.length;
    var yElement = null;

    // Expand all divs so that they can be seen in the print copy
    for (var i = 0; i < length; i++) {
        yElement = new YAHOO.util.Element(document.getElementById(ilios.course_summary.allCollapsibleDivIds[i]));

        yElement.setStyle('display', 'block');
    }

    newWindow.document.writeln('<html><head><link rel="stylesheet" type="text/css" '
                                + 'href="/system/application/views/css/ilios-styles.css" '
                                + 'media="screen,print" charset="utf-8"></head><body class="print">');
    newWindow.document.writeln(summaryDiv.innerHTML);
    newWindow.document.writeln('</body></html>');
    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
//    newWindow.close();
};

// @private
ilios.course_summary.populateCourseSummaryForCourse = function (container, courseModel) {
    var element = null,
    	sessions = courseModel.getSessions(),
    	session = null,
    	n = sessions.length,
    	i = 0,
    	hasSessionInfo = false;

    ilios.utilities.removeAllChildren(container);

    ilios.course_summary.allCollapsibleDivIds = new Array();

    element = ilios.course_summary.createDOMForCourseSummary();
    container.appendChild(element);
    ilios.course_summary.populateDOMForCourse(courseModel);

    if (0 < n) {
        for (i = 0; i < n; i++) {
            session = sessions[i];
            // sparse array, ignore it's empty items
            if ('undefined' !== typeof session) {
                hasSessionInfo = true;
                element = ilios.course_summary.createDOMForSessionSummary(session, i);
                container.appendChild(element);
                ilios.course_summary.populateDOMForSession(session, i);
            }
        }
    }
    if (! hasSessionInfo) {
    	element = ilios.course_summary._createDOMForNoPublishedSessionInfoInCourseSummary();
    	container.appendChild(element);
    }
};

// @private
ilios.course_summary.populateDOMForCourse = function (courseModel) {
    var academicYearString = '' + courseModel.getYear() + '-'
                                    + (parseInt(courseModel.getYear()) + 1);
    var element = document.getElementById('summary_title_div');
    var contentString = '';
    var anArray = null;
    var listElement = null;
    var itemElement = null;
    var key = null;

    element.innerHTML = courseModel.getTitle() + ' ' + academicYearString;

    element = document.getElementById('summary_course_id');
    element.innerHTML = courseModel.getExternalCourseId();

    element = document.getElementById('summary_course_level');
    element.innerHTML = courseModel.getCourseLevel();

    element = document.getElementById('summary_course_start_date');
    element.innerHTML = courseModel.getStartDateAsDateObject().format('mm/dd/yyyy');

    element = document.getElementById('summary_course_end_date');
    element.innerHTML = courseModel.getEndDateAsDateObject().format('mm/dd/yyyy');

    element = document.getElementById('summary_course_director_content');
    element.innerHTML = courseModel.getDirectorsAsFormattedText();

    element = document.getElementById('summary_course_competency_content');
    element.innerHTML
        = ilios.competencies.generateListHTMLForSelectedCompetencies(courseModel.getCompetencies());

    element = document.getElementById('summary_course_discipline_content');
    anArray = courseModel.getDisciplines();
    for (i = 0, n = anArray.length; i < n; i++) {
        if (contentString != '') {
            contentString += ', ';
        }
        contentString += anArray[i].getTitle();
    }
    element.innerHTML = contentString;

    element = document.getElementById('summary_course_objective_content');
    anArray = courseModel.getObjectives();
    if (ilios.utilities.arraySize(anArray) > 0) {
        listElement = document.createElement('ul');
        for (key in anArray) {
            itemElement = document.createElement('li');
            itemElement.innerHTML = anArray[key].getTitle();
            listElement.appendChild(itemElement);
        }
        element.appendChild(listElement);
    }

    element = document.getElementById('summary_course_lm_content');
    anArray = courseModel.getLearningMaterials();
    if (ilios.utilities.arraySize(anArray) > 0) {
        listElement = document.createElement('ul');
        listElement.setAttribute('class', 'learning_material_list');
        for (key in anArray) {
            itemElement
                = ilios.course_summary.createListElementForLearnerViewLearningMaterial(anArray[key], true);
            listElement.appendChild(itemElement);
        }
        element.appendChild(listElement);
    }

    element = document.getElementById('summary_course_mesh_content');
    element.innerHTML = courseModel.getMeSHItemsAsFormattedText();
};

// @private
ilios.course_summary.populateDOMForSession = function (sessionModel, sessionNumber) {
    var element = document.getElementById('summary_session_' + sessionNumber + '_title_string_div');
    var anArray = null;
    var contentString = '';
    var key = null;
    var listElement = null;
    var itemElement = null;
    var i, n;
    var ilm;

    element.innerHTML = sessionModel.getTitle();

    element = document.getElementById('summary_session_' + sessionNumber + '_session_type');
    ilm = sessionModel.getIndependentLearningModel();

    if (ilm) {
        element.innerHTML = ilios_i18nVendor.getI18NString('course_management.session.independent_learning');
    } else {
        element.innerHTML = sessionModel.getSessionTypeTitle();
    }

    element = document.getElementById('summary_session_' + sessionNumber + '_type');
    element.innerHTML = sessionModel.isSupplemental() ? 'Supplemental' : 'Core';

    element = document.getElementById('summary_session_' + sessionNumber + '_offered_content');
    if (ilm) {
        element.innerHTML = ilm.getDueDate().format('ddd mmm dd yyyy');
    } else {
        sessionModel.sortOfferings();
        anArray = sessionModel.getOfferings();
        if (ilios.utilities.arraySize(anArray) > 0) {
            element.innerHTML = sessionModel.getOfferingsEnumerationFormattedHTML();
        }
    }

    element = document.getElementById('summary_session_' + sessionNumber + '_description_content');
    element.innerHTML = sessionModel.getDescription();

    element = document.getElementById('summary_session_' + sessionNumber + '_objective_content');
    anArray = sessionModel.getObjectives();
    if (ilios.utilities.arraySize(anArray) > 0) {
        listElement = document.createElement('ul');
        for (key in anArray) {
            itemElement = document.createElement('li');
            itemElement.innerHTML = anArray[key].getTitle();
            listElement.appendChild(itemElement);
        }
        element.appendChild(listElement);
    }

    element = document.getElementById('summary_session_' + sessionNumber + '_lm_content');
    anArray = sessionModel.getLearningMaterials();
    if (ilios.utilities.arraySize(anArray) > 0) {
        listElement = document.createElement('ul');
        listElement.setAttribute('class', 'learning_material_list');
        for (key in anArray) {
            itemElement
                = ilios.course_summary.createListElementForLearnerViewLearningMaterial(anArray[key], sessionModel.isLearner());
            listElement.appendChild(itemElement);
        }
        element.appendChild(listElement);
    }

    element = document.getElementById('summary_session_' + sessionNumber + '_discipline_content');
    anArray = sessionModel.getDisciplines();
    for (i = 0, n = anArray.length; i < n; i++) {
        if (contentString != '') {
            contentString += ', ';
        }
        contentString += anArray[i].getTitle();
    }
    element.innerHTML = contentString;

    element = document.getElementById('summary_session_' + sessionNumber + '_mesh_content');
    element.innerHTML = sessionModel.getMeSHItemsAsFormattedText();
};

// @private
ilios.course_summary.createListElementForLearnerViewLearningMaterial = function (model, enableLink) {
	var rhett = document.createElement('li');
    var isLink = (model.getMimeType() == 'link');
    var isCitation = (model.getMimeType() == 'citation');
    var isFile = ((! isLink) && (! isCitation));
    var elementType = (isCitation || !enableLink) ? 'span' : 'a';
    var titleElement = document.createElement(elementType);
    var element = null;

    rhett.setAttribute('class', ilios.utilities.convertMimeTypeToCSSClassName(model.getMimeType()));
    rhett.setAttribute('style', 'position: relative;');

    if (model.isRequired()) {
        element = document.createElement('i');
        element.setAttribute('class', 'icon-star');
        element.setAttribute('title', 'Required');
        titleElement.appendChild(element);
    }


    titleElement.appendChild(document.createTextNode(model.getTitle()));
    titleElement.setAttribute('class', 'title');

    rhett.appendChild(titleElement);


    if (isFile) {
        var downloadURL = learningMaterialsControllerURL
                            + "getLearningMaterialWithId?learning_material_id=" + model.getDBId();
        var str = " (" + model.getFileSize() + "KB)";

    if (enableLink)
            titleElement.setAttribute('href', downloadURL);

        element = document.createElement('span');
        element.setAttribute('class','filesize');
        element.appendChild(document.createTextNode(str));
        rhett.appendChild(element);

        if ((model.getDescription() != null) && (model.getDescription().length > 0)) {
            element = document.createElement('div');
            element.setAttribute('class', 'description');
            element.appendChild(document.createTextNode(model.getDescription()));
            rhett.appendChild(element);
        }
    }
    else if (isLink) {
        var linkURL = model.getLink();

        if (linkURL.indexOf('://') == -1) {
            linkURL = 'http://' + model.getLink();
        }

        if (enableLink) {
            titleElement.setAttribute('href', linkURL);
            titleElement.setAttribute('target', '_new');
    }

        if ((model.getDescription() != null) && (model.getDescription().length > 0)) {
            element = document.createElement('div');
            element.setAttribute('class', 'description');
            element.appendChild(document.createTextNode(model.getDescription()));
            rhett.appendChild(element);
        }
    }
    else if (isCitation) {
        var divElement = document.createElement('div');

        divElement.setAttribute('class', 'description');
        divElement.appendChild(document.createTextNode(model.getCitation()));

        rhett.appendChild(divElement);
    }

    if (model.isNotesPubliclyViewable() && (model.getNotes() != '')) {
        element = document.createElement('div');
        element.setAttribute('class', 'description');
        element.innerHTML = model.getNotes();

        rhett.appendChild(element);
    }
    return rhett;
};

ilios.course_summary.loadCourseAndShowSummary = function (courseId) {
    var container = document.getElementById('course_summary_view_content_div');

    ilios.ui.renderIndeterminateInView(container);

    ilios.course_summary.courseSummaryViewDialog.showDialogPane();

    ilios.course_summary.loadedCourseModel = ilios.course_model.loadCourseTreeSynchronously(courseId, true,
        ilios.course_summary.offeringsLoadsFinishedListener, 'getDashboardCourseTree');
};

ilios.course_summary.offeringsLoadsFinishedListener = function () {
    var container = document.getElementById('course_summary_view_content_div');

    ilios.course_summary.populateCourseSummaryForCourse(container,
                                                        ilios.course_summary.loadedCourseModel);
};
