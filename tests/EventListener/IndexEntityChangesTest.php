<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\Entity\CourseInterface;
use App\Entity\LearningMaterialInterface;
use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\LearningMaterialTextExtractionRequest;
use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\UserInterface;
use App\EventListener\IndexEntityChanges;
use App\Message\UserIndexRequest;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use App\Service\Index\Mesh;
use App\Service\Index\Users;
use App\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery as m;
use stdClass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

#[CoversClass(IndexEntityChanges::class)]
final class IndexEntityChangesTest extends TestCase
{
    protected m\MockInterface $curriculumIndex;
    protected m\MockInterface $learningMaterialIndex;

    protected m\MockInterface $meshIndex;

    protected m\MockInterface $usersIndex;

    protected m\MockInterface $bus;

    protected m\MockInterface $logger;

    protected IndexEntityChanges $indexEntityChanges;

    protected function setUp(): void
    {
        $this->curriculumIndex = m::mock(Curriculum::class);
        $this->learningMaterialIndex = m::mock(LearningMaterials::class);
        $this->meshIndex = m::mock(Mesh::class);
        $this->usersIndex = m::mock(Users::class);
        $this->bus = m::mock(MessageBusInterface::class);
        $this->logger = m::mock(LoggerInterface::class);
        $this->indexEntityChanges = new IndexEntityChanges(
            $this->curriculumIndex,
            $this->learningMaterialIndex,
            $this->meshIndex,
            $this->usersIndex,
            $this->bus,
            $this->logger
        );
    }

    protected function tearDown(): void
    {
        unset($this->curriculumIndex);
        unset($this->learningMaterialIndex);
        unset($this->meshIndex);
        unset($this->usersIndex);
        unset($this->bus);
    }

    public function testPostUpdateUserNoDispatchOnExaminedOnlyChange(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(UserInterface::class);
        $changed = ['examined' => 'we only care about the key'];
        $args = new PostUpdateEventArgs($entity, $objectManager);

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);

        $this->indexEntityChanges->postUpdate($args);

        $this->bus->shouldNotHaveReceived('dispatch');
    }

    public function testPostUpdateUserNoDispatchIfIndexerIsDisabled(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(UserInterface::class);
        $changed = ['nyuk' => 'nyuk nuyk'];
        $args = new PostUpdateEventArgs($entity, $objectManager);

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->usersIndex->shouldReceive('isEnabled')->andReturn(false);

        $this->indexEntityChanges->postUpdate($args);

        $this->bus->shouldNotHaveReceived('dispatch');
    }

    public function testPostUpdateUserDispatch(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(UserInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk', 'examined' => 'lorem ipsum'];
        $args = new PostUpdateEventArgs($entity, $objectManager);
        $userId = 12;

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->usersIndex->shouldReceive('isEnabled')->andReturn(true);
        $entity->shouldReceive('getId')->andReturn($userId);
        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (UserIndexRequest $request) => in_array($userId, $request->getUserIds()))
            ->andReturn(new Envelope(new stdClass()))
            ->once();
        $this->logger->shouldReceive('debug')->once();
        $this->indexEntityChanges->postUpdate($args);
    }

    public function testPostUpdateMaterialDispatch(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(LearningMaterialInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk'];
        $args = new PostUpdateEventArgs($entity, $objectManager);
        $materialId = 12;

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->learningMaterialIndex->shouldReceive('isEnabled')->andReturn(true);
        $this->curriculumIndex->shouldReceive('isEnabled')->andReturn(false);
        $entity->shouldReceive('getId')->andReturn($materialId);
        $entity->shouldReceive('getFilename')->andReturn('skiziks.pdf');
        $entity->shouldReceive('getIndexableCourses')->andReturn([]);
        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (LearningMaterialIndexRequest $request) => in_array($materialId, $request->getIds()))
            ->andReturn(new Envelope(new stdClass()))
            ->once();
        $this->logger->shouldReceive('debug')->once();
        $this->indexEntityChanges->postUpdate($args);
    }

    public function testPostUpdateMaterialDispatchSkipsNonFileLms(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(LearningMaterialInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk'];
        $args = new PostUpdateEventArgs($entity, $objectManager);

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->learningMaterialIndex->shouldReceive('isEnabled')->andReturn(true);
        $this->curriculumIndex->shouldReceive('isEnabled')->andReturn(false);
        $entity->shouldReceive('getFilename')->andReturn(null);
        $entity->shouldReceive('getIndexableCourses')->andReturn([]);
        $this->bus->shouldNotReceive('dispatch');
        $this->logger->shouldNotReceive('debug');
        $this->indexEntityChanges->postUpdate($args);
    }

    public function testPostUpdateCourseDispatch(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(CourseInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk'];
        $args = new PostUpdateEventArgs($entity, $objectManager);
        $courseId = 12;

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->curriculumIndex->shouldReceive('isEnabled')->andReturn(true);
        $entity->shouldReceive('getId')->andReturn($courseId);
        $entity->shouldReceive('getIndexableCourses')->andReturn([$entity]);
        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (CourseIndexRequest $request) => in_array($courseId, $request->getCourseIds()))
            ->andReturn(new Envelope(new stdClass()))
            ->once();
        $this->logger->shouldReceive('debug')->once();
        $this->indexEntityChanges->postUpdate($args);
    }

    public function testPostPersistMaterialDispatch(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(LearningMaterialInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk'];
        $args = new PostPersistEventArgs($entity, $objectManager);
        $materialId = 12;

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->learningMaterialIndex->shouldReceive('isEnabled')->andReturn(true);
        $this->curriculumIndex->shouldReceive('isEnabled')->andReturn(false);
        $entity->shouldReceive('getId')->andReturn($materialId);
        $entity->shouldReceive('getFilename')->andReturn('skiziks.pdf');
        $entity->shouldReceive('getIndexableCourses')->andReturn([]);
        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (LearningMaterialTextExtractionRequest $request) => in_array(
                $materialId,
                $request->getLearningMaterialIds()
            ))
            ->andReturn(new Envelope(new stdClass()))
            ->once();
        $this->indexEntityChanges->postPersist($args);
    }

    public function testPostPersistMaterialDispatchSkipsNonFileMaterials(): void
    {
        $objectManager = m::mock(EntityManagerInterface::class);
        $unitOfWork = m::mock(UnitOfWork::class);
        $entity = m::mock(LearningMaterialInterface::class);
        $changed = ['nyuk' => 'nuyk nyuk'];
        $args = new PostPersistEventArgs($entity, $objectManager);

        $objectManager->shouldReceive('getUnitOfWork')->andReturn($unitOfWork);
        $unitOfWork->shouldReceive('getEntityChangeSet')->with($entity)->andReturn($changed);
        $this->learningMaterialIndex->shouldReceive('isEnabled')->andReturn(true);
        $this->curriculumIndex->shouldReceive('isEnabled')->andReturn(false);
        $entity->shouldReceive('getFilename')->andReturn(null);
        $entity->shouldReceive('getIndexableCourses')->andReturn([]);
        $this->bus->shouldNotReceive('dispatch');
        $this->indexEntityChanges->postPersist($args);
    }
}
