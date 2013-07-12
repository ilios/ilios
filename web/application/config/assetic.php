<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['assetic'] = array(
    'js' => array(
        //For every page
        'autoload' => array(),
        'default-group' => 'common',
    ),
    'css' => array(
        //For every page
        'autoload' => array(),
        'default-group' => 'style',
    ),
    'static' => array(
        //Directory where Assetic puts the merged files
        'dir' => 'static/',
    )
);
