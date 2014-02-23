<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Group management page template
 */
$controllerURL = site_url() . '/group_management'; // TODO: consider how to avoid this coupling
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
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <style type="text/css"></style>
    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!--[if lt IE 9]>
    <script src="<?php echo $viewsUrlRoot; ?>scripts/third_party/html5shiv.js"></script>
    <![endif]-->

    <!-- Third party JS -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/json2.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!--  Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_alert.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_preferences.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_timer.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/"; // expose this to our group_manager_*.js
        ilios.namespace('gm');          // assure the existence of this page's namespace
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/program_cohort_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/student_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/group_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/group_manager_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/group_manager_transaction.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/subgroup_dom_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "group/manage_member_dialog_support.js"); ?>"></script>
    <?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</head>
<body class="learner yui-skin-sam">
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

            <div id="content" class="clearfix">
                <h2 class="page-header"><?php echo $page_header_string; ?> <span id="page_title"></span></h2>

<?php
    include 'cohort_content_container_include.php';
?>

            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>
    <div class="tabdialog" id="cohort_pick_dialog"></div>

<?php
    include 'add_new_members_include.php';
    include 'instructors_picker_include.php';
?>
    <div class="tabdialog" id="manage_member_pick_dialog"></div>

    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });

        ilios.preferences.installPreferencesModel();

        YAHOO.util.Event.onDOMReady(ilios.dom.generateGenericDialogMarkupAndWireContent, {
            trigger : 'manage_member_picker_show_dialog',
            remote_data : ilios.gm.mm.ugtDataSource,
            hide_autocomplete_input : 'you bet',
            display_handler : ilios.gm.mm.resetUserGroupTree,
            submit_override : ilios.gm.mm.ugtSubmitMethod,
            filter_results_handler : ilios.gm.mm.userGroupTreeFilterResults,
            format_results_handler : ilios.gm.mm.userGroupTreeHandleAutoCompleteResults,
            widget_dom_generator : ilios.gm.mm.userGroupTreeDOMContentGenerator,
            tab_title : ilios_i18nVendor.getI18NString('groups.manage_dialog.tab_title'),
            id_uniquer : 'ugt_',
            panel_title_text : ilios_i18nVendor.getI18NString('groups.manage_dialog.panel_title'),
            dom_root : 'manage_member_pick_dialog',
            deselect_handler : ilios.gm.mm.deselectionHandler,
            max_displayed_results : 2250,
            load_finish_listener : ilios.gm.mm.userGroupTreeFinishedPopulation
        });

        YAHOO.util.Event.onDOMReady(ilios.dom.generateSelectAndCloseDialogMarkupAndWireContent, {
            trigger : 'find_cohort_and_program',
            display_handler : ilios.ui.handleProgramCohortSelectionDialogDisplay,
            widget_dom_generator : ilios.ui.programCohortDialogTreeDOMGenerator,
            tab_title : ilios_i18nVendor.getI18NString('general.phrases.available_cohorts'),
            id_uniquer : 'dme_',
            title: ilios_i18nVendor.getI18NString('groups.select_program'),
            dom_root : 'cohort_pick_dialog'
        });

        YAHOO.util.Event.onDOMReady(ilios.gm.registerUIElements);

<?php
    // load and display groups for given cohort
    if (false !== $cohort_load_stub) :
?>
        YAHOO.util.Event.onDOMReady(function () {
            var cohort = {};
            var programTitle = '<?php echo fullyEscapedText($cohort_load_stub['program_title']); ?>';
            var model;

            cohort.cohort_id = '<?php echo $cohort_load_stub['cohort_id']; ?>';
            cohort.title = '<?php echo fullyEscapedText($cohort_load_stub['cohort_title']); ?>';
            cohort.program_year_id = '<?php echo $cohort_load_stub['program_year_id']; ?>';
            cohort.program_short_title = '<?php echo fullyEscapedText($cohort_load_stub['program_short_title']); ?>';
            cohort.program_duration = '<?php echo fullyEscapedText($cohort_load_stub['program_duration']); ?>';
            cohort.enrollment = '<?php echo fullyEscapedText($cohort_load_stub['enrollment']); ?>';

            model = ilios.ui.buildNodeModel(cohort, programTitle);
            ilios.gm.loadAndDisplayGroupsForCohort(model);
        });
<?php
    endif;
?>
        window.onbeforeunload = ilios.gm.windowWillClose;
        </script>
    </body>
</html>
