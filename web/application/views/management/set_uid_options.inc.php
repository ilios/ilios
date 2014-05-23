<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Inserts JSON info for setting user uid options.
 *
 * Include this snippet in the JavaScript block in the <head> of your page template.
 *
 */
?>

<script type="application/json" id="uidOptions">
<?php
    //check for value in config file...
    if($this->config->item('uid_min_length')){
        $uidMinLength = $this->config->item('uid_min_length');
    } else {
        //set the default minimum length of 9 characters
        $uidMinLength = 9;
    }

    if($this->config->item('uid_max_length')){
        $uidMaxLength = $this->config->item('uid_max_length');
    } else {
        //set the default maximum length of 9 characters
        $uidMaxLength = 9;
    }

    echo json_encode(array(
	//set the uid min/max character lengths as defined in the ilios.php config file
	'uid_min_length' => $uidMinLength,
    'uid_max_length' => $uidMaxLength
    ));
?>
</script>
