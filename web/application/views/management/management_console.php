<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User admininstration page template.
 *
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
$controllerURL = site_url() . '/management_console'; // TODO: consider how to avoid this coupling
$dashboardControllerURL = site_url() . '/dashboard_controller';
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
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/management_console.css"); ?>" media="screen">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <style type="text/css">
        #permissions_autolist .yui-ac-content { width: 413px !important; margin-left: 3px; }
        .ygtvlabel { background-color: transparent; }
        .yui-skin-sam .yui-ac-content li { cursor: pointer; }
        .ygtv-highlight1 { color: #880000; font-weight: bold; }
    </style>

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!--[if lt IE 9]>
    <script src="<?php echo $viewsUrlRoot; ?>scripts/third_party/html5shiv.js"></script>
    <![endif]-->

    <!-- Third party JS -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/date_formatter.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!-- Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_alert.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_preferences.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_timer.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/";    // expose this to our *.js

        ilios.namespace('management');        // assure the existence of this page's namespace

        ilios.management.cohortlessUserCount = <?php echo $cohortless_user_count; ?>;
        ilios.management.syncExceptionsCount = <?php echo $users_with_sync_exceptions_count; ?>;
        ilios.management.schoolTree = YAHOO.lang.JSON.parse('<?php echo $school_tree; ?>');
        ilios.management.schoolCohorts= [];
       </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/program_cohort_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/school_program_cohort_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/user_management_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/management_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/management_permissions.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/management_transaction.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/management_user_accounts.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/permission_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/program_cohort_dom.js"); ?>"></script>
    <script type="text/javascript">
            ilios.management.user_accounts.manageLoginCredentials = <?php echo ($manage_login_credentials ? "true" : "false"); ?>;
            ilios.management.user_accounts.passwordRequired = <?php echo ($password_required ? "true" : "false"); ?>;
            YAHOO.util.Event.onDOMReady(ilios.management.user_accounts.startUserAccountsWorkflow);
    </script>
    <?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</head>
<body class="admin yui-skin-sam">
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
                <h2 class="page-header">Management Console</h2>
                <div class="content_container">
                    <div class="column primary clearfix">
                        <div style="position: absolute; top: 24px; right: 9px;">
<?php echo generateProgressDivMarkup(); ?>
                            <div class="clear"></div>
                        </div>
                        <div id="management_center_content">
                            <div id="temporary_content_which_will_get_nuked" style="height: 580px;"></div>
                        </div>
                    </div>

                    <div class="column secondary">
                        <div class="dashboard_widget">
                            <div class="hd toggle collapse" onclick="ilios.dom.toggleWidget(this);">
                                <h3 class="dashboard_widget_title"><?php echo $widget_title; ?></h3>
                            </div>
                            <div class="widget_collapse_content bd" id="options_widget_content">
                                <ul id="option_links_list">
                                    <li id="permissions_li">
                                        <a href="#"
                                            onclick="ilios.management.permissions.startPermissionsWorkflow(); return false;">
                                        <?php echo $permissions_str; ?></a>
                                    </li>
                                    <li id="users_li">
                                        <a href="#"
                                            onclick="ilios.management.user_accounts.startUserAccountsWorkflow(); return false;">
                                            <?php echo $manage_users_str; ?></a>
                                    </li>
    <!--
                                    <li id="passwords_li"><a href="" onclick="return false;"><?php echo $manage_passwords_str; ?></a></li>
                                    <li id="emails_li"><a href="" onclick="return false;"><?php echo $emails_str; ?></a></li>
                                    <li id="system_preferences_li"><a href="" onclick="return false;"><?php echo $system_preferences_str; ?></a></li>
                                    <li id="data_lists_li"><a href="" onclick="return false;"><?php echo $data_lists_str; ?></a></li>
    -->
                                    <li id="dashboard_return_li" style="margin-top: 9px;">
                                        <a href="<?php echo $dashboardControllerURL; ?>">
                                            <?php echo $dashboard_return_str; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
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
<?php
    include 'course_picker_include.php';
    include 'program_picker_include.php';
    include 'school_picker_include.php';
    include 'user_account_details_include.php';
?>
    <div class="tabdialog" id="cohort_pick_dialog"></div>
<?php if ($manage_login_credentials) : ?>
    <!-- edit login credentials dialog -->
    <div class="tabdialog" id="edit_login_credentials_dialog">
        <div class="hd">Update Login Credentials</div>
        <div class="bd">
            <form action="#">
                <label for="ua_edit_login_username_tf">Login Name</label>:<br />
                <input id="ua_edit_login_username_tf" name="ua_edit_login_username_tf" type="text" value="" /><br />
        <?php if ($password_required) : ?>
                <label for="ua_edit_login_password_tf">New Password</label>:<br />
                <input id="ua_edit_login_password_tf" name="ua_edit_login_password_tf" type="password" value="" />
                <div class="small"><?php echo t('management.user_accounts.password_strength_requirements'); ?></div>
        <?php endif; ?>
            </form>
        </div>
        <div class="ft"></div>
    </div>
    <!-- add login credentials dialog -->
    <div class="tabdialog" id="add_login_credentials_dialog">
        <div class="hd">Add Login Credentials</div>
        <div class="bd">
            <form action="#">
                <label for="ua_add_login_username_tf">Login Name</label>:<br />
                <input id="ua_add_login_username_tf" name="ua_add_login_username_tf" type="text" value="" /><br />
            <?php if ($password_required) : ?>
                <label for="ua_add_login_password_tf">Password</label>:<br />
                <input id="ua_add_login_password_tf" name="ua_add_login_password_tf" type="password" value="" />
                <div class="small"><?php echo t('management.user_accounts.password_strength_requirements'); ?></div>
            <?php endif; ?>
            </form>
        </div>
        <div class="ft"></div>
    </div>
<?php endif; ?>
</div>
<?php if ($manage_login_credentials) : ?>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/edit_login_credentials_dialog.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "management/add_login_credentials_dialog.js"); ?>"></script>
<?php endif; ?>
    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });

        // load school cohorts
        YAHOO.util.Event.onDOMReady(function () {
            var o;
            var jsonStr = '<?php echo $cohorts_json; ?>';

            try {
                o = YAHOO.lang.JSON.parse(jsonStr);
            } catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);
                return;
            }

            if (o !== undefined) {
                ilios.management.schoolCohorts= o;
            }
        });

        YAHOO.util.Event.onDOMReady(ilios.dom.generateGenericDialogMarkupAndWireContent, {
            trigger : 'find_cohort_and_program',
            indeterminate_loading_id : 'steward_indeterminate_div',
            display_handler : ilios.management.user_accounts.secondaryProgramCohortSelectionDialogDisplay,
            hide_autocomplete_input : 'value matters not',
            submit_override : ilios.management.user_accounts.handleProgramCohortDialogSubmit,
            widget_dom_generator : ilios.management.user_accounts.programCohortDialogTreeDOMGenerator,
            tab_title : ilios_i18nVendor.getI18NString('general.phrases.select_cohort'),
            id_uniquer : 'mcps_',
            panel_title_text : ilios_i18nVendor.getI18NString('general.terms.secondary_cohorts'),
            dom_root : 'cohort_pick_dialog',
            deselect_handler : ilios.management.user_accounts.handleProgramCohortDialogDeselection,
            max_displayed_results: 250,
            panel_width: '700px'
        });

