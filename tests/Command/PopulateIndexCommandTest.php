<?php
namespace App\Tests\Command;

use App\Command\PopulateIndexCommand;
use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\MeshDescriptorManager;
use App\Entity\Manager\UserManager;
use App\Entity\User;
use App\Service\Index;
use Ilios\MeSH\Model\Descriptor;
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
    private $index;

    /** @var m\Mock */
    private $userManager;

    /** @var m\Mock */
    private $courseManager;

    /** @var m\Mock */
    private $meshDescrriptorManager;

    public function setUp()
    {
        $this->index = m::mock(Index::class);
        $this->userManager = m::mock(UserManager::class);
        $this->courseManager = m::mock(CourseManager::class);
        $this->meshDescrriptorManager = m::mock(MeshDescriptorManager::class);
        $command = new PopulateIndexCommand(
            $this->index,
            $this->userManager,
            $this->courseManager,
            $this->meshDescrriptorManager
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
    public function tearDown() : void
    {
        unset($this->search);
        unset($this->userManager);
        unset($this->courseManager);
        unset($this->meshDescrriptorManager);
        unset($this->commandTester);
    }
    
    public function testExecute()
    {
        $this->index->shouldReceive('clear');

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
        $this->index->shouldReceive('indexUsers')->with([$mockUserDto]);


        $this->courseManager->shouldReceive('getIds')->andReturn([89]);
        $mockCourseDTO = m::mock(CourseDTO::class);
        $mockCourseDTO->id = 11;
        $mockCourseDTO->title = 'course title';

        $this->courseManager->shouldReceive('findDTOsBy')
            ->with(['id' => [89]])->andReturn([$mockCourseDTO]);
        $this->index->shouldReceive('indexCourses')->with([$mockCourseDTO]);


        $this->meshDescrriptorManager->shouldReceive('getIds')->andReturn([99]);
        $mockDescriptor = m::mock(Descriptor::class);

        $this->meshDescrriptorManager->shouldReceive('getIliosMeshDescriptorsById')
            ->with([99])->andReturn([$mockDescriptor]);
        $this->index->shouldReceive('indexMeshDescriptors')->with([$mockDescriptor]);

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
