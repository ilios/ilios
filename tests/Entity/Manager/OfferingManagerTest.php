<?php

declare(strict_types=1);

namespace App\Tests\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use App\Entity\Manager\OfferingManager;
use App\Entity\Offering;
use App\Repository\DTORepositoryInterface;
use Mockery as m;
use App\Tests\TestCase;

/**
 * Tests for Offering manager.
 * @group model
 */
class OfferingManagerTest extends TestCase
{
    /**
     * @covers \App\Entity\Manager\OfferingManager::delete
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
}
