<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Program management page template.
 */

$controllerURL = site_url() . '/program_management'; // TODO: consider how to avoid this coupling
$yuiUrl = getYUILibrariesURL();
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
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/md5-min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!-- Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/preferences_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/"; // expose this to our program_manager_*.js
        ilios.namespace('pm');            // assure the existence of this page's namespace
        // We do this here due to load-time issue; program_manager_dom loaded below, and
        // prior to the include-include of mesh picking php that eventually creates
        // this namespace, implements the custom save handler in this namespace
        ilios.namespace('common.picker.mesh');
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/mesh_item_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/discipline_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/objective_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/mesh_item_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/school_competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/program_year_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/program_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/steward_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/program_manager_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/program_manager_transaction.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/program_search_support.js"); ?>"></script>

    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/mesh_base_framework.js"); ?>"></script>

    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/competency_base_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/competency_dialog_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/steward_dialog_support.js"); ?>"></script>
</head>

<body class="program yui-skin-sam">
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
                <h2 class="page-header"><?php echo $page_header_string; ?></h2>
<?php
    include 'program_content_container_include.php';
?>
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>
<!-- start dialog tabs -->
    <div class="tabdialog" id="program_add_dialog">
        <div class="hd"><?php echo $add_new_program_string; ?></div>
        <div class="bd">
            <div class="dialog_wrap">
                <div id="program_add_picked" style="visibility:hidden;">meaningless data</div>
                <form method="post" action="<?php echo current_url(); ?>/addNewProgram">
                    <input id="new_program_hidden" name="new_program_hidden" type="hidden" />
                    <div style="position: relative; margin-bottom: 18px;">
                        <span class="entity_widget_title"><?php echo $program_title_full_string; ?></span>
                        <br/>
                        <input id="new_program_title" name="new_program_title" type="text"  value="" size="50"/>
                    </div>
                    <div style="position: relative; margin-bottom: 6px;">
                        <span class="entity_widget_title"><?php echo $program_title_short_string; ?></span>
                        <br />
                        <input id="new_short_title" name="new_short_title" type="text" value="" size="20"
                            style="margin-bottom: 9px;" />
                        <br />
                        <span class="entity_widget_title"><?php echo $duration_string; ?></span>
                        <br />
                        <select id="new_duration_selector" name="duration">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4" selected="selected">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                    <div class="clear"></div>
                 </form>
            </div>
        </div>
    </div> <!-- end #program_add_dialog -->
<?php
    include $viewsPath . 'common/mesh_picker_include.php';
    include 'program_search_include.php';
    include 'archiving_dialog.php';
    include 'competency_include.php';
    include 'edit_objective_text_dialog.php';
    include 'discipline_include.php';
    include 'director_include.php';
    include 'steward_include.php';
?>
<!-- end dialog tabs -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "program/add_program_dialog_include.js"); ?>"></script>
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

<?php
    include_once $viewsPath . 'common/load_school_competencies.inc.php';
    include_once $viewsPath . 'common/start_idle_page_timer.inc.php';
?>

        YAHOO.util.Event.onDOMReady(ilios.dom.buildDialogPanel, {
            trigger: 'add_new_program',
            target: "program_add_picked",
            hidden: "new_program_hidden",
            container: "program_add_dialog",
            panel_width: "424px",
            display_handler: ilios.pm.clearProgramAddDialogContents
        });

        YAHOO.util.Event.onDOMReady(ilios.pm.assembleArchivingDialog, { });

        YAHOO.util.Event.onDOMReady(function () {
            ilios.ui.onIliosEvent.subscribe(ilios.pm.newProgramCreationResponseHandler);
        });

        YAHOO.util.Event.onDOMReady(ilios.pm.eot.assembleEditObjectiveTextDialog, {
            display_handler: ilios.pm.resetEditObjectiveTextDialog,
            dom_root: 'edit_objective_text_dialog'
        });

        YAHOO.util.Event.onDOMReady(ilios.pm.registerProgramUIListeners);
<?php
    if ($program_row['program_id'] != '') :
?>
        // @private
        ilios.pm.startProgramLoad = function () {
            ilios.pm.populateProgramAndSetEnable('<?php echo fullyEscapedText($program_row['title']); ?>',
                '<?php echo fullyEscapedText($program_row['short_title']); ?>',
                '<?php echo $program_row['duration']; ?>',
                '<?php echo $program_row['program_id']; ?>',
                <?php echo ($program_row['publish_event_id'] != '' ? $program_row['publish_event_id'] : 'null'); ?>,
                true, true, false);
        };
        YAHOO.util.Event.onDOMReady(ilios.pm.startProgramLoad);
<?php
    endif;
?>
        //ilios.ui.onIliosEvent.subscribe(ilios.pm.newProgramCreationResponseHandler);
        window.onbeforeunload = ilios.pm.windowWillClose;

    </script>
</body>
</html>
