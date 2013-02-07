<?php
require_once  'Ilios/TestCase.php';

/**
 * Test case for the logger class.
 * @see Ilios_Logger
 */
class Ilios_LoggerTest extends Ilios_TestCase
{
    /**
     * Data provider function for <code>Ilios_LoggerTest::log()</code>.
     * Returns a nested array of arrays, where in each sub-array
     * - the first element holds the log message
     * - the second element holds the process id
     * - the third element holds the indentation level
     * - the fourth element holds the log level
     * - the fifth element holds the a pattern expected to match the log output generated from the input given
     *   in the previous four elements
     * @return array
     */
    public function providerTestLog ()
    {
    	return array(
    	    array('', 100, 0, Ilios_Logger::LOG_LEVEL_INFO, "/\\[I\\]\\[p:100\\]  /"),
            array('foo', 1000, 0, Ilios_Logger::LOG_LEVEL_INFO, "/\\[I\\]\\[p:1000\\]  foo/"),
    	    array('bar', 1000, 1, Ilios_Logger::LOG_LEVEL_WARN, "/\\[W\\]\\[p:1000\\]    bar/"),
    	    array('baz', 11, 2, Ilios_Logger::LOG_LEVEL_DEBUG, "/\\[D\\]\\[p:11\\]      baz/"),
    	    array('babba booey', null, 3, Ilios_Logger::LOG_LEVEL_ERROR, "/\\[E\\]        babba booey/")
    	);
    }


    /**
     * @test
     * @covers Ilios_Logger::getInstance()
     * @see Ilios_Logger::getInstance()
     * @group ilios
     * @group log
     */
    public function testGetInstance ()
    {
        $logger1 = Ilios_Logger::getInstance('/tmp/test1.log');
        $len = count($this->_getLoggerRegistry()); // count entries in logger registry
        $logger2 = Ilios_Logger::getInstance('/tmp/test1.log');
        $this->assertEquals($logger1, $logger2); // should be the same instance
        // there should be no new entries in the logger registry
        $this->assertEquals($len, count($this->_getLoggerRegistry()));
        $logger3 = Ilios_Logger::getInstance('/tmp/test2.log');
        // the logger registry should have +1 entries now
        $this->assertEquals($len + 1, count($this->_getLoggerRegistry()));
    }

    /**
     * @test
     * @covers Ilios_Logger::getInstance()
     * @see Ilios_Logger::getInstance()
     * @expectedException Ilios_Log_Exception
     * @group ilios
     * @group log
     */
    public function testGetInstanceFailure ()
    {
    	$logger1 = Ilios_Logger::getInstance('/some/path/to/a/logfile/that/doesnt/exist.log');
    }

    /**
     * @test
     * @covers Ilios_Logger::info()
     * @see Ilios_Logger::info()
     * @group ilios
     * @group log
     */
    public function testInfo ()
    {
        $logger = Ilios_Logger::getInstance('/tmp/test1.log');
        $processId = time();
        $logger->info('testing info()', $processId);
        $line = $this->_readLastLineFromLogFile($logger);
        $this->assertRegExp("/\\[I\\]\\[p:{$processId}\\]  testing info\\(\\)/", $line);

    }

    /**
     * @test
     * @covers Ilios_Logger::debug()
     * @see Ilios_Logger::debug()
     * @group ilios
     * @group log
     */
    public function testDebug ()
    {
        $logger = Ilios_Logger::getInstance('/tmp/test1.log');
        $processId = time();
        $logger->debug('testing debug()', $processId);
        $line = $this->_readLastLineFromLogFile($logger);
        $this->assertRegExp("/\\[D\\]\\[p:{$processId}\\]  testing debug\\(\\)/", $line);
    }

    /**
     * @test
     * @covers Ilios_Logger::warn()
     * @see Ilios_Logger::warn()
     * @group ilios
     * @group log
     */
    public function testWarn ()
    {
        $logger = Ilios_Logger::getInstance('/tmp/test1.log');
        $processId = time();
        $logger->warn('testing warn()', $processId);
        $line = $this->_readLastLineFromLogFile($logger);
        $this->assertRegExp("/\\[W\\]\\[p:{$processId}\\]  testing warn\\(\\)/", $line);
    }

    /**
     * @test
     * @covers Ilios_Logger::error()
     * @see Ilios_Logger::error()
     * @group ilios
     * @group log
     */
    public function testError ()
    {
        $logger = Ilios_Logger::getInstance('/tmp/test1.log');
        $processId = time();
        $logger->error('testing error()', $processId);
        $line = $this->_readLastLineFromLogFile($logger);
        $this->assertRegExp("/\\[E\\]\\[p:{$processId}\\]  testing error\\(\\)/", $line);
    }

    /**
     * @test
     * @covers Ilios_Logger::log()
     * @dataProvider providerTestLog
     * @param string $message test input to function under test
     * @param int $processId test input to function under test
     * @param int $indentationLevel test input to function under test
     * @param string $logLevel test input to function under test
     * @param $expectedOutputPattern expected pattern to match the output from function under test
     * @see Ilios_Logger::log()
     * @group ilios
     * @group log
     */
    public function testLog ($message, $processId, $indentationLevel, $logLevel, $expectedOutputPattern)
    {
        $logger = Ilios_Logger::getInstance('/tmp/test1.log');
        $logger->log($message, $processId, $indentationLevel, $logLevel);
        $line = $this->_readLastLineFromLogFile($logger);
        $this->assertRegExp($expectedOutputPattern, $line);
    }

    /**
     * Test-utility function.
     * Returns the static protected "$_registry" property from the Ilios_Logger class
     * by deliberately breaking encapsuling.
     * @return array
     */
    protected function _getLoggerRegistry ()
    {
         $reflection = new ReflectionClass('Ilios_Logger');
         $props = $reflection->getStaticProperties();
         return $props['_registry'];
    }

    /**
     * Test-utility function.
     * Returns the last line written to a log file handled by a given logger.
     * @param Ilios_Logger $logger
     * @return string the last line from the log file
     */
    protected function _readLastLineFromLogFile (Ilios_Logger $logger)
    {
        $path = $logger->getLogFilePath();
        $fp = fopen($path, 'r');
        fseek($fp, -1, SEEK_END);
        $pos = ftell($fp);
        $line = '';
        // deal with trailing linebreaks
        $c = null;
        do {
            fseek($fp, $pos--);
        	$c = fgetc($fp);
        } while (PHP_EOL == $c);

        // read last line
        do {
            $line = $c . $line;
        	fseek($fp, $pos--);
        	$c = fgetc($fp);
        } while (PHP_EOL != $c);

        fclose($fp); // cleanup
        return $line;
    }
}
