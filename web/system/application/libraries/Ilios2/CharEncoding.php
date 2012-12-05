<?php

/**
 * Utility class providing helper methods for dealing with character encoding issues.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

/**
 * Utility class providing helper methods for dealing with character encoding issues.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
class Ilios2_CharEncoding
{
    /**
     * Converts a given text to UTF-8 character encoding.
     * If the string already has the right encoding it is returned as given.
     * @param string $str the text to be converted
     * @return string the UTF-8 char. encoded text
     */
    public static function convertToUtf8 ($str)
    {
        // check encoding and convert to UTF-8 on demand
        if (! mb_check_encoding($str, 'UTF-8')) {
        	$order = mb_detect_order(); // set the detect order in your PHP configuration!
        	$encoding = mb_detect_encoding($str, $order);
        	if (false !== $encoding) { // SOL if encoding couldn't be detected. move on.
        		$str = mb_convert_encoding($str, 'UTF-8', $encoding);
        	}
        }
        return $str;
    }

    /**
     * URL-decode given input from a HTTP-request in a UTF8-safe manner.
     *
     * @param string $str the raw input
     * @return string the url-decoded output
     * @link http://www.php.net/manual/en/function.urldecode.php#79595
     * @see urlencode()
     */
    public static function utf8UrlDecode ($str)
    {
        // url-decode, then convert %uXXXX to &#XXXX (html entities),
        // and then decode these entities.
        $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
        return html_entity_decode($str, null, 'UTF-8');
    }
}
