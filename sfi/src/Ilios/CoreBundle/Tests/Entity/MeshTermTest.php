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
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setMeshTermUid
     */
    public function testSetMeshTermUid()
    {
        $this->basicSetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getMeshTermUid
     */
    public function testGetMeshTermUid()
    {
        $this->basicGetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getLexicalTag
     */
    public function testGetLexicalTag()
    {
        $this->basicGetTest('lexicalTag', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->basicSetTest('conceptPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getConceptPreferred
     */
    public function testGetConceptPreferred()
    {
        $this->basicGetTest('conceptPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->basicSetTest('recordPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getRecordPreferred
     */
    public function testGetRecordPreferred()
    {
        $this->basicGetTest('recordPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setPermuted
     */
    public function testSetPermuted()
    {
        $this->basicSetTest('permuted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getPermuted
     */
    public function testGetPermuted()
    {
        $this->basicGetTest('permuted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setPrint
     */
    public function testSetPrint()
    {
        $this->basicSetTest('print', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getPrint
     */
    public function testGetPrint()
    {
        $this->basicGetTest('print', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTerm::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
