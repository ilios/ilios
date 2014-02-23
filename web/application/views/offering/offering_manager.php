<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Offering management page template.
 */

$siteUrl = site_url();
$baseUrl = base_url();
$controllerURL = site_url() . '/offering_management'; // TODO: consider how to avoid this coupling
$courseControllerURL = site_url() . '/course_management';
$learningMaterialsControllerURL = site_url() . '/learning_materials';
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
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/session-types.css"); ?>" media="all">
    <link rel="stylesheet" href="<?php echo appendRevision($viewsUrlRoot . "css/custom.css"); ?>" media="all">

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!--[if lt IE 9]>
    <script src="<?php echo $viewsUrlRoot; ?>scripts/third_party/html5shiv.js"></script>
    <![endif]-->

    <!-- Third party JS -->
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/yui_kitchensink.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/date_formatter.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/md5-min.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/dhtmlx_scheduler/codebase/dhtmlxscheduler.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_recurring.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/dhtmlx_scheduler/codebase/ext/dhtmlxscheduler_week_agenda.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/third_party/idle-timer.js"); ?>"></script>

    <!-- Ilios JS -->
    <script type="text/javascript" src="<?php echo $controllerURL; ?>/getI18NJavascriptVendor"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_base.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_alert.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_preferences.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_utilities.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_ui.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_timer.js"); ?>"></script>
    <script type="text/javascript">
        var controllerURL = "<?php echo $controllerURL; ?>/";    // expose this to our javascript land
        var courseControllerURL = "<?php echo $courseControllerURL; ?>";    // similarly...
        var learningMaterialsControllerURL = "<?php echo $learningMaterialsControllerURL; ?>/";    // ...
        var parentCourseId = "<?php echo $course_id; ?>";                    // ...

        ilios.namespace('om');            // assure the existence of this page's namespace
    </script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/abstract_js_model_form.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/school_competency_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/discipline_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/course_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/independent_learning_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/learning_material_model.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/models/mesh_item_model.js"); ?>"></script>
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
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/learner_view_base_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "scripts/public_course_summary_base_framework.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "offering/offering_manager_calendar_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "offering/offering_manager_inspector_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "offering/offering_manager_lightbox_support.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "offering/offering_manager_dom.js"); ?>"></script>
    <script type="text/javascript" src="<?php echo appendRevision($viewsUrlRoot . "offering/offering_manager_transaction.js"); ?>"></script>
    <?php include_once $viewsPath . 'common/start_idle_page_timer.inc.php'; ?>
</head>

<body class="offerings yui-skin-sam">
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
                <h2 class="page-header"><?php echo $title_bar_string; ?><span id="page_title"></span></h2>
                <div class="content_container">
                    <div class="column primary clearfix">
                        <h3>
                            <a href="<?php echo $courseControllerURL; ?>?course_id=<?php echo $course_id; ?>&session_id=<?php echo $session_id; ?>">
                                <?php echo $word_course_string; ?>:
                                <?php echo $course_title; ?> -
                                <?php echo $word_session_string; ?>:
                                <?php echo $session_model->title; ?>
                            </a>
                        </h3>
                        <div class="calendar_tools clearfix">
<?php echo generateProgressDivMarkup(); ?>
                        </div>
                        <div class="calendar-filters clearfix">
                            <ul>
                                <li>
                                    <label>
                                        <input type="radio" id="show_all_events_radio" value="all_events" name="event_type_radio" />
                                        <?php echo $show_all_events_string; ?>
                                    </label>
                                    <label>
                                        <input type="radio" id="show_sessions_radio" value="sessions" name="event_type_radio" checked="checked" />
                                        <?php echo $show_session_events_string; ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" id="show_busy_instructors_checkbox" value="busy_instructors" />
                                        <?php echo $show_busy_instructors_string; ?>
                                    </label>
                                    <label>
                                        <input type="checkbox" id="show_busy_students_checkbox" value="busy_students" />
                                        <?php echo $show_busy_students_string; ?>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div id="calendar-container" class="clearfix">
                            <div id="dhtmlx_scheduler_container" class="dhx_cal_container">
                                <div class="dhx_cal_navline">
                                    <div class="dhx_cal_prev_button">&nbsp;</div>
                                    <div class="dhx_cal_next_button">&nbsp;</div>
                                    <div class="dhx_cal_today_button"></div>
                                    <div class="dhx_cal_date"></div>
                                    <div class="dhx_cal_tab day_tab" name="day_tab"></div>
                                    <div class="dhx_cal_tab week_tab" name="week_tab"></div>
                                    <div class="dhx_cal_tab month_tab" name="month_tab"></div>
                                    <div class="dhx_cal_tab week_agenda_tab" name="week_agenda_tab"></div>
                                </div>
                                <div class="dhx_cal_header"></div>
                                <div class="dhx_cal_data" id="dhx_cal_data"></div>
                            </div>
                        </div>
                        <div id="offering_summary_table_div" class="offering_summary_management_table clearfix"></div>
                    </div><!-- end .primary.column -->
                    <div class="column secondary clearfix">
                            <?php
                                include 'offering_inspector_pane.php';
                            ?>
                    </div>
                </div><!-- content_container close -->
            </div>
        </div>
    </div>
    <footer>
    <!-- reserve for later use -->
    </footer>

    <!-- overlays at the bottom - avoid z-index issues -->
    <div id="view-menu"></div>

