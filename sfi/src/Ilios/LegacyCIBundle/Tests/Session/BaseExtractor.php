<?php

namespace Ilios\LegacyCIBundle\Tests\Session;

use Ilios\LegacyCIBundle\Session\Extractor;
use Ilios\LegacyCIBundle\Utilities;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

/**
 * Abstract extractor test
 */
abstract class BaseExtractor extends TestCase
{

    /**
     * @var Extractor
     */
    protected $extractor;
    protected $util;
    protected $logger;
    protected $ciCookieId;
    protected $ciEncryptionKey;
    protected $ciIsEncrypted;
    protected $encryptedData;
    protected $cookieData;

    /**
     * Create Session Extractor
     */
    protected function setUp()
    {
        $this->encryptedData = 'startencrypteddataencrypteddataencrypteddataend';
        $this->cookieData = 'startcookiedatacookiedatacookiedatacookiedataend';
        $this->ciCookieId = 'test_ci_session';
        $this->ciEncryptionKey = 'test_ci_key';
        $this->util = m::mock('Ilios\LegacyCIBundle\Utilities');
        $this->logger = m::mock('Symfony\Bridge\Monolog\Logger');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testNoCookie()
    {
        $this->util->shouldReceive('getCookieData')->with($this->ciCookieId)
            ->andreturn(false);
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testEmptyCookie()
    {
        $this->util->shouldReceive('getCookieData')->with($this->ciCookieId)
            ->andreturn('');
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testShortCookie()
    {
        $this->util->shouldReceive('getCookieData')->with($this->ciCookieId)
            ->andreturn( str_pad('', 38, 'a'));
        $this->logger->shouldReceive('error')->once()
                ->with('Session: The Code Igniter session cookie was not signed.');
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testBadCookieDataField()
    {
        $this->util->shouldReceive('getCookieData')->with($this->ciCookieId)
                ->andreturn($this->cookieData);
        $this->util->shouldReceive('validateHash')
                ->with($this->ciEncryptionKey, $this->cookieData)
                ->andReturn(false);
        $this->logger->shouldReceive('error')->once()
                ->with('/^Session: HMAC mismatch/');
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testGetSessionId()
    {
        $parameters = $this->setupCiCookie();
        $this->assertSame($parameters['session_id'], $this->extractor->getSessionId());
    }
    
    public function testMissingIdField()
    {
        $key = 'session_id';
        $this->setupCiCookie($key);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingIpField()
    {
        $key = 'ip_address';
        $this->setupCiCookie($key);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingUserField()
    {
        $key = 'user_agent';
        $this->setupCiCookie($key);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingActivityField()
    {
        $key = 'last_activity';
        $this->setupCiCookie($key);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    /**
     * Child classes create their own calls to util
     * @param array $parameters
     */
    abstract protected function setupCalls(array $parameters);
      
    /**
     * Setup cookie and calls to util
     * @param string $keyToRemove
     * @return array
     */
    protected function setupCiCookie($keyToRemove = false)
    {
        $parameters = $this->getCiCookieArray();
        if($keyToRemove and array_key_exists($keyToRemove, $parameters)){
            unset($parameters[$keyToRemove]);
        }
        $this->setupCalls($parameters);
        
        return $parameters;
    }

    /**
     * Get a pre-created array for a cookie
     * 
     * @return array
     */
    protected function getCiCookieArray()
    {
        $faker = \Faker\Factory::create();
        $arr = array();
        $arr['session_id'] = $faker->sha256;
        $arr['ip_address'] = $faker->ipv4;
        $arr['user_agent'] = substr($faker->userAgent, 0, 120);
        $arr['last_activity'] = $faker->dateTime;

        return $arr;
    }
}
