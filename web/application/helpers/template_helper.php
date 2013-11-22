<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file template_helper.php
 *
 * Templating helper functions.
 */

if ( ! function_exists('ilios_print_daytime_options')) {
    /**
     * Prints out <code>option</code> tags for day times in 15min intervals
     * within given boundaries.
     *
     * @param int $start
     * @param int $end
     * @param int $hoursOffset
     *
     * @todo improve code docs. [ST 2013/11/22]
     */
    function ilios_print_daytime_options ($start = 0, $end = 60, $hoursOffset = 6) {
        for ($i = $start; $i < $end; $i++) {
            $hours = floor($i / 4) + $hoursOffset;
            $minutes = ($i % 4) * 15;

            if ($hours < 10) {
                $hours = '0' . $hours;
            }

            if ($minutes == 0) {
                $minutes = '00';
            }

            $string = $hours . ':' . $minutes;

            echo '<option value="' . $string . '">' . $string . '</option>';
        }
    }
}

