<?php
/**
 * @file calendar_filters_dialog.inc.php
 *
 * Prints the markup for the calendar filters dialog widget.
 *
 * Available template variables:
 *
 *      $calendar_filters_data ... An associative array containing the filtering options, the dialog title etc.
 *
 * @todo refactor date calculation stuff outta here. [ST 2013/11/30]
 * @todo clean up this godawful mess, e.g. move inline CSS into stylesheet, translate hardwired labels etc. [ST 2013/11/30]
 */
?>
<div class="tabdialog" id="calendar_filters_dialog">
    <div class="hd"><?php echo $calendar_filters_data['calendar_filters_title']; ?></div>
    <div class="bd">
        <form method="GET" action="#">
            <div>
                <span style="float: right; padding-left: 5px;">
                    <?php echo $calendar_filters_data['academic_year_title']; ?>&nbsp;
<?php
    $current_year = date("Y");
    // If current month is before September, set last academic year as default.  We should probably make this
    // customizable, so that each school can define its own academic year cut-off date.
    if (date("m") < 9) {
        $current_year--;
    }
    $academic_years_array = range($current_year - 5, $current_year + 1);
?>
                    <select id="calendar_filters_academic_year_select" style="width: 100px;">
<?php
    foreach ($academic_years_array as $year) :
?>
                        <option value="<?php echo $year; ?>"<?php if ($current_year == $year) : ?> selected="selected"<?php endif; ?>>
                            <?php echo $year; ?>-<?php echo ($year + 1); ?>
                        </option>
<?php
    endforeach;
?>
                    </select>
                </span>
                <!-- Search by topic / course toggle -->
                <span style="float: right;">
                    <a id="search_by_course_toggle" href="#"><?php echo $calendar_filters_data['search_by_course_text']; ?></a>
                    <a id="search_by_topic_toggle" href="#" style="display: none;"><?php echo $calendar_filters_data['search_by_topic_text']; ?></a>
                </span>
                <div class="clear"></div>
            </div>
            <div>
                <div style="border-style: solid; border-width: medium thin thin; border-color: chocolate grey grey;
                    margin-top: 8px; padding: 5px; background-color: AliceBlue;">
                    <!-- Generate 'Search by Topic/Detail' panel (div) -->
                    <div id="search_by_topic_panel">
                        Show Topic:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>
                        <div id="calendar_filters_topic_list" class="calendar_filters_checkbox_list" style="height: 5em;">
                            <?php echo generateCheckboxElementsFromArray($calendar_filters_data['discipline_titles']); ?>
                        </div><br />

                        Show Session Type:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>';
                        <div id="calendar_filters_topic_session_type_list" class="calendar_filters_checkbox_list" style="height:5em;">
                            <?php echo generateCheckboxElementsFromArray($calendar_filters_data['session_type_titles']); ?>
                        </div><br />

                        Show Course Level:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>
                        <div id="calendar_filters_course_level_list" class="calendar_filters_checkbox_list" style="height:5em;">
                            <?php echo generateCheckboxElementsFromArray($calendar_filters_data['course_levels']); ?>
                        </div><br />

                        Show Program / Cohort:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>
                        <div id="calendar_filters_program_cohort_list" class="calendar_filters_checkbox_list" style="height:5em;">
                            <?php echo generateCheckboxElementsFromArray($calendar_filters_data['program_cohort_titles']); ?>
                        </div><br />
                     </div>

                    <!-- Generate 'Search by Course' panel (div) -->
                    <div id="search_by_course_panel" style="display: none;">
<?php
    // Since course titles are not unique, we will use the whole string instead of the corresponding course id.
    $course_array = array_values(array_unique($calendar_filters_data['course_titles']));
?>
                        Show Course:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>
                        <div id="calendar_filters_course_list" class="calendar_filters_checkbox_list">
                            <?php echo empty($course_array) ? '' : generateCheckboxElementsFromArray(array_combine($course_array, $course_array)); ?>
                        </div><br />

                        Show Session Type:
                        <span style="float: right;">
                            <a class="select_all_toggle" href="javascript:void(0);" >select all + </a>
                            <a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>
                        </span>
                        <div id="calendar_filters_course_session_type_list" class="calendar_filters_checkbox_list" style="height: 10em;">
                            <?php echo generateCheckboxElementsFromArray($calendar_filters_data['session_type_titles']); ?>
                        </div><br />
                    </div>
                </div>
            </div>

            <div style="padding: 10px 0; overflow:hidden;">
                <div style="float:left">
                    <input id="calendar_filters_showmyactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" />
                    <label id="calendar_filters_showmyactivities_label" for="calendar_filters_showmyactivities">show my schedule only </label><br />
                    <input id="calendar_filters_showallactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" checked />
                    <label id="calendar_filters_showallactivities_label" for="calendar_filters_showallactivities" style="font-weight:bold;" >show all activities for selection</label>
                </div>
             </div>
        </form>
    </div>
    <div class="ft"></div>
</div>
