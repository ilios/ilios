<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Message\LearningMaterialIndexRequest;
use App\MessageHandler\LearningMaterialIndexHandler;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use App\Service\NonCachingIliosFileSystem;
use App\Tests\TestCase;
use Mockery as m;

class LearningMaterialIndexHandlerTest extends TestCase
{
    protected m\MockInterface|LearningMaterials $index;
    protected m\MockInterface|LearningMaterialRepository $repository;
    protected m\MockInterface|NonCachingIliosFileSystem $fs;

    public function setUp(): void
    {
        parent::setUp();
        $this->index = m::mock(LearningMaterials::class);
        $this->repository = m::mock(LearningMaterialRepository::class);
        $this->fs = m::mock(NonCachingIliosFileSystem::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->index);
        unset($this->repository);
        unset($this->fs);
    }

    public function testInvoke(): void
    {
        $dto1 = m::mock(LearningMaterialDTO::class);
        $dto1->relativePath = 'one';
        $dto2 = m::mock(LearningMaterialDTO::class);
        $dto2->relativePath = 'two';
        $dto3 = m::mock(LearningMaterialDTO::class);
        $dto3->relativePath = 'three';
        $handler = new LearningMaterialIndexHandler($this->index, $this->repository, $this->fs);
        $request = new LearningMaterialIndexRequest([6, 24, 2005]);

        $this->repository->shouldReceive(('findDTOsBy'))
            ->once()
            ->with(['id' => [6, 24, 2005]])
            ->andReturn([
                $dto1,
                $dto2,
                $dto3,
            ]);

        $this->fs
            ->shouldReceive('checkIfLearningMaterialTextFileExists')
            ->with('one')
            ->andReturn(true);

        $this->fs
            ->shouldReceive('checkIfLearningMaterialTextFileExists')
            ->with('two')
            ->andReturn(false);

        $this->fs
            ->shouldReceive('checkIfLearningMaterialTextFileExists')
            ->with('three')
            ->andReturn(true);

        $this->index
            ->shouldReceive('index')
            ->once()
            ->withArgs(function (array $materials) use ($dto1, $dto2, $dto3) {
                $this->assertCount(2, $materials);
                $this->assertContains($dto1, $materials);
                $this->assertNotContains($dto2, $materials);
                $this->assertContains($dto3, $materials);

                $this->assertEquals([$dto1, $dto3], $materials);

                return true;
            });

        $handler->__invoke($request);
    }
}
