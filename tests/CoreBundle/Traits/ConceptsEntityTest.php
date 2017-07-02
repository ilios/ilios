<?php
namespace Tests\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshConcept;
use Ilios\CoreBundle\Traits\ConceptsEntity;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * @coversDefaultClass \Ilios\CoreBundle\Traits\ConceptsEntity
 */

class ConceptsEntityTest extends TestCase
{
    /**
     * @var ConceptsEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = ConceptsEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers ::setConcepts
     */
    public function testSetConcepts()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(MeshConcept::class));
        $collection->add(m::mock(MeshConcept::class));
        $collection->add(m::mock(MeshConcept::class));

        $this->traitObject->setConcepts($collection);
        $this->assertEquals($collection, $this->traitObject->getConcepts());
    }

    /**
     * @covers ::removeConcept
     */
    public function testRemoveConcept()
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
}
