<?php
namespace Ilios\LegacyCIBundle\Tests\Session;


use Ilios\LegacyCIBundle\Session\Extractor;

/**
 * Tests for Unencrypted Extractors
 */
class UnEncryptedExtractorTest extends BaseExtractor
{
    /**
     * Create Session Extractor
     */
    protected function setUp()
    {
        parent::setUp();
        $this->ciIsEncrypted = false;
        $this->extractor = new Extractor(
            $this->ciCookieId,
            $this->ciEncryptionKey,
            $this->ciIsEncrypted,
            $this->util,
            $this->logger
        );
    }

    public function testBadUserAgent()
    {
        $badUserAgent = 'baddata';
        $parameters = $this->getCiCookieArray();
        $this->util->shouldReceive('getCookieData')->once()
            ->with($this->ciCookieId)
            ->andreturn($this->cookieData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->cookieData)
                ->andReturn(true);

        $this->util->shouldReceive('unserialize')->once()
            ->with(substr($this->cookieData, 0, strlen($this->cookieData) - 40))
            ->andReturn($parameters);
        
        $this->util->shouldReceive('getUserAgent')
                ->andreturn($badUserAgent);
        
        $this->logger->shouldReceive('info')->once()
            ->with("/^Mismatched user agents in CI Session \({$badUserAgent}/");
        $this->assertFalse($this->extractor->getSessionId());
    }

    public function testCookieNotArray()
    {
        $this->util->shouldReceive('getCookieData')->once()
            ->with($this->ciCookieId)
            ->andreturn($this->cookieData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->cookieData)
                ->andReturn(true);
        
        $this->util->shouldReceive('unserialize')->once()
            ->with(substr($this->cookieData, 0, strlen($this->cookieData) - 40))
            ->andReturn('a string');
        
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was not extracted into an array.');
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function setupCalls(array $parameters)
    {
        $this->util->shouldReceive('getCookieData')->once()
            ->with($this->ciCookieId)
            ->andreturn($this->cookieData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->cookieData)
                ->andReturn(true);
        
        $this->util->shouldReceive('unserialize')->once()
            ->with(substr($this->cookieData, 0, strlen($this->cookieData) - 40))
            ->andReturn($parameters);
        
        if (array_key_exists('user_agent', $parameters)) {
            $this->util->shouldReceive('getUserAgent')
                ->andreturn($parameters['user_agent']);
        }
    }
}
