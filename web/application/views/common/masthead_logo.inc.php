<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="main-logo">
    <img src="<?php echo getViewsURLRoot(); ?>images/ilios-logo.png" alt="Ilios" width="84" height="42" />
    <span><?php echo $this->languagemap->getI18NString('general.terms.version'); ?> <?php include_once dirname(__FILE__) . '/../../../version.php'; ?></span>
</div>
