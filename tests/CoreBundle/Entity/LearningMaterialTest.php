<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\User;
use Mockery as m;

/**
 * Tests for Entity LearningMaterial
 */
class LearningMaterialTest extends EntityBase
{
    /**
     * @var LearningMaterial
     */
    protected $object;

    /**
     * Instantiate a LearningMaterial object
     */
    protected function setUp()
    {
        $this->object = new LearningMaterial;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->object->setUserRole(m::mock('Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('Ilios\CoreBundle\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('Ilios\CoreBundle\Entity\UserInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'userRole',
            'status',
            'owningUser'
        );
        $this->object->setTitle('test');

        $this->validateNotNulls($notNulls);

        $this->object->setUserRole(m::mock('Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('Ilios\CoreBundle\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('Ilios\CoreBundle\Entity\UserInterface'));

        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::setTitle
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::setDescription
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::setOriginalAuthor
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getOriginalAuthor
     */
    public function testSetOriginalAuthor()
    {
        $this->basicSetTest('originalAuthor', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getOwningSchool
     */
    public function testGetOwningSchool()
    {
        $this->assertNull($this->object->getOwningSchool());

        $school = new School();
        $user = new User();
        $user->setSchool($school);
        $lm = new LearningMaterial();
        $lm->setOwningUser($user);
        $this->assertSame($school, $lm->getOwningSchool());

        $user = new User();
        $lm = new LearningMaterial();
        $lm->setOwningUser($user);
        $this->assertNull($lm->getOwningSchool());

        $lm = new LearningMaterial();
        $this->assertNull($lm->getOwningSchool());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getCourseLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::setCourseLearningMaterials
     */
    public function getGetCourseLearningMaterials()
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::getSessionLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\LearningMaterial::setSessionLearningMaterials
     */
    public function getGetSessionLearningMaterials()
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }
}
