<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\AcademicYearFactory;
use App\Service\Config;
use App\Tests\TestCase;
use Mockery as m;

final class AcademicYearFactoryTest extends TestCase
{
    protected m\MockInterface $config;
    protected AcademicYearFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = m::mock(Config::class);
        $this->factory = new AcademicYearFactory($this->config);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->factory);
        unset($this->config);
    }

    public function testCreateAcademicYearCrossingCalendarYearBoundaries(): void
    {
        $this->config->shouldReceive('get')
            ->once()->with('academic_year_crosses_calendar_year_boundaries')->andReturn(true);
        $year = 2021;
        $academicYear = $this->factory->create($year);
        $this->assertEquals($year, $academicYear->id);
        $this->assertEquals('2021 - 2022', $academicYear->title);
    }

    public function testCreateAcademicYearContainedWithinCalendarYearBoundaries(): void
    {
        $this->config->shouldReceive('get')
            ->once()->with('academic_year_crosses_calendar_year_boundaries')->andReturn(false);
        $year = 2021;
        $academicYear = $this->factory->create($year);
        $this->assertEquals($year, $academicYear->id);
        $this->assertEquals('2021', $academicYear->title);
    }
}
