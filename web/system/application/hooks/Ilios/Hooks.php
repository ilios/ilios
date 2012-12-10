<?php

/**
 * This class provides pre/post-processing hooks into the Ilios
 * workflow as defined by the CodeIgniter framework.
 *
 * @link http://codeigniter.com/user_guide/general/hooks.html
 */
class Ilios2_Hooks
{
    /**
     * Implements a pre-system hook.
     * Registers a class autoloader.
     * @see Ilios2_Hooks::autoload()
     */
    public function registerAutoloader ()
    {
        spl_autoload_register(array('Ilios2_Hooks', 'autoload'));
    }

    /**
     * Class autoloader function.
     * @param string $className
     */
    public static function autoload ($className)
    {
        // do not attempt to autoload CodeIgniter classes
        // since CI has its own ways to load this stuff
        if (0 === strpos($className, 'CI_')) {
            return;
        }

        // convert the class name to a path
        // by replacing underscores with path separators
        // and by appending it with the '.php' file suffix
        // e.g. a class name "Ilios2_Database_Constant"
        // will be converted to a corresponding file path
        // "Ilios2/Database/Constant.php"
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        // complete the file path construction
        // by scoping it down to the APPPATH/libraries subdirectory
        $filePath = APPPATH . 'libraries' . DIRECTORY_SEPARATOR . $filePath;

        // check if the file exists
        // if so then load it.
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
}
