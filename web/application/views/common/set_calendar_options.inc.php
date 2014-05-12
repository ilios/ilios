<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Inserts JSON info for calendar options overrides.
 *
 * Include this snippet in the JavaScript block in the <head> of your page template.
 *
 */
?>

<script type="application/json" id="calendarOptionsOverrides">
<?php
    //check for value in config file...
    if($this->config->item('calendar_options_time_step')){
        $timeStep = $this->config->item('calendar_options_time_step');
    } else {
        //set the default step of 15 minutes (:00, :15, :30, :45)
        $timeStep = 15;
    }

    echo json_encode(array(
	//set the time_step increment to one defined in ilios.php config file
	'time_step' => $timeStep,
    ));
?>
</script>
