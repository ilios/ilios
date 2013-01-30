<?php

/**
 * DEPENDENCIES
 *  scripts/ilios_utilities.js
 */
?>
<div class="tabdialog" id="course_add_dialog">
    <div class="hd"><?php echo $add_new_course_string; ?></div>
    <div class="bd">
        <div class="dialog_wrap" style="height: 120px;">
            <div id="course_add_picked" style="display: none;">meaningless data</div>
            <form method="post" action="<?php echo current_url(); ?>/addNewCourse">
                <input id="new_course_hidden" name="new_course_hidden" type="hidden" />
                <div style="position: relative; margin-bottom: 18px;">
                    <span class="entity_widget_title"><?php echo $phrase_course_name_string; ?></span>
                    <br />
                    <input id="new_course_title" name="new_course_title" type="text" value="" size="50"/>
                </div>
                <div style="position: relative; margin-bottom: -6px;">
                    <span class="entity_widget_title"><?php echo $phrase_academic_year_string; ?></span>
                    <br />
                    <select id="new_academic_year_selector" name="new_academic_year">
<?php
    $currentYear = date('Y');
    $currentMonth = date('n');
    // you could have someone after Jan. 1 adding courses for the still current academic year
    if ($currentMonth < 6) {
        $currentYear--;
    }
    for ($i = 0; $i < 5; $i++) {
        $start = $currentYear + $i;
        echo '<option value="' . $start . '"' . (($i == 0) ? ' selected="selected"' : '') . '>';
        echo $start . '-' . ($start + 1) . '</option>\n';
    }
?>
                    </select>
                </div>
                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
<script type="text/javascript">
    ilios.cm.clearCourseAddDialogContents = function (dialog) {

        var element = document.getElementById('new_course_hidden');
        element.value = '';

        element = document.getElementById('new_course_title');
        element.value = '';

        element = document.getElementById('new_academic_year_selector');
        ilios.utilities.selectOptionWithValue(element, '<?php echo date('Y') ?>');

        return true;
    };
</script>
