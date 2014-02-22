/**
 * Curriculum inventory management (cim) application module components.
 *
 * Defines the following namespaces:
 *     ilios.cim
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     application/views/scripts/ilios_alert.js
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
     * @namespace cim
     * @class App
     * @param {Object} config the application configuration. Expect the following attributes to be present:
     *     @param {String} config.controllerUrl The URL to the server-side "curriculum inventory manager" controller.
     *     @param {String} config.programControllerUrl The URL to the server-side "program manager" controller.
     * @param {Object} payload The initial page payload. It may have these data points as properties:
     *     @param {Object} payload.programs An object holding the programs available for reporting on.
     *     @param {Object} [payload.report] An object representing the currently selected report.
     *     @param {Array} [payload.courses] An array of courses, linked-with or linkable-to sequence blocks in this report.
     *     @param {Array} [payload.sequence_blocks] An array of report sequence blocks.
     *     @param {Array} [payload.academic_levels] An array of academic levels available in the given report.
     * @constructor
     * @todo way to much things going on in there for a single function. chunk it up into separate init functions.
     */
    var App = function (config, payload) {

        var i, n, dataSource;

        // set module configuration
        this._config = config;

        this._initPrograms(payload.programs);

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
                this._createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog', {},
                    this.getPrograms());
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
            this._initAcademicLevels(payload.academic_levels);
            this._initCourses(payload.courses);
            this._initReport(payload.report);
            this._sequenceBlockTopToolbar = new ilios.cim.widget.SequenceBlockTopToolbar({});
            this._sequenceBlockTopToolbar.render();
            this._sequenceBlockBottomToolbar = new ilios.cim.widget.SequenceBlockBottomToolbar({});
            this._sequenceBlockBottomToolbar.render(! this._reportModel.get('isFinalized'));


            // subscribe "export report" events
            this._reportView.subscribe(this._reportView.EVT_EXPORT_STARTED, function() {
                this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.export.status.in_progress'), true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_STARTED, function() {
                this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.download.status.in_progress'), true);
            }, this, true);
            this._reportView.subscribe(this._reportView.EVT_EXPORT_COMPLETED, function () {
                this.getStatusBar().reset();
            }, this, true);

            // subscribe "download report" events
            this._reportView.subscribe(this._reportView.EVT_DOWNLOAD_COMPLETED, function () {
                this.getStatusBar().reset();
            }, this, true);
            this._reportModel.subscribe(this._reportModel.EVT_UPDATED, function () {
                this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.update.status.success'), false);
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
                Event.addListener(this._reportView.getFinalizeButton(), 'click', function() {
                    var model = this._reportModel;
                    var dataSource = this.getDataSource();
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        args.dataSource.finalizeReport(args.model.get('id'));
                        this.hide(); // hide the calling dialog
                    }, { model: model, dataSource: dataSource });
                }, {}, this);
                // subscribe to "finalize report"-events emitted by the data source
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_STARTED, function () {
                    this.show(ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.status.in_progress'), true);
                }, this.getStatusBar(), true);
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_SUCCEEDED, function () {
                    // update the report model
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.status.success'));
                    this._reportModel.set('isFinalized', true);
                    this.disableAllSequenceBlocks(); // disable "draft mode" for all sequence blocks
                    // disable and hide the bottom toolbar
                    this._sequenceBlockBottomToolbar.disableButtons();
                    this._sequenceBlockBottomToolbar.hide();
                }, this, true);
                dataSource.subscribe(dataSource.EVT_FINALIZE_REPORT_FAILED, function () {
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.error.general'), false);
                }, this, true);

                // wire up the "delete report" button
                Event.addListener(this._reportView.getDeleteButton(), 'click', function (event, args) {
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.delete.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    args = {};
                    args.model = this._reportModel;
                    args.dataSource = dataSource;
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        args.dataSource.deleteReport(args.model.get('id'));
                        this.hide(); // hide the calling dialog
                    }, args);
                }, {}, this);
                // subscribe to "delete report"-events emitted by the data source
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_STARTED, function () {
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.delete.status.in_progress'), true);
                }, this, true);
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_FAILED, function () {
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.delete.error.general'));
                }, this, true);
                dataSource.subscribe(dataSource.EVT_DELETE_REPORT_SUCCEEDED, function() {
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.delete.status.success'), true);
                    // reload the page
                    window.location = window.location.protocol + "//" + window.location.host + window.location.pathname;
                }, this, true);

                dataSource.subscribe(dataSource.EVT_DELETE_SEQUENCE_BLOCK_SUCCEEDED, function (args) {
                    var i;
                    var map = this.getSequenceBlockModelMap();
                    var viewMap = this.getSequenceBlockViewMap();
                    var model = map.get(args.id);
                    var ids = model.getIds();
                    for (i in ids) {
                        if (ids.hasOwnProperty(i)) {
                            map.remove(i);
                            viewMap.remove(i);
                        }
                    }
                    model.delete();
                    this._updateBlockOrderInSequence(args.updated_siblings_order);
                    this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.delete.status.success'));
                }, this, true);

                // wire up the "edit report" button
                Event.addListener(this._reportView.getEditButton(), 'click', function(event) {
                    if (! this._editReportDialog) {
                        this._editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog',
                            this._reportModel, {});
                        this._editReportDialog.render();
                    }
                    this._editReportDialog.show();
                    Event.stopEvent(event);
                    return false;
                }, {}, this);

                // wire up "add sequence block" button in the bottom toolbar
                Event.addListener(this._sequenceBlockBottomToolbar.getAddButton(), 'click',
                    this.onSequenceBlockAddButtonClick, { report_id: this._reportModel.getId(), parent: null }, this);
            }

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
         * The application's sequence block view map.
         *
         * @property _sequenceBlockViewMap
         * @type {ilios.cim.SequenceBlockViewMap}
         * @protected
         */
        _sequenceBlockViewMap: null,


        /**
         * The application's sequence block model map.
         *
         * @property _sequenceBlockModelMap
         * @type {ilios.cim.model.SequenceBlockModelMap}
         * @protected
         */
        _sequenceBlockModelMap: null,


        /**
         * The application-wide course repository.
         *
         * @property _courseRepository
         * @type {ilios.cim.model.CourseRepository}
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
        _programs: null,

        /**
         * A map of academic levels available in the report, keyed off by their level id.
         *
         * @property _academicLevels
         * @type {Object}
         * @protected
         */
        _academicLevels: null,

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
         * A dialog widget for creating a new sequence block.
         *
         * @property _createSequenceBlockDialog
         * @type {ilios.cim.widget.CreateSequenceBlockDialog}
         * @protected
         */
        _createSequenceBlockDialog: null,

        /**
         * A dialog widget for editing a sequence block.
         *
         * @property _editSequenceBlockDialog
         * @type {ilios.cim.widget.EditSequenceBlockDialog}
         * @protected
         */
        _editSequenceBlockDialog: null,


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
         * Initializes the programs infrastructure in the application.
         * Stores the given map of program data in an application property for later reference.
         *
         * @method _initPrograms
         * @param {Object} data A map of program data objects, each one representing a program record, keyed off by program id.
         *
         * @protected
         */
        _initPrograms: function (data) {
            data = Lang.isObject(data) ? data : {};
            this._programs = data;
        },

        /**
         * Initializes the academic levels infrastructure in the application.
         * Instantiates model objects from the given data and stores them in a container object for later reference.
         *
         * @method _initAcademicLevels
         * @param {Array} data A list of data objects, each one representing an academic level record.
         * @protected
         */
        _initAcademicLevels: function (data) {
            var i, n, model, map;
            data = Lang.isArray(data) ? data : [];

            map = {};

            for (i = 0, n = data.length; i < n; i++) {
                model = new ilios.cim.model.AcademicLevelModel(data[i]);
                map[model.get('id')] = model;
            }
            this._academicLevels = map;
        },

        /**
         * Initializes the course infrastructure in the application.
         * Instantiates course models from the given data and checks them into the course repo.
         *
         * @method _initCourses
         * @param {Array} data A list of course data objects.
         * @protected
         */
        _initCourses: function (data) {
            var i, n, model, repo;
            data = Lang.isArray(data) ? data : [];
            repo = this.getCourseRepository();

            for (i = 0, n = data.length; i < n; i++ ) {
                model = new ilios.cim.model.CourseModel(data[i]);
                repo.add(model);
            }
        },

        /**
         * Initialization method.
         * Instantiates the application's report model and creates/renders the report view.
         *
         * @method _initReport
         * @param {Object} data An object containing the report data.
         * @protected
         */
        _initReport: function (data) {
            this._reportModel = new ilios.cim.model.ReportModel(data);

            // set up views and widgets
            this._reportView = new ilios.cim.view.ReportView(this._reportModel);
            this._reportView.render();
        },

        //
        // public API
        //

        /**
         * Retrieves the application's currently loaded course.
         *
         * @method getReportModel
         * @return {ilios.cim.model.ReportModel|null}
         */
        getReportModel: function () {
            return this._reportModel;
        },

        /**
         * Retrieves the application's view for the currently loaded course.
         *
         * @method getReportView
         * @return {ilios.cim.view.ReportView|null}
         */
        getReportView: function () {
            return this._reportView;
        },

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
         * @return {ilios.cim.model.CourseRepository} The application's course repository.
         */
        getCourseRepository: function () {
            if (! this._courseRepository) {
                this._courseRepository = new ilios.cim.model.CourseRepository();
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
         * Retrieves the application's sequence block view map.
         *
         * @method getSequenceBlockViewMap
         * @return {ilios.cim.SequenceBlockViewMap} The application's sequence block view map.
         */
        getSequenceBlockViewMap: function () {
            if (! this._sequenceBlockViewMap) {
                this._sequenceBlockViewMap = new ilios.cim.SequenceBlockViewMap();
            }
            return this._sequenceBlockViewMap;
        },

        /**
         * Retrieves the application's sequence block model map.
         *
         * @method getSequenceBlockModelMap
         * @return {ilios.cim.model.SequenceBlockModelMap} The application's sequence block model map.
         */
        getSequenceBlockModelMap: function () {
            if (! this._sequenceBlockModelMap) {
                this._sequenceBlockModelMap = new ilios.cim.model.SequenceBlockModelMap();
            }
            return this._sequenceBlockModelMap;
        },

        /**
         * Returns the application's "create a new sequence block" dialog.
         *
         * @method getCreateSequenceBlockDialog
         * @return {ilios.cim.widget.CreateSequenceBlockDialog} The dialog instance.
         */
        getCreateSequenceBlockDialog: function () {
            if (! this._createSequenceBlockDialog) {
                this._createSequenceBlockDialog
                    = new ilios.cim.widget.CreateSequenceBlockDialog('create-sequence-block-dialog', this.getCourseRepository(), {});
                this._createSequenceBlockDialog.render();
                // wire the dialog's success/failure events up to the application
                this._createSequenceBlockDialog.sequenceBlockCreationSucceededEvent.subscribe(function (type, args, me) {
                    var data = args[0].data;
                    var map = args[0].updated_siblings_order;
                    me._updateBlockOrderInSequence(map);
                    me.addSequenceBlock(data, false);
                    me.getStatusBar().show(
                        ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.create.status.success'));
                }, this);
            }
            return this._createSequenceBlockDialog;
        },

        /**
         * Returns the application's "edit a new sequence block" dialog.
         *
         * @method getEditSequenceBlockDialog
         * @return {ilios.cim.widget.EditSequenceBlockDialog} The dialog instance.
         */
        getEditSequenceBlockDialog: function () {
            if (! this._editSequenceBlockDialog) {
                this._editSequenceBlockDialog
                    = new ilios.cim.widget.EditSequenceBlockDialog('edit-sequence-block-dialog', this.getCourseRepository(), {});
                this._editSequenceBlockDialog.render();
                // wire the dialog's success/failure events up to the application
                this._editSequenceBlockDialog.sequenceBlockUpdateSucceededEvent.subscribe(function (type, args, me) {
                    var data = args[0].data;
                    me._updateBlockOrderInSequence(args[0].updated_children_order);
                    me._updateBlockOrderInSequence(args[0].updated_siblings_order);
                    me.updateSequenceBlock(data);
                    me.getStatusBar().show(
                        ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.update.status.success'));
                }, this);
            }
            return this._editSequenceBlockDialog;
        },

        /**
         * Retrieves a map of programs that can be reported on in this application.
         * @method getPrograms
         * @return {Object}
         */
        getPrograms: function () {
            return this._programs;
        },

        /**
         * Retrieves a map of academic levels that can be assigned in sequence blocks.
         * @method getAcademicLevels
         * @return {Object}
         */
        getAcademicLevels: function () {
            return this._academicLevels;
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
         * @throws {Error}
         */
        addSequenceBlock: function (oData, silent) {
            var i, n, model, view, parent, parentView;

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
                { id: model.getId() }, this);
                Event.addListener(view.getAddButton(), 'click', this.onSequenceBlockAddButtonClick,
                { report_id: model.get('reportId'), parent: model }, this);
                Event.addListener(view.getEditButton(), 'click', this.onSequenceBlockEditButtonClick,
                    { block: model }, this);
            }

            if (oData.children) {
                for (i = 0, n = oData.children.length; i < n; i++) {
                    this.addSequenceBlock(oData.children[i], silent);
                }
            }

            if (! silent) {
                parent = model.get('parent');
                if (! model.get('parent')) {
                    this._sortTopLevelSequenceBlocks();
                } else {
                    parentView = this.getSequenceBlockViewMap().get(parent.getId());
                    parentView.sortChildViews();
                }
            }

            view.show();

            if (! silent) {
                view.expand();
                view.get('anchorEl').focus();
                this.getStatusBar().show(ilios_i18nVendor.getI18NString('curriculum_inventory.create.status.success'));
            }
        },

        /**
         * Updates a sequence block with the given data.
         *
         * @method updateSequenceBlock
         * @param {Object} oData A map containing data of the new sequence block.
         * @throws {Error}
         */
        updateSequenceBlock: function (oData) {
            var course, parent, view, parentView;
            var block = this.getSequenceBlockModelMap().get(oData.sequence_block_id);
            var levels = this.getAcademicLevels();
            oData.academic_level_model = levels[oData.academic_level_id];
            if (oData.course_id) {
                course = block.get('course');
                if (! course  || (course.getId() != oData.course_id)) {
                    oData.course_model = this.getCourseRepository().checkOut(oData.course_id);
                }
            } else {
                oData.course_model = null;
            }

            block.update(oData);

            view = this.getSequenceBlockViewMap().get(block.getId());
            view.sortChildViews();

            parent = block.get('parent');
            if (! block.get('parent')) {
                this._sortTopLevelSequenceBlocks();
            } else {
                parentView = this.getSequenceBlockViewMap().get(parent.getId());
                parentView.sortChildViews();
            }
            view.get('anchorEl').focus();
        },

        /**
         * Creates a sequence block model object from a given data transfer object representing a sequence block record.
         *
         * @method createSequenceBlockModel
         * @param {Object} oData The data transfer object.
         * @return {ilios.cim.model.SequenceBlockModel} The created model.
         * @throws {Error}
         */
        createSequenceBlockModel: function (oData) {
            var rhett, courseModel, level, levels, parentId, parent, map;

            map = this.getSequenceBlockModelMap();

            // if applicable, check out the course model linked to this sequence block from the repository.
            courseModel = null;
            if (oData.course_id) {
                courseModel = this.getCourseRepository().checkOut(oData.course_id);
            }
            oData.course_model = courseModel;

            levels = this.getAcademicLevels();
            level = levels[oData.academic_level_id];
            if (! level) {
                throw new Error('createSequenceBlockModel(): could not find academic level for sequence block, sequence block id = ' + oData.sequence_block_id);
            }
            oData.academic_level_model = level;

            parentId = oData.parent_sequence_block_id;
            parent = null;
            if (parentId) {
                parent = map.get(parentId);
            }
            oData.parent_model = parent;

            // instantiate model
            rhett = new ilios.cim.model.SequenceBlockModel(oData);

            // add the model to its parent
            if (parent) {
                parent.get('children').add(rhett);
            }

            // subscribe the app to the model's "course model change" event
            rhett.subscribe("courseChange", this.onCourseModelChangeInSequenceBlock, this, true);

            // register model
            map.add(rhett);

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
            var parent, parentId, id, parentEl, el, view;

            parent = model.get('parent');
            parentId = false;
            if (parent) {
                parentId = parent.get('id');
            }
            id = model.get('id');

            parentEl = parentId ?  document.getElementById('sequence-block-view-children-' + parentId) : document.getElementById('report-sequence-container');
            el = generateSequenceBlockMarkup(id);

            // attach the view element to it's parent in the document.
            parentEl.appendChild(el);

            view = new ilios.cim.view.SequenceBlockView(model, el);

            // add sequence block to map
            this.getSequenceBlockViewMap().add(view);

            return view;
        },
        /**
         * Expands all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        expandAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.expand;
            this.getSequenceBlockViewMap().walk(fn);
        },

        /**
         * Collapses all sequence block views.
         *
         * @method expandAllSequenceBlock
         */
        collapseAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.collapse;
            this.getSequenceBlockViewMap().walk(fn);
        },

        /**
         * Disable "draft mode" for all sequence block views.
         *
         * @method disableAllSequenceBlocks
         * @see ilios.cim.view.SequenceBlockView.disableDraftMode
         */
        disableAllSequenceBlocks: function () {
            var fn = ilios.cim.view.SequenceBlockView.prototype.disableDraftMode;
            this.getSequenceBlockViewMap().walk(fn);
        },


        //
        // internal API
        //

        /**
         * Sorts the top level blocks and updates their display in the DOM accordingly.
         *
         * @method _sortTopLevelSequenceBlocks
         * @protected
         */
        _sortTopLevelSequenceBlocks: function () {
            var i, n, containerEl, el, map, list, id;
            map = this.getSequenceBlockModelMap();
            if (! map.size()) {
                return;
            }
            // get top level blocks
            list = map.list(function (item) {
                return Lang.isNull(item.get('parent'));
            });
            // aways sort by academic level, order-in-sequence is not supported at the top level
            list.sort(map.sortByAcademicLevel);
            containerEl = document.getElementById('report-sequence-container');

            // re-attach each child view element in the proper order (no need to detach them first)
            for (i = 0, n = list.length; i < n; i++) {
                id = 'sequence-block-view-' + list[i].getId();
                el = document.getElementById(id); // hokey but works.
                containerEl.appendChild(el);
            }
        },

        /**
         * Updates the order-in-sequence value for given sequence blocks.
         *
         * @method _updateBlockOrderInSequence
         * @param {Object} o A map of key/value pairs. Each pair consists of a sequence block id as key, and
         *      the order-in-sequence as the value.
         * @protected
         */
        _updateBlockOrderInSequence: function (o) {
            var i, n, blocks;
            blocks = this.getSequenceBlockModelMap();
            for (i in o) {
                if (o.hasOwnProperty(i)) {
                    n = parseInt(o[i], 10);
                    blocks.get(i).set('orderInSequence', n);
                }
            }
        },

        //
        // event handling
        //

        /**
         * Event handler function.
         * Subscribe this to each sequence block's "Delete" button click-event.
         * This will throw up a confirmation dialog which will continue the sequence block deletion
         * process upon confirmation.
         *
         * @method onSequenceBlockDeleteButtonClick
         * @param {Event} The click event.
         * @param {Object} args A map of arguments passed on method-invocation. Expected values are:
         *     @param {Number} args.id The id of the to-be-deleted sequence block.
         */
        onSequenceBlockDeleteButtonClick: function (event, args) {
            var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.delete.confirm.warning')
                + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
            var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
            ilios.alert.inform(continueStr, yesStr, function (event, args) {
                args.dataSource.deleteSequenceBlock(args.id);
                this.hide(); // hide the calling dialog
            }, { id: args.id, dataSource: this.getDataSource()});
            Event.stopEvent(event);
            return false;
        },

        /**
         * Event handler function.
         * Subscribe this to each sequence block's "Add" button click-event, and the "add" button in the bottom toolbar.
         * This will populate the "create a sequence block" dialog with a given parent model (if applicable) and the
         * available courses within the application, then display the dialog.
         *
         * @method onSequenceBlockAddButtonClick
         * @param {Event} The click event.
         * @param {Object} args A map of arguments passed on method-invocation. Expected values are:
         *     @param {Number} args.report_id The id of the report that a new sequence block should be added to.
         *     @param {ilios.cim.model.SequenceBlockModel|null} args.parent The parent sequence block, or NULL if a top-level block is to be created.
         */
        onSequenceBlockAddButtonClick: function (event, args) {
            var parent = args.parent;
            var reportId = args.report_id;
            var dialog = this.getCreateSequenceBlockDialog();
            dialog.show(reportId, parent);
        },

        /**
         * Event handler function.
         * Subscribe this to each sequence block's "Edit" button click-event.
         * This will populate the "edit a sequence block" dialog with a given block model and the
         * available courses within the application, then display the dialog.
         *
         * @method onSequenceBlockEditButtonClick
         * @param {Event} The click event.
         * @param {Object} args A map of arguments passed on method-invocation. Expected values are:
         *     @param {ilios.cim.model.SequenceBlockModel} args.block The sequence block to update.
         */
        onSequenceBlockEditButtonClick: function (event, args) {
            var block = args.block;
            var dialog = this.getEditSequenceBlockDialog();
            dialog.show(block);
        },

        /**
         * Event handler function.
         * Subscribe this method to each sequence block's "courseChange" event, so we can
         * capture changes to sequence block/course associations.
         * Check-in previously assigned courses.
         *
         * The invocation scope of this method should be the application.
         *
         * @method onCourseModelChangeInSequenceBlock
         * @param {Object} args Value object containing the old and new value.
         *     @param {ilios.cim.model.CourseModel|null} args.prevValue The previous course, or NULL if n/a.
         *     @param {ilios.cim.model.CourseModel|null} args.newValue The new course, or NULL if n/a
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
     * A map of sequence block views within the application.
     *
     * @namespace cim
     * @class SequenceBlockViewMap
     * @extends ilios.cim.model.ObjectMap
     * @constructor
     */
    var SequenceBlockViewMap = function () {
        SequenceBlockViewMap.superclass.constructor.call(this);
    };

    Lang.extend(SequenceBlockViewMap, ilios.cim.model.ObjectMap, {

        /**
         * Adds a given sequence block view to the map.
         *
         * @param {ilios.cim.view.SequenceBlockView} view The view to add.
         * @return {ilios.cim.view.SequenceBlockView} The added view.
         * @throw {Error} If the data type didn't match, or if the view already exists in the map.
         * @see ilios.cim.model.ObjectMap.add
         * @override
         */
        add: function (view) {
            if (! view instanceof ilios.cim.view.SequenceBlockView) {
                throw new Error('add(): type mismatch.');
            }
            return SequenceBlockViewMap.superclass.add.call(this, view);
        },

        /*
         * @override
         * @see ilios.cim.model.ObjectMap._getIdFromObject
         */
        _getIdFromObject: function (o) {
            return o.getCnumber();
        }
    });

    /**
     * Provides functionality for exchanging data with the server-side backend.
     * All communication with the server will be handled asynchronous via XHR calls.
     *
     * @namespace cim
     * @class DataSource
     * @uses YAHOO.util.EventProvider
     * @constructor
     * @param {Object} config The data source configuration object. Expect to contain the following attributes:
     *     @param {String} config.deleteReportUrl The URL to the server-side "delete report" controller action.
     *     @param {String} config.finalizeReportUrl The URL to the server-side "finalize report" controller action.
     *     @param {String} deleteSequenceBlockUrl The URL to te  server-side "delete sequence block" controller action.
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
        this.createEvent(this.EVT_DELETE_SEQUENCE_BLOCK_STARTED);
        this.createEvent(this.EVT_DELETE_SEQUENCE_BLOCK_SUCCEEDED);
        this.createEvent(this.EVT_DELETE_SEQUENCE_BLOCK_FAILED);
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
                    this.fireEvent(this.EVT_DELETE_SEQUENCE_BLOCK_SUCCEEDED, {
                        id: o.argument.id,
                        updated_siblings_order: response.updated_siblings_order
                    });
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
         * @param {Object} updated_siblings_order A map containing sequence-block-ids/order-in-sequence values as
         *      key/value pairs. The referenced blocks are siblings in a sequence to the deleted block, and had
         *      their order-in-sequence value changed as a side-effect of the block deletion.
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
        el = rootEl.appendChild(document.createElement('a'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-anchor-' + cnumber);
        Dom.setAttribute(el, 'href', '#sequence-block-view-' + cnumber);
        // header
        headerEl = rootEl.appendChild(document.createElement('div'));
        Dom.addClass(headerEl, 'hd');
        Dom.setAttribute(headerEl, 'id', 'sequence-block-view-header-' + cnumber);
        el = headerEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'toggle');
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
        // description
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
        // required
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.required') + ' ?'));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-required-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // academic level
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.phrases.academic_level')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-academic-level-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // course
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.course')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-course-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // child sequence order
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.child_sequence_order')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-child-sequence-order-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // order in sequence
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.order_in_sequence')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-order-in-sequence-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // start date
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.phrases.start_date')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-start-date-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // end date
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.phrases.end_date')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-end-date-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // duration
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.duration')));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-duration-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
        // is track?
        rowEl = bodyEl.appendChild(document.createElement('div'));
        Dom.addClass(rowEl, 'row');
        el = rowEl.appendChild(document.createElement('div'));
        Dom.addClass(el, 'label');
        Dom.addClass(el, 'column');
        el.appendChild(document.createTextNode(ilios_i18nVendor.getI18NString('general.phrases.is_track') + " ?"));
        el = rowEl.appendChild(document.createElement('div'));
        Dom.setAttribute(el, 'id', 'sequence-block-view-track-' + cnumber);
        Dom.addClass(el, 'data');
        Dom.addClass(el, 'column');
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

    ilios.cim.SequenceBlockViewMap = SequenceBlockViewMap;
    ilios.cim.DataSource = DataSource;
    ilios.cim.App = App;
}());
