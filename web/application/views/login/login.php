<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Login page template.
 *
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
$controllerURL = site_url() . '/authentication_controller'; // TODO: consider how to avoid this coupling
$dashboardControllerUrl = site_url() . '/dashboard_controller';
$viewsUrlRoot = getViewsURLRoot();
$viewsPath = getServerFilePath('views');

?><!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title><?php echo $login_title; ?></title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/ilios-styles.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <!-- Third party JS -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>

    <!-- Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/";                 // expose this to our *.js
        var dashboardControllerUrl = "<?php echo $dashboardControllerUrl; ?>";
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "login/login_transaction.js"); ?>"></script>
</head>
<body class="yui-skin-sam">
    <div id="wrapper">
        <header id="masthead" class="clearfix">
            <div class="inner">
<?php
    include_once $viewsPath . 'common/masthead_logo.inc.php';
    include_once $viewsPath . 'common/masthead_toolbar.inc.php';
?>
           </div>
<?php include_once $viewsPath . 'common/masthead_viewbar.inc.php'; ?>
        </header>
        <div id="main" role="main">
            <div id="content" class="clearfix">

                <div style="font-size: 16pt; margin-top: 12px; margin-bottom: 12px; position: relative;">
                    <center id="login_status_message"><?php echo $login_message; ?></center>
                </div>

                <div id="login_panel_div"
                     style="margin: auto;  padding: 0.5em; width: 17em;
                             background-color: #696B61; color: #FCF8E2; border: 1px solid #3A325A;">
                    <label for="user_name"><?php echo $word_username; ?></label>
                    <input type="text" id="user_name" name="user_name" value=""
                            style="margin-right: 2px; float: right; width: 160px;"
                            onkeypress="return handleUserNameFieldInput(this, event);"/>
                    <div style="height: 9px;" class="clear"></div>
                    <label for="password"><?php echo $word_password; ?></label>
                    <input type="password" id="password" name="password" value=""
                            style="margin-right: 2px; float: right; width: 160px;"
                            onkeypress="return handlePasswordFieldInput(this, event);"/>
                    <div style="height: 6px;" class="clear"></div>
                    <button id="login_button" style="margin-right: 9px; float: right;"
                            onclick="attemptLogin(); return false;"><?php echo $word_login; ?></button>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>

    <footer>
    <!-- reserve for later use -->
    </footer>
    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>

    <script type="text/javascript">
        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });

        function handleUserNameFieldInput (inputField, event) {
            var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
            if (charCode == 13) {
                var passwordField = document.getElementById('password');
                passwordField.focus();
                event.cancelBubble = true;
                event.returnValue = false;
                return false;
            }
            return true;
        }

        function handlePasswordFieldInput (inputField, event) {
            var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
            if (charCode == 13) {
                var button = document.getElementById('login_button');
                button.click();
                event.cancelBubble = true;
                event.returnValue = false;
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
