<?php
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Ilios\CoreBundle\Entity\Permission;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\User;
use Mockery as m;

/**
 * Class PermissionManagerTest
 * @package Ilios\CoreBundle\Tests\Entity\Manager
 */
class PermissionManagerTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasPermission
     */
    public function testUserHasPermission()
    {
        $user = new User();
        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 10,
                'tableName' => 'foo',
                'canWrite' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = \Mockery::mock('Ilios\CoreBundle\Entity\Manager\PermissionManager', [$registry, $class])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->assertTrue($manager->userHasPermission($user, PermissionManager::CAN_WRITE, 'foo', 10));

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 10,
                'tableName' => 'foo',
                'canWrite' => true,
                'user' => $user,
            ], null)
            ->andReturn(null)
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = \Mockery::mock('Ilios\CoreBundle\Entity\Manager\PermissionManager', [$registry, $class])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->assertFalse($manager->userHasPermission($user, PermissionManager::CAN_WRITE, 'foo', 10));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasWritePermissionToCourse
     */
    public function testUserHasWritePermissionToCourse()
    {
        $user = new User();
        $user->setId(10);

        $course = new Course();
        $course->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'course',
                'canWrite' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasWritePermissionToCourse($user, $course));
        $this->assertFalse($manager->userHasWritePermissionToCourse($user, null));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToCourse
     */
    public function testUserHasReadPermissionToCourse()
    {
        $user = new User();
        $user->setId(10);

        $course = new Course();
        $course->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'course',
                'canRead' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasReadPermissionToCourse($user, $course));
        $this->assertFalse($manager->userHasReadPermissionToCourse($user, null));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasWritePermissionToProgram
     */
    public function testUserHasWritePermissionToProgram()
    {
        $user = new User();
        $user->setId(10);

        $program = new Program();
        $program->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'program',
                'canWrite' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasWritePermissionToProgram($user, $program));
        $this->assertFalse($manager->userHasWritePermissionToProgram($user, null));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToProgram
     */
    public function testUserHasReadPermissionToProgram()
    {
        $user = new User();
        $user->setId(10);

        $program = new Program();
        $program->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'program',
                'canRead' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasReadPermissionToProgram($user, $program));
        $this->assertFalse($manager->userHasReadPermissionToProgram($user, null));
    }


    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasWritePermissionToSchool
     */
    public function testUserHasWritePermissionToSchool()
    {
        $user = new User();
        $user->setId(10);

        $school = new School();
        $school->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'school',
                'canWrite' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasWritePermissionToSchool($user, $school->getId()));
        $this->assertFalse($manager->userHasWritePermissionToSchool($user, null));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToSchool
     */
    public function testUserHasReadPermissionToSchool()
    {
        $user = new User();
        $user->setId(10);

        $school = new School();
        $school->setId(100);

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableRowId' => 100,
                'tableName' => 'school',
                'canRead' => true,
                'user' => $user,
            ], null)
            ->andReturn(new Permission())
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertTrue($manager->userHasReadPermissionToSchool($user, $school));
        $this->assertFalse($manager->userHasReadPermissionToSchool($user, null));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasWritePermissionToSchools
     */
    public function testUserHasWritePermissionToSchools()
    {
        $schoolA = new School();
        $schoolA->setId(100);
        $schoolB = new School();
        $schoolB->setId(200);
        $schoolC = new School();
        $schoolC->setId(300);

        $schoolPermissionA = new Permission();
        $schoolPermissionA->setTableRowId(100);
        $schoolPermissionB = new Permission();
        $schoolPermissionB->setTableRowId(200);
        $schoolPermissionC = new Permission();
        $schoolPermissionC->setTableRowId(300);

        $user = new User();

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findBy')
            ->with([
                'tableName' => 'school',
                'canWrite' => true,
                'user' => $user,
            ], null, null, null)
            ->andReturn([$schoolPermissionA, $schoolPermissionB])
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertTrue($manager->userHasWritePermissionToSchools($user, new ArrayCollection([$schoolA])));
        $this->assertTrue($manager->userHasWritePermissionToSchools($user, new ArrayCollection([$schoolA, $schoolC])));
        $this->assertFalse($manager->userHasWritePermissionToSchools($user, new ArrayCollection([$schoolC])));
        $this->assertFalse($manager->userHasWritePermissionToSchools($user, new ArrayCollection()));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToSchools
     */
    public function testUserHasReadPermissionToSchools()
    {
        $schoolA = new School();
        $schoolA->setId(100);
        $schoolB = new School();
        $schoolB->setId(200);
        $schoolC = new School();
        $schoolC->setId(300);

        $schoolPermissionA = new Permission();
        $schoolPermissionA->setTableRowId(100);
        $schoolPermissionB = new Permission();
        $schoolPermissionB->setTableRowId(200);
        $schoolPermissionC = new Permission();
        $schoolPermissionC->setTableRowId(300);

        $user = new User();

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findBy')
            ->with([
                'tableName' => 'school',
                'canRead' => true,
                'user' => $user,
            ], null, null, null)
            ->andReturn([$schoolPermissionA, $schoolPermissionB])
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertTrue($manager->userHasReadPermissionToSchools($user, new ArrayCollection([$schoolA])));
        $this->assertTrue($manager->userHasReadPermissionToSchools($user, new ArrayCollection([$schoolA, $schoolC])));
        $this->assertFalse($manager->userHasReadPermissionToSchools($user, new ArrayCollection([$schoolC])));
        $this->assertFalse($manager->userHasReadPermissionToSchools($user, new ArrayCollection()));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToCoursesBySchool
     */
    public function testUserHasReadPermissionToCoursesBySchool()
    {
        $schoolA = new School();
        $schoolA->setId(100);
        $schoolB = new School();
        $schoolB->setId(200);
        $schoolC = new School();
        $schoolC->setId(300);

        $courseA = new Course();
        $courseA->setId(1);
        $schoolA->addCourse($courseA);

        $courseB = new Course();
        $courseB->setId(2);
        $schoolB->addCourse($courseB);

        $coursePermissionA = new Permission();
        $coursePermissionA->setTableRowId(1);

        $user = new User();

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableName' => 'course',
                'canRead' => true,
                'user' => $user,
                'tableRowId' => $courseA->getId()
            ], null)
            ->andReturn([$coursePermissionA])
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertFalse($manager->userHasReadPermissionToCoursesInSchool($user, null));
        $this->assertTrue($manager->userHasReadPermissionToCoursesInSchool($user, $schoolA));

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableName' => 'course',
                'canRead' => true,
                'user' => $user,
                'tableRowId' => $courseB->getId()
            ], null)
            ->andReturn(null)
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertFalse($manager->userHasReadPermissionToCoursesInSchool($user, $schoolB));

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldNotReceive('findOneBy')
            ->mock();

        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertFalse($manager->userHasReadPermissionToCoursesInSchool($user, $schoolC));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Manager\PermissionManager::userHasReadPermissionToCoursesAssociatedWithCohort
     */
    public function testUserHasReadPermissionToCoursesAssociatedWithCohort()
    {
        $cohortA = new Cohort();
        $cohortA->setId(100);
        $cohortB = new Cohort();
        $cohortB->setId(200);
        $cohortC = new Cohort();
        $cohortC->setId(300);

        $courseA = new Course();
        $courseA->setId(1);
        $cohortA->addCourse($courseA);

        $courseB = new Course();
        $courseB->setId(2);
        $cohortB->addCourse($courseB);

        $coursePermissionA = new Permission();
        $coursePermissionA->setTableRowId(1);

        $user = new User();

        $class = 'Ilios\CoreBundle\Entity\Permission';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableName' => 'course',
                'canRead' => true,
                'user' => $user,
                'tableRowId' => $courseA->getId()
            ], null)
            ->andReturn([$coursePermissionA])
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);

        $this->assertFalse($manager->userHasReadPermissionToCoursesAssociatedWithCohort($user, null));
        $this->assertTrue($manager->userHasReadPermissionToCoursesAssociatedWithCohort($user, $cohortA));

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with([
                'tableName' => 'course',
                'canRead' => true,
                'user' => $user,
                'tableRowId' => $courseB->getId()
            ], null)
            ->andReturn(null)
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertFalse($manager->userHasReadPermissionToCoursesAssociatedWithCohort($user, $cohortB));

        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldNotReceive('findOneBy')
            ->mock();

        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $this->assertFalse($manager->userHasReadPermissionToCoursesAssociatedWithCohort($user, $cohortC));
    }
}
