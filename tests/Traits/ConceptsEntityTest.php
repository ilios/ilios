<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\MeshConcept;
use App\Traits\ConceptsEntity;
use Mockery as m;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

#[CoversTrait(ConceptsEntity::class)]
class ConceptsEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use ConceptsEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetConcepts(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(MeshConcept::class));
        $collection->add(m::mock(MeshConcept::class));
        $collection->add(m::mock(MeshConcept::class));

        $this->traitObject->setConcepts($collection);
        $this->assertEquals($collection, $this->traitObject->getConcepts());
    }

    public function testRemoveConcept(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(MeshConcept::class);
        $two = m::mock(MeshConcept::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setConcepts($collection);
        $this->traitObject->removeConcept($one);
        $concepts = $this->traitObject->getConcepts();
        $this->assertEquals(1, $concepts->count());
        $this->assertEquals($two, $concepts->first());
    }

    public function testAddConcept(): void
    {
        $this->traitObject->setConcepts(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getConcepts());

        $one = m::mock(MeshConcept::class);
        $this->traitObject->addConcept($one);
        $this->assertCount(1, $this->traitObject->getConcepts());
        $this->assertEquals($one, $this->traitObject->getConcepts()->first());
        // duplicate prevention check
        $this->traitObject->addConcept($one);
        $this->assertCount(1, $this->traitObject->getConcepts());
        $this->assertEquals($one, $this->traitObject->getConcepts()->first());

        $two = m::mock(MeshConcept::class);
        $this->traitObject->addConcept($two);
        $this->assertCount(2, $this->traitObject->getConcepts());
        $this->assertEquals($one, $this->traitObject->getConcepts()->first());
        $this->assertEquals($two, $this->traitObject->getConcepts()->last());
    }
}
