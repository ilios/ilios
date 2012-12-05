<?php

/**
 * Returns the URL of he YUI library root directory within Ilios.
 * @return string
 */
function getYUILibrariesURL ()
{
    return base_url() . 'scripts/yui/build/';
}

/**
 * Returns the URL of the "views" directory within Ilios.
 * @return string
 */
function getViewsURLRoot ()
{
    return base_url() . "system/application/views/";
}

/**
 * Returns the fully qualified filesystem path for a given subdirectory
 * within the <code>/system/application</code> directory
 * @param string $subdirectory the name of the subdirectory
 * @return string
 */
function getServerFilePath ($subdirectory)
{
    $helpers_segment = '/helpers/';
    $path_parts = pathinfo(__FILE__);
    $cwd = str_replace("\\", "/", $path_parts['dirname']) . '/';
    return preg_replace($helpers_segment, $subdirectory, $cwd);
}

/**
 * Appends the ilios revision number as request parameter "irev" to a given url.
 * Use this function to wrap URLs to Ilios-internal CSS and JS files.
 * This ensures that browser caches get properly busted whenever a new build is deployed.
 * @param String $url the URL to append to
 * @return String the URL with the appended revision number
 */
function appendRevision ($url)
{
    $CI =& get_instance();
    $rev = $CI->config->item('ilios_revision');
    $pos = strrpos($url, '?');

    if (false === $pos) {
        $url .= '?irev=' . $rev;
    } else {
        $url = rtrim($url, '?#') . '&amp;irev=' . $rev;
    }
    return $url;
}
