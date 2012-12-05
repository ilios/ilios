<?php
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
        <li class="username"><?php echo $this->session->userdata('display_fullname'); ?></li>
        <li class="last-login"><?php echo $this->i18nVendor->getI18NString('general.phrases.last_login', $lang); ?>:
            <span><?php echo $this->session->userdata('display_last'); ?></span></li>
        <li><a id="logout_link" class="tiny radius button" href="<?php echo site_url(); ?>/authentication_controller?logout=yes">
            <?php echo $this->i18nVendor->getI18NString('general.terms.logout', $lang); ?></a>
        </li>
<?php else: // show login button ?>
        <li id="logout_link"><a class="tiny radius button" href="<?php echo site_url(); ?>/dashboard_controller">
            <?php echo $this->i18nVendor->getI18NString('general.terms.login', $lang); ?></a>
        </li>
<?php endif; ?>
    </ul>
</nav> <!-- end #utility -->
