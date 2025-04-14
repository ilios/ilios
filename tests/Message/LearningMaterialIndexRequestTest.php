<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\LearningMaterialIndexRequest;
use App\Tests\TestCase;
use Exception;

class LearningMaterialIndexRequestTest extends TestCase
{
    public function testMaximumValues(): void
    {
        $this->expectException(Exception::class);
        $arr = array_fill(0, 100, '');
        new LearningMaterialIndexRequest($arr);
    }

    public function testGetIds(): void
    {
        $arr = [1, 2, 3];
        $request = new LearningMaterialIndexRequest($arr);
        $this->assertEquals($arr, $request->getIds());
    }
}
