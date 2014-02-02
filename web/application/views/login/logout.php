<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * Logout page template.
 *
 * The purpose of this page is to land somewhere within our application hierarchy to allow
 * CodeIgniter to cleanly clear out session variables before jumping to the logout redirects.
 * Google for CodeIgniter, session variables, and redirects to read of other people's unluck
 * in this area.
 *
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

    $redirectLocation = $this->config->item("ilios_authentication_shibboleth_logout_path");
    $viewsUrlRoot = getViewsURLRoot();
    $viewsPath = getServerFilePath('views');

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Ilios Logout Stepping Stone</title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/ilios-styles.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>
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
                <div style="position: relative; width: 420px; margin-left: auto;
                    margin-right: auto; margin-top: 170px; font-size: 14pt;">
                    <div class="indeterminate_progress" style="float: left;"></div>
                    <div style="float: right;"><?php echo $logout_in_progress; ?></div>
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
        function redirectToLogout () {
            window.location.href = "<?php echo $redirectLocation; ?>";
        }

        function startRedirectTimer () {
            setTimeout('redirectToLogout()', 1300);
        }
        YAHOO.util.Event.onDOMReady(startRedirectTimer);
    </script>
</body>
</html>
