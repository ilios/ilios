<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\DetectMissingCommand;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
class DetectMissingCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface | LearningMaterialRepository $learningMaterialRepository;
    protected m\MockInterface | LearningMaterials $materialIndex;
    protected m\MockInterface | CourseRepository $courseRepository;
    protected m\MockInterface | Curriculum $curriculumIndex;

    public function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->materialIndex = m::mock(LearningMaterials::class);
        $this->courseRepository = m::mock(CourseRepository::class);
        $this->curriculumIndex = m::mock(Curriculum::class);

        $command = new DetectMissingCommand(
            $this->learningMaterialRepository,
            $this->courseRepository,
            $this->materialIndex,
            $this->curriculumIndex,
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->learningMaterialRepository);
        unset($this->materialIndex);
        unset($this->courseRepository);
        unset($this->curriculumIndex);
        unset($this->commandTester);
    }

    public function testExecuteeWithIndexDisabled(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->materialIndex->shouldNotReceive('getAllIds');
        $this->curriculumIndex->shouldNotReceive('getAllCourseIds');
        $this->learningMaterialRepository->shouldNotReceive('getFileLearningMaterialIds');
        $this->courseRepository->shouldNotReceive('getIdsForCoursesWithSessions');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
    public function testExecuteWithIndexEnabled(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(true);

        $this->materialIndex->shouldReceive('getAllIds')->once()->andReturn([13]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([13, 14]);

        $this->curriculumIndex->shouldReceive('getAllCourseIds')->once()->andReturn([11]);
        $this->courseRepository->shouldReceive('getIdsForCoursesWithSessions')->once()->andReturn([11, 33]);


        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 materials are missing from the index/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Materials: 14/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/1 courses are missing from the index/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Courses: 33/',
            $output
        );
    }
}
