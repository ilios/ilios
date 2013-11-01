<?php
/**
 * The is the default landing page for the Ilios application.
 *
 * Customize the page's main content to suit the needs of your organization/institution.
 */
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

    <title>Ilios</title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->
    <link rel="stylesheet" href="application/views/css/ilios-styles.css?iref=%%ILIOS_REVISION%%" media="all">
    <link rel="stylesheet" href="application/views/css/custom.css?iref=%%ILIOS_REVISION%%" media="all">
    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!-- All JavaScript at the bottom, except this Modernizr build.
         Modernizr enables HTML5 elements & feature detects for optimal performance.
         Create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script type="text/javascript" src="application/views/scripts/third_party/modernizr-2.5.3.min.js"></script>
</head>
<body class="welcome yui-skin-sam">
    <div id="wrapper">
        <header id="masthead" class="clearfix">
            <div class="inner">
<div class="main-logo">
    <img src="application/views/images/ilios-logo.png" alt="Ilios" width="84" height="42" />
    <span>Version <?php include_once dirname(__FILE__) . '/version.php'; ?></span>
</div>                 <nav id="utility">
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
            <div id="content">
                <div style="margin-top: 50px; text-align: center;">
                    <a href="ilios.php/dashboard_controller">Enter the UCSF Ilios Application (log in required)</a>
                </div>

                <div style="margin-top: 96px; text-align: center;">
                    <a href="http://www.iliosproject.org/">Go to the Ilios Project Website</a>
                </div>

                <div style="margin-top: 128px; text-align: center;">
                           For assistance, please contact your school's Ilios administrator.<br/><br/>
                           <strong>School of Medicine:</strong> <a href="mailto:irocket@ucsf.edu?subject=Ilios Project Help Request">irocket@ucsf.edu</a><br/><br/>
                           <strong>School of Pharmacy:</strong> <a href="mailto:EducationSOP@ucsf.edu?subject=Ilios Project Help Request">EducationSOP@ucsf.edu</a><br/><br/>
                           <strong>School of Dentistry:</strong> <a href="mailto:SODCLEHelp@ucsf.edu?subject=Ilios Project Help Request">SODCLEHelp@ucsf.edu</a><br/>
                </div>
            </div><!--end #content-->
        </div><!--end #main-->

        <div style="position: relative; width: 100%; bottom: 1em; top: 8em; text-align: center;">
            <div>
                    <span style="margin-right: 18px;"><a href="http://www.iliosproject.org/" >About us</a></span>
                    <span style="margin-right: 18px;"><a href="http://www.iliosproject.org/about" >Learn More</a></span>
                    <span style="margin-right: 18px;"><a href="http://github.com/ilios/ilios" >Collaborate</a></span>
            </div>
            <div style="margin-top: 66px; font-size: 8pt;">
                Copyright &copy; 2011 - <?php echo date('Y'); ?> by the Regents of the University of California. Distributed under the GNU GPL 3.0 license. For more information see <a href="http://opensource.org/licenses/gpl-3.0.html">here.</a>
            </div>
        </div>
    </div> <!--end #wrapper-->
    <footer>
    <!-- reserve for later use -->
    </footer>
</body>
</html>
