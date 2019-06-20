<?php
namespace App\Tests\Service;

use App\Classes\ElasticSearchBase;
use App\Classes\IndexableCourse;
use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Index;
use App\Tests\TestCase;
use Elasticsearch\Client;
use Ilios\MeSH\Model\Descriptor;
use Mockery as m;

class IndexTest extends TestCase
{
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


    public function testIndexUsers()
    {
        $client = m::mock(Client::class);
        $obj = new Index($client);
        $user1 = m::mock(UserDTO::class);
        $user1->id = 13;
        $user1->firstName = 'first';
        $user1->middleName = 'middle';
        $user1->lastName = 'last';
        $user1->displayName = 'display name';
        $user1->email = 'jackson@awesome.com';
        $user1->enabled = false;
        $user1->campusId = '99';
        $user1->username = 'thebestone';

        $user2 = m::mock(UserDTO::class);
        $user2->id = 11;
        $user2->firstName = 'first2';
        $user2->middleName = 'middle2';
        $user2->lastName = 'last2';
        $user2->displayName = null;
        $user2->email = 'jasper@awesome.com';
        $user1->enabled = true;
        $user2->campusId = 'OG';
        $user2->username = null;

        $client->shouldReceive('bulk')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_index' => ElasticSearchBase::PRIVATE_USER_INDEX,
                        '_type' => '_doc',
                        '_id' => $user1->id
                    ]
                ],
                [
                    'id' => $user1->id,
                    'firstName' => $user1->firstName,
                    'lastName' => $user1->lastName,
                    'middleName' => $user1->middleName,
                    'displayName' => $user1->displayName,
                    'email' => $user1->email,
                    'campusId' => $user1->campusId,
                    'username' => $user1->username,
                    'enabled' => $user1->enabled,
                    'fullName' => 'first middle last',
                    'fullNameLastFirst' => 'last, first middle',
                ],
                [
                    'index' => [
                        '_index' => ElasticSearchBase::PRIVATE_USER_INDEX,
                        '_type' => '_doc',
                        '_id' => $user2->id
                    ]
                ],
                [
                    'id' => $user2->id,
                    'firstName' => $user2->firstName,
                    'lastName' => $user2->lastName,
                    'middleName' => $user2->middleName,
                    'displayName' => $user2->displayName,
                    'email' => $user2->email,
                    'campusId' => $user2->campusId,
                    'username' => $user2->username,
                    'enabled' => $user2->enabled,
                    'fullName' => 'first2 middle2 last2',
                    'fullNameLastFirst' => 'last2, first2 middle2',
                ],
            ]
        ])->andReturn(['errors' => false]);
        $obj->indexUsers([$user1, $user2]);
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

    public function testIndexCourseWithNoSessions()
    {
        $client = m::mock(Client::class);
        $obj = new Index($client);
        $course1 = m::mock(IndexableCourse::class);
        $course1->shouldReceive('createIndexObjects')->once()->andReturn([]);

        $client->shouldNotReceive('bulk');
        $obj->indexCourses([$course1]);
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
