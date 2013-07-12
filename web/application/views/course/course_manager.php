<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Course Management page template.
 */
$controllerURL = site_url() . '/course_management'; // TODO: consider how to avoid this coupling
$offeringControllerURL = site_url() . '/offering_management';
$learningMaterialsControllerURL = site_url() . '/learning_materials';
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
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <style type="text/css"></style>

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!-- Modernizr enables HTML5 elements & feature detects for optimal performance.
         Create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script type="text/javascript" src="<?php echo $viewsUrlRoot; ?>scripts/third_party/modernizr-2.5.3.min.js"></script>

    <!-- Third party JS -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/date_formatter.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/md5-min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!-- Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/preferences_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui_rte.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/";
        var currentUserId = "<?php echo $user_id; ?>";
        var offeringControllerURL = "<?php echo $offeringControllerURL; ?>";
        var learningMaterialsControllerURL = "<?php echo $learningMaterialsControllerURL; ?>/";
        var adminUserDisplayName = "<?php echo $admin_user_short_name; ?>";

        ilios.namespace('cm'); // assure the existence of this page's namespace

        // We do this here due to load-order issues
        ilios.namespace('common.lm');
        ilios.namespace('common.picker.mesh');
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/school_competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/discipline_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/course_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/independent_learning_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/learning_material_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/objective_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/offering_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/program_cohort_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/recurring_event_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/session_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/simplified_group_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/competency_base_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/course_model_support_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/learner_group_picker_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/public_course_summary_base_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_learning_material_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_program_cohort_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_session_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_ilm_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_rollover.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_manager_transaction.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/course_search_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/multiple_offerings_lightbox_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/multipurpose_session_lightbox_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/mesh_item_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/mesh_base_framework.js"); ?>"></script>
</head>
<body class="course yui-skin-sam">
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
    include 'course_container_include.php';
?>
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>
    <div id="date_picking_calendar_container" style="z-index: 10999;"></div>
    <div class="tabdialog" id="cohort_pick_dialog"></div>    <!-- edit session description dialog -->
<?php
    include $viewsPath . 'common/course_summary_view_include.php';
    include $viewsPath . 'common/mesh_picker_include.php';
    include $viewsPath . 'common/learning_material_lightbox_include.php';
    include 'add_course_include.php';
    include 'add_learning_materials_dialog.php';
    include 'archiving_dialog.php';
    include 'course_search_include.php';
    include 'discipline_include.php';
    include 'director_include.php';
    include 'edit_learning_material_notes_dialog.php';
    include 'edit_course_objective_dialog.php';
    include 'edit_session_objective_dialog.php';
    include 'multipurpose_session_lightbox_include.php';
    include 'independent_learning_dialog.php';
    include 'review_dialog.php';
    include 'rollover_dialog.php';
?>
    <!-- multiple offerings picker dialog -->
    <div id="multiple_offerings_recurring_date_picking_calendar_container" style="z-index: 10999; position: absolute;">
    </div>

    <div class="tabdialog" id="edit_session_description_dialog">
        <div class="hd"></div>
        <div class="bd">
            <form action="#" method="post">
                <textarea id="esd_textarea" style="width: 99%"></textarea>
            </form>
        </div>
        <div class="ft"></div>
    </div><!-- end #edit_session_description_dialog -->

    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "course/edit_session_description_dialog.js"); ?>"></script>
    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });

        var tmpStr = '<?php echo $institution_name; ?> ' + ilios_i18nVendor.getI18NString('general.terms.programs');
        var sessionTypeModel = null;

<?php
    generateJavascriptRepresentationCodeOfPHPArray($preference_array, 'dbObjectRepresentation', false);
?>
        ilios.global.installPreferencesModel();
        ilios.global.preferencesModel.updateWithServerDispatchedObject(dbObjectRepresentation);

<?php
    include_once $viewsPath . 'common/load_school_competencies.inc.php';
    include_once $viewsPath . 'common/start_idle_page_timer.inc.php';
