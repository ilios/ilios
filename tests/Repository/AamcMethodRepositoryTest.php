<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\AamcMethod;
use App\Entity\DTO\AamcMethodDTO;
use App\Repository\AamcMethodRepository;
use App\Tests\Fixture\LoadAamcMethodData;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AamcMethodRepositoryTest extends KernelTestCase
{
    protected ReferenceRepository $fixtures;
    protected AamcMethodRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $databaseToot = self::$kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseToot->loadFixtures([
            LoadAamcMethodData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        /** @var AamcMethodRepository $repository */
        $repository = $entityManager->getRepository(AamcMethod::class);
        $this->repository = $repository;
    }

    public function testFindById(): void
    {
        $entity = $this->repository->findOneBy(['id' => 'AM002']);
        $this->assertNotNull($entity);
        $this->assertInstanceOf(AamcMethod::class, $entity);
        $this->assertSame('AM002', $entity->getId());
    }

    public function testFindDtoById(): void
    {
        $dto = $this->repository->findDTOBy(['id' => 'AM002']);
        $this->assertNotNull($dto);
        $this->assertInstanceOf(AamcMethodDTO::class, $dto);
        $this->assertSame('AM002', $dto->id);
    }

    public function testFindDtosBy(): void
    {
        $dtos = $this->repository->findDTOsBy([]);
        $this->assertCount(2, $dtos);
        $this->assertInstanceOf(AamcMethodDTO::class, $dtos[0]);
        $this->assertSame('AM001', $dtos[0]->id);
        $this->assertInstanceOf(AamcMethodDTO::class, $dtos[1]);
        $this->assertSame('AM002', $dtos[1]->id);
    }

    public function testFindDtosByWithCacheEnabled(): void
    {
        $env = getenv('ILIOS_FEATURE_DTO_CACHING');
        putenv("ILIOS_FEATURE_DTO_CACHING=true");
        for ($i = 0; $i <= 1; $i++) {
            $dtos = $this->repository->findDTOsBy([]);
            $this->assertCount(2, $dtos);
            $this->assertInstanceOf(AamcMethodDTO::class, $dtos[0]);
            $this->assertSame('AM001', $dtos[0]->id);
            $this->assertInstanceOf(AamcMethodDTO::class, $dtos[1]);
            $this->assertSame('AM002', $dtos[1]->id);
        }
        putenv("ILIOS_FEATURE_DTO_CACHING={$env}");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->fixtures);
        unset($this->repository);
    }
}
