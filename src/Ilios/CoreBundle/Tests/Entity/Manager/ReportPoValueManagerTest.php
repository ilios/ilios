<?php 
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ReportPoValueManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class ReportPoValueManagerTest  extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ReportPoValueManager::deleteReportPoValue
     */
    public function testDeleteReportPoValue()
    {
        $class = 'Ilios\CoreBundle\Entity\ReportPoValue';
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
        $manager = new ReportPoValueManager($registry, $class);
        $manager->deleteReportPoValue($entity);
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ReportPoValueManager::findReportPoValueBy
     */
    public function testFindReportPoValueDoesNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\ReportPoValue';
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
        $manager = new ReportPoValueManager($registry, $class);
        $manager->findReportPoValueBy(array('foo' => 'bar'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ReportPoValueManager::findReportPoValuesBy
     */
    public function testFindReportPoValuesDoNotIncludeDeleted()
    {
        $class = 'Ilios\CoreBundle\Entity\ReportPoValue';
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
        $manager = new ReportPoValueManager($registry, $class);
        $manager->findReportPoValuesBy(array('foo' => 'bar'));
    }
}
