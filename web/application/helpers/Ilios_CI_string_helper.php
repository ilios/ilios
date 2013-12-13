<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function fullyEscapedText ($text) {
    return preg_replace('/\'/', '\\\'',
        preg_replace('/"/', '\\"',
            preg_replace('/\n/', ' ', $text)));
}

function generateJavascriptRepresentationCodeOfPHPArray ($anArray, $variableName,
                                                         $declareVariable = true) {
    echo ($declareVariable ? "var " : "") . $variableName . " = new Object();\n";

    foreach ($anArray as $key => $val) {
        echo $variableName . "." . $key . " = '" . fullyEscapedText($val) . "'; \n";
    }
}

if (! function_exists('unHTML')) {

    /**
     * Transform a given HTML formatted string into plain text.
     *
     * @param string $s The markup.
     * @return string The plain text.
     * @todo Move this into a string utility class. [ST 2013/12/12]
     * @todo This function does too much. Move the newline removal stuff out. [ST 2013/12/12]
     */
    function unHTML($s) {
        return str_replace("\n", ' ', trim(strip_tags($s)));
    }
}

