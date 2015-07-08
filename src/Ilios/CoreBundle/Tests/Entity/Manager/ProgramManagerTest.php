<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ProgramManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class ProgramManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ProgramManager::deleteProgram
     */
    public function testDeleteProgram()
    {
        $class = 'Ilios\CoreBundle\Entity\Program';
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
        $manager = new ProgramManager($registry, $class);
        $manager->deleteProgram($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ProgramManager::findProgramBy
     */
    public function testFindProgramDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Program';
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
        $manager = new ProgramManager($registry, $class);
        $manager->findProgramBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ProgramManager::findProgramsBy
     */
    public function testFindProgramsDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Program';
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
        $manager = new ProgramManager($registry, $class);
        $manager->findProgramsBy(array('foo' => 'bar'));
    }
}
