<?php

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
