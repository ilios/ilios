<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Tests\Fixture\LoadSessionData;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SessionRepositoryTest extends KernelTestCase
{
    protected ReferenceRepository $fixtures;
    protected SessionRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $databaseToot = self::$kernel->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseToot->loadFixtures([
            LoadSessionData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();

        $entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        /** @var SessionRepository $repository */
        $repository = $entityManager->getRepository(Session::class);
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
        $this->assertInstanceOf(Session::class, $entity);
        $this->assertSame(1, $entity->getId());
        $this->assertSame('session1Title', $entity->getTitle());
    }

    public function testGetCoursesForSessionIds(): void
    {
        $arr = $this->repository->getCoursesForSessionIds([2, 3]);
        $this->assertCount(2, $arr);
        $this->assertSame([
            'sessionId' => 2,
            'courseId' => 1,
            'courseTitle' => 'firstCourse',
        ], $arr[0]);
        $this->assertSame([
            'sessionId' => 3,
            'courseId' => 2,
            'courseTitle' => 'course 2',
        ], $arr[1]);
    }
}
