<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\CourseManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class CourseManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\CourseManager::deleteCourse
     */
    public function testDeleteCourse()
    {
        $class = 'Ilios\CoreBundle\Entity\Course';
        $em = m::mock('Doctrine\ORM\EntityManager')
            ->shouldReceive('persist')->shouldReceive('flush')->mock();
        $repository = m::mock('Doctrine\ORM\Repository');
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        
        $entity = m::mock($class)
            ->shouldReceive('setDeleted')->with(true)->mock();
        $manager = new CourseManager($registry, $class);
        $manager->deleteCourse($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\CourseManager::findCourseBy
     */
    public function testFindCourseDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Course';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findOneBy')
            ->with(array('foo' => 'bar', 'deleted' => false), null)
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new CourseManager($registry, $class);
        $manager->findCourseBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\CourseManager::findCoursesBy
     */
    public function testFindCoursesDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Course';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('findBy')
            ->with(array('foo' => 'bar', 'deleted' => false), null, null, null)
            ->mock();
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        $manager = new CourseManager($registry, $class);
        $manager->findCoursesBy(array('foo' => 'bar'));
    }
}
