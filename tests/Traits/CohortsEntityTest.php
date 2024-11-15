<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Cohort;
use App\Traits\CohortsEntity;
use Mockery as m;
use App\Tests\TestCase;

#[CoversClass(CohortsEntity::class)]
class CohortsEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use CohortsEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetCohorts(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Cohort::class));
        $collection->add(m::mock(Cohort::class));
        $collection->add(m::mock(Cohort::class));

        $this->traitObject->setCohorts($collection);
        $this->assertEquals($collection, $this->traitObject->getCohorts());
    }

    public function testRemoveCohort(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(Cohort::class);
        $two = m::mock(Cohort::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setCohorts($collection);
        $this->traitObject->removeCohort($one);
        $cohorts = $this->traitObject->getCohorts();
        $this->assertEquals(1, $cohorts->count());
        $this->assertEquals($two, $cohorts->first());
    }

    public function testAddCohort(): void
    {
        $this->traitObject->setCohorts(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getCohorts());

        $one = m::mock(Cohort::class);
        $this->traitObject->addCohort($one);
        $this->assertCount(1, $this->traitObject->getCohorts());
        $this->assertEquals($one, $this->traitObject->getCohorts()->first());
        // duplicate prevention check
        $this->traitObject->addCohort($one);
        $this->assertCount(1, $this->traitObject->getCohorts());
        $this->assertEquals($one, $this->traitObject->getCohorts()->first());

        $two = m::mock(Cohort::class);
        $this->traitObject->addCohort($two);
        $this->assertCount(2, $this->traitObject->getCohorts());
        $this->assertEquals($one, $this->traitObject->getCohorts()->first());
        $this->assertEquals($two, $this->traitObject->getCohorts()->last());
    }
}
