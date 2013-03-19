<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Configures and registers the page idle timer to start on page load.
 *
 * Include this snippet in the JavaScript block at the bottom of your page template.
 *
 * Dependencies:
 * YUI base libs
 * YUI idle timer (scripts/third_party/idle-timer.js)
 */
?>

// register and start the idle timer on page load
YAHOO.util.Event.onDOMReady(function () {
    ilios.global.startIdleTimer(<?php echo $this->config->item('ilios_idle_page_timeout'); ?>, '<?php echo base_url(); ?>ilios.php/authentication_controller?logout=yes');
});
