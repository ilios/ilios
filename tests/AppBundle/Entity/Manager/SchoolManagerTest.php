<?php
namespace Tests\AppBundle\Entity\Manager;

use AppBundle\Entity\Manager\SchoolManager;
use AppBundle\Service\UserMaterialFactory;
use Mockery as m;
use Tests\AppBundle\TestCase;

/**
 * Class SchoolManagerTest
 */
class SchoolManagerTest extends TestCase
{
    
    /**
     * @covers \AppBundle\Entity\Manager\SchoolManager::delete
     */
    public function testDeleteSchool()
    {
        $class = 'AppBundle\Entity\School';
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
        $manager = new SchoolManager($registry, $class, m::mock(UserMaterialFactory::class));
        $manager->delete($entity);
    }
}
