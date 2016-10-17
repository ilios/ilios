<?php

namespace Tests\CoreBundle\Form\DataTransformer;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;

/**
 * Class RemoveMarkupTransformerTest
 * @package Tests\CoreBundle\\Form\DataTransformer
 */
class RemoveMarkupTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemoveMarkupTransformer
     */
    private $transformer;

    protected function setUp()
    {
        $this->transformer = new RemoveMarkupTransformer();
    }

    protected function tearDown()
    {
        $this->transformer = null;
    }

    /**
     * @covers \Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::reverseTransform
     */
    public function testReverseTransformNotString()
    {
        $input = [
            1,
            [],
            new \stdClass(),
            null,
        ];

        foreach ($input as $in) {
            $this->assertEquals($in, $this->transformer->reverseTransform($in));
        }
    }

    /**
     * @covers \Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::reverseTransform
     */
    public function testReverseTransform()
    {
        $this->assertSame('foo bar baz', $this->transformer->reverseTransform('foo bar baz'));
        $this->assertSame('foobar', $this->transformer->reverseTransform('<h1>foo</h1>bar<br><br>'));
        $this->assertSame('alert();', $this->transformer->reverseTransform('<script>alert();</script>'));
    }

    /**
     * @covers \Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::Transform
     */
    public function testTransform()
    {
        $input = [
            'foo',
            '<pre>bar</pre>',
            1,
            [],
            new \stdClass(),
            null,
        ];

        foreach ($input as $in) {
            $this->assertEquals($in, $this->transformer->transform($in));
        }
    }
}
