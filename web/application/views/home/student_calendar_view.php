<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Student calendar page template.
 */
$siteUrl = site_url();
$baseUrl = base_url();
$controllerURL = $siteUrl . '/calendar_controller';
$dashboardURL = $siteUrl . '/dashboard_controller';
$courseManagementURL = $siteUrl . '/course_management';
$learningMaterialsControllerURL = $siteUrl . '/learning_materials';
$programManagementURL = $siteUrl . '/program_management';
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
        var courseManagementURL = "<?php echo $courseManagementURL; ?>/";    // similarly...
        var learningMaterialsControllerURL = "<?php echo $learningMaterialsControllerURL; ?>/";    // ...
        var programManagementURL = "<?php echo $programManagementURL; ?>/";    // similarly...
        var pageLoadedForStudent = true;
        var isCalendarView = true;
    </script>
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
<?php
    include $viewsPath . 'home/calendar_header_js.inc.php';
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
                                <a href="<?php echo $controllerURL; ?>/switchView?preferred_view=instructor" id="role_switch" class="tiny secondary radius button">
                                    <?php echo $switch_to_instructor_view_string; ?>
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
                </div>
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
    <!-- end dialog tabs -->

    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });
<?php include_once $viewsPath . 'common/load_school_competencies.inc.php'; ?>
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initCalendar);
        YAHOO.util.Event.onDOMReady(ilios.home.transaction.loadAllOfferings);
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.assembleCalendarEventDetailsDialog);
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initFilterHooks);
        YAHOO.util.Event.onDOMReady(ilios.home.calendar.initFeedHooks);
    </script>
</body>
</html>
