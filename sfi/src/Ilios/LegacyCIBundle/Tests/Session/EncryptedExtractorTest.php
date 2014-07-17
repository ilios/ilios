<?php
namespace Ilios\LegacyCIBundle\Tests\Session;


use Ilios\LegacyCIBundle\Session\Extractor;

/**
 * Tests for Unencrypted Extractors
 */
class EncryptedExtractorTest extends BaseExtractor
{
    /**
     * Create Session Extractor
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ciIsEncrypted = true;
        $this->extractor = new Extractor(
            $this->ciCookieId,
            $this->ciEncryptionKey,
            $this->ciIsEncrypted,
            $this->util,
            $this->logger
        );
    }
    
    public function testGetSessionId()
    {
        $parameters = $this->getCiCookieArray();
        $this->createCiCookie($parameters);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->assertSame($parameters['session_id'], $this->extractor->getSessionId());
    }
    
    public function testMissingIdField()
    {
        $key = 'session_id';
        $parameters = $this->getCiCookieArray();
        unset($parameters[$key]);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->createCiCookie($parameters);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingIpField()
    {
        $key = 'ip_address';
        $parameters = $this->getCiCookieArray();
        unset($parameters[$key]);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->createCiCookie($parameters);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingUserField()
    {
        $key = 'user_agent';
        $parameters = $this->getCiCookieArray();
        unset($parameters[$key]);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->createCiCookie($parameters);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testMissingActivityField()
    {
        $key = 'last_activity';
        $parameters = $this->getCiCookieArray();
        unset($parameters[$key]);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->createCiCookie($parameters);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was missing key: ' . $key);
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testCookieNotArray()
    {
        $faker = \Faker\Factory::create();
        $parameters = $this->getCiCookieArray();
        $this->createCiCookie($parameters);
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($faker->text);
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was not extracted into an array.');
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function testBadUserAgent()
    {
        $userAgent = 'baddata';
        $parameters = $this->getCiCookieArray();
        $this->createCiCookie($parameters);
        $_SERVER['HTTP_USER_AGENT'] = $userAgent;
        $this->util->shouldReceive('decrypt')
            ->once()->with($this->garbageEncryptedData, $this->ciEncryptionKey)->andReturn('clean');
        $this->util->shouldReceive('unserialize')->with('clean')->once()->andReturn($parameters);
        $this->logger->shouldReceive('info')->once()
            ->with("/^Mismatched user agents in CI Session \({$userAgent}/");
        $this->assertFalse($this->extractor->getSessionId());
    }
}
