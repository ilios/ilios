<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
    return base_url() . "application/views/";
}

/**
 * Returns the fully qualified filesystem path for a given subdirectory
 * within the <code>application</code> directory
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

/**
 * Prints out <script> tags linking to a given list of JS files, or, if given, to aggregations of these files.
 * @param array $js an associative array of arrays. Each item represents a group of script assets and contains
 *     a list of paths to JS files, relative to the webroot.
 * @param string $asseticGroupPrefix a prefix for naming aggregate files. typically,
 *  one distinct prefix per page should be given.
 * @param boolean aggregate Set to TRUE to use file aggregation.
 * @param string $revision The ilios revision string. Used as cache busting mechanism.
 */
function writeJsScripts (array $js, $asseticGroupPrefix = 'default', $aggregate = false, $revision = '')
{
    $CI =& get_instance();

    $baseUrl = base_url();

    foreach ($js as $group => $paths) {
        $asseticGroup = $asseticGroupPrefix . '_' . $group . '_' . $revision;
        foreach ($paths as $path) {
            if ($aggregate) {
                $CI->assetic->addJs($baseUrl . $path, $asseticGroup);
            } else {
                $CI->assetic->addJs(FCPATH . $path, $asseticGroup);
            }
        }
    }

    if ($aggregate) {
        $CI->assetic->writeJsScripts();
    } else {
        $CI->assetic->writeStaticJsScripts();
    }
}
