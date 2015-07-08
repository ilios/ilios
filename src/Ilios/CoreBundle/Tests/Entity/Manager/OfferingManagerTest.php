<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class OfferingManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\OfferingManager::deleteOffering
     */
    public function testDeleteOffering()
    {
        $class = 'Ilios\CoreBundle\Entity\Offering';
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
        $manager = new OfferingManager($registry, $class);
        $manager->deleteOffering($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\OfferingManager::findOfferingBy
     */
    public function testFindOfferingDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Offering';
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
        $manager = new OfferingManager($registry, $class);
        $manager->findOfferingBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\OfferingManager::findOfferingsBy
     */
    public function testFindOfferingsDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\Offering';
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
        $manager = new OfferingManager($registry, $class);
        $manager->findOfferingsBy(array('foo' => 'bar'));
    }
}
