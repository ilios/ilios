<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\UserPreference;
use App\Repository\UserPreferenceRepository;
use App\Tests\Fixture\LoadUserPreferenceData;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserPreferenceRepositoryTest extends KernelTestCase
{
    protected ReferenceRepository $fixtures;
    protected UserPreferenceRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $databaseTool = self::$kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseTool->loadFixtures([
            LoadUserPreferenceData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        /** @var UserPreferenceRepository $repository */
        $repository = $entityManager->getRepository(UserPreference::class);
        $this->repository = $repository;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->fixtures);
        unset($this->repository);
    }

    public function testGetJsonForUserReturnsJsonWhenPreferenceExists(): void
    {
        $this->assertSame('{"theme":"dark","locale":"en"}', $this->repository->getJsonForUser(1));
    }

    public function testGetJsonForUserReturnsNullWhenNoPreferenceExists(): void
    {
        $this->assertNull($this->repository->getJsonForUser(2));
    }

    public function testGetJsonForUserReturnsNullForUnknownUser(): void
    {
        $this->assertNull($this->repository->getJsonForUser(999999));
    }
}
