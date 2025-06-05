<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\CourseDeleteRequest;
use App\Tests\TestCase;

final class CourseDeleteRequestTest extends TestCase
{
    public function testItWorks(): void
    {
        $id = 42;
        $request = new CourseDeleteRequest($id);
        $this->assertSame($id, $request->getCourseId());
    }
}
