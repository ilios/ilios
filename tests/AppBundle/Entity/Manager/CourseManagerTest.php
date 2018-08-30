<?php
namespace Tests\AppBundle\Entity\Manager;

use AppBundle\Entity\Manager\CourseManager;
use Mockery as m;
use Tests\AppBundle\TestCase;

/**
 * Class CourseManagerTest
 */
class CourseManagerTest extends TestCase
{
    /**
     * @covers \AppBundle\Entity\Manager\CourseManager::delete
     */
    public function testDeleteCourse()
    {
        $class = 'AppBundle\Entity\Course';
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
        $manager = new CourseManager($registry, $class);
        $manager->delete($entity);
    }
}
