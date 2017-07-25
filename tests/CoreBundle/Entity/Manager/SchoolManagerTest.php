<?php
namespace Tests\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * Class SchoolManagerTest
 */
class SchoolManagerTest extends TestCase
{
    
    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\SchoolManager::delete
     */
    public function testDeleteSchool()
    {
        $class = 'Ilios\CoreBundle\Entity\School';
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
        $manager = new SchoolManager($registry, $class);
        $manager->delete($entity);
    }
}
