<?php
namespace Ilios\CoreBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

use Ilios\WebBundle\Service\WebIndexFromRedis;

class WebIndexFromRedisTest extends TestCase
{

    protected $fakeTestFileDir;

    /**
     * @var WebIndexFromRedis
     */
    protected $obj;

    /**
     * @var Mockery/MockInterface
     */
    protected $mockRedis;

    /**
     * @var Mockery/MockInterface
     */
    protected $mockTemplating;

    protected $sampleJson = '{"base":[{"href":"/something/something"}],"meta":[{"name":"ilios/config/environment","content":"test-config"}],"link":[{"rel":"stylesheet","href":"first.css"},{"rel":"stylesheet","href":"second.css"}],"script":[{"src":"first.js"},{"src":"second.js"},{"src":"third.js"}]}';

    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->mockRedis = m::mock('Predis\Client');
        $this->mockTemplating = m::mock('Symfony\Component\Templating\EngineInterface');

        $this->obj = new WebIndexFromRedis($this->mockRedis, $this->mockTemplating, $this->fakeTestFileDir);
    }

    public function tearDown()
    {
        $fs = new SymfonyFileSystem();
        $fs->remove($this->fakeTestFileDir);

        unset($this->obj);

        m::close();
    }

    /**
     * @covers Ilios\CoreBundle\Service\Directory::__construct
     */
    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof WebIndexFromRedis);
    }

    public function testGetIndex()
    {
        $this->mockRedis->shouldReceive('get')->with('ilios:index:current-content')->once()
            ->andReturn($this->sampleJson);

        $this->mockTemplating->shouldReceive('exists')
            ->with('@custom_webindex_templates/webindex.html.twig')->andReturn(false);
        $this->mockTemplating->shouldReceive('exists')
            ->with('IliosWebBundle:WebIndex:webindex.html.twig')->andReturn(true);
        $this->mockTemplating->shouldReceive('render')->with('IliosWebBundle:WebIndex:webindex.html.twig', [
            'base_url' => '/something/something',
            'metas' => [
                [
                    'name' => 'ilios/config/environment',
                    'content' => 'test-config'
                ]
            ],
            'links' => [
                [
                    'rel' => 'stylesheet',
                    'href' => 'first.css'
                ],
                [
                    'rel' => 'stylesheet',
                    'href' => 'second.css'
                ],

            ],
            'scripts' => [
                'first.js',
                'second.js',
                'third.js',
            ],
        ])->once()->andReturn('compiledtemplatestring');
        $result = $this->obj->getIndex('current-content');

        $this->assertEquals('compiledtemplatestring', $result);
    }

    public function testClearCache()
    {

        $this->mockRedis->shouldReceive('get')->once()->andReturn($this->sampleJson);

        $this->mockTemplating->shouldReceive('exists')->andReturn(true);
        $this->mockTemplating->shouldReceive('render')->andReturn('compiledtemplatestring');
        $this->obj->getIndex('current-content');
        $fs = new SymfonyFileSystem();
        $this->assertTrue($fs->exists($this->fakeTestFileDir), 'dir exists');
        $this->assertTrue($fs->exists($this->fakeTestFileDir . '/ilios/ilios:index:current-content'), 'cached file exists');
        $this->obj->clearCache('current-content');
        $this->assertNotTrue($fs->exists($this->fakeTestFileDir . '/ilios/ilios:index:current-content'), 'cached file removed');
    }



    public function testGetIndexWithBadVersion()
    {
        $this->mockRedis->shouldReceive('get')->with('ilios:index:bad')->once()->andReturn('');
        $this->setExpectedException(\Exception::class, 'Failed to get contents from redis for version bad');
        $result = $this->obj->getIndex('bad');

    }
}
