<?php

/**
 * JSON utility class.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

/**
 * JSON utility class.
 *
 * @category Ilios2
 * @package Ilios2
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
class Ilios2_Json
{
    /**
     * Escape all single quotes with slashes.
     * @var int
     */
    const JSON_ENC_SINGLE_QUOTES = 1;

    /**
     * Escape all double quotes with slashes.
     * @var int
     */
    const JSON_ENC_DOUBLE_QUOTES = 2;

    /**
     * Decodes a given JSON string.
     * @param string $json the JSON-encoded input
     * @param boolean $assoc when TRUE then given objects will be converted to assoc. arrays
     * @return mixed the decoded input
     * @see json_decode()
     * @see json_last_error()
     * @throws Ilios2_Exception when decoding failed
     */
    public static function decode ($json, $assoc = false)
    {
        // decode JSON
        $rhett = json_decode($json, $assoc);

        // error checking
        $error = json_last_error();
        if (JSON_ERROR_NONE === $error) { // all is good.
            return $rhett;
        }

        // error handling - throw them up as exceptions
        $msg = '';
        switch ($error) {
            case JSON_ERROR_DEPTH :
                $msg = 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_CTRL_CHAR :
                $msg = 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX :
                $msg = 'Syntax error';
                break;
            case JSON_ERROR_STATE_MISMATCH :
                $msg = 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_UTF8 :
                $msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default : // catch-all for future error types
                $msg = 'Unknown error';
                break;
        }
        throw new Ilios2_Exception('JSON decoding error: ' . $msg, $error);
    }

    /**
     * Returns the JSON representation of a given value that is safe for directly embedding into JavaScript.
     * This function is essentially wrapper around <code>json_encode()</code> with extra escaping-options added.
     * @param mixed $value e value being encoded.
     * @param int $options a bitmask consisting of <code>Ilios2_Json::JSON_ENC_SINGLE_QUOTES, Ilios2_Json::JSON_ENC_DOUBLE_QUOTES</code>
     * @param int $jsonEncodeOptions a bitmask passed as second param to json_encode()
     * @return string|boolean a JSON encoded string on success of FALSE on failure
     * @see json_encode()
     */
    public static function encodeForJavascriptEmbedding ($value, $options = 0, $jsonEncodeOptions = 0)
    {
        $rhett = json_encode($value, $jsonEncodeOptions);
        
        if ($rhett === false) {
            return false;
        }
        // escape slashes
        $rhett = str_replace('\\', '\\\\', $rhett);

        // escape single quotes
        if (Ilios2_Json::JSON_ENC_SINGLE_QUOTES & $options) {
            $rhett = str_replace("'", "\\'", $rhett);
        }

        // escape double quotes
        if (Ilios2_Json::JSON_ENC_DOUBLE_QUOTES & $options) {
            $rhett = str_replace('"', '\\"', $rhett);
        }

        return $rhett;
    }
}
