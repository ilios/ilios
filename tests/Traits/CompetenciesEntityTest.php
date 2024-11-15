<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Competency;
use App\Traits\CompetenciesEntity;
use Mockery as m;
use App\Tests\TestCase;

#[CoversClass(CompetenciesEntity::class)]
class CompetenciesEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use CompetenciesEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetCompetencies(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Competency::class));
        $collection->add(m::mock(Competency::class));
        $collection->add(m::mock(Competency::class));

        $this->traitObject->setCompetencies($collection);
        $this->assertEquals($collection, $this->traitObject->getCompetencies());
    }

    public function testRemoveCompetency(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(Competency::class);
        $two = m::mock(Competency::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setCompetencies($collection);
        $this->traitObject->removeCompetency($one);
        $competencies = $this->traitObject->getCompetencies();
        $this->assertEquals(1, $competencies->count());
        $this->assertEquals($two, $competencies->first());
    }

    public function testAddCompetency(): void
    {
        $this->traitObject->setCompetencies(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getCompetencies());

        $one = m::mock(Competency::class);
        $this->traitObject->addCompetency($one);
        $this->assertCount(1, $this->traitObject->getCompetencies());
        $this->assertEquals($one, $this->traitObject->getCompetencies()->first());
        // duplicate prevention check
        $this->traitObject->addCompetency($one);
        $this->assertCount(1, $this->traitObject->getCompetencies());
        $this->assertEquals($one, $this->traitObject->getCompetencies()->first());

        $two = m::mock(Competency::class);
        $this->traitObject->addCompetency($two);
        $this->assertCount(2, $this->traitObject->getCompetencies());
        $this->assertEquals($one, $this->traitObject->getCompetencies()->first());
        $this->assertEquals($two, $this->traitObject->getCompetencies()->last());
    }
}
