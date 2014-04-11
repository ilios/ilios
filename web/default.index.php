<?php
/**
 * The is the default landing page for the Ilios application.
 *
 * Customize the page's main content to suit the needs of your organization/institution.
 */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Ilios</title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="application/views/css/ilios-styles.css?iref=%%ILIOS_REVISION%%" media="all">
    <link rel="stylesheet" href="application/views/css/custom.css?iref=%%ILIOS_REVISION%%" media="all">
    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!--[if lt IE 9]>
    <script src="application/views/scripts/third_party/html5shiv.js"></script>
    <![endif]-->

</head>
<body class="welcome yui-skin-sam">
    <div id="wrapper">
        <header id="masthead" class="clearfix">
            <div class="inner">
                <div class="main-logo">
                    <img src="application/views/images/ilios-logo.png" alt="Ilios" width="84" height="42" />
                    <span>Version <?php include_once dirname(__FILE__) . '/version.php'; ?></span>
                </div>
                <nav id="utility">
                    <ul>
                        <li id="logout_link"><a class="tiny radius button" href="ilios.php/dashboard_controller">Login</a></li>
                    </ul>
                </nav>
            </div>
            <div id="viewbar" class="clearfix">
                <h1 id="view-current"></h1>
            </div>
        </header>
        <div id="main" role="main">
            <div id="content" class="align-c">
                <div class="margin-t">
                    <a href="ilios.php/dashboard_controller" class="button">Ilios Login</a>
                </div>

                <div class="help">
                    <h4 class="margin-0">Help</h4>
                    <ul class="no-bullets">
                        <li class="margin-b0">Medicine: <a href="mailto:iROCKET@ucsf.edu?subject=Ilios%20Project%20Help%20Request">iROCKET@ucsf.edu</a></li>
                        <li class="margin-b0">Pharmacy: <a href="mailto:EducationSOP@ucsf.edu?subject=Ilios%20Project%20Help%20Request">EducationSOP@ucsf.edu</a></li>
                        <li class="margin-b0">Dentistry: <a href="mailto:SODCLEHelp@ucsf.edu?subject=Ilios%20Project%20Help%20Request">SODCLEHelp@ucsf.edu</a></li>
                    </ul>
                </div>

                <div class="margin-t">
                    <a href="http://www.iliosproject.org/">About the Ilios Project</a>
                </div>
            </div><!--end #content-->
        </div><!--end #main-->
    </div> <!--end #wrapper-->
</body>
</html>