<?php if ($manage_login_credentials) : ?>

        // register the add/edit login credential dialogs with the pagewide event registry
        YAHOO.util.Event.onDOMReady(function(type, args, obj) {
            IEvent.subscribe(function (type, args) {
                if ('elc_dialog_open' === args[0].action) {
                    if (! ilios.management.user_accounts.editLoginCredentialsDialog) {
                        ilios.management.user_accounts.editLoginCredentialsDialog
                            = new ilios.management.user_accounts.widget.EditLoginCredentialsDialog('edit_login_credentials_dialog');
                        ilios.management.user_accounts.editLoginCredentialsDialog.render();
                    }
                    ilios.management.user_accounts.editLoginCredentialsDialog.setUserModel(args[0].model);
                    ilios.management.user_accounts.editLoginCredentialsDialog.center();
                    ilios.management.user_accounts.editLoginCredentialsDialog.show();
                } else if ('alc_dialog_open' === args[0].action) {
                    if (! ilios.management.user_accounts.addLoginCredentialsDialog) {
                        ilios.management.user_accounts.addLoginCredentialsDialog
                            = new ilios.management.user_accounts.widget.AddLoginCredentialsDialog('add_login_credentials_dialog');
                        ilios.management.user_accounts.addLoginCredentialsDialog.render();
                    }
                    ilios.management.user_accounts.addLoginCredentialsDialog.setUserModel(args[0].model);
                    ilios.management.user_accounts.addLoginCredentialsDialog.center();
                    ilios.management.user_accounts.addLoginCredentialsDialog.show();
                }
            });
        });


<?php endif; ?>

    </script>
</body>
</html>
