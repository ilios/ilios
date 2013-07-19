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
        Event = YAHOO.util.Event;
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
     */
    var App = function (config, payload) {

        // set module configuration
        this.config = Lang.isObject(config) ? config : {};

        this.statusView = new ilios.cim.view.StatusView();
        this.statusView.render('status-toolbar');

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
            this.academicLevels = payload.academic_levels;
            this.sequenceBlock = payload.sequence_block;
            this.sequenceBlocks = payload.sequence_blocks;
            this.linkableCourses = payload.linkable_courses;
            this.linkedCourses = payload.linked_courses;
            this.reportView.render();
            this.reportView.show();
        }
    };

    App.getStatusView = function () {
        return this.statusView;
    };

    App.getConfig = function () {
        return this.config;
    };

    ilios.cim.App = App;
}());
