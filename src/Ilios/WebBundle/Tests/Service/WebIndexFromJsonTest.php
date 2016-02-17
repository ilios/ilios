<?php
namespace Ilios\CoreBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

use Ilios\WebBundle\Service\WebIndexFromJson;

class WebIndexFromJsonTest extends TestCase
{
    protected $sampleJson = '{"base":[{"href":"/something/something"}],"meta":[{"name":"ilios/config/environment","content":"test-config"}],"link":[{"rel":"stylesheet","href":"first.css"},{"rel":"stylesheet","href":"second.css"}],"script":[{"src":"first.js"},{"src":"second.js"},{"src":"third.js"}]}';

    public function tearDown()
    {
        m::close();
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
        $result = $obj->getIndex(WebIndexFromJson::DEVELOPMENT);

        $this->assertEquals('compiledtemplatestring', $result);

        unset($mockTemplating);
        unset($obj);
    }
}
