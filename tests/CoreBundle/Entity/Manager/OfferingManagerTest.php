<?php
namespace Tests\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\Session;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class OfferingManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\OfferingManager::delete
     */
    public function testDeleteOffering()
    {
        $class = 'Ilios\CoreBundle\Entity\Offering';
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
        $entity->shouldReceive('stampUpdate')->once();
        $entity->shouldReceive('getSessions')->once()->andReturn([]);
        $manager = new OfferingManager($registry, $class);
        $manager->delete($entity);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\OfferingManager::getOfferingsForTeachingReminders
     */
    public function testGetOfferingsForTeachingReminders()
    {
        $offering = new Offering();
        $session  = new Session();
        $offering->setSession($session);
        $course = new Course();
        $session->setCourse($course);

        $class = 'Ilios\CoreBundle\Entity\Offering';
        $em = m::mock('Doctrine\ORM\EntityManager');
        $repository = m::mock('Doctrine\ORM\Repository')
            ->shouldReceive('matching')
            ->andReturn(new ArrayCollection([$offering]))
            ->mock();

        $registry = m::mock('Doctrine\Bundle\DoctrineBundle\Registry')
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();

        $manager = new OfferingManager($registry, $class);

        $session->setPublished(true);
        $course->setPublished(true);
        $offerings  = $manager->getOfferingsForTeachingReminders(10);
        $this->assertEquals(1, $offerings->count());
        $this->assertEquals($offering, $offerings->first());

        $session->setPublished(false);
        $course->setPublished(true);
        $offerings  = $manager->getOfferingsForTeachingReminders(10);
        $this->assertEquals(0, $offerings->count());

        $session->setPublished(true);
        $course->setPublished(false);
        $offerings  = $manager->getOfferingsForTeachingReminders(10);
        $this->assertEquals(0, $offerings->count());

        $session->setPublished(false);
        $course->setPublished(false);
        $offerings  = $manager->getOfferingsForTeachingReminders(10);
        $this->assertEquals(0, $offerings->count());
    }
}
