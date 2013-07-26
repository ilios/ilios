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
        Dom = YAHOO.util.Dom;
    /**
     * Creates a client-side application.
     * It's the top level object responsible for instantiating a self-contained application environment
     * and for providing routing and high-level workflow management functionality.
     *
     * @namespace ilios.cim
     * @class App
     * @param {Object} config the application configuration.
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
        this.config = Lang.isObject(config) ? config : {};

        this.statusBar = new ilios.cim.widget.StatusBar({});
        this.statusBar.render('status-toolbar');

        this.programs = payload.programs;

        // wire dialogs to buttons
        Event.addListener('pick_reports_btn', 'click', function (event) {
            if (! this.reportPickerDialog) { // instantiate on demand
                this.reportPickerDialog = new ilios.cim.widget.ReportPickerDialog('report_picker_dialog');
            }
            this.reportPickerDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        Event.addListener('create_report_btn', 'click', function (event) {
            if (! this.createReportDialog) {
                this.createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog', {}, this.programs);
            }
            this.createReportDialog.show();
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
            this.reportModel = new ilios.cim.model.ReportModel(payload.report);

            // set up views and widgets

            this.reportView = new ilios.cim.view.ReportView(this.reportModel, {
                finalizeUrl: this.config.controllerUrl + 'finalize',
                deleteUrl: this.config.controllerUrl + 'delete'
            });
            this.reportView.subscribe('exportStarted', function() {
                this.show('Started Report Export &hellip;', true);
            }, this.statusBar, true);
            this.reportView.subscribe('downloadStarted', function() {
                this.show('Started Report Download &hellip;', true);
            }, this.statusBar, true);
            this.reportView.subscribe('exportFinished', function () {
                this.reset();
            }, this.statusBar, true);
            this.reportView.subscribe('downloadFinished', function () {
                this.reset();
            }, this.statusBar, true);
            this.reportModel.subscribe('afterUpdate', function () {
                this.show('Report updated.', false);
            }, this.statusBar, true);
            this.reportView.subscribe('finalizeStarted', function () {
                this.show('Finalizing report started &hellip;', true);
            }, this.statusBar, true);
            this.reportView.subscribe('finalizeSucceeded', function () {
                this.reset();
            }, this.statusBar, true);
            this.reportView.subscribe('finalizeFailed', function () {
                this.show('Finalizing report failed.', false);
            }, this.statusBar, true);
            this.reportView.subscribe('deleteStarted', function () {
                this.show('Deleting report &hellip;', true);
            }, this.statusBar, true);
            this.reportView.subscribe('deleteFailed', function () {
                this.show('Failed to delete report.', false);
            }, this.statusBar, true);
            this.reportView.subscribe('deleteSucceeded', function() {
                this.show('Successfully deleted report. Reloading page &hellip;', true);
                // reload the page
                window.location = window.location.protocol + "//" + window.location.host + window.location.pathname;

            }, this.statusBar, true);

            this.reportView.render();

            this.sequenceBlockTopToolbar = new ilios.cim.widget.SequenceBlockTopToolbar({});
            this.sequenceBlockTopToolbar.render();

            this.sequenceBlockBottomToolbar = new ilios.cim.widget.SequenceBlockBottomToolbar({});
            this.sequenceBlockBottomToolbar.render(! this.reportModel.get('isFinalized'));

            // more wiring of event handlers

            Event.addListener(this.sequenceBlockTopToolbar.get('expandBtnEl'), 'click', function (event) {
                this.expandAllSequenceBlocks();
            }, {}, this);

            Event.addListener(this.sequenceBlockTopToolbar.get('collapseBtnEl'), 'click', function (event) {
                this.collapseAllSequenceBlocks();
            }, {}, this);


            this.reportView.subscribe('finalizeSucceeded', function () {
                this.disableButtons();
            }, this.sequenceBlockBottomToolbar, true);

            // show views and widgets
            this.reportView.show();
            this.sequenceBlockBottomToolbar.show();
            this.sequenceBlockTopToolbar.show();

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
         * Registry of sequence blocks instances.
         * @var sequenceBlockViewRegistry
         * @type {Object}
         */
        sequenceBlockViewRegistry: {},

        /**
         * The application's status-message bar.
         * @var statusBar
         * @type {ilios.cim.widget.StatusBar}
         */
        statusBar: null,

        /**
         * A map of programs, keyed off by their program id.
         * @param programs
         * @type {Object}
         */
        programs: {},

        /**
         * A dialog widget for selecting and loading existing reports onto the page.
         * @param reportPickerDialog
         * @type {ilios.cim.widget.ReportPickerDialog}
         */
        reportPickerDialog: null,

        /**
         * A dialog widget for creating a new report.
         * @param createReportDialog
         * @type {ilios.cim.widget.CreateReportDialog}
         */
        createReportDialog: null,
        /**
         * @method getConfig
         * Returns the application configuration.
         * @return {Object] The configuration object.
        */
        getConfig: function () {
            return this.config;
        },

        /**
         * @method createSequenceBlockModel
         * Creates a sequence block model object from a given data transfer object representing a sequence block record.
         * @param {Object} oData The data transfer object.
         * @return {ilios.cim.model.SequenceBlockModel} The created model.
         */
        createSequenceBlockModel: function (oData) {
            // @todo add model to registry
            return new ilios.cim.model.SequenceBlockModel(oData);

        },
        /**
         * @method createSequenceBlockView
         * Creates and returns an sequence block view for a given sequence block model.
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

            this.sequenceBlockViewRegistry[id] = view; // add the view to the registry so we can pull it up later

            return view;
        },
        /**
         * @method expandAllSequenceBlock
         * Expands all sequence block views.
         */
        expandAllSequenceBlocks: function () {
            var cnumber, view, registry;
            registry = this.sequenceBlockViewRegistry;
            for (cnumber in registry) {
                if (registry.hasOwnProperty(cnumber)) {
                    view = registry[cnumber];
                    view.expand();
                }
            }
        },

        /**
         * @method expandAllSequenceBlock
         * Collapses all sequence block views.
         */
        collapseAllSequenceBlocks: function () {
            var cnumber, view, registry;
            registry = this.sequenceBlockViewRegistry;
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
     * @namespace ilios.cim
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
     * @namespace ilios.cim
     * @class App
     * @uses YAHOO.util.EventProvider
     * @constructor
     */
    var DataSource = function () {

        // create custom events provided by this object
        this.createEvent(DataSource.EVT_DOWNLOAD_STARTED);
        this.createEvent(DataSource.EVT_DOWNLOAD_FINISHED);
        this.createEvent(DataSource.EVT_EXPORT_STARTED);
        this.createEvent(DataSource.EVT_EXPORT_FINISHED);

    };

    DataSource.prototype = {
        /**
         * Fired when the report download request is sent to the server.
         * @event downloadStarted
         * @final
         */
        EVT_DOWNLOAD_STARTED: 'downloadStarted',

        /**
         * Fired when the report download response has been received from the server.
         * @event downloadFinished
         * @final
         */
        EVT_DOWNLOAD_FINISHED: 'downloadFinished',

        /**
         * Fired when the report download request is sent to the server.
         * @event exportStarted
         * @final
         */
        EVT_EXPORT_STARTED: 'exportStarted',

        /**
         * Fired when the report download response has been received from the server.
         * @event exportFinished
         * @final
         */
        EVT_EXPORT_FINISHED: 'exportFinished'
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
