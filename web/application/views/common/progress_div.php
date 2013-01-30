<?php

function generateProgressDivMarkup ($divStyleDefinition = null) {
    if (is_null($divStyleDefinition)) {
        $divStyleDefinition= "float: right; margin-right: 36px; display: none;";
    }

    $rhett = '<div style="' . $divStyleDefinition . '" id="save_in_progress_div" class="indeterminate_progress_text">
                <div class="indeterminate_progress" style="display: inline-block;"></div>
                <span id="save_in_progress_text">XX</span>&hellip;
            </div>';

    return $rhett;
}

?>
