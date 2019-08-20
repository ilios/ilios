<?php
namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\LearningMaterial;
use App\Entity\School;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\User;
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
        $this->object->setUserRole(m::mock('App\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('App\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('App\Entity\UserInterface'));

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

        $this->object->setUserRole(m::mock('App\Entity\LearningMaterialUserRoleInterface'));
        $this->object->setStatus(m::mock('App\Entity\LearningMaterialStatusInterface'));
        $this->object->setOwningUser(m::mock('App\Entity\UserInterface'));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
    }

    /**
     * @covers \App\Entity\LearningMaterial::setTitle
     * @covers \App\Entity\LearningMaterial::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::setDescription
     * @covers \App\Entity\LearningMaterial::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::setOriginalAuthor
     * @covers \App\Entity\LearningMaterial::getOriginalAuthor
     */
    public function testSetOriginalAuthor()
    {
        $this->basicSetTest('originalAuthor', 'string');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getOwningSchool
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
     * @covers \App\Entity\LearningMaterial::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getCourseLearningMaterials
     * @covers \App\Entity\LearningMaterial::setCourseLearningMaterials
     */
    public function getGetCourseLearningMaterials()
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getSessionLearningMaterials
     * @covers \App\Entity\LearningMaterial::setSessionLearningMaterials
     */
    public function getGetSessionLearningMaterials()
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers \App\Entity\LearningMaterial::getIndexableCourses
     */
    public function testGetIndexableCourses()
    {
        $course1 = m::mock(CourseInterface::class);
        $courseLearningMaterial = m::mock(CourseLearningMaterialInterface::class)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course1);
        $this->object->addCourseLearningMaterial($courseLearningMaterial->getMock());

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course2);
        $sessionLearningMaterial = m::mock(SessionLearningMaterialInterface::class)
                    ->shouldReceive('getSession')->once()
                    ->andReturn($session->getMock());
        $this->object->addSessionLearningMaterial($sessionLearningMaterial->getMock());

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }
}
