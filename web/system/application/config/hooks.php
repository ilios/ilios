<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['pre_system'] = array();

// register a class autoloader as pre-system hook
$hook['pre_system'][] = array(
    'class' => 'Ilios_Hooks',
    'function' => 'registerAutoloader',
    'filename' => 'Hooks.php',
    'filepath' => 'hooks/Ilios'
);

/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */