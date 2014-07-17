<?php
namespace Ilios\CoreBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase as BaseTestCase;
use Mockery as m;

/**
 * Default Test Case
 */
class TestCase extends BaseTestCase
{
    public function tearDown()
    {
        m::close();
    }
}
