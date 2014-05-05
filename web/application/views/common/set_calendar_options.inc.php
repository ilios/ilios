<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Inserts JSON info for idle timer.
 *
 * Include this snippet in the JavaScript block in the <head> of your page template.
 *
 */
?>

<script type="application/json" id="calendarOptionsOverrides">
<?php
    echo json_encode(array(
	//set the time_step increment to one defined in ilios.php config file
	'time_step' => $this->config->item('calendar_option_time_step'),
    ));
?>
</script>
