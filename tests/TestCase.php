<?php

declare(strict_types=1);

namespace App\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Default Test Case
 */
class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;
}
