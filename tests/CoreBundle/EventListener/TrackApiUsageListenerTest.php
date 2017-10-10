<?php
namespace Tests\CoreBundle\EventListener;

use Ilios\CoreBundle\Service\Config;
use Mockery as m;

use Ilios\CoreBundle\EventListener\TrackApiUsageListener;
use Symfony\Component\HttpFoundation\HeaderBag;
use Tests\CoreBundle\TestCase;

/**
 * Class TrackApiUsageListenerTest
 */
class TrackApiUsageListenerTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $mockEvent;

    /**
     * @var m\MockInterface
     */
    protected $mockRequest;

    /**
     * @var m\MockInterface
     */
    protected $mockController;

    /**
     * @var m\MockInterface
     */
    protected $mockConfig;

    /**
     * @var m\MockInterface
     */
    protected $mockTracker;

    /**
     * @var m\MockInterface
     */
    protected $mockLogger;

    /** @var  m\MockInterface */
    protected $mockHeaders;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->mockConfig = m::mock(Config::class);
        $this->mockController = m::mock('Symfony\Bundle\FrameworkBundle\Controller\Controller');
        $this->mockRequest = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterControllerEvent');
        $this->mockEvent->shouldReceive('getController')->andReturn([$this->mockController]);
        $this->mockEvent->shouldReceive('getRequest')->andReturn($this->mockRequest);
        $this->mockTracker = m::mock('Happyr\GoogleAnalyticsBundle\Service\Tracker');
        $this->mockLogger = m::mock('Psr\Log\LoggerInterface');
        $this->mockLogger->shouldReceive('error');
        $this->mockHeaders = m::mock(HeaderBag::class);
        $this->mockRequest->headers = $this->mockHeaders;
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->listener);
        unset($this->mockTracker);
        unset($this->mockContainer);
        unset($this->mockConfig);
        unset($this->mockEvent);
        unset($this->mockRequest);
        unset($this->mockLogger);
        unset($this->mockHeaders);
    }

    /**
     * @covers \Ilios\CoreBundle\EventListener\TrackApiUsageListener::onKernelController
     */
    public function testTrackingIsDisabled()
    {
        $this->mockConfig->shouldReceive('get')->with('enable_tracking')->once()->andReturn(false);
        $this->mockConfig->shouldReceive('get')->with('tracking_code')->once()->andReturn(null);
        $listener = new TrackApiUsageListener($this->mockConfig, $this->mockTracker, $this->mockLogger);
        $listener->onKernelController($this->mockEvent);
        $this->mockTracker->shouldNotHaveReceived('send');
    }

    /**
     * @covers \Ilios\CoreBundle\EventListener\TrackApiUsageListener::onKernelController
     */
    public function testTracking()
    {
        $uri = '/api/v1/foo/bar/baz';
        $host = 'iliosproject.org';
        $trackingCode = 'UA-XXXXX';
        $userAgent = 'Geflarknik Browser, Version 0.1';
        $clientIp = '123.123.123.123';
        $this->mockConfig->shouldReceive('get')->with('enable_tracking')->once()->andReturn(true);
        $this->mockConfig->shouldReceive('get')->with('tracking_code')->once()->andReturn($trackingCode);
        $this->mockRequest->shouldReceive('getRequestUri')->andReturn($uri);
        $this->mockRequest->shouldReceive('getHost')->andReturn($host);
        $this->mockRequest->shouldReceive('getClientIp')->andReturn($clientIp);
        $this->mockHeaders->shouldReceive('get')->with('User-Agent')->once()->andReturn($userAgent);

        $listener = new TrackApiUsageListener($this->mockConfig, $this->mockTracker, $this->mockLogger);
        $listener->onKernelController($this->mockEvent);
        $this->mockTracker->shouldHaveReceived('send')->withArgs([
            [
                'tid' => $trackingCode,
                'dh' => $host,
                'dp' => $uri,
                'dt' => get_class($this->mockController),
                'uip' => $clientIp,
                'ua' => $userAgent,
            ],
            'pageview'
        ]);
    }

    /**
     * @covers \Ilios\CoreBundle\EventListener\TrackApiUsageListener::onKernelController
     */
    public function testTrackingFailure()
    {
        $uri = '/api/v1/foo/bar/baz';
        $host = 'iliosproject.org';
        $userAgent = 'Geflarknik Browser, Version 0.1';
        $clientIp = '123.123.123.123';
        $e = new \Exception();
        $this->mockConfig->shouldReceive('get')->with('enable_tracking')->once()->andReturn(true);
        $this->mockConfig->shouldReceive('get')->with('tracking_code')->once()->andReturn('foo');
        $this->mockRequest->shouldReceive('getRequestUri')->andReturn($uri);
        $this->mockRequest->shouldReceive('getHost')->andReturn($host);
        $this->mockRequest->shouldReceive('getClientIp')->andReturn($clientIp);
        $this->mockHeaders->shouldReceive('get')->with('User-Agent')->once()->andReturn($userAgent);
        $this->mockTracker->shouldReceive('send')->andReturnUsing(function () use ($e) {
            throw $e;
        });
        $listener = new TrackApiUsageListener($this->mockConfig, $this->mockTracker, $this->mockLogger);
        $listener->onKernelController($this->mockEvent);
        $this->mockLogger
            ->shouldHaveReceived('error')
            ->withArgs(['Failed to send tracking data.', ['exception' => $e]]);
        unset($mockLogger);
    }
}
