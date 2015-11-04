<?php
namespace Ilios\CoreBundle\Tests\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ReportManager;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class ReportManagerTest extends TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Manager\ReportManager::deleteReport
     */
    public function testDeleteReport()
    {
        $class = 'Ilios\CoreBundle\Entity\Report';
        $em = m::mock('Doctrine\ORM\EntityManager')
            ->shouldReceive('remove')->shouldReceive('flush')->mock();
        $repository = m::mock('Doctrine\ORM\Repository');
        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        
        $entity = m::mock($class);
        $manager = new ReportManager($registry, $class);
        $manager->deleteReport($entity);
    }
}
