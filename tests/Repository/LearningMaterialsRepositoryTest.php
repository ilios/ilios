<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\LearningMaterial;
use App\Repository\LearningMaterialRepository;
use App\Tests\Fixture\LoadLearningMaterialData;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class LearningMaterialsRepositoryTest extends KernelTestCase
{
    protected ReferenceRepository $fixtures;
    protected LearningMaterialRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $databaseToot = self::$kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseToot->loadFixtures([
            LoadLearningMaterialData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        /** @var LearningMaterialRepository $repository */
        $repository = $entityManager->getRepository(LearningMaterial::class);
        $this->repository = $repository;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->fixtures);
        unset($this->repository);
    }

    public function testFindById(): void
    {
        $entity = $this->repository->findOneBy(['id' => 1]);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(LearningMaterial::class, $entity);
        $this->assertSame(1, $entity->getId());
        $this->assertSame('firstlmtitle', $entity->getTitle());
    }
}
