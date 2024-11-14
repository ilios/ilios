<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

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
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\EventListener\IndexEntityChanges::class)]
class IndexEntityChangesTest extends TestCase
{
    protected m\MockInterface $curriculumIndex;
    protected m\MockInterface $learningMaterialIndex;

    protected m\MockInterface $meshIndex;

    protected m\MockInterface $usersIndex;

    protected m\MockInterface $bus;

    protected IndexEntityChanges $indexEntityChanges;

    protected function setUp(): void
    {
        $this->curriculumIndex = m::mock(Curriculum::class);
        $this->learningMaterialIndex = m::mock(LearningMaterials::class);
        $this->meshIndex = m::mock(Mesh::class);
        $this->usersIndex = m::mock(Users::class);
        $this->bus = m::mock(MessageBusInterface::class);
        $this->indexEntityChanges = new IndexEntityChanges(
            $this->curriculumIndex,
            $this->learningMaterialIndex,
            $this->meshIndex,
            $this->usersIndex,
            $this->bus
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

        $this->indexEntityChanges->postUpdate($args);
    }
}
