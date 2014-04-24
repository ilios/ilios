<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @deprecated
 *
 * In a give text, this function will escape single and double quotes in a given text,
 * and replace newline characters with blank spaces.
 *
 * @param string $text The text to escape.
 * @return string The escaped text.
 *
 * @todo Replace this hodgepodge of junk. [ST 2014/04/23]
 *
 */
function fullyEscapedText ($text) {
    return preg_replace('/\'/', '\\\'',
        preg_replace('/"/', '\\"',
            preg_replace('/\n/', ' ', $text)));
}

/**
 * @deprecated
 * Do not use this junk going forward. Instead, properly (de)serialize your values from/to JSON your values
 * when passing them from PHP to JS-land. [ST 2014/02/01]
 * @see https://github.com/ilios/ilios/issues/406
 */ 
function generateJavascriptRepresentationCodeOfPHPArray ($anArray, $variableName,
                                                         $declareVariable = true) {
    echo ($declareVariable ? "var " : "") . $variableName . " = new Object();\n";

    foreach ($anArray as $key => $val) {
        echo $variableName . "." . $key . " = '" . fullyEscapedText($val) . "'; \n";
    }
}
