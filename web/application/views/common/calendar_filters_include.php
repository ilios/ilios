<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file calendar_filters_include.php
 *
 * An assortment of form-builder/templating functions for the search/filter widget on the calendar/dashboard pages.
 * @todo the only "real" helper function here is <code>generateCheckboxElementsFromArray</code>.
 * @todo Move this to the templating helper script, convert the rest of this mess into a template script.
 * @todo [ST 2013/11/27]
 */

/**
 * Generates and returns a HTML checkbox element plus corresponding label for each item in a given array.
 *
 * @param array $element_key_and_value_array An associative array, each item's key being used as the checkbox value and each item's value being used as the label text.
 * @return string The generated markup.
 */
function generateCheckboxElementsFromArray ($element_key_and_value_array) {

    $retval = "";

    if (!empty($element_key_and_value_array)) {
        foreach ($element_key_and_value_array as $key=>$value) {
            $retval .= '<input type="checkbox" value="'. htmlentities($key, ENT_COMPAT, 'UTF-8').'" />';
            $retval .= '<label>' . htmlentities($value, ENT_COMPAT, 'UTF-8') . '</label><br />';
        }
    }

    return $retval;
}


/**
 * Generates and returns an HTML form displaying given filtering options.
 *
 * @param array $filtersData An associative array containing the form content.
 * @param boolean $asDialog Set to TRUE generate markup for a dialog widget.
 * @return string The generated markup.
 */
function generateCalendarFiltersFormContent ($filtersData, $asDialog = false) {

    // Header Div
    $content = '<div class="hd">';
    if ($asDialog) {
        $content .= $filtersData['calendar_filters_title'];
    } else {
        $content .= '';
    }
    $content .= '</div>';

    // Body Div
    $content .= '<div class="bd">' . '<form method="GET" action="#">';

    $content .= '<div>';

    if (! $asDialog) {
        $content .= '<strong>' . $filtersData['calendar_filters_title'] . '</strong>';
    }

    // Generate drop down box for academic year
    $content .= '<span style="float: right; padding-left: 5px;">'.$filtersData['academic_year_title'].'&nbsp;';

    $current_year = date("Y");
    // If current month is before September, set last academic year as default.  We should probably make this
    // customizable, so that each school can define its own academic year cut-off date.
    if (date("m") < 9) {
        $current_year--;
    }
    $academic_years_array = range($current_year - 5, $current_year + 1);
    $content .= '<select id="calendar_filters_academic_year_select" style="width: 100px;">';
    foreach ($academic_years_array as $year) {
        $nextyear = $year + 1;
        $content .= '<option value="'.$year.'" ';
        if ($current_year == $year)
            $content .= 'selected="selected" ';
        $content .= '>'.$year."-".$nextyear."</option>\n";
    }
    $content .= '</select>';

    $content .= '</span>';

    // Search by topic / course toggle
    $content .= '<span style="float: right;">'
        . '<a id="search_by_course_toggle" href="#">'
        . $filtersData['search_by_course_text'] . '</a>'

        . '<a id="search_by_topic_toggle" href="#" style="display: none;">'
        . $filtersData['search_by_topic_text']. '</a>';
    $content .= '</span>';

    $content .= '<div class="clear"></div>';

    $content .= '</div>';

    $content .= '<div><div style="border-style: solid; border-width: medium thin thin; border-color: chocolate grey grey; margin-top: 8px; padding: 5px; background-color: AliceBlue;">';

    // Generate 'Search by Topic/Detail' panel (div)
    $content .= generateTopicFilters($filtersData);
    $content .= generateCourseFilters($filtersData);

    $content .= '</div></div>';

    $content .= <<<EOF
        <div style="padding: 10px 0; overflow:hidden;">
        <div style="float:left">
        <input id="calendar_filters_showmyactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" />
        <label id="calendar_filters_showmyactivities_label" for="calendar_filters_showmyactivities">show my schedule only </label><br />
        <input id="calendar_filters_showallactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" checked />
        <label id="calendar_filters_showallactivities_label" for="calendar_filters_showallactivities" style="font-weight:bold;" >show all activities for selection</label>
        </div>
EOF;
    if (!$asDialog) {
        $content .= '<div style="float:right;"> <br />'
            /* .       '<input id="calendar_filters_search_button" type="button" value="Search" />' */
            /* .       '<input id="calendar_filters_clear_button" type="button" value="Clear" />' */
            . '<span id="calendar_filters_button_group" class="yui-buttongroup">'
            . ' <span id="calendar_filters_search_button" class="yui-button">'
            . '   <span class="first-child">'
            . '     <button type="button">Search</button>'
            . '   </span>'
            . ' </span>'
            . ' <span id="calendar_filters_clear_button" class="yui-button">'
            . '   <span class="first-child">'
            . '     <button type="button">Clear</button>'
            . '   </span>'
            . ' </span>'
            . '</span>'
            .       '</div>';
    }
    $content .= '</div>';

    $content .= '</form>';
    $content .= '</div>';
    $content .= '<div class="ft"></div>';

    return $content;
}


/**
 * Generates and returns an HTML container for displaying the topic-related elements
 * of the search/filter form.
 *
 * @param array $filtersData An associative array containing the form content.
 * @return string The generated markup.
 */
function generateTopicFilters ($filtersData) {

    // Generate 'Search by Topic/Detail' panel (div)
    $content = '<div id="search_by_topic_panel">';

    $content .= 'Show Topic: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_topic_list" class="calendar_filters_checkbox_list" style="height: 5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['discipline_titles']);
    $content .= '</div><br />';

    $content .= 'Show Session Type: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_topic_session_type_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['session_type_titles']);
    $content .= '</div><br />';

    $content .= 'Show Course Level: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_level_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['course_levels']);
    $content .= '</div><br />';

    $content .= 'Show Program / Cohort: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_program_cohort_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['program_cohort_titles']);
    $content .= '</div><br />';

    $content .= '</div><!-- div-search_by_topic_panel -->';

    return $content;
}

/**
 * Generates and returns an HTML container for displaying the course-related elements
 * of the search/filter form.
 *
 * @param array $filtersData An associative array containing the form content.
 * @return string The generated markup.
 */
function generateCourseFilters ($filtersData) {

    // Generate 'Search by Course' panel (div)
    $content = '<div id="search_by_course_panel" style="display: none;">';

    // Since course titles are not unique, we will use the whole string instead of the corresponding course id.
    $course_array = array_values(array_unique($filtersData['course_titles']));

    $content .= 'Show Course: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_list" class="calendar_filters_checkbox_list">';
    $content .= empty($course_array) ? '' : generateCheckboxElementsFromArray(array_combine($course_array, $course_array));
    $content .= '</div><br />';

    $content .= 'Show Session Type: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_session_type_list" class="calendar_filters_checkbox_list" style="height: 10em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['session_type_titles']);
    $content .= '</div><br />';

    $content .= '</div><!-- div-search_by_course_panel -->';

    return $content;
}
