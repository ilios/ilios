<?php

/**
 * Static class providing mail utilities.
 * @author stopfstedt
 *
 */
class Ilios2_MailUtils
{
    /**
     * max. line length limit
     * @link http://tools.ietf.org/html/rfc2822#section-2.1.1
     * @var int
     */
    const RFC2822_MAX_LINE_LENGTH = 998;

    /**
     * recommended line length limit
     * @link http://tools.ietf.org/html/rfc2822#section-2.1.1
     * @var int
     */
    const RFC2822_RECOMMENDED_LINE_LENGTH = 72;


    /**
     * Utility function.
     * Flattens out a given array of strings to a one text string, think <code>implode()</code>.
     * In addition to that, it breaks the strings according to a given char. limit.
     * @param array $list array of strings
     * @param string $separator the text separator between list items
     * @param int $maxLineLength max. line length, the default is recommended line length according to RFC-2822
     * @return string the text
     */
    public static function implodeListForMail (array $list, $separator = ', ',
        $maxLineLength = Ilios2_MailUtils::RFC2822_RECOMMENDED_LINE_LENGTH)
    {
    	$lines = array();
    	$line = '';
    	$lineLength = 0;
    	$delimLength = strlen($separator);
    	for ($i = 0, $n = count($list); $i < $n; $i++) {
    		$item = $i < ($n - 1) ? $list[$i] . $separator : $list[$i];
    		$itemLength = strlen($item);
    		if ($maxLineLength < $lineLength + $itemLength) {
    			$lines[] = $line;
    			$line = $item;
    			$lineLength = $itemLength;
    		} else {
    			$line .= $item;
    			$lineLength += $itemLength;
    		}
    	}
    	if ($lineLength) {
    	    $lines[] = $line;
    	}
    	return implode(PHP_EOL, $lines);
    }
}
