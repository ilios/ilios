<?php

/**
 * JSON utility class.
 *
 * @category Ilios
 * @package Ilios
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */

/**
 * JSON utility class.
 *
 * @category Ilios
 * @package Ilios
 * @copyright Copyright (c) 2010-2012 The Regents of the University of California.
 * @license http://www.iliosproject.org/license GNU GPL v3
 */
class Ilios_Json
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
     * @throws Ilios_Exception when decoding failed
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
        throw new Ilios_Exception('JSON decoding error: ' . $msg, $error);
    }

    /**
     * Returns the JSON representation of a given value that is safe for directly embedding into JavaScript.
     * This function is essentially wrapper around <code>json_encode()</code> with extra escaping-options added.
     * @param mixed $value e value being encoded.
     * @param int $options a bitmask consisting of <code>Ilios_Json::JSON_ENC_SINGLE_QUOTES, Ilios_Json::JSON_ENC_DOUBLE_QUOTES</code>
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
        if (Ilios_Json::JSON_ENC_SINGLE_QUOTES & $options) {
            $rhett = str_replace("'", "\\'", $rhett);
        }

        // escape double quotes
        if (Ilios_Json::JSON_ENC_DOUBLE_QUOTES & $options) {
            $rhett = str_replace('"', '\\"', $rhett);
        }

        return $rhett;
    }


    /**
     * Deserializes a given JSON-formatted text into the appropriate PHP type.
     * @param string $json JSON-formatted text
     * @param boolean $assoc when TRUE then given objects will be converted to assoc. arrays
     * @param boolean $utf8urlDecode when TRUE then the given input will be URL-decoded in an UTF8-safe manner
     * @return mixed the de-serialized data
     * @throws Ilios_Exception on decoding failure
     */
    public static function deserializeJson ($json, $assoc = false, $utf8urlDecode = true)
    {
        if ($utf8urlDecode) {
            $json = Ilios_CharEncoding::utf8UrlDecode($json);
        }
        return self::decode($json, $assoc);
    }

    /**
     * Deserializes a given JSON-formatted text into a PHP array
     * @param string $json
     * @param boolean $assoc when TRUE then given objects will be converted to assoc. arrays
     * @param boolean $utf8urlDecode when TRUE then the given input will be URL-decoded in an UTF8-safe manner
     * @return array the de-serialized array
     * @throws Ilios_Exception on decoding failure and on type mismatch
     */
    public static function deserializeJsonArray ($json, $assoc = false, $utf8urlDecode = true)
    {
        $rhett = self::deserializeJson($json, $assoc, $utf8urlDecode);
        if (! is_array($rhett)) {
            throw new Ilios_Exception("Failed to deserialize given text into a PHP array.");
        }
        return $rhett;
    }
}
