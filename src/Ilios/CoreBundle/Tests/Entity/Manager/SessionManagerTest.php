<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SessionManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class SessionManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SessionManager::deleteSession
     */
    public function testDeleteSession()
    {
        $class = 'Ilios\CoreBundle\Entity\Session';
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
        $manager = new SessionManager($registry, $class);
        $manager->deleteSession($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SessionManager::findSessionBy
     */
    public function testFindSessionDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Session';
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
        $manager = new SessionManager($registry, $class);
        $manager->findSessionBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\SessionManager::findSessionsBy
     */
    public function testFindSessionsDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Session';
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
        $manager = new SessionManager($registry, $class);
        $manager->findSessionsBy(array('foo' => 'bar'));
    }
}
