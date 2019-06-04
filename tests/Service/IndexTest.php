<?php
namespace App\Tests\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Index;
use Elasticsearch\Client;
use Ilios\MeSH\Model\Descriptor;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

class IndexTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testSetup()
    {
        $obj1 = $this->createWithHost();
        $this->assertTrue($obj1 instanceof Index);
        $this->assertTrue($obj1->isEnabled());

        $obj2 = $this->createWithoutHost();
        $this->assertTrue($obj2 instanceof Index);
        $this->assertFalse($obj2->isEnabled());
    }

    public function testIndexUsersThrowsWhenNotDTO()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\InvalidArgumentException::class);
        $users = [
            m::mock(UserDTO::class),
            m::mock(User::class),
            m::mock(UserDTO::class)
        ];
        $obj->indexUsers($users);
    }

    public function testIndexUsersWorksWithoutSearch()
    {
        $obj = $this->createWithoutHost();
        $users = [
            m::mock(UserDTO::class),
            m::mock(UserDTO::class)
        ];
        $this->assertTrue($obj->indexUsers($users));
    }

    public function testIndexCoursesThrowsWhenNotIndexableCourse()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\InvalidArgumentException::class);
        $courses = [
            m::mock(IndexableCourse::class),
            m::mock(CourseDTO::class),
            m::mock(IndexableCourse::class)
        ];
        $obj->indexCourses($courses);
    }

    public function testIndexCoursesWorksWithoutSearch()
    {
        $obj = $this->createWithoutHost();
        $mockCourse = m::mock(IndexableCourse::class);
        $mockDto = m::mock(CourseDTO::class);
        $mockCourse->courseDTO = $mockDto;
        $mockCourse->shouldReceive('createIndexObjects')->andReturn([]);
        $this->assertTrue($obj->indexCourses([$mockCourse]));
    }

    public function testIndexCourses()
    {
        $client = m::mock(Client::class);
        $obj = new Index($client);
        $course1 = m::mock(IndexableCourse::class);
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([['id' => 1]]);

        $course2 = m::mock(IndexableCourse::class);
        $course2->shouldReceive('createIndexObjects')->once()->andReturn([['id' => 2], ['id' => 3]]);

        $client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => ElasticSearchBase::PUBLIC_CURRICULUM_INDEX,
                        '_type' => '_doc',
                        '_id' => 1
                    ]
                ],
                [
                    'id' => 1,
                ],
                [
                    'index' => [
                        '_index' => ElasticSearchBase::PUBLIC_CURRICULUM_INDEX,
                        '_type' => '_doc',
                        '_id' => 2
                    ]
                ],
                [
                    'id' => 2,
                ],
                [
                    'index' => [
                        '_index' => ElasticSearchBase::PUBLIC_CURRICULUM_INDEX,
                        '_type' => '_doc',
                        '_id' => 3
                    ]
                ],
                [
                    'id' => 3,
                ],
            ]
        ])->andReturn(['errors' => false]);
        $obj->indexCourses([$course1, $course2]);
    }

    public function testIndexMeshDescriptorsThrowsWhenNotDescriptor()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\InvalidArgumentException::class);
        $arr = [
            m::mock(Descriptor::class),
            m::mock(Course::class),
            m::mock(Descriptor::class)
        ];
        $obj->indexMeshDescriptors($arr);
    }

    public function testIndexMeshDescriptorsWorksWithoutSearch()
    {
        $desc1 = m::mock(Descriptor::class)
            ->shouldReceive('getConcepts')->once()->andReturn([])
            ->shouldReceive('getUi')->once()->andReturn('id')
            ->shouldReceive('getName')->once()->andReturn('name')
            ->shouldReceive('getAnnotation')->once()->andReturn('annt')
            ->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi'])
            ->getMock();
        $obj = $this->createWithoutHost();
        $desc2 = m::mock(Descriptor::class)
            ->shouldReceive('getConcepts')->once()->andReturn([])
            ->shouldReceive('getUi')->once()->andReturn('id')
            ->shouldReceive('getName')->once()->andReturn('name')
            ->shouldReceive('getAnnotation')->once()->andReturn('annt')
            ->shouldReceive('getPreviousIndexing')->once()->andReturn(['pi'])
            ->getMock();
        $obj = $this->createWithoutHost();
        $arr = [
            $desc1,
            $desc2,
        ];
        $this->assertTrue($obj->indexMeshDescriptors($arr));
    }

    public function testClearWorksWhenNotConfigured()
    {
        $obj = $this->createWithoutHost();
        $obj->clear();
        $this->assertTrue(true);
    }

    protected function createWithHost()
    {
        $client = m::mock(Client::class);
        return new Index($client);
    }

    protected function createWithoutHost()
    {
        return new Index(null);
    }
}
