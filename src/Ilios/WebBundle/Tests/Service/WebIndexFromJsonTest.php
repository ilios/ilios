<?php
namespace Ilios\CoreBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

use Ilios\WebBundle\Service\WebIndexFromJson;

class WebIndexFromJsonTest extends TestCase
{

    protected $fakeTestFileDir;

    /**
     * @var WebIndexFromRedis
     */
    protected $obj;

    /**
     * @var Mockery/MockInterface
     */
    protected $mockFileSystem;

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

        $this->mockFileSystem = m::mock('Ilios\CoreBundle\Classes\Filesystem');
        $this->mockTemplating = m::mock('Symfony\Component\Templating\EngineInterface');

        $this->obj = new WebIndexFromJson($this->mockFileSystem, $this->mockTemplating, $this->fakeTestFileDir);
    }

    public function tearDown()
    {
        $fs = new SymfonyFileSystem();
        $fs->remove($this->fakeTestFileDir);

        unset($this->obj);
        unset($this->mockFileSystem);
        unset($this->mockTemplating);

        m::close();
    }

    /**
     * @covers Ilios\CoreBundle\Service\Directory::__construct
     */
    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof WebIndexFromJson);
    }

    public function testGetIndex()
    {
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->fakeTestFileDir . '/ilios/dev-v1.1/index.json')
            ->andReturn(true);
        $this->mockFileSystem->shouldReceive('readFile')
            ->with($this->fakeTestFileDir . '/ilios/dev-v1.1/index.json')
            ->andReturn($this->sampleJson);
        $this->mockFileSystem->shouldReceive('exists')
            ->with($this->fakeTestFileDir . '/ilios/dev-v1.1/index.json')
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
        $result = $this->obj->getIndex(WebIndexFromJson::DEVELOPMENT);

        $this->assertEquals('compiledtemplatestring', $result);
    }
}
