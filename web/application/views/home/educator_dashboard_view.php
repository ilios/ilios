<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Educator dashboard page template.
 */
$siteUrl = site_url();
$baseUrl = base_url();
$controllerURL = $siteUrl . '/dashboard_controller'; // TODO: consider how to avoid this coupling
$courseManagementURL = $siteUrl . '/course_management';
$learningMaterialsControllerURL = $siteUrl . '/learning_materials';
$programManagementURL = $siteUrl . '/program_management';
$managementConsoleURL = $siteUrl . '/management_console';
$curriculumInventoryManagerUrl = $siteUrl . '/curriculum_inventory_manager';
$viewsUrlRoot = getViewsURLRoot();
$viewsPath = getServerFilePath('views');

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title><?php echo $title_bar_string; ?></title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/ilios-styles.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/session-types.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <style type="text/css"></style>
    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <script type="text/javascript">
        var baseURL = "<?php echo $siteUrl; ?>/";
        var controllerURL = "<?php echo $controllerURL; ?>/";    // expose this to our javascript land
        var courseManagementURL = "<?php echo $courseManagementURL; ?>/";       // similarly...
        var learningMaterialsControllerURL = "<?php echo $learningMaterialsControllerURL; ?>/";    // ...
        var programManagementURL = "<?php echo $programManagementURL; ?>/";     // similarly...
        var pageLoadedForStudent = false;
        var isCalendarView = false;
    </script>
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
<?php
    $js = array(
        'vendor' => array( // third-party js
            'application/views/scripts/third_party/html5shiv.js',
            'scripts/yui/build/yahoo-dom-event/yahoo-dom-event.js',
            'scripts/yui/build/connection/connection-min.js',
            'scripts/yui/build/datasource/datasource-min.js',
            'scripts/yui/build/autocomplete/autocomplete-min.js',
            'scripts/yui/build/element/element-min.js',
            'scripts/yui/build/button/button-min.js',
            'scripts/yui/build/calendar/calendar-min.js',
            'scripts/yui/build/container/container-min.js',
            'scripts/yui/build/json/json-min.js',
            'scripts/yui/build/selector/selector-min.js',
            'scripts/yui/build/treeview/treeview-min.js',
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
            'application/views/scripts/models/report_model.js',
            'application/views/scripts/competency_base_framework.js',
            'application/views/scripts/course_model_support_framework.js',
            'application/views/scripts/learner_view_base_framework.js',
            'application/views/scripts/public_course_summary_base_framework.js',
            'application/views/scripts/mesh_base_framework.js',
            'application/views/home/calendar_item_model.js',
            'application/views/home/dashboard_calendar_support.js',
            'application/views/home/dashboard_transaction.js',
            'application/views/home/educator_dashboard_transaction.js',
            'application/views/home/educator_dashboard_dom.js',
            'application/views/home/reminder_model.js',
            'application/views/home/educator_dashboard_dialogs_include.js',
            'application/views/home/educator_dashboard_report_dialogs_include.js',
        ),
    );
    writeJsScripts($js, 'educator_dashboard', $this->config->item('script_aggregation_enabled'), $this->config->item('ilios_revision'));
?>
<?php include_once $viewsPath . 'common/set_user_preferences.inc.php'; ?>
<?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</head>
<body class="home yui-skin-sam">
    <div id="wrapper">
        <header id="masthead" class="clearfix">
            <div class="inner">

<?php
    include_once $viewsPath . 'common/masthead_logo.inc.php';
    include_once $viewsPath . 'common/masthead_toolbar.inc.php';
    include_once $viewsPath . 'common/masthead_nav.inc.php';
?>
           </div>
<?php include_once $viewsPath . 'common/masthead_viewbar.inc.php'; ?>
        </header>

        <div id="main" role="main">
            <div id="content" class="dashboard clearfix">
                <h2 class="page-header"><?php echo $page_title_educator_string; ?> <span id="page_title"></span></h2>
                <div class="content_container">
                    <div class="column primary clearfix">
                        <h3><?php echo $my_calendar_string; ?></h3>
<?php
    if ($show_view_switch) :
