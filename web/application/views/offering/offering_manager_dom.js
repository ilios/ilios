/*
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

/*
 * These are the data structures from which the data table is populated.
 */

ilios.om.offeringTableDataStructure = new Array();
ilios.om.sessionOnlyOfferingTableDataStructure = new Array();

/*
 * These will be a non-associative array of Object instances; each instance will have the following
 * 	properties:
 * 		.displayName
 * 		.startDate
 * 		.endDate
 * 		.sessionTypeId
 */
ilios.om.associatedInstructorGroups = null;
ilios.om.associatedLearners = null;

/*
 * Classname uniquers used in rendering busy times.
 *
 * @see ilios.om.calendar.renderBusyEvents(...) in offering_manager_calendar_support.js
 */
ilios.om.instructorGroupsUniqueClassName = 'assoc_instructors_busy_class_name';
ilios.om.learnersUniqueClassName = 'assoc_learners_busy_class_name';


/*
 * We extend the DataTable functionality here with a new method called requery which allows the
 * 	data table to recache from its data source.
 */
YAHOO.widget.DataTable.prototype.requery = function (newRequest) {
	var dataSource = this.getDataSource();
	var req = null;

	if (this.get('dynamicData')) {
		// For dynamic data, newRequest is ignored since the request is built by
		// function generateRequest.
		var paginator = this.get('paginator');

		this.onPaginatorChangeRequest(paginator.getState({ 'page': paginator.getCurrentPage() }));
	}
	else {
		// The LocalDataSource needs to be treated different
		if (dataSource instanceof YAHOO.util.LocalDataSource) {
			dataSource.liveData = newRequest;

			req = "";
		}
		else {
			req = ((newRequest === undefined) ? this.get('initialRequest')
											  : newRequest);
		}

		dataSource.sendRequest(req, { success: this.onDataReturnInitializeTable,
									  failure: this.onDataReturnInitializeTable,
									  scope: this, argument: this.getState() });
	}
};

/**
 * A convenience method handed over to the YAHOO data source instance.
 */
ilios.om.getOfferingSummaryTableData = function () {
	if (document.getElementById('show_all_events_radio').checked) {
		return ilios.om.offeringTableDataStructure;
	}

	return ilios.om.sessionOnlyOfferingTableDataStructure;
};

/*
 * A method to force the data table to recache from its data source.
 *
 * Should be considered @protected
 */
ilios.om.forceDataTableRefresh = function () {
	if (ilios.om.offeringDataTable != null) {
		ilios.om.offeringDataTable.requery();
	}
};

/*
 * A helper method used in the building of the session type legend UI.
 *
 * Should be considered @private
 */
ilios.om.createTypeLegendTableCell = function (title, sessionTypeCssClass, bold) {
    var cell = document.createElement('td');
	var text = document.createTextNode(title);

	sessionTypeCssClass = sessionTypeCssClass || '';

	cell.setAttribute('style', ('padding: 0px 0px 0px 9px'+ (bold ? '; font-weight: bold;' : ';')));

	//set the classes of the table cell to the default of 'session-type-legend' AND
	//it's respective session-type class, as pulled from the db 'session' table
	cell.setAttribute('class', 'session-type-legend ' + sessionTypeCssClass);

	cell.appendChild(text);

	return cell;
};

/*
 * This is invoked via onDomReady and builds the DOM which is responsible for displaying the
 * 	session type legend information.
 */
ilios.om.buildSessionTypeLegend = function () {
	var container = document.getElementById('offering_calendar_color_legend_div');
	var legendTable = document.createElement('table');
	var rowElement = document.createElement('tr');
	var currentColumn = 1;
	var model = null;
	var i18nStr = ilios_i18nVendor.getI18NString('offering_management.session_legend');
	var i;

	legendTable.setAttribute('border', '0');
	legendTable.setAttribute('style', 'width: 100%;');

	rowElement.setAttribute('style', 'padding: 0px;');

	rowElement.appendChild(ilios.om.createTypeLegendTableCell(i18nStr, '', true));
	for (i in ilios.om.loadedSessionTypes) {
		if (ilios.om.loadedSessionTypes.hasOwnProperty(i)) {
			model = ilios.om.loadedSessionTypes[i];

			rowElement.appendChild(ilios.om.createTypeLegendTableCell(model.title, model.sessionTypeCssClass, false));

			currentColumn++;
			if ((currentColumn % 4) == 0) {
				legendTable.appendChild(rowElement);

				rowElement = document.createElement('tr');
				rowElement.setAttribute('style', 'padding: 0px;');
			}
		}
	}

	if (rowElement.children.length > 0) {
		legendTable.appendChild(rowElement);
	}

	container.appendChild(legendTable);
};

/**
 * This is messaged via onDomReady and is responsible for wiring up the main UI interactive items
 * 	with the underlying javascript.
 *
 * This DOES NOT handle the DHTMLX calendar specific listeners (that's done in
 * 		ilios.om.calendar.initCalendar() in offering_manager_calendar_support.js)
 */
ilios.om.registerOfferingUIListeners = function () {
	var Event = YAHOO.util.Event;
	var element = document.getElementById('show_all_events_radio');

	Event.addListener(element, 'click', function () {
        ilios.om.showEventsRadioClicked(false);
    });

	element = document.getElementById('show_sessions_radio');
	Event.addListener(element, 'click', function () {
        ilios.om.showEventsRadioClicked(true);
    });

	element = document.getElementById('show_busy_instructors_checkbox');
	Event.addListener(element, 'click', function () {
        ilios.om.showBusyInstructorsToggled();
    });

	element = document.getElementById('show_busy_students_checkbox');
	Event.addListener(element, 'click', function () {
        ilios.om.showBusyLearnersToggled();
    });

	element = document.getElementById('show_busy_cohorts_checkbox');
	Event.addListener(element, 'click', function () {
        showBusyCohortsToggled();
    });
};

/**
 * This changes the contents of the DHTMLX scheduler canvas to show either only offerings related
 * 	to the session-in-edit, or all offerings associated to the course owning the session-in-edit.
 */
ilios.om.showEventsRadioClicked = function (showOnlySession) {
	ilios.om.calendar.addEventsFromModelToScheduler(null, null, showOnlySession);

	ilios.om.forceDataTableRefresh();
};

/**
 * This sets whether the user wishes to have the DHTMLX scheduler canvas display the busy time for
 * 	associated instructors.
 */
ilios.om.showBusyInstructorsToggled = function () {
	var element = document.getElementById('show_busy_instructors_checkbox');

	if (element.checked) {
		ilios.om.transaction.fetchAssociatedInstructorsBusyTime();
	}
	else {
		ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName(ilios.om.instructorGroupsUniqueClassName);
	}
};

/**
 * This sets whether the user wishes to have the DHTMLX scheduler canvas display the busy time for
 * 	associated learners.
 */
ilios.om.showBusyLearnersToggled = function () {
	var element = document.getElementById('show_busy_students_checkbox');

	if (element.checked) {
		ilios.om.transaction.fetchAssociatedLearnerBusyTime();
	}
	else {
		ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName(ilios.om.learnersUniqueClassName);
	}
};
