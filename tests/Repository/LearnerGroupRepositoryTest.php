<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\LearnerGroup;
use App\Entity\User;
use App\Repository\LearnerGroupRepository;
use App\Tests\Fixture\LoadLearnerGroupData;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class LearnerGroupRepositoryTest extends KernelTestCase
{
    protected ReferenceRepository $fixtures;
    protected LearnerGroupRepository $repository;
    protected ObjectManager $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $databaseTool = self::$kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseTool->loadFixtures([
            LoadLearnerGroupData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();

        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        /** @var LearnerGroupRepository $repository */
        $repository = $this->entityManager->getRepository(LearnerGroup::class);
        $this->repository = $repository;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->fixtures);
        unset($this->repository);
        unset($this->entityManager);
    }

    public function testGetUsersIdsInGroup(): void
    {
        $userIds = $this->repository->getUsersIdsInGroup(1);
        sort($userIds);
        $this->assertSame([2, 5], $userIds);

        $this->assertSame([5], $this->repository->getUsersIdsInGroup(6));
        $this->assertSame([], $this->repository->getUsersIdsInGroup(4));
    }

    public function testGetChildUsersInGroup(): void
    {
        // Groups 4 and 6 are children of parent 1; group 6 has user 5, group 4 has none.
        $this->assertSame([6 => [5]], $this->repository->getChildUsersInGroup(1));

        // Groups with no children return an empty map.
        $this->assertSame([], $this->repository->getChildUsersInGroup(4));
    }

    public function testFindErrorsInGroupTreesReturnsEmptyWhenChildrenAreSubsetsOfParents(): void
    {
        // In the fixtures everything is working
        $errors = $this->repository->findErrorsInGroupTrees();
        $this->assertSame([], $errors);
    }

    public function testFindErrorsInGroupTreesReportsMembersMissingFromParent(): void
    {
        // Add some bad data
        $childGroup = $this->repository->find(4);
        $missingUser = $this->entityManager->getRepository(User::class)->find(3);
        $childGroup->addUser($missingUser);
        $this->entityManager->flush();

        $errors = $this->repository->findErrorsInGroupTrees();

        $this->assertCount(1, $errors);
        $error = $errors[0];
        $this->assertEquals(4, $error['id']);
        $this->assertEquals(1, $error['parentId']);
        $this->assertEquals([3], array_values($error['missingFromParent']));
    }
}
