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

    public function testBadUserAgent()
    {
        $badUserAgent = 'baddata';
        $parameters = $this->getCiCookieArray();
        $this->util->shouldReceive('getCookieData')->once()
            ->with($this->ciCookieId)
            ->andreturn($this->encryptedData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->encryptedData)
                ->andReturn(true);
        
        $this->util->shouldReceive('decrypt')
            ->once()->with(
                substr($this->encryptedData, 0, strlen($this->encryptedData) - 40),
                $this->ciEncryptionKey
            )
            ->andReturn($this->cookieData);
        
        $this->util->shouldReceive('unserialize')->once()
            ->with($this->cookieData)
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
            ->andreturn($this->encryptedData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->encryptedData)
                ->andReturn(true);
        
        $this->util->shouldReceive('decrypt')
            ->once()->with(
                substr($this->encryptedData, 0, strlen($this->encryptedData) - 40),
                $this->ciEncryptionKey
            )
            ->andReturn($this->cookieData);
        
        $this->util->shouldReceive('unserialize')->once()
            ->with($this->cookieData)
            ->andReturn('a string');
        
        $this->logger->shouldReceive('error')->once()
            ->with('CI Session was not extracted into an array.');
        $this->assertFalse($this->extractor->getSessionId());
    }
    
    public function setupCalls(array $parameters)
    {
        $this->util->shouldReceive('getCookieData')->once()
            ->with($this->ciCookieId)
            ->andreturn($this->encryptedData);
        
        $this->util->shouldReceive('validateHash')->once()
                ->with($this->ciEncryptionKey, $this->encryptedData)
                ->andReturn(true);
        
        $this->util->shouldReceive('decrypt')
            ->once()->with(
                substr($this->encryptedData, 0, strlen($this->encryptedData) - 40),
                $this->ciEncryptionKey
            )
            ->andReturn($this->cookieData);
        
        $this->util->shouldReceive('unserialize')->once()
            ->with($this->cookieData)
            ->andReturn($parameters);
        
        if (array_key_exists('user_agent', $parameters)) {
            $this->util->shouldReceive('getUserAgent')
                ->andreturn($parameters['user_agent']);
        }
    }
}
