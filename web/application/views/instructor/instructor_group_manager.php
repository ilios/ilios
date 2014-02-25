<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Instructor groups management page template.
 */
$controllerURL = site_url() . '/instructor_group_management'; // TODO: consider how to avoid this coupling
$courseControllerURL = site_url() . '/course_management';
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
    <script type="text/javascript" src="<?php echo appendRevision(getYUILibrariesURL() . "event-simulate/event-simulate-min.js"); ?>"></script>
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
        var controllerURL = "<?php echo $controllerURL; ?>/"; // expose this to our javascript
        var courseControllerURL = "<?php echo $courseControllerURL; ?>"; // similarly...
        var schoolId = "<?php echo $school_id; ?>";    // similarly...
        ilios.namespace('igm');    // assure the existence of this page's namespace
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "instructor/instructor_group_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "instructor/instructor_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "instructor/instructor_transaction.js"); ?>"></script>
    <?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</head>

<body class="instructor yui-skin-sam">
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
                <div class="content_container">
                    <div class="entity_container">
                        <div class="hd">
                            <h3>
                                <script type="text/javascript">
                                    ilios_i18nVendor.write('', 'general.phrases.instructor_groups', '');
                                    ilios_i18nVendor.write(' - ', 'general.phrases.school_of', '');
                                </script> <?php echo $school_name; ?>
                            </h3>
                        </div>
<?php echo generateProgressDivMarkup('position: absolute; right: 1em; top: .6em;'); ?>
                    </div>
                    <div class="master_button_container clearfix">
                        <div class="add_primary_child_link">
                            <button class="small secondary radius button" onclick="ilios.igm.handleManualGroupAdd();" id="general_new_add_group_link">
                                <script type="text/javascript">
                                    ilios_i18nVendor.write('', 'instructor_groups.add_new_group', '');
                                </script>
                            </button>
                        </div>
                    </div>
                    <div id="group_container" class="clearfix"></div>
                    </div>

    </div></div></div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>

<?php
    include 'faculty_include.php';
    include 'add_new_members_include.php';
?>


    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });
        ilios.preferences.installPreferencesModel();

        ilios.igm.buildPreExistingGroupDivs = function (un, imp, ortant) {
            var groupJSONBlob = '<?php echo $groups_json; ?>';
            var groups = null;
            var container = document.getElementById('group_container');
            var i = 0;
            var len = 0;
            var groupModel = null;
            var YUserAction= YAHOO.util.UserAction;
            var idString = null;
            var element = null;
            try {
                groups = YAHOO.lang.JSON.parse(groupJSONBlob);
            } catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);
                return;
            }
            len = groups.length;
            if (len) {
                for (; i < len; i++) {
                    groupModel = ilios.igm.buildGroupModel(groups[i].instructor_group_id,
                        groups[i].title, groups[i].users);
                    groupModel.addStateChangeListener(ilios.igm.dirtyStateListener, null);

                    ilios.igm.instructorGroupModels[ilios.igm.nextContainerNumber] = groupModel;
                    ilios.igm.createGroupUI(container, ilios.igm.nextContainerNumber);

                    ilios.dom.collapseChildForContainerNumber(ilios.igm.nextContainerNumber, 0,ilios.igm.handleGroupDivCollapse);
                    ilios.igm.nextContainerNumber++;
                }
            } else {
                // hide the loading spinner
                ilios.alert.updateServerInteractionProgress();
            }
        };
        YAHOO.util.Event.onDOMReady(ilios.igm.buildPreExistingGroupDivs);

        window.onbeforeunload = ilios.igm.windowWillClose;
    </script>

</body>
</html>
