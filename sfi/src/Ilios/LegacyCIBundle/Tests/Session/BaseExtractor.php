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
    protected $garbageEncryptedData = 'somegarbagedata';

    /**
     * This is a real instance of Utilities to do the serialization
     * @var Utilities
     */
    protected $utilities;

    /**
     * Create Session Extractor
     */
    protected function setUp()
    {
        $this->utilities = new Utilities();
        $this->ciCookieId = 'test_ci_session';
        $this->ciEncryptionKey = 'test_ci_key';
        $this->util = m::mock('Ilios\LegacyCIBundle\Utilities');
        $this->logger = m::mock('Symfony\Bridge\Monolog\Logger');

        unset($_COOKIE[$this->ciCookieId]);
    }

    public function tearDown()
    {
        unset($_COOKIE[$this->ciCookieId]);
        parent::tearDown();
    }

    public function testNoCookie()
    {
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testEmptyCookie()
    {
        $this->createCiCookie();
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testShortCookie()
    {
        $_COOKIE[$this->ciCookieId] = str_pad('', 38, 'a');
        $this->logger->shouldReceive('error')->once()
                ->with('Session: The Code Igniter session cookie was not signed.');
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testBadCookieDataField()
    {
        $parameters = $this->getCiCookieArray();
        $this->createCiCookie($parameters);
        $_COOKIE[$this->ciCookieId] .= 'randomgarbage';
        $this->logger->shouldReceive('error')->once()
                ->with('/^Session: HMAC mismatch/');
        $this->assertFalse($this->extractor->getSessionId());
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
        $arr['user_agent'] = $faker->userAgent;
        $arr['last_activity'] = $faker->dateTime;

        return $arr;
    }

    /**
     * Set cookie data like Code Igniter
     * 
     * @see CI_Session::_set_cookie()
     * @param array $parameters
     * @param string $encryptionKey
     */
    protected function createCiCookie(array $parameters = array())
    {
        $data = '';
        if (!empty($parameters)) {
            // Serialize the userdata for the cookie
            $data = $this->utilities->serialize($parameters);

            if ($this->ciIsEncrypted) {
                $data = $this->garbageEncryptedData;
            }

            $data .= hash_hmac('sha1', $data, $this->ciEncryptionKey);
        }
        $_COOKIE[$this->ciCookieId] = $data;
        if (array_key_exists('user_agent', $parameters)) {
            $_SERVER['HTTP_USER_AGENT'] = $parameters['user_agent'];
        }
    }
}
