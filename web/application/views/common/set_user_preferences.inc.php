<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file set_user_preferences.inc.php
 *
 * Includes-template.
 * This populates the client-side user preferences object with preferences retrieved from the server-side user-session.
 *
 * Expects the following template variables to be present:
 *
 *  $user_preferences_json ... A JSON encoded assoc. array of user-set preferences.
 *
 */
?>
<script type="application/json" id="preferencesModel">
<?php echo $user_preferences_json; ?>
</script>
