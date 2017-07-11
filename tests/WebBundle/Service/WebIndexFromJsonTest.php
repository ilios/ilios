<?php
namespace Tests\CoreBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

use Ilios\WebBundle\Service\WebIndexFromJson;

class WebIndexFromJsonTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    protected $sampleJson;

    public function setup()
    {
        $this->sampleJson = '{"meta":[{"name":"ilios/config/environment",' .
            '"content":"test-config"}],"link":[{"rel":"stylesheet","href":"first.css"},{"rel":"stylesheet",' .
            '"href":"second.css"}],"script":[{"src":"first.js"},{"src":"second.js"},{"src":"third.js"}, ' .
            '{"content": "<script></script>"}]}';
    }

    public function testGetIndex()
    {
        $mockTemplating = m::mock('Symfony\Component\Templating\EngineInterface');

        $obj = m::mock(WebIndexFromJson::class . '[getIndexFromAWS]', array($mockTemplating));
        $obj->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getIndexFromAWS')->once()->andReturn($this->sampleJson);
        $this->assertTrue($obj instanceof WebIndexFromJson);

        $mockTemplating->shouldReceive('exists')
            ->with('@custom_webindex_templates/webindex.html.twig')->andReturn(false);
        $mockTemplating->shouldReceive('exists')
            ->with('IliosWebBundle:WebIndex:webindex.html.twig')->andReturn(true);
        $mockTemplating->shouldReceive('render')->with('IliosWebBundle:WebIndex:webindex.html.twig', [
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
                [
                    'src' => 'first.js',
                    'content' => null
                ],
                [
                    'src' => 'second.js',
                    'content' => null
                ],
                [
                    'src' => 'third.js',
                    'content' => null
                ],
                [
                    'src' => null,
                    'content' => '<script></script>'
                ],
            ],
        ])->once()->andReturn('compiledtemplatestring');
        $result = $obj->getIndex(WebIndexFromJson::DEVELOPMENT);

        $this->assertEquals('compiledtemplatestring', $result);

        unset($mockTemplating);
        unset($obj);
    }
}
