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
use App\Repository\MeshDescriptorRepository;
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
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
        protected MeshDescriptorRepository $meshDescriptorRepository,
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
            return 0;
        }

        $io->warning('Do not run this against an Ilios instance that already contains data!');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue? ', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $io->info('Started data import, this may take a while...');
        try {
            $this->dataLoader->import($this->aamcMethodRepository, 'aamc_method.csv');
            $this->dataLoader->import($this->aamcPcrsRepository, 'aamc_pcrs.csv');
            $this->dataLoader->import($this->aamcResourceTypeRepository, 'aamc_resource_type.csv');
            $this->dataLoader->import($this->alertChangeTypeRepository, 'alert_change_type.csv');
            $this->dataLoader->import($this->applicationConfigRepository, 'application_config.csv');
            $this->dataLoader->import($this->assessmentOptionRepository, 'assessment_option.csv');
            $this->dataLoader->import($this->courseClerkshipTypeRepository, 'course_clerkship_type.csv');
            $this->dataLoader->import($this->learningMaterialStatusRepository, 'learning_material_status.csv');
            $this->dataLoader->import(
                $this->learningMaterialUserRoleRepository,
                'learning_material_user_role.csv'
            );
            $this->dataLoader->import($this->userRoleRepository, 'user_role.csv');
            $this->dataLoader->import($this->schoolRepository, 'school.csv');
            $this->dataLoader->import(
                $this->curriculumInventoryInstitutionRepository,
                'curriculum_inventory_institution.csv'
            );
            $this->dataLoader->import($this->competencyRepository, 'competency.csv');
            $this->dataLoader->import($this->competencyRepository, 'competency_x_aamc_pcrs.csv');
            $this->dataLoader->import($this->sessionTypeRepository, 'session_type.csv');
            $this->dataLoader->import($this->sessionTypeRepository, 'session_type_x_aamc_method.csv');
            $this->dataLoader->import($this->vocabularyRepository, 'vocabulary.csv');
            $this->dataLoader->import($this->termRepository, 'term.csv');
            $this->dataLoader->import($this->termRepository, 'term_x_aamc_resource_type.csv');
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
