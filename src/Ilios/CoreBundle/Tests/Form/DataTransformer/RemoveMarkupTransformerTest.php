<?php

namespace Ilios\CoreBundle\Tests\Form\DataTransformer;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;

/**
 * Class RemoveMarkupTransformerTest
 * @package Ilios\CoreBundle\Tests\Form\DataTransformer
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
     * @covers Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::transform
     */
    public function testTransformNotString()
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
     * @covers Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::transform
     */
    public function testTransform()
    {
        $this->assertSame('foo bar baz', $this->transformer->transform('foo bar baz'));
        $this->assertSame('foobar', $this->transformer->transform('<h1>foo</h1>bar<br><br>'));
        $this->assertSame('alert();', $this->transformer->transform('<script>alert();</script>'));
    }

    /**
     * @covers Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer::reverseTransform
     */
    public function testReverseTransform()
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
            $this->assertEquals($in, $this->transformer->reverseTransform($in));
        }
    }
}
