<?php
namespace App\Tests\Command;

use App\Command\ListConfigValuesCommand;
use App\Command\PopulateIndexCommand;
use App\Entity\ApplicationConfig;
use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\UserManager;
use App\Entity\User;
use App\Service\Search;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class PopulateIndexCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:populate-index';

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    private $search;

    /** @var m\Mock */
    private $userManager;

    /** @var m\Mock */
    private $courseManager;

    public function setUp()
    {
        $this->search = m::mock(Search::class);
        $this->userManager = m::mock(UserManager::class);
        $this->courseManager = m::mock(CourseManager::class);
        $command = new PopulateIndexCommand(
            $this->search,
            $this->userManager,
            $this->courseManager
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->search);
        unset($this->userManager);
        unset($this->courseManager);
        unset($this->commandTester);
    }
    
    public function testExecute()
    {
        $this->search->shouldReceive('clear');

        $this->userManager->shouldReceive('getIds')->andReturn([11]);
        $mockUserDto = m::mock(UserDTO::class);
        $mockUserDto->id = 11;
        $mockUserDto->firstName = 'first';
        $mockUserDto->lastName = 'last';
        $mockUserDto->middleName = 'middle';
        $mockUserDto->email = 'e@e.com';
        $mockUserDto->campusId = '123C';
        $mockUserDto->username = 'user11';

        $this->userManager->shouldReceive('findDTOsBy')
            ->with(['id' => [11]])->andReturn([$mockUserDto]);
        $this->search->shouldReceive('bulkIndex')->with(
            Search::PRIVATE_INDEX,
            User::class,
            [[
                'id' => $mockUserDto->id,
                'firstName' => $mockUserDto->firstName,
                'lastName' => $mockUserDto->lastName,
                'middleName' => $mockUserDto->middleName,
                'email' => $mockUserDto->email,
                'campusId' => $mockUserDto->campusId,
                'username' => $mockUserDto->username,
            ]]
        );


        $this->courseManager->shouldReceive('getIds')->andReturn([89]);
        $mockCourseDTO = m::mock(CourseDTO::class);
        $mockCourseDTO->id = 11;
        $mockCourseDTO->title = 'course title';

        $this->courseManager->shouldReceive('findDTOsBy')
            ->with(['id' => [89]])->andReturn([$mockCourseDTO]);
        $this->search->shouldReceive('bulkIndex')->with(
            Search::PUBLIC_INDEX,
            Course::class,
            [[
                'id' => $mockCourseDTO->id,
                'title' => $mockCourseDTO->title,
            ]]
        );


        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Index Populated\!/',
            $output
        );
    }
}
