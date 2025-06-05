<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\LearningMaterialDeleteRequest;
use App\Tests\TestCase;

final class LearningMaterialDeleteRequestTest extends TestCase
{
    public function testItWorks(): void
    {
        $id = 42;
        $request = new LearningMaterialDeleteRequest($id);
        $this->assertSame($id, $request->getLearningMaterialId());
    }
}