<?php
    include 'calendar_lightbox_include.php';
    include $viewsPath . 'common/course_summary_view_include.php';
    include 'learner_view_dialog.php';
?>
    <script type="text/javascript">

        // register alert/inform overrides on window load
        YAHOO.util.Event.on(window, 'load', function() {
            window.alert = ilios.alert.alert;
            window.inform = ilios.alert.inform;
        });

        ilios.preferences.installPreferencesModel();

<?php
    include_once $viewsPath . 'common/load_school_competencies.inc.php';
?>

        YAHOO.util.Event.onDOMReady(ilios.om.calendar.initCalendar);
        YAHOO.util.Event.onDOMReady(ilios.om.lightbox.buildLightboxDOM);
        // YAHOO.util.Event.onDOMReady(ilios.om.buildSessionTypeLegend);
        YAHOO.util.Event.onDOMReady(ilios.om.inspector.initializeInspectorPane);
        YAHOO.util.Event.onDOMReady(ilios.om.registerOfferingUIListeners);
        YAHOO.util.Event.onDOMReady(ilios.om.learner.assembleLearnerViewDialog);
        YAHOO.util.Event.onDOMReady(ilios.om.transaction.loadAllOfferings);

        ilios.om.calendarStartDate =
            ilios.utilities.mySQLDateToDateObject("<?php echo $calendar_start_date; ?> 00:00:00", true);

        // relies on date_formatter.js in the scripts/third_party directory
        ilios.om.dataTableDateFormatter = function (element, record, column, data) {
            if ((data == null) || (! (data instanceof Date))) {
                data = record.getData('date');
            }

            if (data != null) {
                var id = record.getData('id');
                var html = '<a href="" ';
                html += 'onclick="ilios.om.calendar.focusCalendarOnStartDateOfOfferingWithId('
                    + id + '); return false;">' + data.format('ddd mmm d yyyy') + '</a>';
                element.innerHTML = html;
            }
        };

        ilios.om.offeringTableColumnDefinitions = [
            {
                key: "date",
                label: "<?php echo $word_date_string; ?>",
                formatter: ilios.om.dataTableDateFormatter,
                sortable: true,
                resizeable: true
            },
            {
                key: "time",
                label: "<?php echo $phrase_time_range_string; ?>",
                sortable: true,
                resizeable: true
            },
            {
                key: "group",
                label: "<?php echo $phrase_student_group_string; ?>",
                sortable: true,
                resizeable: true
            },
            {
                key: "instructor",
                label: "<?php echo $word_instructors_indefinite_string; ?>",
                sortable: true,
                resizeable: true
            },
            {
                key: "location",
                label: "<?php echo $word_room_string; ?>",
                sortable: true,
                resizeable: true
            },
            {
                key: "status",
                label: "<?php echo $word_status_string; ?>",
                sortable: true,
                resizeable: true
            }
        ];

        ilios.om.offeringDataTable = null;


        YAHOO.util.Event.addListener(window, "load", function() {
            var dataSource = new YAHOO.util.FunctionDataSource(ilios.om.getOfferingSummaryTableData);

            dataSource.responseType = YAHOO.util.XHRDataSource.TYPE_JSARRAY;
            dataSource.responseSchema = {fields: ["date", "time", "group", "instructor", "location", "status", "id"]};

            ilios.om.offeringDataTable = new YAHOO.widget.DataTable("offering_summary_table_div",
                ilios.om.offeringTableColumnDefinitions, dataSource, {});
        });

        ilios.om.loadedSessionTypes = {};

        var sessionTypeModel = null;

<?php
    foreach ($session_type_array as $sessionType) :
?>
        sessionTypeModel = {};
        sessionTypeModel.dbId = <?php echo $sessionType['session_type_id']; ?>;
        sessionTypeModel.title = '<?php echo fullyEscapedText($sessionType['title']); ?>';
        sessionTypeModel.sessionTypeCssClass = '<?php echo $sessionType['session_type_css_class']; ?>';
        sessionTypeModel.iconURL = '';
        ilios.om.loadedSessionTypes[sessionTypeModel.dbId] = sessionTypeModel;
