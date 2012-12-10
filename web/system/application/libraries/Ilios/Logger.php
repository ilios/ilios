<?php

/**
 * File logger implementation.
 * Provides functionality to format and output messages to a given log file.
 */
class Ilios2_Logger
{
    /**
     * Dashed line, used for formatting log output.
     * @var string
     */
    const LOG_SEPARATION_LINE = '------------------------------------------------------------';

    /**
     * Indicates 'Info' log level
     * @var string
     */
    const LOG_LEVEL_INFO = 'I';
    /**
     * Indicates 'Error' log level
     * @var string
     */
    const LOG_LEVEL_ERROR = 'E';
    /**
     * Indicates 'Warning' log level
     * @var string
     */
    const LOG_LEVEL_WARN = 'W';

    /**
     * Indicates 'Debug' log level
     * @var string
     */
    const LOG_LEVEL_DEBUG = 'D';

    /**
     * Log file handle
     * @var resource
     */
    protected $_logFileHandle;

    /**
     * path to log file
     * @var string
     */
    protected $_logFilePath;

    /**
     * Internal logger registry.
     * @var array
     */
    static protected $_registry = array();


    /**
     * Returns a logger object bound to a given log file path.
     * @param string $logFilePath path to the log file
     * @return Ilios2_Logger
     * @throws Ilios2_Log_Exception
     */
    static public function getInstance ($logFilePath)
    {
    	if (! array_key_exists($logFilePath, self::$_registry)) {
    		$logger = new Ilios2_Logger($logFilePath);
    		self::$_registry[$logFilePath] = $logger;
    	}
    	return self::$_registry[$logFilePath];

    }


    /**
     * Constructor.
     * @param string $logFilePath path to the log file
     * @throws Ilios2_Log_Exception
     */
    protected function __construct ($logFilePath)
    {
        $this->_logFilePath = $logFilePath;
        $this->_logFileHandle = $this->_getLogFileHandle($this->_logFilePath);
    }


    /**
     * Destructor.
     */
    public function __destruct ()
    {
        // cleanup - attempt to close log file
        $this->_closeLogFile($this->_logFileHandle);
        // remove from the registry
        unset(self::$_registry[$this->_logFilePath]);
    }

    /**
     * Logs a given message.
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @param string $logLevel log level, see the available LOG_LEVEL_* constants
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function log ($message, $processId = 0, $indentationLevel = 0, $logLevel = self::LOG_LEVEL_INFO) {
        return $this->_writeToLogFile($this->_logFileHandle, $message, $processId,  $indentationLevel, $logLevel);
    }

    /**
     * Returns the path to the log file.
     * @return string
     */
    public function getLogFilePath ()
    {
        return $this->_logFilePath;
    }

    /**
     * Opens a log file located at a given path for appending and returns the file handle.
     * @param string $logFilePath path to the log file
     * @throws Ilios2_Log_Exception if the log file could not be opened for writing
     * @return resource the log file handle
     */
    protected function _getLogFileHandle ($logFilePath)
    {
    	$fh = @fopen($logFilePath, 'a');
    	if (false === $fh) {
    		throw new Ilios2_Log_Exception('Could not open cron tasks log file ' . $logFilePath, Ilios2_Log_Exception::OPENING_FILE_FAILED);
    	}
    	return $fh;
    }

    /**
     * Writes a given message to the log file.
     * @param resource $logFileHandle the log file handle
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @param string $logLevel log level, see the available LOG_LEVEL_* constants
     * @return boolean TRUE on success, FALSE otherwise
     */
    protected function _writeToLogFile ($logFileHandle, $message, $processId = 0, $indentationLevel = 0, $logLevel = self::LOG_LEVEL_INFO)
    {
    	$indent = str_repeat("  ", (int) $indentationLevel);
    	$now = date('d/M/Y:H:i:s O'); // get the current datetime
    	$out = "[{$now}][{$logLevel}]";
    	if (! empty($processId)) {
    	    $out .= "[p:{$processId}]";
    	}
    	$out .= "  {$indent}{$message}\n";
    	return fwrite($logFileHandle, $out);
    }

    /**
     * Closes a given file handle.
     * @param resource $logFileHandle
     * @return boolean TRUE on success
     */
    protected function _closeLogFile ($logFileHandle)
    {
    	return fclose($logFileHandle);
    }

    /**
     * Writes an info message to the log file.
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function info ($message, $processId = 0, $indentationLevel = 0)
    {
        $this->log($message, $processId, $indentationLevel, self::LOG_LEVEL_INFO);
    }

    /**
     * Writes a warning message to the log file.
     * @param resource $logFileHandle the log file handle
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function warn ($message, $processId = 0, $indentationLevel = 0)
    {
    	$this->log($message, $processId, $indentationLevel, self::LOG_LEVEL_WARN);
    }

    /**
     * Writes an error message to the log file.
     * @param resource $logFileHandle the log file handle
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function error ($message, $processId = 0, $indentationLevel = 0)
    {
    	$this->log($message, $processId, $indentationLevel, self::LOG_LEVEL_ERROR);
    }

    /**
     * Writes a debug message to the log file.
     * @param resource $logFileHandle the log file handle
     * @param string $message the log message
     * @param int $processId the Id of the currently running process (optional)
     * @param int $indentationLevel indents the given log message by two spaces times the given level
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function debug ($message, $processId = 0, $indentationLevel = 0)
    {
    	$this->log($message, $processId, $indentationLevel, self::LOG_LEVEL_DEBUG);
    }
}
