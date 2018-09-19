<?php

namespace App\Command;

use App\Entity\Manager\SchoolConfigManager;
use App\Entity\Manager\SchoolManager;
use App\Entity\SchoolConfigInterface;
use App\Entity\SchoolInterface;
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
    /**
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @var SchoolConfigManager
     */
    protected $schoolConfigManager;

    /**
     * RolloverCourseCommand constructor.
     * @param SchoolManager $schoolManager
     * @param SchoolConfigManager $schoolConfigManager
     */
    public function __construct(SchoolManager $schoolManager, SchoolConfigManager $schoolConfigManager)
    {
        $this->schoolManager = $schoolManager;
        $this->schoolConfigManager = $schoolConfigManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:list-school-config-values')
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
        $school = $this->schoolManager->findOneBy(['id' => $schoolId]);
        if (!$school) {
            $output->writeln("<error>There are no schools with id ${schoolId}.</error>");
            return 1;
        }
        /** @var SchoolConfigInterface[] $configs */
        $configs = $this->schoolConfigManager->findBy(['school' => $schoolId], ['name' => 'asc']);
        if (empty($configs)) {
            $output->writeln('<error>There are no configuration values in the database.</error>');
        } else {
            $table = new Table($output);
            $table->setHeaders(array('Name', 'Value'))
                ->setRows(array_map(function (SchoolConfigInterface $config) {
                    return [$config->getName(), $config->getValue()];
                }, $configs));
            $table->render();
        }
    }
}
