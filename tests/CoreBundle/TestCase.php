<?php
namespace Tests\CoreBundle;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase as BaseTestCase;

/**
 * Default Test Case
 */
class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;
}
