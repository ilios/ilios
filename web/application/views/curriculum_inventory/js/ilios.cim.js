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
     *     "courses" ... (optional) An array of courses, linked-with or linkable-to sequence blocks in this report.
     *     "sequence_blocks" ... (optional) An array of report sequence blocks.
     *     "academic_levels" ... (optional) An array of academic levels available in the given report.
     * @constructor
     * @todo way to much things going on in there for a single function. chunk it up into separate init functions.
     */
    var App = function (config, payload) {

        var i, n, dataSource;

        // set module configuration
        this._config = config;

        this._programs = payload.programs;

        // wire up the "search report" button
        Event.addListener('pick_reports_btn', 'click', function (event) {
            if (! this._reportPickerDialog) { // instantiate on demand
                this._reportPickerDialog = new ilios.cim.widget.ReportPickerDialog('report_picker_dialog');
                this._reportPickerDialog.render();
            }
            this._reportPickerDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        // wire up the "create report" button
        Event.addListener('create_report_btn', 'click', function (event) {
            if (! this._createReportDialog) {
                this._createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog', {}, this._programs);
                this._createReportDialog.render();
            }
            this._createReportDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        //
        // initialize the rest of the application if a report is present in the given payload
        //
        if (payload.hasOwnProperty('report')) {

            // process data
            this.academicLevels = payload.academic_levels;
            this.sequenceBlocks = payload.sequence_blocks;

            this._initCourseModel(payload.courses);
            this._reportModel = new ilios.cim.model.ReportModel(payload.report);

            // set up views and widgets
            this._reportView = new ilios.cim.view.ReportView(this._reportModel, {});
            this._reportView.render();
            this._sequenceBlockTopToolbar = new ilios.cim.widget.SequenceBlockTopToolbar({});
            this._sequenceBlockTopToolbar.render();
            this._sequenceBlockBottomToolbar = new ilios.cim.widget.SequenceBlockBottomToolbar({});
            this._sequenceBlockBottomToolbar.render(! this._reportModel.get('isFinalized'));


            // subscribe "export report" events
            this._reportView.subscribe(this._reportView.EVT_EXPORT_STARTED, function() {
                this.getStatusBar().show('Started Report Export &hellip;', true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_STARTED, function() {
                this.getStatusBar().show('Started Report Download &hellip;', true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_EXPORT_COMPLETED, function () {
                this.getStatusBar().reset();
            }, this, true);

            // subscribe "download report" events
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_COMPLETED, function () {
                this.getStatusBar().reset();
            }, this, true);
            this._reportModel.subscribe(this._reportModel.EVT_UPDATED, function () {
                this.getStatusBar().show('Report updated.', false);
            }, this, true);

            // wire up toolbars
            Event.addListener(this._sequenceBlockTopToolbar.getExpandButton(), 'click', function (event) {
                this.expandAllSequenceBlocks();
            }, {}, this);
            Event.addListener(this._sequenceBlockTopToolbar.getCollapseButton(), 'click', function (event) {
                this.collapseAllSequenceBlocks();
            }, {}, this);

            //
            // if the report got loaded in "draft" mode then wire up the "finalize", "edit" and "delete" buttons
            // in the view, and subscribe the app to the events emitted by it's data source.
            //
            if (! this._reportModel.get('isFinalized')) {

                dataSource = this.getDataSource();

                // wire up "finalize report" button
                Event.addListener(this._reportView.getFinalizeButton(), 'click', function(event) {
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    var args = {};
                    args.model = this._reportModel;
                    args.dataSource = this.getDataSource();
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        args.dataSource.finalizeReport(args.model.get('id'));
                        this.hide(); // hide the calling dialog
                    }, args);
                }, {}, this);
                // subscribe to "finalize report"-events emitted by the data source
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_STARTED, function () {
                    this.show('Finalizing report started &hellip;', true);
                }, this.getStatusBar(), true);
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_SUCCEEDED, function () {
                    // update the report model
                    this.getStatusBar().reset();
                    this._reportModel.set('isFinalized', true);
                    this.disableAllSequenceBlocks(); // disable "draft mode" for all sequence blocks
                    // disable and hide the bottom toolbar
                    this._sequenceBlockBottomToolbar.disableButtons();
                    this._sequenceBlockBottomToolbar.hide();
                }, this, true);
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_FAILED, function () {
                    this.getStatusBar().show('Finalizing report failed.', false);
                }, this, true);

                // wire up the "delete report" button
                Event.addListener(this._reportView.getDeleteButton(), 'click', function (event, args) {
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.delete.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    var args = {};
                    args.model = this._reportModel;
                    args.dataSource = dataSource;
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        args.dataSource.deleteReport(args.model.get('id'));
                        this.hide(); // hide the calling dialog
                    }, args);
                }, {}, this);
                // subscribe to "delete report"-events emitted by the data source
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_STARTED, function () {
                    this.getStatusBar().show('Deleting report &hellip;', true);
                }, this, true);
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_FAILED, function () {
                    this.getStatusBar().show('Failed to delete report.', false);
                }, this, true);
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_SUCCEEDED, function() {
                    this.getStatusBar().show('Successfully deleted report. Reloading page &hellip;', true);
                    // reload the page
                    window.location = window.location.protocol + "//" + window.location.host + window.location.pathname;
                }, this, true);

                // wire up the "edit report" button
                Event.addListener(this._reportView.getEditButton(), 'click', function(event) {
                    if (! this._editReportDialog) {
                        this._editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog', this._reportModel);
                        this._editReportDialog.render();
                    }
                    this._editReportDialog.show();
                    Event.stopEvent(event);
                    return false;
                }, {}, this);
            };

            // show views and widgets
            this._reportView.show();
            this._sequenceBlockBottomToolbar.show();
            this._sequenceBlockTopToolbar.show();

            // deal with sequence block models and views
            for (i = 0, n = payload.sequence_blocks.length; i < n; i++) {
                this.addSequenceBlock(payload.sequence_blocks[i], true);
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
         * The application's sequence block view registry.
         *
         * @property _sequenceBlockViewRegistry
         * @type {ilios.cim.SequenceBlockViewRegistry}
         * @protected
         */
        _sequenceBlockViewRegistry: null,


        /**
         * The application's sequence block model registry.
         *
         * @property _sequenceBlockModelRegistry
         * @type {ilios.cim.SequenceBlockModelRegistry}
         * @protected
         */
        _sequenceBlockModelRegistry: null,


        /**
         * The application-wide course repository.
         *
         * @property _courseRepository
         * @type {ilios.cim.CourseRepository}
         * @protected
         */
        _courseRepository: null,

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
         * A dialog widget for editing the report.
         *
         * @property _editReportDialog
         * @type {ilios.cim.widget.EditReportDialog}
         * @protected
         */
        _editReportDialog: null,

        //
        // init methods
        //

        /**
         * Initialization-method for the application's course model and its container objects.
         * Instantiates course models from the given data and checks them into the course repo.
         *
         * @method _initCourseModel
         * @param {Array} data A list of course data objects.
         * @protected
         */
        _initCourseModel: function (data) {
            var i, n, model, repo;
            repo = this.getCourseRepository();

            for (i = 0, n = data.length; i < n; i++ ) {
                model = new ilios.cim.model.CourseModel(data[i]);
                repo.add(model);
            }
        },

        //
        // API
        //

        /**
         * Retrieves the application's status bar widget.
         *
         * @method getStatusBar
         * @return {ilios.cim.widget.StatusBar} The application's status bar.
         */
        getStatusBar: function () {
            if (! this._statusBar) {
                // lazy instantiation
                this._statusBar = new ilios.cim.widget.StatusBar({});
                this._statusBar.render('status-toolbar'); // render the widget onto the page the first time around.
            }
            return this._statusBar;
        },

        /**
         * Retrieves the application's course repository.
         *
         * @method getCourseRepository
         * @return {ilios.cim.CourseRepository} The application's course repository.
         */
        getCourseRepository: function () {
            if (! this._courseRepository) {
                this._courseRepository = new ilios.cim.CourseRepository();
            }
            return this._courseRepository;
        },

        /**
         * Retrieves the application's data source object.
         *
         * @method getDataSource
         * @return {ilios.cim.DataSource} The application's data source.
         */
        getDataSource: function () {
            if (! this._dataSource) {
                // instantiate data source
                this._dataSource = new ilios.cim.DataSource({
                    finalizeReportUrl: this._config.controllerUrl + 'finalize',
                    deleteReportUrl: this._config.controllerUrl + 'delete',
                    deleteSequenceBlockUrl: this._config.controllerUrl + 'deleteSequenceBlock'
                });
            }
            return this._dataSource;
        },

        /**
         * Retrieves the application's sequence block view registry object.
         *
         * @method getSequenceBlockViewRegistry
         * @return {ilios.cim.SequenceBlockViewRegistry} The application's sequence block view registry.
         */
        getSequenceBlockViewRegistry: function () {
            if (! this._sequenceBlockViewRegistry) {
                this._sequenceBlockViewRegistry = new ilios.cim.SequenceBlockViewRegistry();
            }
            return this._sequenceBlockViewRegistry;
        },

        /**
         * Retrieves the application's sequence block model registry object.
         *
         * @method getSequenceBlockModelRegistry
         * @return {ilios.cim.SequenceBlockModelRegistry} The application's sequence block model registry.
         */
        getSequenceBlockModelRegistry: function () {
            if (! this._sequenceBlockModelRegistry) {
                this._sequenceBlockModelRegistry = new ilios.cim.SequenceBlockModelRegistry();
            }
            return this._sequenceBlockModelRegistry;
        },


        /**
         * Adds a sequence block to the application.
         * This entails instantiating and populating the model from the given data, instantiating and wiring the view,
         * event wiring of the view and registration of model and view with the application's various object containers
         * for further references.
         *
         * @method addSequenceBlock
         * @param {Object} oData A map containing data of the new sequence block.
         * @param {Boolean} silent Don't fire any sequence block related "create" events, and don't show a message in the status bar.
         */
        addSequenceBlock: function (oData, silent) {
            var model, view;

            var finalized = this._reportModel.get('isFinalized');



            //create model and view
            model = this.createSequenceBlockModel(oData);
            view = this.createSequenceBlockView(model);
            view.render(! finalized);

            //
            // wire the view's buttons
            //
            if (! finalized) {
                Event.addListener(view.getDeleteButton(), 'click', this.onSequenceBlockDeleteButtonClick,
                { id: view.getModel().getId() }, this);
            }

            if (! silent) {
                this.getStatusBar().show('Added new sequence block.');
            }

            view.show();
        },

        /**
         * Creates a sequence block model object from a given data transfer object representing a sequence block record.
         *
         * @method createSequenceBlockModel
         * @param {Object} oData The data transfer object.
         * @return {ilios.cim.model.SequenceBlockModel} The created model.
         */
        createSequenceBlockModel: function (oData) {
            var rhett, courseModel;
            // if applicable, check out the course model linked to this sequence block from the repository.
            courseModel = this.getCourseRepository().checkOut(oData.course_id);
            oData.course_model = courseModel;
            // @todo pull the associated academic year model from their respective registries and add them to the model

            // instantiate model
            rhett = new ilios.cim.model.SequenceBlockModel(oData);

            // subscribe the app to the model's "course model change" event
            rhett.subscribe("courseChange", this.onCourseModelChangeInSequenceBlock, this, true);

            // @todo add model to registry
            // @todo get parent model from registry and add this model as a child

            // return the damned thing
            return rhett;
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

            // add sequence block to registry
            this.getSequenceBlockViewRegistry().add(view);

            return view;
        },
        /**
         * Expands all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        expandAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.expand;
            this.getSequenceBlockViewRegistry().walk(fn);
        },

        /**
         * Collapses all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        collapseAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.collapse;
            this.getSequenceBlockViewRegistry().walk(fn);
        },

        /**
         * Disable "draft mode" for all sequence block views.
         *
         * @method disableAllSequenceBlocks
         * @see ilios.cim.view.SequenceBlockView.disableDraftMode
         */
        disableAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.disableDraftMode;
            this.getSequenceBlockViewRegistry().walk(fn);
        },

        /**
         * Event handler function.
         * Subscribe this to each sequence block's "Delete" button click-event.
         *
         * @method onSequenceBlockDeleteButtonClick
         * @param {Event} The click event.
         * @param {Object} args A map of arguments passed on method-invocation. Expected values are:
         *     'id' ... the id of the to-be-deleted sequence block
         */
        onSequenceBlockDeleteButtonClick: function (event, args) {
            var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
            var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
            ilios.alert.inform(continueStr, yesStr, function (event, args) {
                args.dataSource.deleteSequenceBlock(args.id);
                this.hide(); // hide the calling dialog
            }, { id: args.id, dataSource: this.getDataSource()});
            Event.stopEvent(event);
            return false;
        },

        /**
         * Change-event handler function.
         * Subscribe this method to each sequence block's "courseChange" event, so we can
         * capture changes to sequence block/course associations.
         * Check-in previously assigned courses.
         *
         * The invocation scope of this method should be the application.
         *
         * @method onCourseModelChangeInSequenceBlock
         * @param {Object} args Value object containing the old value ("prevValue") and new value ("newValue).
         */
        onCourseModelChangeInSequenceBlock: function (args) {
            var repo = this.getCourseRepository();
            if (args.prevValue) {
                repo = this.getCourseRepository();
                repo.checkIn(args.prevValue.getId());
            }
        }
    };


    /**
     * Course repository.
     * Allows for state management of courses within the repository application.
     * A course can either be "checked in" (available for assignment to a sequence block),
     * or "checked out" (unavailable for assignment)
     * Courses will be checked-in/out from the repo by its owning application as part of sequence block lifecycle
     * management (block-creation/deletion/update).
     *
     * @namespace cim
     * @class CourseRepository
     * @constructor
     * @see ilios.cim.model.CourseModel
     */
    var CourseRepository = function () {};

    CourseRepository.prototype = {

        /**
         * The container object for checked-out/unavailable courses.
         *
         * @property _unavailable
         * @type {Object}
         * @protected
         */
        _unavailable: {},

        /**
         * The container object for checked-in/available courses.
         *
         * @property _unavailable
         * @type {Object}
         * @protected
         */
        _available: {},


        /**
         * Checks if the repo contains a given course.
         * @param {Number} The course id.
         * @returns {Boolean} TRUE if the course exists in the repo, otherwise FALSE.
         */
        exists: function (id) {
            return this._available.hasOwnProperty(id) || this._unavailable.hasOwnProperty(id);
        },

        /**
         * Adds a course to the repo.
         * Newly checked-in courses are automatically available.
         *
         * @method add
         * @param {ilios.cim.model.CourseModel} course
         */
        add: function (course) {
            this._available[course.getId()] = course;
        },

        /**
         * Checks a given course out of the repo, and flags it as unavailable.
         * If the course has already been checked-out, then it is simply returned without a status change.
         *
         * @method checkOut
         * @param {Number} id The course id.
         * @return {ilios.cim.model.CourseModel|null} the checked-out course, or NULL if it doesn't exist.
         */
        checkOut: function (id) {
            var course;
            if (! this.exists(id)) {
                return null;
            }
            if (this._available.hasOwnProperty(id)) {
                course = this._available[id];
                delete this._available[id];
                this._unavailable[id] = course;
            }
            return this._unavailable[id];
        },

        /**
         * Checks a given course into the repo, and flags it as available.
         * If the course has already been checked-in, then it is simply returned without a status change.
         *
         * @method checkIn
         * @param {Number} id The course id.
         * @return {ilios.cim.model.CourseModel|null} the checked-in course, or NULL if it doesn't exist.
         */
        checkIn: function (id) {
            var course;
            if (! this.exists(id)) {
                return null;
            }
            if (this._unavailable.hasOwnProperty(id)) {
                course = this._unavailable[id];
                delete this._unavailable[id];
                this._available[id] = course;
            }
            return this._available[id];
        },

        /**
         * Retrieves a sorted list of available courses.
         *
         * @method listAvailable
         * @returns {Array} A list of sorted courses.
         */
        listAvailable : function () {
            var i, rhett;
            rhett  = [];
            for (i in this._available) {
                if (this._available.hasOwnProperty(i)) {
                    rhett.push(this._available[i]);
                }
            }
            rhett.sort(function (a, b) {
                return a.get('title').localeCompare(b.get('title'));
            });

            return rhett;
        }
    };

    /**
     * Storage container of sequence block models within the application
     *
     * @namespace cim
     * @class SequenceBlockModelRegistry
     * @constructor
     */
    var SequenceBlockModelRegistry = function () {};

    SequenceBlockModelRegistry.prototype = {
        // @todo implement
    };

    /**
     * Storage container of sequence block views within the application.
     *
     * @namespace cim
     * @class SequenceBlockViewRegistry
     * @constructor
     */
    var SequenceBlockViewRegistry = function () {};

    SequenceBlockViewRegistry.prototype = {
        /**
         * The internal registry object.
         *
         * @var _registry
         * @type {Object}
         * @protected
         */
        _registry: {},
        /**
         * Adds a given view to the registry.
         *
         * @method add
         * @param {ilios.cim.view.SequenceBlockView} view
         * @return {ilios.cim.view.SequenceBlockView} The added view.
         * @throws {Error} If a view has already been registered under the same cnumber as key.
         */
        add: function (view) {
            var cnumber = view.getCnumber();
            if (this.exists(cnumber)) {
                throw new Error('A view with this cnumber has already been added to the registry. cnumber: ' + cnumber);
            }
            this._registry[cnumber] = view;
            return view;
        },
        /**
         *
         * @method remove
         * @param {Number} cnumber
         * @return {ilios.cim.view.SequenceBlockView} The removed view object.
         * @throws {Error}
         */
        remove: function (cnumber) {
            var view = this.get(cnumber);
            delete this._registry[cnumber];
            return view;
        },

        /**
         * @method list
         * @return {Array}
         */
        list: function () {
            var i, rhett;
            rhett = [];
            for (i in this._registry) {
                if (this._registry.hasOwnProperty(i)) {
                    rhett.push(this._registry[i]);
                }
            }
            return rhett;
        },

        /**
         * @method exists
         * @param {Number} cnumber
         * @return {Boolean}
         */
        exists: function (cnumber) {
            return this._registry.hasOwnProperty(cnumber);
        },

        /**
         * @method get
         * @param {Number} cnumber
         * @return {ilios.cim.view.SequenceBlockView}
         * @throws {Error}
         */
        get: function (cnumber) {
            if (! this.exists(cnumber)) {
                throw new Error('A view with this cnumber does not exist in the registry. cnumber: ' + cnumber);
            }
            this._registry[cnumber];
        },

        /**
         * Applies a given function with given arguments to each view in the registry.
         *
         * @method walk
         * @param {Function} fn
         * @param {Array} [args]
         */
        walk: function (fn, args) {
            var i, view;
            args = args || [];

            for (i in this._registry) {
                if (this._registry.hasOwnProperty(i)) {
                    view = this._registry[i];
                    fn.apply(view, args);
                }
            }
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
     *     'deleteReportUrl' ... The URL to the server-side "delete report" controller action.
     *     'finalizeReportUrl' ... The URL to the server-side "finalize report" controller action.
     *     'deleteSequenceBlockUrl' ... The URL to te  server-side "delete sequence block" controller action.
     */
    var DataSource = function (config) {

        this._config = config;

        // create custom events provided by this object
        this.createEvent(this.EVT_FINALIZE_REPORT_STARTED);
        this.createEvent(this.EVT_FINALIZE_REPORT_SUCCEEDED);
        this.createEvent(this.EVT_FINALIZE_REPORT_FAILED);
        this.createEvent(this.EVT_DELETE_REPORT_STARTED);
        this.createEvent(this.EVT_DELETE_REPORT_SUCCEEDED);
        this.createEvent(this.EVT_DELETE_REPORT_FAILED);
    };

    DataSource.prototype = {

        /**
         * The data source configuration.
         * @property _config
         * @type {Object}
         */
        _config: null,
        /**
         * Makes an XHR call to the backend to request a given report to be finalized.
         * Fires the "finalizeReportStarted" event on transaction start, and, depending on its outcome,
         * either the "finalizeReportSucceeded" or the "finalizeReportFailed" event on completion.
         *
         * @method finalizeReport
         * @param {Number} id The report id.
         */
        finalizeReport: function (id) {
            var url = this._config.finalizeReportUrl;
            var postData = 'report_id=' + id;
            var callback = {
                success: function (o) {
                    var response, msg;
                    try {
                        response = YAHOO.lang.JSON.parse(o.responseText);
                    } catch (e) {
                        this.fireEvent(this.EVT_FINALIZE_REPORT_FAILED);
                        ilios.global.defaultAJAXFailureHandler(null, e);
                        return;
                    }
                    if (response.error) {
                        this.fireEvent(this.EVT_FINALIZE_REPORT_FAILED);
                        msg = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.error.general');
                        ilios.alert.alert(msg + ": " + response.error);
                        return;
                    }
                    this.fireEvent(this.EVT_FINALIZE_REPORT_SUCCEEDED);
                },
                failure: function (o) {
                    this.fireEvent(this.EVT_FINALIZE_REPORT_FAILED);
                    ilios.global.defaultAJAXFailureHandler(o);
                },
                scope: this
            };
            this.fireEvent(this.EVT_FINALIZE_REPORT_STARTED);
            YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
        },

        /**
         * Makes an XHR call to the backend to request a given report to be deleted.
         * Fires the "deleteReportStarted" event on transaction start, and, depending on its outcome,
         * either the "deleteReportSucceeded" or the "deleteReportFailed" event on completion.
         *
         * @method deleteReport
         * @param {Number} id The report id.
         */
        deleteReport: function (id) {
            var url = this._config.deleteReportUrl;
            var postData = 'report_id=' + id;
            var callback = {
                success: function (o) {
                    var response, msg;
                    try {
                        response = YAHOO.lang.JSON.parse(o.responseText);
                    } catch (e) {
                        this.fireEvent(this.EVT_DELETE_REPORT_FAILED);
                        ilios.global.defaultAJAXFailureHandler(null, e);
                        return;
                    }
                    if (response.error) {
                        this.fireEvent(this.EVT_DELETE_REPORT_FAILED);
                        msg = ilios_i18nVendor.getI18NString('curriculum_inventory.delete.error.general');
                        ilios.alert.alert(msg + ": " + response.error);
                        return;
                    }
                    this.fireEvent(this.EVT_DELETE_REPORT_SUCCEEDED);
                },
                failure: function (o) {
                    this.fireEvent(this.EVT_DELETE_REPORT_FAILED);
                    ilios.global.defaultAJAXFailureHandler(o);
                },
                scope: this
            };

            this.fireEvent(this.EVT_DELETE_REPORT_STARTED);
            YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
        },

        /**
         * Makes an XHR call to the backend to request a given sequence block to be deleted.
         * Fires the "deleteSequenceBlockStarted" event on transaction start, and, depending on its outcome,
         * either the "deleteSequenceBlockSucceeded" or the "deleteSequenceBlockFailed" event on completion.
         *
         * @method deleteSequenceBlock
         * @param {Number} id The report id.
         */
        deleteSequenceBlock: function (id) {
            var url = this._config.deleteSequenceBlockUrl;
            var postData = 'sequence_block_id=' + id;
            var callback = {
                success: function (o) {
                    var response, msg;
                    try {
                        response = YAHOO.lang.JSON.parse(o.responseText);
                    } catch (e) {
                        this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_FAILED, {id: o.argument.id});
                        ilios.global.defaultAJAXFailureHandler(null, e);
                        return;
                    }
                    if (response.error) {
                        this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_FAILED, {id: o.argument.id});
                        msg = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.delete.error.general');
                        ilios.alert.alert(msg + ": " + response.error);
                        return;
                    }
                    this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_SUCCEEDED, {id: o.argument.id});
                },
                failure: function (o) {
                    this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_FAILED, {id: o.argument.id});
                    ilios.global.defaultAJAXFailureHandler(o);
                },
                scope: this,
                argument: { id: id }
            };

            this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_STARTED);
            YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
            YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
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
        EVT_DELETE_REPORT_FAILED: 'deleteReportFailed',

        /**
         * Fired when the a request for sequence block deletion has been sent to the server.
         * @event deleteSequenceBlockStarted
         * @final
         */
        EVT_DELETE_SEQUENCE_BLOCK_STARTED: 'deleteSequenceBlockStarted',

        /**
         * Fired when a server response indicating a failure in deleting a sequence block has been received.
         * @event deleteSequenceBlockFailed
         * @param {Number} id The sequence block id.
         * @final
         */
        EVT_DELETE_SEQUENCE_BLOCK_FAILED: 'deleteSequenceBlockFailed',

        /**
         * Fired when a server response indication a successful sequence block deletion has been received.
         * @event deleteSequenceBlockCompleted
         * @param {Number} id The sequence block id.
         * @final
         */
        EVT_DELETE_SEQUENCE_BLOCK_SUCCEEDED: 'deleteSequenceBlockCompleted'
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
        var rootEl, headerEl, bodyEl, rowEl, el, ulEl, liEl;

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
        Dom.setAttribute(el, 'id', 'sequence-block-view-delete-btn-' + cnumber);
        Dom.addClass(el, 'delete_widget');
        Dom.addClass(el, 'icon-cancel');
        Dom.addClass(el, 'hidden');
        // body
        bodyEl = rootEl.appendChild(document.createElement('div'));
        Dom.setAttribute(bodyEl, 'id', 'sequence-block-view-body-' + cnumber);
        Dom.addClass(bodyEl, 'bd');
        Dom.addClass(bodyEl, 'collapsible_container');
        Dom.addClass(bodyEl, 'hidden');
        // top-row with buttons
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.setAttribute(rowEl, 'id', 'sequence-block-view-top-buttons-row-' + cnumber);
        Dom.addClass(rowEl, 'hidden');
        Dom.addClass(rowEl, 'row');
        ulEl = rowEl.appendChild(document.createElement('ul'));
        Dom.addClass(ulEl, 'buttons');
        Dom.addClass(ulEl, 'right');
        liEl = ulEl.appendChild(document.createElement('li'));
        el = liEl.appendChild(document.createElement('button'));
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.add')));
        Dom.setAttribute(el, 'id', 'sequence-block-view-add-btn-' + cnumber);
        Dom.addClass(el, 'small');
        Dom.addClass(el, 'radius');
        Dom.addClass(el, 'button');
        Dom.addClass(el, 'hidden');
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
        // bottom-row with buttons
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.setAttribute(rowEl, 'id', 'sequence-block-view-bottom-buttons-row-' + cnumber);
        Dom.addClass(rowEl, 'buttons');
        Dom.addClass(rowEl, 'bottom');
        Dom.addClass(rowEl, 'hidden');
        el = rowEl.appendChild(document.createElement('button'));
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.edit')));
        Dom.setAttribute(el, 'id', 'sequence-block-view-edit-btn-' + cnumber);
        Dom.addClass(el, 'medium');
        Dom.addClass(el, 'radius');
        Dom.addClass(el, 'button');
        Dom.addClass(el, 'hidden');
        return rootEl;
    };

    ilios.cim.SequenceBlockViewRegistry = SequenceBlockViewRegistry;
    ilios.cim.SequenceBlockModelRegistry = SequenceBlockModelRegistry;
    ilios.cim.CourseRepository = CourseRepository;
    ilios.cim.DataSource = DataSource;
    ilios.cim.App = App;
}());
