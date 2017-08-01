<?php
namespace Tests\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\Repository\DTORepositoryInterface;
use Ilios\CoreBundle\Entity\Session;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * Tests for Entity AamcMethod
 */
class OfferingManagerTest extends TestCase
{
    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\OfferingManager::delete
     */
    public function testDeleteOffering()
    {
        $em = m::mock(EntityManager::class)
            ->shouldReceive('remove')->shouldReceive('flush')->mock();
        $repository = m::mock(DTORepositoryInterface::class);
        $registry = m::mock(Registry::class)
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();
        
        $entity = m::mock(Offering::class);
        $manager = new OfferingManager($registry, Offering::class);
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

        $em = m::mock(EntityManager::class);
        $repository = m::mock(DTORepositoryInterface::class)
            ->shouldReceive('matching')
            ->andReturn(new ArrayCollection([$offering]))
            ->mock();

        $registry = m::mock(Registry::class)
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($repository)
            ->mock();

        $manager = new OfferingManager($registry, Offering::class);

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
