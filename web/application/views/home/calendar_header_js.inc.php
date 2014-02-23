<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * calendar_header_js.inc.php
 *
 * Includes template.
 * Puts the JS links into the page header for the student dashboard and the student/educator calendar pages.
 *
 * @see home/student_calendar_view.php
 * @see home/educator_calendar_view.php
 * @see home/student_dashboard_view.php
 */
$js = array(
    'vendor' => array( // third-party js
        'application/views/scripts/third_party/html5shiv.js',
        'scripts/yui/build/yahoo-dom-event/yahoo-dom-event.js',
        'scripts/yui/build/connection/connection-min.js',
        'scripts/yui/build/container/container-min.js',
        'scripts/yui/build/element/element-min.js',
        'scripts/yui/build/button/button-min.js',
        'scripts/yui/build/json/json-min.js',
        'scripts/yui/build/selector/selector-min.js',
        'application/views/scripts/third_party/date_formatter.js',
        'application/views/scripts/third_party/md5-min.js',
        'application/views/scripts/third_party/dhtmlx_scheduler/codebase/dhtmlxscheduler.js',
        'application/views/scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_recurring.js',
        'application/views/scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_week_agenda.js',
        'application/views/scripts/third_party/idle-timer.js',
    ),
    'ilios' => array( // ilios js
        'application/views/scripts/ilios_base.js',
        'application/views/scripts/ilios_alert.js',
        'application/views/scripts/ilios_utilities.js',
        'application/views/scripts/ilios_ui.js',
        'application/views/scripts/ilios_dom.js',
        'application/views/scripts/models/abstract_js_model_form.js',
        'application/views/scripts/ilios_preferences.js',
        'application/views/scripts/ilios_timer.js',
        'application/views/scripts/models/competency_model.js',
        'application/views/scripts/models/school_competency_model.js',
        'application/views/scripts/models/discipline_model.js',
        'application/views/scripts/models/course_model.js',
        'application/views/scripts/models/simplified_group_model.js',
        'application/views/scripts/models/independent_learning_model.js',
        'application/views/scripts/models/learning_material_model.js',
        'application/views/scripts/models/mesh_item_model.js',
        'application/views/scripts/models/objective_model.js',
        'application/views/scripts/models/offering_model.js',
        'application/views/scripts/models/program_cohort_model.js',
        'application/views/scripts/models/session_model.js',
        'application/views/scripts/models/user_model.js',
        'application/views/scripts/competency_base_framework.js',
        'application/views/scripts/course_model_support_framework.js',
        'application/views/scripts/learner_view_base_framework.js',
        'application/views/scripts/public_course_summary_base_framework.js',
        'application/views/home/calendar_item_model.js',
        'application/views/home/dashboard_transaction.js',
        'application/views/home/dashboard_calendar_support.js',
    ),
);
writeJsScripts($js, 'home', $this->config->item('script_aggregation_enabled'), $this->config->item('ilios_revision'));
