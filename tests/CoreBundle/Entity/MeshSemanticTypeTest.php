<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\MeshSemanticType;
use Mockery as m;

/**
 * Tests for Entity MeshSemanticType
 */
class MeshSemanticTypeTest extends EntityBase
{
    /**
     * @var MeshSemanticType
     */
    protected $object;

    /**
     * Instantiate a MeshSemanticType object
     */
    protected function setUp()
    {
        $this->object = new MeshSemanticType;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name',
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('long name test');
        $this->validate(0);
    }


    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::__construct
     */
    public function testConstructor()
    {
        $now = new \DateTime();
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setName
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept', false, false, 'addSemanticType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept', false, false, false, 'removeSemanticType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getConcepts
     */
    public function getGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept', false, false, 'addSemanticType');
    }
}
