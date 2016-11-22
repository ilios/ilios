<?php
namespace Tests\CoreBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

use Ilios\CoreBundle\EventListener\TrackApiUsageListener;

/**
 * Class TrackApiUsageListenerTest
 * @package Tests\CoreBundle\EventListener
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
    protected $mockContainer;

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
    protected $mockTracker;

    /**
     * @var TrackApiUsageListener
     */
    protected $listener;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->mockController = m::mock('Symfony\Bundle\FrameworkBundle\Controller\Controller');
        $this->mockRequest = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->mockEvent = m::mock('Symfony\Component\HttpKernel\Event\FilterControllerEvent');
        $this->mockEvent->shouldReceive('getController')->andReturn([$this->mockController]);
        $this->mockEvent->shouldReceive('getRequest')->andReturn($this->mockRequest);
        $this->mockTracker = m::mock('Happyr\GoogleAnalyticsBundle\Service\Tracker');
        $this->mockContainer = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockContainer->shouldReceive('get')
            ->with('happyr.google_analytics.tracker')
            ->andReturn($this->mockTracker);

        $this->listener = new TrackApiUsageListener();
        $this->listener->setContainer($this->mockContainer);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->listener);
        unset($this->mockTracker);
        unset($this->mockContainer);
        unset($this->mockEvent);
        unset($this->mockRequest);
        unset($this->mockController);
        m::close();
    }

    /**
     * @covers \Ilios\CoreBundle\EventListener\TrackApiUsageListener::onKernelController
     */
    public function testTrackingIsDisabled()
    {
        $this->mockContainer->shouldReceive('getParameter')->with('ilios_core.enable_tracking')->andReturn(false);
        $this->listener->onKernelController($this->mockEvent);
        $this->mockTracker->shouldNotHaveReceived('send');
    }

    /**
     * @covers \Ilios\CoreBundle\EventListener\TrackApiUsageListener::onKernelController
     */
    public function testTracking()
    {
        $uri = '/api/v1/foo/bar/baz';
        $host = 'iliosproject.org';
        $this->mockRequest->shouldReceive('getRequestUri')->andReturn($uri);
        $this->mockRequest->shouldReceive('getHost')->andReturn($host);
        $this->mockContainer->shouldReceive('getParameter')->with('ilios_core.enable_tracking')->andReturn(true);
        $this->mockTracker->shouldReceive('send');
        $this->listener->onKernelController($this->mockEvent);
        $this->mockTracker->shouldHaveReceived('send')->withArgs([
            ['dh' => $host, 'dp' => $uri, 'dt' => get_class($this->mockController)],
            'pageview'
        ]);
    }
}
