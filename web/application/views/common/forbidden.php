<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * "Forbidden" page template.
 *
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

    $viewsUrlRoot = getViewsURLRoot();
    $viewsPath = getServerFilePath('views');

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title><?php echo $forbidden_warning_text; ?></title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/ilios-styles.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!--[if lt IE 9]>
    <script src="<?php echo $viewsUrlRoot; ?>scripts/third_party/html5shiv.js"></script>
    <![endif]-->
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
                <?php echo $forbidden_warning_text; ?>
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>
    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>
</body>
</html>
