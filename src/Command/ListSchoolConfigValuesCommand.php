<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\SchoolConfigInterface;
use App\Entity\SchoolInterface;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get an application configuration value from the DB
 *
 * Class ListSchoolConfigValuesCommand
 * @package App\Command
 */
class ListSchoolConfigValuesCommand extends Command
{
    protected SchoolRepository $schoolRepository;
    protected SchoolConfigRepository $schoolConfigRepository;

    /**
     * RolloverCourseCommand constructor.
     */
    public function __construct(SchoolRepository $schoolRepository, SchoolConfigRepository $schoolConfigRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolConfigRepository = $schoolConfigRepository;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:list-school-config-values')
            ->setAliases(['ilios:maintenance:list-school-config-values'])
            ->setDescription('Read school configuration values from the DB')
            //required arguments
            ->addArgument(
                'school',
                InputArgument::REQUIRED,
                'ID of the school.'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schoolId = $input->getArgument('school');

        /** @var SchoolInterface $school */
        $school = $this->schoolRepository->findOneBy(['id' => $schoolId]);
        if (!$school) {
            $output->writeln("<error>There are no schools with id ${schoolId}.</error>");
            return 1;
        }
        /** @var SchoolConfigInterface[] $configs */
        $configs = $this->schoolConfigRepository->findBy(['school' => $schoolId], ['name' => 'asc']);
        if (empty($configs)) {
            $output->writeln('<error>There are no configuration values in the database.</error>');
        } else {
            $table = new Table($output);
            $table->setHeaders(['Name', 'Value'])
                ->setRows(array_map(function (SchoolConfigInterface $config) {
                    return [$config->getName(), $config->getValue()];
                }, $configs));
            $table->render();
        }

        return 0;
    }
}