?>
                                    <a href="<?php echo $controllerURL; ?>/switchView?preferred_view=student" id="role_switch" class="tiny secondary radius button">
                                                <?php echo $switch_to_student_view_string; ?>
                                    </a>
<?php
    endif;
?>
                        <div class="calendar_tools clearfix">
<?php echo generateProgressDivMarkup('position:absolute; left: 25%;float:none;margin:0;'); ?>
                            <ul class="buttons right">
                                <li>
                                    <span id="calendar_filters_btn" title="<?php echo $calendar_filters_title; ?>" class="medium radius button">
                                        <span class="icon-search icon-alone"></span>
                                        <span class="screen-reader-text"><?php echo $calendar_filters_btn; ?></span>
                                    </span>
                                </li>
                                <li>
                                    <a href="<?php echo $siteUrl; ?>/calendar_controller/exportICalendar/instructor" class="medium radius button" title="<?php echo $ical_download_title; ?>">
                                        <span class="icon-download icon-alone"></span>
                                        <span class="screen-reader-text"><?php echo $ical_download_button; ?></span>
                                    </a>
                                </li>
                                <li>
                                    <span id="ical_feed_btn" title="<?php echo t("dashboard.icalendar.feed_title", false); ?>" class="medium radius button">
                                        <span class="icon-feed icon-alone"></span>
                                        <span class="screen-reader-text"><?php echo t("dashboard.icalendar.feed_title"); ?></span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div id="calendar_filters_breadcrumb_block">
                            <div class="calendar_filter_titlebar">
                                <span id="calendar_filters_clear_search_link" class="icon-cancel" title="<?php echo $calendar_clear_search_filters; ?>"></span>
                                <span class="calendar_filters_breadcrumb_title"> <?php echo $calendar_search_mode_title; ?> : </span>
                            </div>
                            <span id="calendar_filters_breadcrumb_content"></span>
                        </div>
                        <div id="dhtmlx_scheduler_container" class="dhx_cal_container">
                            <div class="dhx_cal_navline">
                                <div class="dhx_cal_prev_button">&nbsp;</div>
                                <div class="dhx_cal_next_button">&nbsp;</div>
                                <div class="dhx_cal_today_button"></div>
                                <div class="dhx_cal_date"></div>
                                <div class="dhx_cal_tab day_tab" name="day_tab"></div>
                                <div class="dhx_cal_tab week_tab" name="week_tab"></div>
                                <div class="dhx_cal_tab month_tab" name="month_tab"></div>
                                <div class="dhx_cal_tab week_agenda_tab" name="week_agenda_tab"></div>
                            </div>
                            <div class="dhx_cal_header"></div>
                            <div class="dhx_cal_data" id="dhx_cal_data"></div>
                        </div>
                    </div><!--end .primary.column -->

                    <div class="column secondary">

                        <div class="dashboard_widget">
                            <div class="hd toggle collapse" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $recent_activities_string; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="recent_widget_content"></div>
                        </div>

                        <div class="dashboard_widget">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $my_courses_string; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="course_widget_content" style="display: none;"></div>
                        </div>

                        <div class="dashboard_widget">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $my_programs_string; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="program_widget_content"></div>
                        </div>

                        <div class="dashboard_widget">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $my_reports_string; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="reports_widget_content" style="display:none;">
                                <div class="buttons">
                                    <a href="" class="tiny button" id="report_widget_add_new_div" onclick="IEvent.fire({action: 'report_dialog_open', report_model: null}); return false;">
                                        <?php echo $phrase_add_new_string; ?></a>
                                </div>
                                <ul id="reports_widget_list_container"></ul>
                            </div>
                        </div>

                        <div class="dashboard_widget" style="display: none;">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $competency_mapping_string ?></h3>
                            </div>
                            <div class="widget_collapse_content" id="competency_widget_content"></div>
                        </div>

                        <div class="dashboard_widget">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $word_administration_string; ?>
<?php
    if ($has_non_student_sync_exceptions || $has_student_sync_exceptions) :
?>
                                    <span id="administration_widget_alert" class="icon-warning"></span>
<?php
    endif;
