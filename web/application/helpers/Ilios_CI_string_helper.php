<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
