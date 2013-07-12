<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Educator calendar page template.
 */
$siteUrl = site_url();
$baseUrl = base_url();
$controllerURL = $siteUrl . '/calendar_controller';
$dashboardURL = $siteUrl . '/dashboard_controller';
$courseManagementURL = $siteUrl . '/course_management';
$learningMaterialsControllerURL = $siteUrl . '/learning_materials';
$programManagementURL = $siteUrl . '/program_management';
$managementConsoleURL = $siteUrl . '/management_console';
$viewsUrlRoot = getViewsURLRoot();
$viewsPath = getServerFilePath('views');
?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
        More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php echo $title_bar_string; ?></title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/ilios-styles.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/session-types.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <style type="text/css"></style>
    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!-- All JavaScript at the bottom, except this Modernizr build.
         Modernizr enables HTML5 elements & feature detects for optimal performance.
         Create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script type="text/javascript">
        var baseURL = "<?php echo $siteUrl; ?>/";
        var controllerURL = "<?php echo $controllerURL; ?>/";    // expose this to our javascript land
        var courseManagementURL = "<?php echo $courseManagementURL; ?>/";       // similarly...
        var learningMaterialsControllerURL = "<?php echo $learningMaterialsControllerURL; ?>/";    // ...
        var programManagementURL = "<?php echo $programManagementURL; ?>/";     // similarly...

        var pageLoadedForStudent = false;
        var isCalendarView = true;
    </script>
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
<?php
    $js = array(
        'vendor' => array( // third-party js
            'application/views/scripts/third_party/modernizr-2.5.3.min.js',
            'application/views/scripts/third_party/yui_kitchensink.js',
            'application/views/scripts/third_party/date_formatter.js',
            'application/views/scripts/third_party/md5-min.js',
            'application/views/scripts/third_party/dhtmlx_scheduler/codebase/dhtmlxscheduler.js',
            'application/views/scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_recurring.js',
            'application/views/scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_week_agenda.js',
            'application/views/scripts/third_party/idle-timer.js',
        ),
        'ilios' => array( // ilios js
            'application/views/scripts/ilios_base.js',
            'application/views/scripts/ilios_utilities.js',
            'application/views/scripts/ilios_ui.js',
            'application/views/scripts/ilios_dom.js',
            'application/views/scripts/models/abstract_js_model_form.js',
            'application/views/scripts/models/preferences_model.js',
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
            'application/views/home/dashboard_calendar_support.js',
            'application/views/home/dashboard_transaction.js',
            'application/views/home/reminder_model.js',
        ),
    );
    writeJsScripts($js, 'educator_calendar', $this->config->item('script_aggregation_enabled'), $this->config->item('ilios_revision'));
?>
</head>

<body class="home yui-skin-sam">
    <div id="wrapper">
        <header id="masthead" class="clearfix headless">
<?php include_once $viewsPath . 'common/masthead_viewbar.inc.php'; ?>
        </header>
        <div id="main" class="headless" role="main">

            <div id="content" class="dashboard clearfix">
                <div class="content_container full-width">
                    <h3><?php echo $my_calendar_string; ?></h3>
<?php
if (!$render_headerless && $show_view_switch) :
?>
                                <a href="<?php echo $controllerURL; ?>/switchView?preferred_view=student" id="role_switch" class="tiny secondary radius button">
                                    <?php echo $switch_to_student_view_string; ?>
                                </a>
<?php
endif;
?>
                    <div class="calendar_tools clearfix">
                       <?php include $viewsPath . 'common/progress_div.php';
                            echo generateProgressDivMarkup('position:absolute; left: 25%;float:none;margin:0;');
                       ?>
                        <ul class="buttons right">
                            <li>
                                <span id="calendar_filters_btn" title="<?php echo $calendar_filters_title; ?>" class="medium radius button">
                                    <span class="icon-search icon-alone"></span>
                                    <span class="screen-reader-text"><?php echo $calendar_filters_btn; ?></span>
                                </span>
                            </li>
                            <li>
                                <a href="<?php echo $siteUrl; ?>/calendar_exporter/exportICalendar/instructor" class="medium radius button" title="<?php echo $ical_download_title; ?>">
                                    <span class="icon-download icon-alone"></span>
                                    <span class="screen-reader-text"><?php echo $ical_download_button; ?></span>
                                </a>
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
                </div><!--end .content_container -->
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>
    <!-- start dialog tabs -->
<?php
    include $viewsPath . 'common/course_summary_view_include.php';
    include $viewsPath . 'common/calendar_filters_include.php';
?>
    <div class="tabdialog" id="calendar_filters_dialog">
        <?php echo generateCalendarFiltersFormContent($calendar_filters_data, true); ?>
    </div>

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
    <!-- end dialog tabs -->

    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });
<?php
    generateJavascriptRepresentationCodeOfPHPArray($preference_array, 'dbObjectRepresentation', false);
?>
        ilios.global.installPreferencesModel();
        ilios.global.preferencesModel.updateWithServerDispatchedObject(dbObjectRepresentation);

<?php include_once $viewsPath . 'common/load_school_competencies.inc.php'; ?>

        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initCalendar);
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadAllOfferings);
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.assembleCalendarEventDetailsDialog);
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initFilterHooks);

    </script>
</body>
</html>
