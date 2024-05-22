<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\IndexableCourse;
use App\Entity\DTO\CourseDTO;
use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use OpenSearch\Client;
use DateTime;
use Exception;
use InvalidArgumentException;
use Mockery as m;

class CurriculumTest extends TestCase
{
    private m\MockInterface $client;
    private m\MockInterface $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = m::mock(Client::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')
            ->with('search_upload_limit')
            ->andReturn(8000000);
    }
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->config);
    }

    public function testSetup(): void
    {
        $obj1 = new Curriculum($this->config, $this->client);
        $this->assertTrue($obj1 instanceof Curriculum);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Curriculum($this->config, null);
        $this->assertTrue($obj2 instanceof Curriculum);
        $this->assertFalse($obj2->isEnabled());
    }


    public function testIndexCoursesThrowsWhenNotIndexableCourse(): void
    {
        $obj = new Curriculum($this->config, null);
        $this->expectException(InvalidArgumentException::class);
        $courses = [
            m::mock(IndexableCourse::class),
            m::mock(CourseDTO::class),
            m::mock(IndexableCourse::class),
        ];
        $obj->index($courses, new DateTime());
    }

    public function testIndexCoursesWorksWithoutSearch(): void
    {
        $obj = new Curriculum($this->config, null);
        $mockCourse = m::mock(IndexableCourse::class);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Search is not configured, isEnabled() should be called before calling this method'
        );
        $obj->index([$mockCourse], new DateTime());
    }

    public function testIndexCourses(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $dto1 = m::mock(CourseDTO::class);
        $dto1->id = 1;
        $course1->courseDTO = $dto1;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 1, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $course2 = m::mock(IndexableCourse::class);
        $dto2 = m::mock(CourseDTO::class);
        $dto2->id = 2;
        $course2->courseDTO = $dto2;
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 2, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
            ['id' => 3, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);
        $stamp = new DateTime();

        $this->client->shouldReceive('search')->once()
            ->with(m::capture($searchArgs))
            ->andReturn(['aggregations' => ['courseId' => ['buckets' => []]]]);

        $this->client->shouldReceive('bulk')->once()
            ->with(m::capture($bulkArgs))
            ->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$course1, $course2], $stamp);

        $this->assertTrue(isset($searchArgs['body']['query']['bool']['filter']));
        $this->assertEquals($searchArgs['body']['query']['bool']['filter'][1], ['terms' => ['courseId' => [1, 2]]]);

        $this->assertArrayHasKey('body', $bulkArgs);
        $b = $bulkArgs['body'];
        $this->assertCount(6, $b);
        $this->assertEquals(1, $b[1]['id']);
        $this->assertEquals(2, $b[3]['id']);
        $this->assertEquals(3, $b[5]['id']);
    }

    public function testDontReIndexCourses()
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $dto1 = m::mock(CourseDTO::class);
        $dto1->id = 1;
        $course1->courseDTO = $dto1;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 1, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $course2 = m::mock(IndexableCourse::class);
        $dto2 = m::mock(CourseDTO::class);
        $dto2->id = 2;
        $course2->courseDTO = $dto2;
        $course2->shouldNotReceive('createIndexObjects');

        $this->client->shouldReceive('search')->once()
            ->with(m::capture($searchArgs))
            ->andReturn(['aggregations' => ['courseId' => ['buckets' => [['key' => 2]]]]]);

        $this->client->shouldReceive('bulk')->once()
            ->with(m::capture($bulkArgs))
            ->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$course1, $course2], new DateTime());

        $this->assertTrue(isset($searchArgs['body']['query']['bool']['filter']));
        $this->assertEquals($searchArgs['body']['query']['bool']['filter'][1], ['terms' => ['courseId' => [1, 2]]]);

        $this->assertArrayHasKey('body', $bulkArgs);
        $b = $bulkArgs['body'];
        $this->assertCount(2, $b);
        $this->assertEquals(1, $b[1]['id']);
    }

    public function testIndexCourseWithNoSessions(): void
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $dto1 = m::mock(CourseDTO::class);
        $dto1->id = 1;
        $course1->courseDTO = $dto1;
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([]);

        $this->client->shouldNotReceive('bulk');
        $this->client->shouldReceive('search')->once()
            ->with(m::capture($searchArgs))
            ->andReturn(['aggregations' => ['courseId' => ['buckets' => []]]]);
        $obj->index([$course1], new DateTime());

        $this->assertTrue(isset($searchArgs['body']['query']['bool']['filter']));
        $this->assertEquals($searchArgs['body']['query']['bool']['filter'][1], ['terms' => ['courseId' => [1]]]);
    }
}
