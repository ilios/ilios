<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshTerm;
use Mockery as m;

/**
 * Tests for Model MeshTerm
 */
class MeshTermTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\MeshTerm::setMeshTermUid
     */
    public function testSetMeshTermUid()
    {
        $this->basicSetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getMeshTermUid
     */
    public function testGetMeshTermUid()
    {
        $this->basicGetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getLexicalTag
     */
    public function testGetLexicalTag()
    {
        $this->basicGetTest('lexicalTag', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->basicSetTest('conceptPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getConceptPreferred
     */
    public function testGetConceptPreferred()
    {
        $this->basicGetTest('conceptPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->basicSetTest('recordPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getRecordPreferred
     */
    public function testGetRecordPreferred()
    {
        $this->basicGetTest('recordPreferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setPermuted
     */
    public function testSetPermuted()
    {
        $this->basicSetTest('permuted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getPermuted
     */
    public function testGetPermuted()
    {
        $this->basicGetTest('permuted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setPrint
     */
    public function testSetPrint()
    {
        $this->basicSetTest('print', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getPrint
     */
    public function testGetPrint()
    {
        $this->basicGetTest('print', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTerm::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
