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
        $referenceMap = [];
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
            $referenceMap = $this->dataLoader->import(
                $this->aamcMethodRepository,
                DefaultDataLoader::AAMC_METHOD,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->aamcPcrsRepository,
                DefaultDataLoader::AAMC_PCRS,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->aamcResourceTypeRepository,
                DefaultDataLoader::AAMC_RESOURCE_TYPE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->alertChangeTypeRepository,
                DefaultDataLoader::ALERT_CHANGE_TYPE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->applicationConfigRepository,
                DefaultDataLoader::APPLICATION_CONFIG,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->assessmentOptionRepository,
                DefaultDataLoader::ASSESSMENT_OPTION,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->courseClerkshipTypeRepository,
                DefaultDataLoader::COURSE_CLERKSHIP_TYPE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->learningMaterialStatusRepository,
                DefaultDataLoader::LEARNING_MATERIAL_STATUS,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->learningMaterialUserRoleRepository,
                DefaultDataLoader::LEARNING_MATERIAL_USER_ROLE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->userRoleRepository,
                DefaultDataLoader::USER_ROLE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->schoolRepository,
                DefaultDataLoader::SCHOOL,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->curriculumInventoryInstitutionRepository,
                DefaultDataLoader::CURRICULUM_INVENTORY_INSTITUTION,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->competencyRepository,
                DefaultDataLoader::COMPETENCY,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->competencyRepository,
                DefaultDataLoader::COMPETENCY_X_AAMC_PCRS,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->sessionTypeRepository,
                DefaultDataLoader::SESSION_TYPE,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->sessionTypeRepository,
                DefaultDataLoader::SESSION_TYPE_X_AAMC_METHOD,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->vocabularyRepository,
                DefaultDataLoader::VOCABULARY,
                $referenceMap
            );
            $referenceMap = $this->dataLoader->import(
                $this->termRepository,
                DefaultDataLoader::TERM,
                $referenceMap
            );
            $this->dataLoader->import(
                $this->termRepository,
                DefaultDataLoader::TERM_X_AAMC_RESOURCE_TYPE,
                $referenceMap
            );
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
