<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ImportDefaultDataCommand;
use App\Entity\DTO\SchoolDTO;
use App\Repository\AamcMethodRepository;
use App\Repository\AamcPcrsRepository;
use App\Repository\AamcResourceTypeRepository;
use App\Repository\AlertChangeTypeRepository;
use App\Repository\ApplicationConfigRepository;
use App\Repository\AssessmentOptionRepository;
use App\Repository\CompetencyRepository;
use App\Repository\CourseClerkshipTypeRepository;
use App\Repository\CurriculumInventoryInstitutionRepository;
use App\Repository\LearningMaterialStatusRepository;
use App\Repository\LearningMaterialUserRoleRepository;
use App\Repository\SchoolRepository;
use App\Repository\SessionTypeRepository;
use App\Repository\TermRepository;
use App\Repository\UserRoleRepository;
use App\Repository\VocabularyRepository;
use App\Service\DefaultDataImporter;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ImportDefaultDataCommandTest
 * @package App\Tests\Command
 * @group cli
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Command\ImportDefaultDataCommand::class)]
class ImportDefaultDataCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $defaultDataImporter;
    protected m\MockInterface $aamcMethodRepository;
    protected m\MockInterface $aamcPcrsRepository;
    protected m\MockInterface $aamcResourceTypeRepository;
    protected m\MockInterface $alertChangeTypeRepository;
    protected m\MockInterface $applicationConfigRepository;
    protected m\MockInterface $assessmentOptionRepository;
    protected m\MockInterface $competencyRepository;
    protected m\MockInterface $courseClerkshipTypeRepository;
    protected m\MockInterface $curriculumInventoryInstitutionRepository;
    protected m\MockInterface $learningMaterialStatusRepository;
    protected m\MockInterface $learningMaterialUserRoleRepository;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $sessionTypeRepository;
    protected m\MockInterface $termRepository;
    protected m\MockInterface $userRoleRepository;
    protected m\MockInterface $vocabularyRepository;

    public function setUp(): void
    {
        $this->defaultDataImporter = m::mock(DefaultDataImporter::class);
        $this->aamcMethodRepository = m::mock(AamcMethodRepository::class);
        $this->aamcPcrsRepository = m::mock(AamcPcrsRepository::class);
        $this->aamcResourceTypeRepository = m::mock(AamcResourceTypeRepository::class);
        $this->alertChangeTypeRepository = m::mock(AlertChangeTypeRepository::class);
        $this->applicationConfigRepository = m::mock(ApplicationConfigRepository::class);
        $this->assessmentOptionRepository = m::mock(AssessmentOptionRepository::class);
        $this->competencyRepository = m::mock(CompetencyRepository::class);
        $this->courseClerkshipTypeRepository = m::mock(CourseClerkshipTypeRepository::class);
        $this->curriculumInventoryInstitutionRepository = m::mock(CurriculumInventoryInstitutionRepository::class);
        $this->learningMaterialStatusRepository = m::mock(LearningMaterialStatusRepository::class);
        $this->learningMaterialUserRoleRepository = m::mock(LearningMaterialUserRoleRepository::class);
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->sessionTypeRepository = m::mock(SessionTypeRepository::class);
        $this->termRepository = m::mock(TermRepository::class);
        $this->userRoleRepository = m::mock(UserRoleRepository::class);
        $this->vocabularyRepository = m::mock(VocabularyRepository::class);

        $command = new ImportDefaultDataCommand(
            $this->defaultDataImporter,
            $this->aamcMethodRepository,
            $this->aamcPcrsRepository,
            $this->aamcResourceTypeRepository,
            $this->alertChangeTypeRepository,
            $this->applicationConfigRepository,
            $this->assessmentOptionRepository,
            $this->competencyRepository,
            $this->courseClerkshipTypeRepository,
            $this->curriculumInventoryInstitutionRepository,
            $this->learningMaterialStatusRepository,
            $this->learningMaterialUserRoleRepository,
            $this->schoolRepository,
            $this->sessionTypeRepository,
            $this->termRepository,
            $this->userRoleRepository,
            $this->vocabularyRepository
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->defaultDataImporter);
        unset($this->aamcMethodRepository);
        unset($this->aamcPcrsRepository);
        unset($this->aamcResourceTypeRepository);
        unset($this->alertChangeTypeRepository);
        unset($this->applicationConfigRepository);
        unset($this->assessmentOptionRepository);
        unset($this->competencyRepository);
        unset($this->courseClerkshipTypeRepository);
        unset($this->curriculumInventoryInstitutionRepository);
        unset($this->learningMaterialStatusRepository);
        unset($this->learningMaterialUserRoleRepository);
        unset($this->schoolRepository);
        unset($this->sessionTypeRepository);
        unset($this->termRepository);
        unset($this->userRoleRepository);
        unset($this->vocabularyRepository);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $this->schoolRepository->shouldReceive('findDTOBy')->withAnyArgs()->andReturn(null);
        $this->aamcMethodRepository->shouldReceive('deleteAll');
        $this->applicationConfigRepository->shouldReceive('deleteAll');
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->aamcMethodRepository, DefaultDataImporter::AAMC_METHOD, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->aamcPcrsRepository, DefaultDataImporter::AAMC_PCRS, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->aamcResourceTypeRepository, DefaultDataImporter::AAMC_RESOURCE_TYPE, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->alertChangeTypeRepository, DefaultDataImporter::ALERT_CHANGE_TYPE, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->applicationConfigRepository, DefaultDataImporter::APPLICATION_CONFIG, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->assessmentOptionRepository, DefaultDataImporter::ASSESSMENT_OPTION, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->courseClerkshipTypeRepository, DefaultDataImporter::COURSE_CLERKSHIP_TYPE, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->learningMaterialStatusRepository, DefaultDataImporter::LEARNING_MATERIAL_STATUS, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs(
                [
                    $this->learningMaterialUserRoleRepository,
                    DefaultDataImporter::LEARNING_MATERIAL_USER_ROLE,
                    [],
                ]
            )
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->userRoleRepository, DefaultDataImporter::USER_ROLE, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->schoolRepository, DefaultDataImporter::SCHOOL, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs(
                [
                    $this->curriculumInventoryInstitutionRepository,
                    DefaultDataImporter::CURRICULUM_INVENTORY_INSTITUTION,
                    [],
                ]
            )
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->competencyRepository, DefaultDataImporter::COMPETENCY, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->competencyRepository, DefaultDataImporter::COMPETENCY_X_AAMC_PCRS, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->sessionTypeRepository, DefaultDataImporter::SESSION_TYPE, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->sessionTypeRepository, DefaultDataImporter::SESSION_TYPE_X_AAMC_METHOD, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->vocabularyRepository, DefaultDataImporter::VOCABULARY, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->termRepository, DefaultDataImporter::TERM, []])
            ->andReturn([]);
        $this->defaultDataImporter->shouldReceive('import')
            ->withArgs([$this->termRepository, DefaultDataImporter::TERM_X_AAMC_RESOURCE_TYPE, []])
            ->andReturn([]);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Completed data import', $output);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testExecuteFailsIfDatabaseIsNotEmpty(): void
    {
        $this->schoolRepository->shouldReceive('findDTOBy')->withAnyArgs()->andReturn(
            new SchoolDTO(1, 'foo', null, 'foo@test.edu', null)
        );
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            '[ERROR] Your database already contains data. Aborting import process.',
            $output
        );
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