<?php
    endforeach;
    generateJavascriptRepresentationCodeOfPHPArray($session_model, 'dbObjectRepresentation');
?>
        // NOTE THAT THIS GENERATES AN INCOMPLETE VERSION OF THE MODEL - DO NOT SAVE THIS
        // MODEL VIA THE COURSE CONTROLLER LEST YOU LOSE CROSS TABLE ASSOCIATIONS
        var sessionModel = new SessionModel(dbObjectRepresentation);

        var meaninglessCounter = 1;
<?php
    foreach ($objectives as $objectiveText) :
?>
        dbObjectRepresentation = new ObjectiveModel();
        dbObjectRepresentation.setDescription('<?php echo fullyEscapedText($objectiveText); ?>');
        sessionModel.addObjectiveForContainer(dbObjectRepresentation, meaninglessCounter);
        meaninglessCounter++;

<?php
    endforeach;

    foreach ($learning_materials as $lmModel) :
?>
        dbObjectRepresentation = new LearningMaterialModel();
        dbObjectRepresentation.setDBId(<?php echo $lmModel['learning_material_id']; ?>);
        dbObjectRepresentation.setTitle("<?php echo fullyEscapedText($lmModel['title']); ?>");
        dbObjectRepresentation.setMimeType("<?php echo $lmModel['mime_type']; ?>");
        dbObjectRepresentation.setOwningUserName("<?php echo fullyEscapedText($lmModel['owning_user_name']); ?>");
        dbObjectRepresentation.setOwningUserId(<?php echo $lmModel['owning_user_id']; ?>);
        dbObjectRepresentation.setFilename("<?php echo fullyEscapedText($lmModel['filename']); ?>");
        dbObjectRepresentation.setFileSize(<?php echo $lmModel['filesize']; ?>);
        dbObjectRepresentation.setDescription("<?php echo fullyEscapedText($lmModel['description']); ?>");
        dbObjectRepresentation.setStatusId(<?php echo $lmModel['status_id']; ?>);
        dbObjectRepresentation.setOwnerRoleId(<?php echo $lmModel['owner_role_id']; ?>);
        sessionModel.addLearningMaterial(dbObjectRepresentation);
<?php
    endforeach;
    foreach ($mesh_terms as $meshTerm) :
?>
        dbObjectRepresentation = new MeSHItemModel();
        dbObjectRepresentation.setTitle("<?php echo fullyEscapedText($meshTerm); ?>");
        sessionModel.addMeSHItem(dbObjectRepresentation);

<?php
    endforeach;
?>
    var groupModel = null;

<?php
    /**
     * Recursive function that generates a tree structure of group/sub-group model objects in JavaScript.
     * @todo get this outta here
     */
    function recursivelyWriteTreeGroupJavascript ($group, $parentGroupVariableName, $jsVariableName)
    {
        $thisLevelCounter = 1;

        $subgroups = $group['subgroups'];
        $pgid = $group['parent_group_id'];

        if (($pgid == '') || ($pgid < 1)) {
            $pgid = -1;
        }

        echo "
            var " . $jsVariableName . " = new SimplifiedGroupModel('" . fullyEscapedText($group['title'])
                . "', " . $pgid . ", " . $group['group_id'] . ");
        ";

        if ($parentGroupVariableName != null) {
            echo "

                " . $parentGroupVariableName . ".addSubgroup(" . $jsVariableName . ");
                ";
        }

        foreach ($subgroups as $subgroup) {
            recursivelyWriteTreeGroupJavascript($subgroup, $jsVariableName, ($jsVariableName . '_' . $thisLevelCounter));
            $thisLevelCounter++;
        }
    }

    $topLevelCounter = 1;
    foreach ($student_groups as $program) {
        $groupCounter = 1;
        $topLevelVariableName = 'group_' . $topLevelCounter;
        echo "

            var " . $topLevelVariableName . " = {};

        " . $topLevelVariableName . ".program_title = \"" . fullyEscapedText($program['title']) . "\";
        " . $topLevelVariableName . ".groups = [];
        ";

        foreach ($program['groups'] as $topLevelGroup) {
            $groupVariableName = $topLevelVariableName . '_' . $groupCounter;

            recursivelyWriteTreeGroupJavascript($topLevelGroup, null, $groupVariableName);
            $groupCounter++;

            echo "
            " . $topLevelVariableName . ".groups.push(" . $groupVariableName . ");
            ";
        }

        echo "
            ilios.lg.picker.learnerTreeModel.push(" . $topLevelVariableName . ");
        ";

        $topLevelCounter++;
    }
?>
    </script>
</body>
</html>
