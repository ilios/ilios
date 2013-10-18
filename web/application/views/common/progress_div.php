<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Generates markup for displaying status progress messages.
 * @param string|null $divStyleDefinition Inline CSS styles.
 * @param string $message A status message.
 * @return string The generated markup.
 */
function generateProgressDivMarkup ($divStyleDefinition = null, $message = 'XX') {
    if (is_null($divStyleDefinition)) {
        $divStyleDefinition= "float: right; margin-right: 36px; display: none;";
    }

    $rhett = '<div style="' . $divStyleDefinition . '" id="save_in_progress_div" class="indeterminate_progress_text">
                <div class="indeterminate_progress" style="display: inline-block;"></div>
                <span id="save_in_progress_text">' . htmlentities($message, ENT_COMPAT, 'UTF-8') . '</span>&hellip;
            </div>';

    return $rhett;
}