?>
                                </h3>
                            </div>
                            <div class="widget_collapse_content bd" id="administration_widget_content" style="display: none;">
                                <ul>
                                    <li>
                                        <a href="" onclick="IEvent.fire({action: 'ap_dialog_open'}); return false;">
                                            <?php echo $word_archiving_string; ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="" onclick="IEvent.fire({action: 'rp_dialog_open'}); return false;">
                                            <?php echo $course_rollover_string; ?>
                                        </a>
                                    </li>
<?php
    if ($show_console) :
?>
                                    <li>
                                        <a href="<?php echo $managementConsoleURL; ?>/"><?php echo $management_console_string; ?></a>
<?php
        if ($has_non_student_sync_exceptions || $has_student_sync_exceptions) :
?>
                                         <span style="font-size: 8pt; font-weight: bold; color: #ee0a0a;">
                                             (<?php echo implode(', ', $sync_exceptions_indicators); ?>)
                                         </span>
                                    </li>
<?php
        endif;
?>
                                    <li>
                                        <a href="<?php echo $curriculumInventoryManagerUrl; ?>">
                                            <?php echo t('curriculum_inventory.title_bar'); ?>
                                        </a>
                                    </li>
<?php
    endif;
?>
                                </ul>
                            </div>
                        </div>

                        <div class="dashboard_widget" style="display: none;">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $account_management_string; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="account_widget_content"></div>
                        </div>

                        <div class="dashboard_widget">
                            <div class="hd toggle expand" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $my_alerts_string; ?>
                                    <span id="alerts_overdue_warning"></span>
                                </h3>

                            </div>
                            <div class="widget_collapse_content bd" id="alerts_widget_content"  style="display: none;">
                                <div class="buttons">
                                    <a href="" class="tiny button" id="alert_widget_add_new" onclick="IEvent.fire({action: 'ur_dialog_open', reminder_model: null }); return false;"><?php echo $phrase_add_new_string; ?></a>
                                </div>
                                <ul id="alerts_widget_list_container">
                                    <li><?php echo $word_none_string; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- end .secondary -->
                </div> <!-- end .dashboard -->
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>

<!-- start dialog tabs -->
    <div class="tabdialog" id="archiving_permissions_dialog">
        <div class="hd" id="archiving_permissions_dialog_title"></div>
        <div class="bd" style="position: relative;">
            <div class="dialog_wrap" id="ap_dialog_wrap" style="height: 216px;">
                <form action="#">
                    <div style="padding: 9px; font-weight: bold; position: relative;">
                        <?php echo t('preferences.archiving.text'); ?>
                        <div style="margin-top: 24px;">
                            <div style="margin-bottom: 9px;">
                                <div style="width: 49%; text-align: right; float: left; padding-top: 3px;">
                                    <?php echo t('preferences.archiving.program_year'); ?>
                                </div>
                                <div style="width: 49%; float: right;">
                                    <input type="radio" name="py_radio" id="ap_py_radio_inactive" checked
                                        onclick="ilios.ui.radioButtonSelected(this);"/>
                                    <label for="ap_py_radio_inactive" id="ap_py_radio_inactive_label">
                                        <?php echo t('general.terms.inactive'); ?>
                                    </label>
                                    <br/>
                                    <input type="radio" name="py_radio" id="ap_py_radio_active"
                                        onclick="ilios.ui.radioButtonSelected(this);"/>
                                    <label for="ap_py_radio_active" id="ap_py_radio_active_label"
                                        style="font-weight: normal;">
                                        <?php echo t('general.terms.active'); ?>
                                    </label>
                                </div>
                                <div class="clear"></div>
                            </div>

                            <div style="width: 49%; text-align: right; float: left; padding-top: 3px;">
                                <?php echo t('preferences.archiving.course'); ?>
                            </div>
                            <div style="width: 49%; float: right;">
                                <input type="radio" name="course_radio" id="ap_course_radio_inactive" checked
                                    onclick="ilios.ui.radioButtonSelected(this);"/>
                                <label for="ap_course_radio_inactive" id="ap_course_radio_inactive_label">
                                    <?php echo t('general.terms.inactive'); ?>
                                </label>
                                <br/>
                                <input type="radio" name="course_radio" id="ap_course_radio_active"
                                    onclick="ilios.ui.radioButtonSelected(this);"/>
                                <label for="ap_course_radio_active" id="ap_course_radio_active_label"
                                    style="font-weight: normal;">
                                    <?php echo t('general.terms.active'); ?>
                                </label>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div><!-- end archiving_permissions_dialog -->
