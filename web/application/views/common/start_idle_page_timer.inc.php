<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Inserts JSON info for idle timer.
 *
 * Include this snippet in the JavaScript block in the <head> of your page template.
 *
 */
?>

<script type="application/json" id="iliosIdleTimer">
<?php
    echo json_encode(array(
        'timeout' => $this->config->item('ilios_idle_page_timeout'),
        'logoutUrl' => base_url('ilios.php/authentication_controller?logout=yes')
    ));
?>
</script>
