/**
 * Curriculum inventory management (cim) application module components.
 *
 * Defines the following namespaces:
 *     ilios.cim
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 *     YUI Dom/Event/Element libs
 *     YUI Container libs
 *     YUI Cookie lib
 *     application/views/curriculum_inventory/js/ilios.cim.model.js
 *     application/views/curriculum_inventory/js/ilios.cim.view.js
 *     application/views/curriculum_inventory/js/ilios.cim.widget.js
 */
(function () {

    ilios.namespace('cim');

    var Lang = YAHOO.lang,
        Event = YAHOO.util.Event,
        Dom = YAHOO.util.Dom

    /**
     * Creates a client-side application.
     * It's the top level object responsible for instantiating a self-contained application environment
     * and for providing routing and high-level workflow management functionality.
     *
     * @namespace cim
     * @class App
     * @param {Object} config the application configuration. Expect the following attributes to be present:
     *     'controllerUrl' ... URL to the server-side "curriculum inventory manager" controller.
     *     'programControllerUrl' ... URL to the server-side "program manager" controller.
     * @param {Object} [payload] The initial page payload. It may have these data points as properties:
     *     "programs" ... an object holding the programs available for reporting on.
     *     "report" ... (optional) an object representing the currently selected report.
     *     "courses" ... (optional) An lookup object (keyed off by course id) holding linked and linkable courses
     *         with/for sequence blocks in this report.
     *     "sequence" ... (optional) An object representing the report sequence.
     *     "sequence_blocks" ... (optional) An array of report sequence blocks.
     *     "academic_levels" ... (optional) An array of academic levels available in the given report.
     * @constructor
     * @todo way to much things going on in there for a single function. chunk it up into separate init functions.
     */
    var App = function (config, payload) {

        var i, n,
            sequenceBlockModel, sequenceBlockView;

        // set module configuration
        this._config = config;

        this._statusBar = new ilios.cim.widget.StatusBar({});
        this._statusBar.render('status-toolbar');

        this._programs = payload.programs;

        // instantiate data source
        this._dataSource = new ilios.cim.DataSource({
            finalizeUrl: this._config.controllerUrl + 'finalize',
            deleteUrl: this._config.controllerUrl + 'delete'
        })

        // wire dialogs to buttons
        Event.addListener('pick_reports_btn', 'click', function (event) {
            if (! this._reportPickerDialog) { // instantiate on demand
                this._reportPickerDialog = new ilios.cim.widget.ReportPickerDialog('report_picker_dialog');
                this._reportPickerDialog.render();
            }
            this._reportPickerDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        Event.addListener('create_report_btn', 'click', function (event) {
            if (! this._createReportDialog) {
                this._createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog', {}, this._programs);
                this._createReportDialog.render();
            }
            this._createReportDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        if (payload.hasOwnProperty('report')) {

            // process data
            this.academicLevels = payload.academic_levels;
            this.sequenceBlock = payload.sequence_block;
            this.sequenceBlocks = payload.sequence_blocks;
            this.linkableCourses = payload.linkable_courses;
            this.linkedCourses = payload.linked_courses;
            this._reportModel = new ilios.cim.model.ReportModel(payload.report);

            // set up views and widgets
            this._reportView = new ilios.cim.view.ReportView(this._reportModel, {});
            this._reportView.render();

            // subscribe to events
            this._reportView.subscribe(this._reportView.EVT_EXPORT_STARTED, function() {
                this._statusBar.show('Started Report Export &hellip;', true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_STARTED, function() {
                this._statusBar.show('Started Report Download &hellip;', true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_EXPORT_COMPLETED, function () {
                this._statusBar.reset();
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_COMPLETED, function () {
                this.reset();
            }, this, true);
            this._reportModel.subscribe(this._reportModel.EVT_ON_UPDATE, function () {
                this._statusBar.show('Report updated.', false);
            }, this, true);
            this._dataSource.subscribe(this._dataSource.EVT_FINALIZE_REPORT_STARTED, function () {
                this.show('Finalizing report started &hellip;', true);
            }, this._statusBar, true);
            this._dataSource.subscribe(this._dataSource.EVT_FINALIZE_REPORT_SUCCEEDED, function () {
                this._statusBar.reset();
            }, this, true);
            this._dataSource.subscribe(this._dataSource.EVT_FINALIZE_REPORT_FAILED, function () {
                this._statusBar.show('Finalizing report failed.', false);
            }, this, true);
            this._dataSource.subscribe(this._dataSource.EVT_DELETE_REPORT_STARTED, function () {
                this._statusBar.show('Deleting report &hellip;', true);
            }, this, true);
            this._dataSource.subscribe(this._dataSource.EVT_DELETE_REPORT_FAILED, function () {
                this._statusBar.show('Failed to delete report.', false);
            }, this, true);
            this._dataSource.subscribe(this._dataSource.EVT_DELETE_REPORT_SUCCEEDED, function() {
                this._statusBar.show('Successfully deleted report. Reloading page &hellip;', true);
                // reload the page
                window.location = window.location.protocol + "//" + window.location.host + window.location.pathname;
            }, this, true);

            this._sequenceBlockTopToolbar = new ilios.cim.widget.SequenceBlockTopToolbar({});
            this._sequenceBlockTopToolbar.render();

            this._sequenceBlockBottomToolbar = new ilios.cim.widget.SequenceBlockBottomToolbar({});
            this._sequenceBlockBottomToolbar.render(! this._reportModel.get('isFinalized'));

            // more wiring of event handlers

            Event.addListener(this._sequenceBlockTopToolbar.getExpandButton(), 'click', function (event) {
                this.expandAllSequenceBlocks();
            }, {}, this);

            Event.addListener(this._sequenceBlockTopToolbar.getCollapseButton(), 'click', function (event) {
                this.collapseAllSequenceBlocks();
            }, {}, this);


            this._reportView.subscribe('finalizeSucceeded', function () {
                this._sequenceBlockBottomToolbar.disableButtons();
                // @todo disable sequence block controls
            }, this , true);

            // show views and widgets
            this._reportView.show();
            this._sequenceBlockBottomToolbar.show();
            this._sequenceBlockTopToolbar.show();

            // deal with sequence block models and views
            // @todo break this out into three steps
            // 1. create models
            // 2. create views from models
            // 3. show views
            for (i = 0, n = payload.sequence_blocks.length; i < n; i++) {
                sequenceBlockModel = this.createSequenceBlockModel(payload.sequence_blocks[i]);
                sequenceBlockView = this.createSequenceBlockView(sequenceBlockModel);
                sequenceBlockView.render();
                sequenceBlockView.show();
            }
        }
    };

    App.prototype = {

        /**
         * The application configuration.
         *
         * @property _config
         * @type {Object}
         * @protected
         */
        _config: null,

        /**
         * The application's data source object.
         *
         * @property _dataSource
         * @type {ilios.cim.DataSource}
         * @protected
         */
        _dataSource: null,

        /**
         * The report model.
         *
         * @property _reportModel
         * @type {ilios.cim.model.ReportModel}
         * @protected
         */
        _reportModel: null,

        /**
         * The report view.
         *
         * @property _reportView
         * @type {ilios.cim.view.ReportView}
         * @protected
         */
        _reportView: null,
        
        /**
         * The registry of sequence blocks instances.
         *
         * @property _sequenceBlockViewRegistry
         * @type {Object}
         * @protected
         */
        _sequenceBlockViewRegistry: {},

        /**
         * The application's status-message bar.
         *
         * @property _statusBar
         * @type {ilios.cim.widget.StatusBar}
         * @protected
         */
        _statusBar: null,

        /**
         * A map of programs, keyed off by their program id.
         *
         * @property _programs
         * @type {Object}
         * @protected
         */
        _programs: {},

        /**
         * A dialog widget for selecting and loading existing reports onto the page.
         *
         * @property _reportPickerDialog
         * @type {ilios.cim.widget.ReportPickerDialog}
         * @protected
         */
        _reportPickerDialog: null,

        /**
         * A dialog widget for creating a new report.
         *
         * @property _createReportDialog
         * @type {ilios.cim.widget.CreateReportDialog}
         * @protected
         */
        _createReportDialog: null,

        /**
         * Returns the application configuration.
         *
         * @method getConfig
         * @return {Object] The configuration object.
         */
        getConfig: function () {
            return this._config;
        },

        /**
         * Creates a sequence block model object from a given data transfer object representing a sequence block record.
         *
         * @method createSequenceBlockModel
         * @param {Object} oData The data transfer object.
         * @return {ilios.cim.model.SequenceBlockModel} The created model.
         */
        createSequenceBlockModel: function (oData) {
            // @todo add model to registry
            return new ilios.cim.model.SequenceBlockModel(oData);
        },

        /**
         * Creates and returns an sequence block view for a given sequence block model.
         *
         * @method createSequenceBlockView
         * @param {ilios.cim.model.SequenceBlockModel} model
         * @return {ilios.cim.view.SequenceBlockView}
         */
        createSequenceBlockView: function (model) {
            var parentId, id, parentEl, el, view;

            parentId = model.get('parentId');
            id = model.get('id');

            parentEl = parentId ?  document.getElementById('sequence-block-view-children-' + parentId) : document.getElementById('report-sequence-container');
            el = generateSequenceBlockMarkup(id);

            // attach the view element to it's parent in the document.
            parentEl.appendChild(el);

            view = new ilios.cim.view.SequenceBlockView(model, el);

            this._sequenceBlockViewRegistry[id] = view; // add the view to the registry so we can pull it up later

            return view;
        },
        /**
         * Expands all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        expandAllSequenceBlocks: function () {
            var cnumber, view, registry;
            registry = this._sequenceBlockViewRegistry;
            for (cnumber in registry) {
                if (registry.hasOwnProperty(cnumber)) {
                    view = registry[cnumber];
                    view.expand();
                }
            }
        },

        /**
         * Collapses all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        collapseAllSequenceBlocks: function () {
            var cnumber, view, registry;
            registry = this._sequenceBlockViewRegistry;
            for (cnumber in registry) {
                if (registry.hasOwnProperty(cnumber)) {
                    view = registry[cnumber];
                    view.collapse();
                }
            }
        }
    };

    /**
     * Storage container of sequence block views within the application.
     * Keeps track of parent-child relationships between views.
     *
     * @namespace cim
     * @class SequenceBlockViewRegistry
     * @constructor
     */
    var SequenceBlockViewRegistry = function () {};

    SequenceBlockViewRegistry.protoype = {
        /**
         * The internal registry object.
         * @var _registry
         * @type {Object}
         * @protected
         */
        _registry: {},
        /**
         * @method add
         * @param {ilios.cim.view.SequenceBlockView} view
         * @return {Boolean} TRUE on success, FALSE on failure.
         */
        add: function (view) {
            // @todo implement
        },
        /**
         * @method remove
         * @param {Number} cnumber
         * @return {ilios.cim.view.SequenceBlockView} The removed view object.
         */
        remove: function (cnumber) {
            // @todo implement
        },

        /**
         * @method list
         * @return {Array}
         */
        list: function () {
            // @todo implement
        },

        /**
         * @method map
         * @param {Number} cnumber
         * @return {Object}
         */
        map: function (cnumber) {
            // @todo implement
        },

        /**
         * @method children
         * @param {Number} cnumber
         * @return {Array}
         */
        children: function (cnumber) {
            // @todo implement
        },

        /**
         * @method exists
         * @param {Number} cnumber
         * @return {Boolean}
         */
        exists: function (cnumber) {
            // @todo implement
        }
    };

    /**
     * Provides functionality for exchanging data with the server-side backend.
     * All communication with the server will be handled asynchronous via XHR calls.
     * @namespace cim
     * @class App
     * @uses YAHOO.util.EventProvider
     * @constructor
     * @param {Object} config The data source configuration object. Expect to contain the following attributes:
     *     'deleteReportUrl' ... The endpoint URL to the server-side "delete report" controller action.
     *     'finalizeReportUrl' ... The endpoint URL to the server-side "finalize report" controller action.
     */
    var DataSource = function (config) {

        this._config = config;

        // create custom events provided by this object
        this.createEvent(DataSource.EVT_FINALIZE_REPORT_STARTED);
        this.createEvent(DataSource.EVT_FINALIZE_REPORT_SUCCEEDED);
        this.createEvent(DataSource.EVT_FINALIZE_REPORT_FAILED);
        this.createEvent(DataSource.EVT_DELETE_REPORT_STARTED);
        this.createEvent(DataSource.EVT_DELETE_REPORT_SUCCEEDED);
        this.createEvent(DataSource.EVT_DELETE_REPORT_FAILED);
    };

    DataSource.prototype = {

        /**
         * The data source configuration.
         * @property _config
         * @type {Object}
         */
        _config: null,
        /**
         * @method finalizeReport
         * @param {Number} id The report id.
         */
        finalizeReport: function (id) {
            // @todo implement
        },

        /**
         * @method deleteReport
         * @param {Number} id The report id.
         */
        deleteReport: function (id) {
            // @todo implement
        },
        /**
         * Fired when a finalize report request has been sent to the server.
         * @event finalizeReportStarted
         * @final
         */
        EVT_FINALIZE_REPORT_STARTED: 'finalizeReportStarted',

        /**
         * Fired when the a server response indicating successful report finalization has been received.
         * @event finalizeReportSucceeded
         * @final
         */
        EVT_FINALIZE_REPORT_SUCCEEDED: 'finalizeReportSucceeded',

        /**
         * Fired when the a server response indicating a failure in finalizing a report has been received.
         * @event finalizeReportFailed
         * @final
         */
        EVT_FINALIZE_REPORT_FAILED: 'finalizeReportFailed',

        /**
         * Fired when a request for report deletion has been sent to the server.
         * @event deleteReportStarted
         * @final
         */
        EVT_DELETE_REPORT_STARTED: 'deleteReportStarted',

        /**
         * Fired when a server response indication a successful report deletion has been received.
         * @event deleteReportSucceeded
         * @final
         */
        EVT_DELETE_REPORT_SUCCEEDED: 'deleteReportSucceeded',

        /**
         * Fired when a server response indicating a failure in deleting a report has been received.
         * @event deleteReportFailed
         * @final
         */
        EVT_DELETE_REPORT_FAILED: 'deleteReportFailed'
    };

    YAHOO.lang.augmentProto(DataSource, YAHOO.util.EventProvider);

    //
    // Utility methods.
    //

    /**
     * @method generateSequenceBlockMarkup
     * Generates the container markup for a sequence block View.
     * Note that data population and wiring of event handlers are not part of this.
     * @param {Number} cnumber The container number. Used as suffix when creating unique ID attributes for HTML elements
     *      within the container and of the container itself.
     * @returns {HTMLElement} The generated markup.
     * @static
     */
    var generateSequenceBlockMarkup = function (cnumber) {
        var rootEl, headerEl, bodyEl, rowEl, el;

        // the container element
        rootEl = document.createElement('div');
        Dom.setAttribute(rootEl, 'id', 'sequence-block-view-' + cnumber);
        Dom.addClass(rootEl, 'entity_container');
        Dom.addClass(rootEl, 'collapsed');
        Dom.addClass(rootEl, 'hidden');

        // header
        headerEl = rootEl.appendChild(document.createElement('div'));
        Dom.addClass(headerEl, 'hd');
        el = headerEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'toggle');
        Dom.setAttribute(el, 'id', 'sequence-block-view-toggle-btn-' + cnumber);
        el = headerEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-title-' + cnumber);
        Dom.addClass(el, 'collapsed_summary_text_div');
        el = headerEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-delete-button-' + cnumber);
        Dom.addClass(el, 'delete_widget');
        Dom.addClass(el, 'icon-cancel');

        // body
        bodyEl = rootEl.appendChild(document.createElement('div'));
        Dom.setAttribute(bodyEl, 'id', 'sequence-block-view-body-' + cnumber);
        Dom.addClass(bodyEl, 'bd');
        Dom.addClass(bodyEl, 'collapsible_container');
        Dom.addClass(bodyEl, 'hidden');
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.description')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-description-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // ..
        // @todo implement the rest
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        Dom.addClass(rowEl, 'sequence-block-children');
        Dom.setAttribute(rowEl, 'id', 'sequence-block-view-children-' + cnumber);
        return rootEl;
    };

    ilios.cim.SequenceBlockViewRegistry = SequenceBlockViewRegistry;
    ilios.cim.DataSource = DataSource;
    ilios.cim.App = App;
}());
