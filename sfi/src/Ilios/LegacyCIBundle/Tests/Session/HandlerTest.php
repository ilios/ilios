<?php
namespace Ilios\LegacyCIBundle\Tests\Session;


use Ilios\LegacyCIBundle\Session\Handler;
use Ilios\LegacyCIBundle\Tests\TestCase;

use Mockery as m;

/**
 * Tests for Authentication Test
 */
class HandlerTest extends TestCase
{
    /**
     * @var Handler
     */
    protected $handler;
    
    protected $repoMock;
    protected $exMock;

    /**
     * Instantiate a handler and service dependencies
     */
    protected function setUp()
    {
        $entity = 'CISession';
        $this->repoMock = m::mock('Doctrine\Common\Persistence\ObjectRepository');
        $omMock = m::mock('Doctrine\Common\Persistence\ObjectManager')
            ->shouldReceive('getRepository')->with($entity)
            ->once()->andReturn($this->repoMock)
            ->getMock();
        $this->exMock = m::mock('Ilios\LegacyCIBundle\Session\Extractor')
            ->shouldReceive('getSessionId')->andReturn('ssid')
            ->getMock();
        $this->handler = new Handler($omMock, $entity, $this->exMock);
    }
    
    public function testGetUserNameNoSession()
    {
        $this->repoMock->shouldReceive('findOneBy')->with(array('sessionId' => 'ssid'))
            ->once()->andReturn(false);
        $this->assertFalse($this->handler->getUserId());
    }
    
    public function testGetUserNameWithSession()
    {
        $session = m::mock('Ilios\CoreBundle\Entity\CISession')
            ->shouldReceive('getUserDataItem')->with('uid')
            ->once()->andReturn(13)->getMock();
        $this->repoMock->shouldReceive('findOneBy')->with(array('sessionId' => 'ssid'))
            ->once()->andReturn($session);
        $this->assertSame(13, $this->handler->getUserId());
    }
}
