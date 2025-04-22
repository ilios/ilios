<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Entity\DTO\LearningMaterialDTO;
use App\Message\LearningMaterialIndexRequest;
use App\Message\LearningMaterialTextExtractionRequest;
use App\MessageHandler\LearningMaterialTextExtractionHandler;
use App\Repository\LearningMaterialRepository;
use App\Service\LearningMaterialTextExtractor;
use App\Tests\TestCase;
use Mockery as m;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class LearningMaterialTextExtractionHandlerTest extends TestCase
{
    protected m\MockInterface|LearningMaterialTextExtractor $extractor;
    protected m\MockInterface|LearningMaterialRepository $repository;
    protected m\MockInterface|MessageBusInterface $bus;

    public function setUp(): void
    {
        parent::setUp();
        $this->extractor = m::mock(LearningMaterialTextExtractor::class);
        $this->repository = m::mock(LearningMaterialRepository::class);
        $this->bus = m::mock(MessageBusInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->extractor);
        unset($this->repository);
        unset($this->bus);
    }

    public function testInvoke(): void
    {
        $dto1 = m::mock(LearningMaterialDTO::class);
        $handler = new LearningMaterialTextExtractionHandler($this->extractor, $this->repository, $this->bus);
        $request = new LearningMaterialTextExtractionRequest([6]);

        $this->repository->shouldReceive(('findDTOsBy'))
            ->once()
            ->with(['id' => [6]])
            ->andReturn([$dto1]);

        $this->extractor
            ->shouldReceive('extract')
            ->with($dto1, false);

        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (LearningMaterialIndexRequest $request) => in_array(6, $request->getIds()))
            ->andReturn(new Envelope(new stdClass()))
            ->once();

        $handler->__invoke($request);
    }

    public function testInvokeWithMaximumMaterials(): void
    {
        $lotsOfMaterials = LearningMaterialIndexRequest::MAX_MATERIALS * 3;
        $dtos = [];
        for ($i = 0; $i < $lotsOfMaterials; $i++) {
            $dto = m::mock(LearningMaterialDTO::class);
            $dto->id = $i + 1;
            $dtos[] = $dto;
            $this->extractor
                ->shouldReceive('extract')
                ->with($dto, false)
                ->once();
        }
        $ids = array_column($dtos, 'id');
        $handler = new LearningMaterialTextExtractionHandler($this->extractor, $this->repository, $this->bus);
        $request = new LearningMaterialTextExtractionRequest($ids);

        $this->repository->shouldReceive(('findDTOsBy'))
            ->once()
            ->with(['id' => $ids])
            ->andReturn($dtos);

        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (LearningMaterialIndexRequest $request) => array_diff($request->getIds(), $ids) === [])
            ->andReturn(new Envelope(new stdClass()))
        ->times(3);

        $handler->__invoke($request);
    }
}