<?php
    include $viewsPath . 'common/course_summary_view_include.php';
    include $viewsPath . 'home/calendar_filters_dialog.inc.php';
    include $viewsPath . 'home/calendar_feed_dialog.inc.php';
?>
    <div class="tabdialog" id="report_competency_pick_dialog"></div>

    <div class="tabdialog" id="calendar_event_details_dialog">
        <div class="hd"></div>
        <div class="bd">
            <div class="dialog_wrap">
                <form method="get" action="#">
                    <div id="learner_view_content_div">
                    </div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #calendar_event_details_dialog -->

    <div class="tabdialog" id="report_results_dialog">
        <div class="hd" id="report_results_dialog_title">
            <?php echo t('dashboard.report.result.dialog_title'); ?>
        </div>
        <div class="bd">
            <div class="dialog_wrap" id="report_results_dialog_wrap">
                <form>
                    <div id="report_results_content"></div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #report_results_dialog -->

    <div class="tabdialog" id="report_dialog">
        <div class="hd" id="report_dialog_title"><?php echo $report_title_string; ?></div>
        <div class="bd" style="position: relative;">
            <div class="dialog_wrap" id="report_dialog_wrap" style="height: 158px;">
                <form>
                    <div style="padding: 9px; position: relative;">
                        <div style="position: absolute; top: 1px; right: 1px; display: none;"
                            id="report_indeterminate_div">
                            <div class="indeterminate_progress"></div>
                        </div>
                        <?php echo $report_header_string; ?>:<br/><br/>
                        <?php echo $report_title_optional_string; ?>
                        <input type="text" id="title" size="50"> <br/><br/>
                        <?php echo $word_all_string; ?>
                        <select id='report_noun_1'>
                            <option value="course"><?php echo t('general.terms.courses'); ?></option>
                            <option value="session"><?php echo t('general.terms.sessions'); ?></option>
                            <option value="program"><?php echo t('general.terms.programs'); ?></option>
                            <option value="program year"><?php echo t('general.terms.program_years'); ?></option>
                            <option value="instructor"><?php echo t('general.terms.instructors'); ?></option>
                            <option value="instructor group"><?php echo t('general.phrases.instructor_groups'); ?></option>
                            <option value="learning material"><?php echo t('general.phrases.learning_materials'); ?></option>
                            <option value="competency"><?php echo t('general.terms.competencies'); ?></option>
                            <option value="topic"><?php echo t('general.terms.topics'); ?></option>
                            <option value="mesh term"><?php echo t('general.phrases.mesh_terms'); ?></option>
                        </select>

                        <input type="checkbox" checked="checked"
                            id='report_support_noun_2_checkbox'>
                        <?php echo $report_association_string; ?>
                        <select id='report_noun_2' style='width: 125px;'></select>
                        <?php echo $phrase_which_is_string; ?>
                        <div id='report_noun_2_value_div' style='display: inline-block;'>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #report_dialog -->

    <div class="tabdialog" id="report_instructor_pick_dialog" style="display: none;">
        <div class="hd" id="report_instructor_dialog_title"></div>
        <div class="bd">
            <div id="ilios_instructor_lightbox_wrap" style="padding-left: 24px;">
                <form method='get' action='#'>
                    <div id="instructors_lightbox_textfield"
                        style="width: 100%; height: 20px; padding-left: 3px; overflow: auto;"
                        class="read_only_data">
                    </div>
                    <div id="instructors_selector_div" style="height: 350px;">
                        <div class="autocomplete_tab" id="instructor_ac_div"
                            style="width: 89%; margin-top: 12px;">
                            <?php echo $word_filter_string; ?>:
                            <input id="instructor_ac_input"
                                name="instructor_ac_input" type="text"
                                style="margin-left: 9px; width: 83%;">
                            <div class="autolist" id="instructor_autolist" style="margin-top: 15px;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #report_instructor_pick_dialog -->

    <div class="tabdialog" id="report_learning_materials_dialog">
        <div class="hd"><?php echo $learning_materials_dialog_title ?></div>
        <div class="bd">
            <div class="dialog_wrap" style="height: 376px;">
                <form>
                    <div style="padding: 9px;">
                        <div style="margin-bottom: 20px; margin-right: 6px; position: relative; padding: 24px 0px 0px;">
                            <div style="padding: 12px 6px; border: 1px solid #AAAAAA; height: 266px;">
                                <?php echo t('learning_material.search.title'); ?>
                                <input type="text" name="alm_search" id="rlm_search_textfield"
                                    style="width: 482px;">
                                <div style="width: 780px; height: 230px; overflow: auto; margin: 6px 0px 15px;">
                                    <ul id="rlm_search_results_ul" class="learning_material_list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div><!-- end #report_learning_materials_dialog -->

    <div class="tabdialog" id="ilios_report_mesh_picker">
        <div class="hd"><?php echo $mesh_dialog_title; ?></div>
        <div class="bd">
            <div class="dialog_wrap">
                <form method="get" action="#">
                    <div class="yui-navset yui-navset-top" id="tabbed_view_mesh">
                        <ul class="yui-nav">
                            <li class="selected" title="active">
                                <a href="#mesh_results_tab"><em><?php echo $mesh_search_mesh; ?></em></a>
                            </li>
                        </ul>
                        <div class="yui-content">
                            <div id="mesh_results_tab">
                                <input type="text" name="mesh_search_terms" id="mesh_search_terms" style="width: 100%"
                                    onkeypress="return ilios.home.report.handleReportMeSHSearchFieldInput(this, event);">
                                <div id="mesh_search_results"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="mesh_search_status" style="position: absolute; left: 12px; bottom: 10px; font-size: 9pt; color: #aa3241;">
            </div>

        </div>
        <div class="ft">
        </div>
    </div> <!-- end #ilios_report_mesh_picker -->

    <div class="tabdialog" id="rollover_permissions_dialog">
        <div class="hd" id="rollover_permissions_dialog_title"></div>
        <div class="bd" style="position: relative;">
            <div class="dialog_wrap" id="rp_dialog_wrap">
                <form action="#">
                    <p>
                    <?php echo t('preferences.rollover.text'); ?>
                    </p>
                    <ul class="no-bullets margin-l">
                        <li>
                            <input type="radio" name="rp_radio" id="rp_radio_inactive" checked
                                onclick="ilios.ui.radioButtonSelected(this);"/>
                            <label for="rp_radio_inactive" id="rp_radio_inactive_label">
                                <?php echo t('general.terms.inactive'); ?>
                            </label>
                        </li>
                        <li>
                            <input type="radio" name="rp_radio" id="rp_radio_active"
                                onclick="ilios.ui.radioButtonSelected(this);"/>
                            <label for="rp_radio_active" id="rp_radio_active_label"
                                style="font-weight: normal;">
                                <?php echo t('general.terms.active'); ?>
                            </label>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #rollover_permissions_dialog -->


    <div class="tabdialog" id="user_reminder_dialog">
        <div class="hd" id="user_reminder_dialog_title">xxx</div>
        <div class="bd">
            <div class="dialog_wrap" id="ur_dialog_wrap" style="height: 196px;">
                <form>
                    <!-- <div style="padding: 9px; font-weight: bold; position: relative;"> -->
                        <div class="small align-r note" id="ur_creation_div">
                            <?php echo $word_created_string; ?>
                            <span id="ur_creation_date"></span>
                        </div>

                        <div class="small">
                            <?php echo $your_alert_string; ?> (150 <?php echo $max_char_string; ?>)
                        </div>

                        <textarea id="ur_textarea" style="width: 99%; height: 90px;" ></textarea>

                        <div style="font-size: 9pt; margin-top: 9px; padding-left: 24px;
                            padding-top: 2px; position: relative;">
                            <div id="due_date_calendar_button" class="calendar_button"
                                style="position: absolute; top: 0px; left: 1px;"></div>
                            <?php echo $phrase_due_date_string; ?>:
                            <span id="ur_due_date" class="read_only_data"></span>
                            <div style="position: absolute; right: 1px; top: 0px;" id="ur_complete_div">
                                <?php echo $mark_complete_string; ?>
                                <input type="checkbox" id="ur_complete_checkbox">
                            </div>
                        </div>
                    <!-- </div> -->
                </form>
            </div>
        </div>
        <div class="ft"></div>
    </div> <!-- end #user_reminder_dialog -->
