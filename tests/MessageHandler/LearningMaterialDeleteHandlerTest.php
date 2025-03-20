<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\LearningMaterialDeleteRequest;
use App\MessageHandler\LearningMaterialDeleteHandler;
use App\Service\Index\LearningMaterials;
use App\Tests\TestCase;
use Mockery as m;

class LearningMaterialDeleteHandlerTest extends TestCase
{
    protected m\MockInterface|LearningMaterials $index;

    public function setUp(): void
    {
        parent::setUp();
        $this->index = m::mock(LearningMaterials::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->index);
    }

    public function testInvoke(): void
    {
        $handler = new LearningMaterialDeleteHandler($this->index);
        $request = new LearningMaterialDeleteRequest(6);

        $this->index
            ->shouldReceive('delete')
            ->once()
            ->with(6);

        $handler->__invoke($request);
    }
}
