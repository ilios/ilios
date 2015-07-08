<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class SchoolManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SchoolManager::deleteSchool
     */
    public function testDeleteSchool()
    {
        $class = 'Ilios\CoreBundle\Entity\School';
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
        $manager = new SchoolManager($registry, $class);
        $manager->deleteSchool($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SchoolManager::findSchoolBy
     */
    public function testFindSchoolDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\School';
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
        $manager = new SchoolManager($registry, $class);
        $manager->findSchoolBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SchoolManager::findSchoolsBy
     */
    public function testFindSchoolsDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\School';
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
        $manager = new SchoolManager($registry, $class);
        $manager->findSchoolsBy(array('foo' => 'bar'));
    }
}
