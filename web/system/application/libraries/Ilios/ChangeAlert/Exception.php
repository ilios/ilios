<?php
/**
 * Ilios Change Alerts Exception.
 */
class Ilios_ChangeAlert_Exception extends Ilios_Exception
{
    /* error codes */

    /**
     * no templates directory path given
     * @var int
     */
    const TEMPLATES_DIR_MISSING = 100;

    /**
     * templates directory not found
     * @var int
     */
    const TEMPLATES_DIR_NOT_FOUND = 101;

    /**
     * template file could not be found
     * @var int
     */
    const TEMPLATE_FILE_NOT_FOUND = 102;

    /**
     * unable to read template file
     * @var int
     */
    const TEMPLATE_FILE_UNREADABLE = 103;
}
