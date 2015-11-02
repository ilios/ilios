<?php
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasWritePermissionToCourse($user, $course);

        $manager = new PermissionManager($registry, $class);
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasReadPermissionToCourse($user, $course);

        $manager = new PermissionManager($registry, $class);
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasWritePermissionToProgram($user, $program);

        $manager = new PermissionManager($registry, $class);
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasReadPermissionToProgram($user, $program);

        $manager = new PermissionManager($registry, $class);
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasWritePermissionToSchool($user, $school);

        $manager = new PermissionManager($registry, $class);
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
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new PermissionManager($registry, $class);
        $manager->userHasReadPermissionToSchool($user, $school);

        $manager = new PermissionManager($registry, $class);
        $this->assertFalse($manager->userHasReadPermissionToSchool($user, null));
    }
}