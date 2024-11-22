<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\LearningMaterialTextExtractionRequest;
use App\Tests\TestCase;
use Exception;

class LearningMaterialTextExtractionRequestTest extends TestCase
{
    public function testMaximumValues(): void
    {
        $this->expectException(Exception::class);
        $arr = array_fill(0, 100, '');
        new LearningMaterialTextExtractionRequest($arr);
    }
}
