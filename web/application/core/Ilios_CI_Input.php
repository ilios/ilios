<?php 

/**
 * Extends CodeIgniter's core Input component with the aim to un-fuck
 * some of the text garbling caused by CI's overreaching attempt
 * to auto-sanitize user input.
 * Still - WTF?!
 * [ST 02/22/2013]
 */
class Ilios_CI_Input extends CI_Input
{
    /**
     * (non-PHPdoc)
     * @see CI_Input::_clean_input_data()
     */
    function _clean_input_data($str)
    {
        if (is_array($str))
        {
            $new_array = array();
            foreach ($str as $key => $val)
            {
                $new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
            return $new_array;
        }
    
        /* We strip slashes if magic quotes is on to keep things consistent
    
        NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
        it will probably not exist in future versions at all.
        */
        if ( ! is_php('5.4') && get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
    
        // Clean UTF-8 if supported
        if (UTF8_ENABLED === TRUE)
        {
            // original call to CI's UTF-8 conversion function.
            // (good-bye umlauts, good-bye accents)
            // BAD! don't use it.
            // $str = $this->uni->clean_string($str);

            // our drop-in replacement
            $str = Ilios_CharEncoding::convertToUtf8($str);
        }
    
        // Remove control characters
        $str = remove_invisible_characters($str);
    
        // Should we filter the input data?
        if ($this->_enable_xss === TRUE)
        {
            $str = $this->security->xss_clean($str);
        }
    
        // Standardize newlines if needed
        if ($this->_standardize_newlines == TRUE)
        {
            if (strpos($str, "\r") !== FALSE)
            {
                $str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
            }
        }
    
        return $str;
    }
}