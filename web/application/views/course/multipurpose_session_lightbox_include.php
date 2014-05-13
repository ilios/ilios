<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* DEPENDENCIES:
*      YUI toolkit
*      scripts/ilios_dom.js
*      scripts/ilios_utilities.js
*
*/
?>
<div class="tabdialog" id="multipurpose_session_lightbox" style="display: none;">

    <div class="hd" id="multipurpose_lightbox_title">MULTIPURPOSE TITLE</div>
    <div class="bd">
        <div class="dialog_wrap" id="multipurpose_lightbox_wrap">
            <form method="get" action="#">
                <div id="multipurpose_instructors_div" style="margin: 6px 9px 12px 6px">
                    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 3px;">
                        <?php echo $word_instructors_string; ?>
                        <span id="ilios_calendar_instructors_selector"
                            style="margin-left: 18px; font-weight: normal; font-size: 9pt;">
                            <a href="" onclick="ilios.cm.session.ilm.showInstructors(true); return false;">
                                <?php echo $select_instructors_string; ?></a>
                        </span>
                    </div>
                    <div id="instructors_lightbox_textfield"
                        style="width: 100%; height: 35px; padding-left: 3px; overflow: auto;"
                        class="read_only_data"></div>
                    <div id="ilios_calendar_instructors_selector_div"
                        style="display: none; height: 295px; margin-bottom: 36px;">
                        <div style="float: left; width: 39%; overflow: auto;">
                            <ul class="picked" id="calendar_instructor_selected"
                                style="height: 274px; margin-top: 22px;"></ul>
                        </div>
                        <div class="autocomplete_tab" id="calendar_instructor_ac_div"
                            style="float: right; width: 59%; height: 284px;">
                            <div>
                                <input id="calendar_instructor_ac_input"
                                name="calendar_instructor_ac_input" type="text">
                            </div>
                            <div class="autolist" id="calendar_instructor_autolist"></div>
                            <div id="calendar_instructor_ac_progress" class="invisible"
                                style="position: absolute; left: 0; top: 168px;">
                                <img src="<?php echo $viewsUrlRoot . 'images/loading.gif' ?>" border="0" />
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div id="calendar_instructor_collapse_selector" class="up_arrow_widget"></div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div style="margin: 6px 9px 12px 6px">
                    <div style="font-weight: bold; margin-bottom: 3px;">
                        <?php echo $phrase_student_groups_string; ?>
                        <span id="ilios_calendar_student_groups_selector"
                            style="margin-left: 18px; font-weight: normal; font-size: 9pt;">
                            <a href="#" onclick="ilios.cm.session.showLearners(true); return false;">
                                <?php echo $select_groups_string; ?></a>
                        </span>
                    </div>
                    <div id="learner_list_lightbox_textfield"
                        style="width: 100%; height: 35px; padding-left: 3px; overflow: auto;"
                        class="read_only_data"></div>
                    <div id="ilios_calendar_student_groups_selector_div" class="clearfix" style="display: none;">
                        <div id="selected_learner_tree_view_div"
                            style="float: left; width: 49%; background-color: #EEEEDD;
                                height: 250px; border: 1px solid #A2A1A2; overflow: auto;">
                        </div>
                        <div id="learner_tree_view_div"
                            style="float: right; width: 49%; margin-bottom: 6px; height: 250px;
                                background-color: #EDF5FF; border: 1px solid #9393A3;
                                overflow: auto;">
                        </div>
                        <div id="learner_assignment_strategy_div" class="clear">
                            <label for="learner_assignment_strategy_select"><?php echo $term_strategy; ?>: </label>
                            <select id="learner_assignment_strategy_select" name="learner_assignment_strategy_select">
                                <option value="leaves"><?php echo $phrase_sub_group_strategy; ?></option>
                                <option value="roots"><?php echo $phrase_parent_group_strategy; ?></option>
                            </select>
                        </div>

                        <div id="calendar_student_group_collapse_selector" class="up_arrow_widget clear"></div>
                    </div>
                </div>

                <div id="ilm_calendar_div" style="margin: 6px 9px 12px 6px; position: relative;">
                    <div style="margin-left: 154px;">
                        <div id="ilm_lightbox_due_date_calendar"></div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div id="multi_offering_calendar_div" style="margin: 6px 9px 12px 6px; position: relative;">
                    <div style="float: left; width: 192px; position: relative;">
                        <div>
                            <div>
                                <span style="font-weight: bold;">
                                    <?php echo $phrase_start_time_string; ?></span>
                                <select id="lightbox_start_time_select" style="float: right;">
                                    <?php ilios_print_daytime_options(0); ?>
                                </select>
                            </div>
                            <br/>
                            <div id="lightbox_start_time_calendar"></div>
                        </div>
                    </div>
                    <div style="width:85px; float:left; margin-left: 13px; margin-top: 217px; text-align: center;">
                        <a href="" id="lightbox_recurring_link"
                            onclick="ilios.cm.session.mo.toggleRecurringDisplay(); return false;">
                            <?php echo $phrase_not_recurring_string; ?>
                        </a>
                    </div>
                    <div style="float: right; width: 192px; position: relative;">
                        <div>
                            <div>
                                <span style="font-weight: bold;">
                                    <?php echo $phrase_end_time_string; ?></span>
                                <select id="lightbox_end_time_select" style="float: right;">
                                    <?php ilios_print_daytime_options(1); ?>
                                </select>
                            </div>
                            <br/>
                            <div id="lightbox_end_time_calendar"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>

                <div id="multi_offering_recurring_div"
                    style="margin: 14px 9px 12px 6px; display: none; border: 1px solid;
                        padding: 16px 6px 22px; position: relative;">
                    <div style="float: left; width: 48%;">
                        <div style="text-align: left; margin-bottom: 9px;"
                            id="lightbox_repeat_weekday_selector_label">
                            <?php echo $repeat_weekday_selector_string; ?>:
                        </div>
                        <ul class="week_list">
                            <li id="repeat_week_0" onclick="ilios.cm.session.mo.repeatDayClicked(0, this);">
                                <?php echo $calendary_short_sunday_string; ?></li>
                            <li id="repeat_week_1" onclick="ilios.cm.session.mo.repeatDayClicked(1, this);">
                                <?php echo $calendary_short_monday_string; ?></li>
                            <li id="repeat_week_2" onclick="ilios.cm.session.mo.repeatDayClicked(2, this);">
                                <?php echo $calendary_short_tuesday_string; ?></li>
                            <li id="repeat_week_3" onclick="ilios.cm.session.mo.repeatDayClicked(3, this);">
                                <?php echo $calendary_short_wednesday_string; ?></li>
                            <li id="repeat_week_4" onclick="ilios.cm.session.mo.repeatDayClicked(4, this);">
                                <?php echo $calendary_short_thursday_string; ?></li>
                            <li id="repeat_week_5" onclick="ilios.cm.session.mo.repeatDayClicked(5, this);">
                                <?php echo $calendary_short_friday_string; ?></li>
                            <li id="repeat_week_6" onclick="ilios.cm.session.mo.repeatDayClicked(6, this);">
                                <?php echo $calendary_short_saturday_string; ?></li>
                        </ul>
                    </div>
                    <div style="float: right; position: relative;">
                        <div style="margin-bottom: 6px;">
                        <input type="radio" id="repeat_ends_on_count_radio" value="count"
                                name="repeat_end_radio" checked="checked" />
                            <?php echo $repeat_ends_on_count_string; ?>
                            <select id="lightbox_repeat_count_select">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select>
                            <?php echo $word_weeks_string; ?>
                            <br/>
                        </div>
                        <input type="radio" id="repeat_ends_on_date_radio" value="date"
                            name="repeat_end_radio" />
                            <?php echo $repeat_ends_on_date_string; ?>
                            <div id="repeat_ends_on_date"
                                    style="width: 100%; height: 20px; padding-left: 3px; display: inline;"
                                    class="read_only_data"></div>
                            <div id="repeat_end_date_calendar_button" class="calendar_button"
                                style="position: absolute; bottom: -19px; right: 1px;"></div>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
