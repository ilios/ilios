<?php

namespace Ilios\LegacyCIBundle;

/**
 * Service for methods broght over from code igniter
 */
class Utilities
{

    /**
     * UnSerialize data in Code ignerts way
     * @see CI_Session::_serialize()
     * @param string $data
     * 
     * @return mixed
     */
    public function unserialize($data)
    {
        $data = unserialize($this->stripSlashes($data));

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('{{slash}}', '\\', $val);
                }
            }

            return $data;
        }

        return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
    }

    /**
     * Serialize data in Code ignerts way
     * @see CI_Session::_serialize()
     * @param mixed $data
     * 
     * @return string
     */
    public function serialize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($val)) {
                    $data[$key] = str_replace('\\', '{{slash}}', $val);
                }
            }
        } else {
            if (is_string($data)) {
                $data = str_replace('\\', '{{slash}}', $data);
            }
        }

        return serialize($data);
    }

    /**
     * Encrypt data in Code ignerts way
     * @see CI_Encrypt::encode()
     * @param string $data
     * @param string $key
     * 
     * @return string
     */
    public function encrypt($string, $key)
    {
        if (!extension_loaded('mcrypt')) {
            throw new \Exception('The Utilities library requires the Mcrypt extension.');
        }
        $cipher = \MCRYPT_RIJNDAEL_256;
        $mode = \MCRYPT_MODE_CBC;
        $key = md5($key);
        $init_size = mcrypt_get_iv_size($cipher, $mode);
        $init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);

        $data = $init_vect . mcrypt_encrypt($cipher, $key, $string, $mode, $init_vect);
        $keyhash = sha1($key);
        $keylen = strlen($keyhash);
        $str = '';

        for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
            if ($j >= $keylen) {
                $j = 0;
            }

            $str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
        }

        return base64_encode($str);
    }

    /**
     * Decrypt data in Code ignerts way
     * @see CI_Encrypt::decode()
     * @param string $encryptedString
     * @param string $key
     * 
     * @return string
     */
    public function decrypt($encryptedString, $key)
    {
        if (!extension_loaded('mcrypt')) {
            throw new \Exception('The Utilities library requires the Mcrypt extension.');
        }
        $cipher = \MCRYPT_RIJNDAEL_256;
        $mode = \MCRYPT_MODE_CBC;
        $key = md5($key);
        if (preg_match('/[^a-zA-Z0-9\/\+=]/', $encryptedString)) {
            return false;
        }

        $decodedString = base64_decode($encryptedString);

        $keyhash = sha1($key);
        $keylen = strlen($keyhash);
        $string = '';

        for ($i = 0, $j = 0, $len = strlen($decodedString); $i < $len; ++$i, ++$j) {
            if ($j >= $keylen) {
                $j = 0;
            }
            $temp = ord($decodedString[$i]) - ord($keyhash[$j]);
            if ($temp < 0) {
                $temp = $temp + 256;
            }
            $string .= chr($temp);
        }

        $init_size = mcrypt_get_iv_size($cipher, $mode);

        if ($init_size > strlen($string)) {
            return false;
        }

        $init_vect = substr($string, 0, $init_size);
        $data = substr($string, $init_size);

        return rtrim(mcrypt_decrypt($cipher, $key, $data, $mode, $init_vect), "\0");
    }

    /**
     * Strip slashes in code igniters way
     * @see CI_Session::_serialize()
     * @param string $str
     * 
     * @return string
     */
    private function stripSlashes($str)
    {
        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = stripSlashes($val);
            }
        } else {
            $str = stripslashes($str);
        }

        return $str;
    }
}
