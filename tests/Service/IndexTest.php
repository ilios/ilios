<?php
namespace App\Tests\Service;

use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Config;
use App\Service\Index;
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

    public function testIndexCoursesThrowsWhenNotDTO()
    {
        $obj = $this->createWithoutHost();
        $this->expectException(\InvalidArgumentException::class);
        $courses = [
            m::mock(CourseDTO::class),
            m::mock(Course::class),
            m::mock(CourseDTO::class)
        ];
        $obj->indexCourses($courses);
    }

    public function testIndexCoursesWorksWithoutSearch()
    {
        $obj = $this->createWithoutHost();
        $courses = [
            m::mock(CourseDTO::class),
            m::mock(CourseDTO::class)
        ];
        $this->assertTrue($obj->indexCourses($courses));
    }

    public function testClearWorksWhenNotConfigured()
    {
        $obj = $this->createWithoutHost();
        $obj->clear();
    }

    protected function createWithHost()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn('host');
        return new Index($config);
    }

    protected function createWithoutHost()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('elasticsearch_hosts')->once()->andReturn(false);
        return new Index($config);
    }
}