?>

        YAHOO.util.Event.onDOMReady(ilios.cm.assembleArchivingDialog, {});
        YAHOO.util.Event.onDOMReady(ilios.cm.assembleRolloverDialog, {});

        YAHOO.util.Event.onDOMReady(ilios.dom.generateGenericDialogMarkupAndWireContent, {
            trigger : 'find_cohort_and_program',
            indeterminate_loading_id : 'steward_indeterminate_div',
            display_handler : ilios.cm.courseManagementHandleProgramCohortSelectionDialogDisplay,
            hide_autocomplete_input : 'value matters not',
            submit_override : ilios.cm.handleProgramCohortDialogSubmit,
            widget_dom_generator : ilios.ui.programCohortDialogTreeDOMGenerator,
            tab_title : ilios_i18nVendor.getI18NString('general.phrases.select_cohort'),
            id_uniquer : 'scde_',
            panel_title_text : tmpStr,
            dom_root : 'cohort_pick_dialog',
            deselect_handler : ilios.cm.handleProgramCohortDialogDeselection,
            max_displayed_results: 250,
            panel_width: '700px'
        });

        YAHOO.util.Event.onDOMReady(ilios.dom.buildDialogPanel, {
            trigger : 'add_new_course',
            target : "course_add_picked",
            hidden : "new_course_hidden",
            container : "course_add_dialog",
            panel_width : "510px",
            display_handler : ilios.cm.clearCourseAddDialogContents
        });

        YAHOO.util.Event.onDOMReady(ilios.cm.session.mo.buildMultiOfferingLightboxDOMComponents);

        YAHOO.util.Event.onDOMReady(ilios.cm.session.mo.registerMultiOfferingLightboxUIListeners);

        YAHOO.util.Event.onDOMReady(ilios.cm.lm.assembleAddLearningMaterialsDialog, {
            display_handler: ilios.cm.lm.resetAddLearningMaterialsDialog
        });

        YAHOO.util.Event.onDOMReady(ilios.cm.assembleEditCourseObjectiveDialog, {
            display_handler: ilios.cm.resetEditCourseObjectiveDialog
        });

        YAHOO.util.Event.onDOMReady(ilios.cm.session.assembleEditSessionObjectiveDialog, {
            display_handler: ilios.cm.session.resetEditSessionObjectiveDialog
        });

        YAHOO.util.Event.onDOMReady(ilios.cm.assembleEditLearningMaterialNotesDialog, {
            display_handler: ilios.cm.resetEditLearningMaterialNotesDialog
        });

        YAHOO.util.Event.onDOMReady(ilios.cm.assembleReviewDialog, {});
        YAHOO.util.Event.onDOMReady(ilios.cm.registerCourseUIListeners);
        YAHOO.util.Event.onDOMReady(ilios.cm.registerSaveAndPublishAll);

        ilios.cm.loadedSessionTypes = [];
        ilios.cm.preloadedCourseModelStub = null;
        ilios.cm.loadedSessionIdToDisplay = <?php echo $session_id; ?>;


        YAHOO.util.Event.onDOMReady(function(type, args, obj) {
            IEvent.subscribe(function (type, args) {
                var updateRte = true;
                if ('esd_dialog_open' === args[0].action) {
                    if (! ilios.cm.editSessionDescriptionDialog) {
                        ilios.cm.editSessionDescriptionDialog
                            = new ilios.cm.widget.EditSessionDescriptionDialog('edit_session_description_dialog');
                        ilios.cm.editSessionDescriptionDialog.render();
                        updateRte = false;
                    }
                    ilios.cm.editSessionDescriptionDialog.setSessionModel(args[0].model, updateRte);
                    ilios.cm.editSessionDescriptionDialog.setSessionModel(args[0].model);
                    ilios.cm.editSessionDescriptionDialog.center();
                    ilios.cm.editSessionDescriptionDialog.show();
                }
            });
        });

<?php
    foreach ($session_type_array as $sessionType) :
?>
        sessionTypeModel = {};
        sessionTypeModel.dbId = <?php echo $sessionType['session_type_id']; ?>;
        sessionTypeModel.title = '<?php echo $sessionType['title']; ?>';
        ilios.cm.loadedSessionTypes.push(sessionTypeModel);
<?php
    endforeach;
    if ($course_id != -1) :
?>

        var courseMockDBObject = {};

        courseMockDBObject.title = '<?php echo fullyEscapedText($course_title); ?>';
        courseMockDBObject.course_id = '<?php echo $course_id; ?>';
        courseMockDBObject.external_id = '<?php echo fullyEscapedText($external_id); ?>';
        courseMockDBObject.unique_id = '<?php echo $course_unique_id; ?>';
        courseMockDBObject.start_date = '<?php echo $course_start_date; ?>';
        courseMockDBObject.end_date = '<?php echo $course_end_date; ?>';
        courseMockDBObject.year = '<?php echo $course_year; ?>';
        courseMockDBObject.course_level = '<?php echo $course_course_level; ?>';
        courseMockDBObject.publish_event_id = '<?php echo $course_publish_event_id; ?>';
        courseMockDBObject.locked = '<?php echo $course_is_locked; ?>';
        courseMockDBObject.published_as_tbd = '<?php echo $course_published_as_tbd; ?>';

        ilios.cm.preloadedCourseModelStub = new CourseModel(courseMockDBObject);
<?php
    endif;
?>
        window.onbeforeunload = ilios.cm.windowWillClose;

        // we do this instead of on dom ready because we have a dependency on the data table
        // being already created prior to course load and the data table (per Yahoo guidance)
        // gets created on window load, not on dom ready...
        YAHOO.util.Event.addListener(window, "load", ilios.cm.loadCourseIfAppropriate);
    </script>
</body>
</html>
