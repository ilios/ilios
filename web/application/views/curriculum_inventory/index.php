<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Curriculum Inventory Management page template.
 *
 * Available template variables:
 *
 *    $lang ... The language key.
 *    $payload ... A JSON-formatted string representation of the entire initial data payload.
 *    $reports ... An array of existing inventory reports.
 */
$controllerURL = site_url() . '/curriculum_inventory_manager';
$programManagerUrl = site_url() . '/program_management';
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

    <title><?php echo $this->languagemap->t('curriculum_inventory.title_bar', $lang) ?></title>
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
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/json2.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision(getYUILibrariesURL() . "event-simulate/event-simulate-min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!--  Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/preferences_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "curriculum_inventory/js/ilios.cim.js"); ?>"></script>
</head>
<body class="curriculum_inventory yui-skin-sam">
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
            <h2 class="page-header"><?php echo $this->languagemap->t('curriculum_inventory.page_header', $lang) ?></h2>
            <div class="master_button_container clearfix">
                <ul class="buttons left">
                    <li>
                        <a class="small radius button" href="" id="pick_reports_btn">
                            <?php echo $this->languagemap->t('curriculum_inventory.select_report', $lang); ?>
                        </a>
                    </li>
                    <li>
                        <a class="small secondary radius button" href="" id="create_report_btn">
                            <?php echo $this->languagemap->t('general.phrases.add_report', $lang); ?>
                         </a>
                    </li>
                </ul>
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
include 'create_report_dialog.inc.php';
include 'report_picker_dialog.inc.php';
?>
<!-- end dialog tabs -->
<script type="text/javascript">
    // register alert/inform overrides on window load
    YAHOO.util.Event.on(window, 'load', function() {
        window.alert = ilios.alert.alert;
        window.inform = ilios.alert.inform;
    });
    ilios.global.installPreferencesModel();

    YAHOO.util.Event.onDOMReady(function() {
        var payload;
        var config = {
            'controllerUrl': "<?php echo $controllerURL; ?>/",
            'programControllerUrl': "<?php echo $programManagerUrl; ?>/"
        };
        try {
            payload = YAHOO.lang.JSON.parse('<?php echo $payload; ?>');
        }  catch (e) {
            //crash and burn
            ilios.global.defaultAJAXFailureHandler(null, e);
            return;
        }
        var app = new ilios.cim.App(config, payload);
    });
    <?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</script>
</body>
</html>
