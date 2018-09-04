<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\LearningMaterial;
use AppBundle\Entity\School;
use AppBundle\Entity\User;
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
        $this->object->setUserRole(m::mock('AppBundle\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('AppBundle\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('AppBundle\Entity\UserInterface'));

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

        $this->object->setUserRole(m::mock('AppBundle\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('AppBundle\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('AppBundle\Entity\UserInterface'));

        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::setTitle
     * @covers \AppBundle\Entity\LearningMaterial::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::setDescription
     * @covers \AppBundle\Entity\LearningMaterial::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::setOriginalAuthor
     * @covers \AppBundle\Entity\LearningMaterial::getOriginalAuthor
     */
    public function testSetOriginalAuthor()
    {
        $this->basicSetTest('originalAuthor', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::getOwningSchool
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
     * @covers \AppBundle\Entity\LearningMaterial::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::getCourseLearningMaterials
     * @covers \AppBundle\Entity\LearningMaterial::setCourseLearningMaterials
     */
    public function getGetCourseLearningMaterials()
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\LearningMaterial::getSessionLearningMaterials
     * @covers \AppBundle\Entity\LearningMaterial::setSessionLearningMaterials
     */
    public function getGetSessionLearningMaterials()
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }
}
