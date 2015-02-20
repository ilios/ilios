<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\MeshTerm;
use Mockery as m;

/**
 * Tests for Entity MeshTerm
 */
class MeshTermTest extends EntityBase
{
    /**
     * @var MeshTerm
     */
    protected $object;

    /**
     * Instantiate a MeshTerm object
     */
    protected function setUp()
    {
        $this->object = new MeshTerm;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setName
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setLexicalTag
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setConceptPreferred
     * @covers Ilios\CoreBundle\Entity\MeshTerm::isConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->booleanSetTest('conceptPreferred');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setRecordPreferred
     * @covers Ilios\CoreBundle\Entity\MeshTerm::isRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->booleanSetTest('recordPreferred');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setPermuted
     * @covers Ilios\CoreBundle\Entity\MeshTerm::isPermuted
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('permuted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setPrintable
     * @covers Ilios\CoreBundle\Entity\MeshTerm::isPrintable
     */
    public function testSetPrintable()
    {
        $this->booleanSetTest('printable');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setUpdatedAt
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }
}