<!-- end dialog tabs -->

    <!-- date picker container for user reminder dialog -->
    <div id="date_picking_calendar_container" style="z-index: 10999; position: absolute;"></div>

    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });
<?php
    include_once $viewsPath . 'common/load_school_competencies.inc.php';
?>
        YAHOO.util.Event.onDOMReady(ilios.dom.generateTreeSelectionDialogMarkupAndWireContent, {
            trigger: 'competency_picker_show_dialog',
            single_selection: 'yup',
            remote_data: new YAHOO.util.FunctionDataSource(ilios.competencies.getActiveSchoolCompetenciesList),
            display_handler: ilios.home.report.resetCompetencyTree,
            submit_override: ilios.home.report.competencySubmitMethod,
            filter_results_handler:
            ilios.home.report.competencyTreeFilterResults,
            format_results_handler:
            ilios.home.report.competencyTreeHandleResults,
            selected_div_dom_generator:
            ilios.home.report.competencyTreeSelectedDOMContentGenerator,
            unselected_div_dom_generator: ilios.home.report.competencyTreeDOMContentGenerator,
            tab_title: ilios_i18nVendor.getI18NString('program_management.competency_dialog.tab_title'),
            id_uniquer: 'csdpe_',
            panel_title_text: ilios_i18nVendor.getI18NString('program_management.competency_dialog.panel_title'),
            dom_root: 'report_competency_pick_dialog',
            max_displayed_results: 500,
            load_finish_listener: ilios.home.report.competencyTreeFinishedPopulation
        });

        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initCalendar);
        YAHOO.util.Event.onDOMReady(ilios.home.preferences.assembleArchivingPermissionsDialog,
            {display_handler: ilios.home.populateArchivingPermissionsDialog}
        );
        YAHOO.util.Event.onDOMReady(ilios.home.preferences.assembleRolloverPermissionsDialog,
            {display_handler: ilios.home.populateRolloverPermissionsDialog}
        );
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadAllOfferings);
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadRecentActivity);
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadReminderAlerts);
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadReports);

        YAHOO.util.Event.onDOMReady(ilios.home.calendar.assembleCalendarEventDetailsDialog);

        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initFilterHooks);

        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initFeedHooks);

        YAHOO.util.Event.onDOMReady(ilios.home.report.assembleReportDialog,
            {display_handler: ilios.home.report.resetReportDialog}
        );
        YAHOO.util.Event.onDOMReady(ilios.home.report.assembleReportResultsDialog, {});


        YAHOO.util.Event.onDOMReady(ilios.home.reminder.assembleUserReminderDialog,
            {display_handler: ilios.home.resetUserReminderDialog }
        );
        YAHOO.util.Event.onDOMReady(ilios.home.registerReminderUIListeners);
        YAHOO.util.Event.onDOMReady(ilios.home.report.registerReportDialogListeners,
            {display_handler: ilios.home.report.resetReportDialog}
        );

        YAHOO.util.Event.onDOMReady(ilios.home.report.setupInstructorUIElements);
        YAHOO.util.Event.onDOMReady(ilios.home.report.buildReportInstructorDialogDOM);

        YAHOO.util.Event.onDOMReady(ilios.home.report.assembleAddLearningMaterialsDialog,
            {display_handler: ilios.home.report.resetAddLearningMaterialsDialog});

        YAHOO.util.Event.onDOMReady(ilios.home.report.registerReportLearningMaterialUI);

        YAHOO.util.Event.onDOMReady(ilios.home.report.buildReportMeSHPickerDialogDOM);
    </script>
</body>
</html>
