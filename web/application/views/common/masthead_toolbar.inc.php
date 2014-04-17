<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Prints out the logout toolbar and user info in the masthead.
 *
 * @todo replace direct calls to i18nVendor model with calls to helper function. see ticket #2567
 */
?>
<nav id="utility">
    <ul>
<?php if ($this->session->userdata('username')) : // show user session info and logout button ?>
        <li class="username" title="<?php echo $this->session->userdata('username'); ?>"><?php echo $this->session->userdata('display_fullname'); ?></li>
        <li class="last-login"><?php echo t('general.phrases.last_login'); ?>:
            <span><?php echo $this->session->userdata('display_last'); ?></span></li>
        <li><a id="logout_link" class="tiny radius button" href="<?php echo site_url(); ?>/authentication_controller?logout=yes">
            <?php echo t('general.terms.logout'); ?></a>
        </li>
<?php else: // show login button ?>
        <li id="logout_link"><a class="tiny radius button" href="<?php echo site_url(); ?>/dashboard_controller">
            <?php echo t('general.terms.login'); ?></a>
        </li>
<?php endif; ?>
    </ul>
</nav> <!-- end #utility -->
