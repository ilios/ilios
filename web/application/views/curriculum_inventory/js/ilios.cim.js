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
     * Creates a client-side application object.
     * It sets up data model, instantiates and wires up views and dialogs.
     * @param {Object} config App. configuration.
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

        this.statusView = new ilios.cim.view.StatusView();
        this.statusView.render('status-toolbar');

        this.viewRegistry = {};

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

        this.programs = payload.programs;

        if (payload.hasOwnProperty('report')) {

            // more wiring of event handlers
            Event.addListener('expand-all-sequence-blocks-btn', 'click', function (event) {
                this.expandAllSequenceBlocks();
                Dom.removeClass('collapse-all-sequence-blocks-btn', 'hidden');
                Dom.addClass('expand-all-sequence-blocks-btn', 'hidden');
                Event.stopEvent(event);
                return false;
            }, {}, this);

            Event.addListener('collapse-all-sequence-blocks-btn', 'click', function (event) {
                this.collapseAllSequenceBlocks();
                Dom.removeClass('expand-all-sequence-blocks-btn', 'hidden');
                Dom.addClass('collapse-all-sequence-blocks-btn', 'hidden');
                Event.stopEvent(event);
                return false;
            }, {}, this);

            this.reportModel = new ilios.cim.model.ReportModel(payload.report);
            this.reportView = new ilios.cim.view.ReportView(this.reportModel, {
                finalizeUrl: this.config.controllerUrl + 'finalize',
                deleteUrl: this.config.controllerUrl + 'delete'
            });
            this.reportView.subscribe('exportStarted', function() {
                this.show('Started Report Export &hellip;', true);
            }, this.statusView, true);
            this.reportView.subscribe('downloadStarted', function() {
                this.show('Started Report Download &hellip;', true);
            }, this.statusView, true);
            this.reportView.subscribe('exportFinished', function () {
                this.reset();
            }, this.statusView, true);
            this.reportView.subscribe('downloadFinished', function () {
                this.reset();
            }, this.statusView, true);
            this.reportModel.subscribe('afterUpdate', function () {
                this.show('Report updated.', false);
            }, this.statusView, true);
            this.reportView.subscribe('finalizeStarted', function () {
                this.show('Finalizing report started &hellip;', true);
            }, this.statusView, true);
            this.reportView.subscribe('finalizeSucceeded', function () {
                this.reset();
            }, this.statusView, true);
            this.reportView.subscribe('finalizeFailed', function () {
                this.show('Finalizing report failed.', false);
            }, this.statusView, true);
            this.reportView.subscribe('deleteStarted', function () {
                this.show('Deleting report &hellip;', true);
            }, this.statusView, true);
            this.reportView.subscribe('deleteFailed', function () {
                this.show('Failed to delete report.', false);
            }, this.statusView, true);
            this.reportView.subscribe('deleteSucceeded', function() {
                this.show('Successfully deleted report. Reloading page &hellip;', true);
                // reload the page
                window.location = window.location.protocol + "//" + window.location.host + window.location.pathname;

            }, this.statusView, true);
            this.academicLevels = payload.academic_levels;
            this.sequenceBlock = payload.sequence_block;
            this.sequenceBlocks = payload.sequence_blocks;
            this.linkableCourses = payload.linkable_courses;
            this.linkedCourses = payload.linked_courses;
            this.reportView.render();
            this.reportView.show();
            Dom.removeClass('sequence-block-toolbar', 'hidden');
            Dom.removeClass('expand-all-sequence-blocks-btn', 'hidden');
            document.getElementById('expand-all-sequence-blocks-btn').disabled = false;
            document.getElementById('collapse-all-sequence-blocks-btn').disabled = false;
            if (! this.reportModel.get('isFinalized')) {
                Dom.removeClass('add-new-sequence-block-btn', 'hidden');
                document.getElementById('add-new-sequence-block-btn').disabled = false;
            }

            for (i = 0, n = payload.sequence_blocks.length; i < n; i++) {
                sequenceBlockModel = this.createSequenceBlockModel(payload.sequence_blocks[i]);
                sequenceBlockView = this.createSequenceBlockView(sequenceBlockModel);
                sequenceBlockView.show();
            }
        }
    };

    /**
     * @method getStatusView
     * Returns the app's status view.
     * @return {ilios.cim.view.StatusView} The view.
     */
    App.prototype.getStatusView = function () {
        return this.statusView;
    };

    /**
     * @method getConfig
     * Returns the app's configuration object.
     * @return {Object] The configuration object.
     */
    App.prototype.getConfig = function () {
        return this.config;
    };

    /**
     * Creates a sequence block model object from a given data transfer object representing a sequence block record.
     * @param {Object} oData The data transfer object.
     * @return {ilios.cim.model.SequenceBlockModel} The created model.
     */
    App.prototype.createSequenceBlockModel = function (oData) {
        return new ilios.cim.model.SequenceBlockModel(oData);
    }

    /**
     * @method createSequenceBlockView
     * Generates, renders and returns a sequence block view for a given sequence block model.
     * @param {ilios.cim.model.SequenceBlockModel} model
     * @return {ilios.cim.view.SequenceBlockView}
     * @static
     */
    App.prototype.createSequenceBlockView = function (model) {
        var parentId, id, parentEl, el, view;

        parentId = model.get('parentId');
        id = model.get('id');

        parentEl = parentId ?  document.getElementById('sequence-block-view-children-' + parentId) : document.getElementById('report-sequence-container');
        el = generateSequenceBlockMarkup(id);
        parentEl.appendChild(el); // insert the view into the dom

        view = new ilios.cim.view.SequenceBlockView(model, el, { cnumber: id });

        this.viewRegistry[id] = view;
        view.render();

        return view;
    };

    App.prototype.expandAllSequenceBlocks = function () {
        var cnumber, view, registry;
        registry = this.viewRegistry;
        for (cnumber in registry) {
            if (registry.hasOwnProperty(cnumber)) {
                view = registry[cnumber];
                view.expand();
            }
        }
    };

    App.prototype.collapseAllSequenceBlocks = function () {
        var cnumber, view, registry;
        registry = this.viewRegistry;
        for (cnumber in registry) {
            if (registry.hasOwnProperty(cnumber)) {
                view = registry[cnumber];
                view.collapse();
            }
        }
    };

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
        Dom.setAttribute(rowEl, 'id', 'sequence-block-view-children-' + cnumber);
        return rootEl;
    };

    ilios.cim.App = App;
}());
