<?php

declare(strict_types=1);

namespace App\Command;

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
use App\Service\DefaultDataLoader;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDefaultDataCommand extends Command
{
    use LockableTrait;

    public function __construct(
        protected DefaultDataLoader $dataLoader,
        protected AamcMethodRepository $aamcMethodRepository,
        protected AamcPcrsRepository $aamcPcrsRepository,
        protected AamcResourceTypeRepository $aamcResourceTypeRepository,
        protected AlertChangeTypeRepository $alertChangeTypeRepository,
        protected ApplicationConfigRepository $applicationConfigRepository,
        protected AssessmentOptionRepository $assessmentOptionRepository,
        protected CompetencyRepository $competencyRepository,
        protected CourseClerkshipTypeRepository $courseClerkshipTypeRepository,
        protected CurriculumInventoryInstitutionRepository $curriculumInventoryInstitutionRepository,
        protected LearningMaterialStatusRepository $learningMaterialStatusRepository,
        protected LearningMaterialUserRoleRepository $learningMaterialUserRoleRepository,
        protected SchoolRepository $schoolRepository,
        protected SessionTypeRepository $sessionTypeRepository,
        protected TermRepository $termRepository,
        protected UserRoleRepository $userRoleRepository,
        protected VocabularyRepository $vocabularyRepository
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setName('ilios:import-default-data')
            ->setAliases(['ilios:setup:import-default-data'])
            ->setDescription('Imports default application data into Ilios. Only works with an empty database schema.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->lock()) {
            $io->error('The command is already running in another process.');
            return Command::FAILURE;
        }

        $school = $this->schoolRepository->findDTOBy([]);
        if ($school) {
            $io->error('Your database already contains data. Aborting import process.');
            return Command::FAILURE;
        }

        $io->info('Started data import, this may take a while...');
        try {
            // ACHTUNG!
            // we MUST clear the the aamc_method and application_configs table as part of the import process,
            // since it gets pre-populated with data in the previous step of the installation
            // process (when migrations are running).
            // So let's just clear all records out here first, in order to avoid data duplication issues.
            // [ST 2021/07/28]
            $this->aamcMethodRepository->deleteAll();
            $this->applicationConfigRepository->deleteAll();

            // now, let's import
            $this->dataLoader->import($this->aamcMethodRepository, 'aamc_method');
            $this->dataLoader->import($this->aamcPcrsRepository, 'aamc_pcrs');
            $this->dataLoader->import($this->aamcResourceTypeRepository, 'aamc_resource_type');
            $this->dataLoader->import($this->alertChangeTypeRepository, 'alert_change_type');
            $this->dataLoader->import($this->applicationConfigRepository, 'application_config');
            $this->dataLoader->import($this->assessmentOptionRepository, 'assessment_option');
            $this->dataLoader->import($this->courseClerkshipTypeRepository, 'course_clerkship_type');
            $this->dataLoader->import($this->learningMaterialStatusRepository, 'learning_material_status');
            $this->dataLoader->import($this->learningMaterialUserRoleRepository, 'learning_material_user_role');
            $this->dataLoader->import($this->userRoleRepository, 'user_role');
            $this->dataLoader->import($this->schoolRepository, 'school');
            $this->dataLoader->import(
                $this->curriculumInventoryInstitutionRepository,
                'curriculum_inventory_institution'
            );
            $this->dataLoader->import($this->competencyRepository, 'competency');
            $this->dataLoader->import($this->competencyRepository, 'competency_x_aamc_pcrs');
            $this->dataLoader->import($this->sessionTypeRepository, 'session_type');
            $this->dataLoader->import($this->sessionTypeRepository, 'session_type_x_aamc_method');
            $this->dataLoader->import($this->vocabularyRepository, 'vocabulary');
            $this->dataLoader->import($this->termRepository, 'term');
            $this->dataLoader->import($this->termRepository, 'term_x_aamc_resource_type');
        } catch (Exception $e) {
            $io->error([
                'An error occurred during data import:',
                $e->getMessage()
            ]);
            return Command::FAILURE;
        }
        $io->text('Completed data import.');
        $this->release();

        return Command::SUCCESS;
    }
}
