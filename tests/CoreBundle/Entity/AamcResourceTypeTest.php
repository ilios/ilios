<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\AamcResourceType;
use Mockery as m;

/**
 * Tests for Entity AamcResourceType
 */
class AamcResourceTypeTest extends EntityBase
{
    /**
     * @var AamcResourceType
     */
    protected $object;

    /**
     * Instantiate a AamcResourceType object
     */
    protected function setUp()
    {
        $this->object = new AamcResourceType();
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'id',
            'title',
            'description'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('foo');
        $this->object->setDescription('bar');
        $this->object->setId('baz');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTerms());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::setTitle
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::setDescription
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeAamcResourceType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcResourceType::getTerms
     */
    public function testGetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addAamcResourceType');
    }
}
