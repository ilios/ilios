<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Classes\IndexableCourse;
use App\Command\Index\DetectMissingCommand;
use App\Entity\DTO\LearningMaterialDTO;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\SessionRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use DateTime;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
final class DetectMissingCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface | LearningMaterialRepository $learningMaterialRepository;
    protected m\MockInterface | LearningMaterials $materialIndex;
    protected m\MockInterface | Curriculum $curriculumIndex;
    protected m\MockInterface | SessionRepository $sessionRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->materialIndex = m::mock(LearningMaterials::class);
        $this->curriculumIndex = m::mock(Curriculum::class);
        $this->sessionRepository = m::mock(SessionRepository::class);

        $command = new DetectMissingCommand(
            $this->learningMaterialRepository,
            $this->sessionRepository,
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
        unset($this->curriculumIndex);
        unset($this->sessionRepository);
        unset($this->commandTester);
    }

    public function testExecuteeWithIndexDisabled(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->materialIndex->shouldNotReceive('getAllIds');
        $this->curriculumIndex->shouldNotReceive('getAllCourseIds');
        $this->curriculumIndex->shouldNotReceive('getAllSessionIds');
        $this->learningMaterialRepository->shouldNotReceive('getFileLearningMaterialIds');
        $this->sessionRepository->shouldNotReceive('getIds');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
    public function testExecuteWithIndexEnabledNotReindexing(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(true);

        $this->materialIndex->shouldReceive('getAllIds')->once()->andReturn([13]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([13, 14]);

        $this->curriculumIndex->shouldReceive('getAllSessionIds')->once()->andReturn([22]);
        $this->sessionRepository->shouldReceive('getIds')->once()->andReturn([1, 22]);

        $this->sessionRepository
            ->shouldReceive('getCoursesForSessionIds')->once()
            ->with([1])
            ->andReturn([
                [
                    'courseId' => 33,
                    'courseTitle' => 'Our Missing Course',
                    'sessionId' => 1,
                ],
            ]);

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Missing Materials \(1\)/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/14/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Missing Sessions \(1\)/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Our Missing Course \(33\) 1 /',
            $output
        );
    }
    public function testReIndexMaterials(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(true);

        $this->materialIndex->shouldReceive('getAllIds')->once()->andReturn([13]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([13, 14]);

        $this->curriculumIndex->shouldReceive('getAllSessionIds')->once()->andReturn([]);
        $this->sessionRepository->shouldReceive('getIds')->once()->andReturn([]);

        $this->sessionRepository->shouldNotReceive('getCoursesForSessionIds');

        $dto = m::mock(LearningMaterialDTO::class);
        $this->learningMaterialRepository->shouldReceive('findDTOsBy')->once()
            ->with(['id' => [14]])
            ->andReturn([$dto]);

        $this->materialIndex->shouldReceive('index')->once()->with([$dto])->andReturn(true);

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute([]);
    }
    public function testReIndexCourses(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(true);

        $this->materialIndex->shouldReceive('getAllIds')->once()->andReturn([]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([]);

        $this->curriculumIndex->shouldReceive('getAllSessionIds')->once()->andReturn([22]);
        $this->sessionRepository->shouldReceive('getIds')->once()->andReturn([1, 22]);

        $this->sessionRepository
            ->shouldReceive('getCoursesForSessionIds')->once()
            ->with([1])
            ->andReturn([
                [
                    'courseId' => 33,
                    'courseTitle' => 'Our Missing Course',
                    'sessionId' => 1,
                ],
            ]);

        $this->curriculumIndex->shouldReceive('index')
            ->once()
            ->withArgs(
                fn (array $ids, DateTime $dateTime) => $ids === [33] && $dateTime->diff(new DateTime())->days === 0
            );

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute([]);
    }
}
