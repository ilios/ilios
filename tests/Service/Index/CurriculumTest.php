<?php

declare(strict_types=1);

namespace App\Tests\Service\Index;

use App\Classes\IndexableCourse;
use App\Entity\DTO\CourseDTO;
use App\Service\Config;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use OpenSearch\Client;
use InvalidArgumentException;
use Mockery as m;

class CurriculumTest extends TestCase
{
    /**
     * @var Client|m\MockInterface
     */
    private $client;

    /**
     * @var Config|m\MockInterface
     */
    private $config;

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

    public function testSetup()
    {
        $obj1 = new Curriculum($this->config, $this->client);
        $this->assertTrue($obj1 instanceof Curriculum);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = new Curriculum($this->config, null);
        $this->assertTrue($obj2 instanceof Curriculum);
        $this->assertFalse($obj2->isEnabled());
    }


    public function testIndexCoursesThrowsWhenNotIndexableCourse()
    {
        $obj = new Curriculum($this->config, null);
        $this->expectException(InvalidArgumentException::class);
        $courses = [
            m::mock(IndexableCourse::class),
            m::mock(CourseDTO::class),
            m::mock(IndexableCourse::class)
        ];
        $obj->index($courses);
    }

    public function testIndexCoursesWorksWithoutSearch()
    {
        $obj = new Curriculum($this->config, null);
        $mockCourse = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockCourse->courseDTO = $mockDto;
        $mockCourse->shouldReceive('createIndexObjects')->andReturn([]);
        $this->assertTrue($obj->index([$mockCourse]));
    }

    public function testIndexCourses()
    {
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 1, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []]
        ]);

        $course2 = m::mock(IndexableCourse::class);
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([
            ['id' => 2, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
            ['id' => 3, 'courseFileLearningMaterialIds' => [], 'sessionFileLearningMaterialIds' => []],
        ]);

        $this->client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 1
                    ]
                ],
                [
                    'id' => 1,
                ],
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 2
                    ]
                ],
                [
                    'id' => 2,
                ],
                [
                    'index' => [
                        '_index' => Curriculum::INDEX,
                        '_id' => 3
                    ]
                ],
                [
                    'id' => 3,
                ],
            ]
        ])->andReturn(['errors' => false, 'took' => 1, 'items' => []]);
        $obj->index([$course1, $course2]);
    }

    public function testIndexCourseWithNoSessions()
    {
        $this->client = m::mock(Client::class);
        $obj = new Curriculum($this->config, $this->client);
        $course1 = m::mock(IndexableCourse::class);
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([]);

        $this->client->shouldNotReceive('bulk');
        $obj->index([$course1]);
    }
}
