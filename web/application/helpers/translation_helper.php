<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file translate_helper.php
 *
 * Translation helper functions.
 */

if ( ! function_exists('t')) {

    /**
     * Wrapper around <code>LanguageMap->t()</code>.
     * Returns the text value for a given key from a language pack.
     *
     * @see LanguageMap::t()
     * @param string $key The text key.
     * @param boolean unescape If TRUE then escaped double quotes in the value will be unescaped.
     * @return string The text.
     */
    function t ($key, $unescape = true) {
        $CI =& get_instance();
        return $CI->languagemap->t($key, $unescape);
    }
}
