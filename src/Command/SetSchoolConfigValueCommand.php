<?php

namespace App\Command;

use App\Entity\Manager\SchoolManager;
use App\Entity\SchoolConfig;
use App\Entity\Manager\SchoolConfigManager;
use App\Entity\SchoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Set a school configuration value in the DB
 *
 * Class SetConfigValueCommand
 * @package AppBundle\Command
 */
class SetSchoolConfigValueCommand extends Command
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
     * SetSchoolConfigValueCommand constructor.
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
            ->setName('ilios:maintenance:set-school-config-value')
            ->setDescription('Set a configuration value in the DB')
            //required arguments
            ->addArgument(
                'school',
                InputArgument::REQUIRED,
                'The id of the school the config belongs to'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the configuration we are setting'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'The value of the configuration we are setting'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schoolId = $input->getArgument('school');
        $name = $input->getArgument('name');
        $value = $input->getArgument('value');

        /** @var SchoolInterface $school */
        $school = $this->schoolManager->findOneBy(['id' => $schoolId]);
        if (!$school) {
            $output->writeln("<error>There are no schools with id ${schoolId}.</error>");
            return 1;
        }

        /** @var SchoolConfig $config */
        $config = $this->schoolConfigManager->findOneBy(['school' => $school->getId(), 'name' => $name]);
        if (!$config) {
            $config = $this->schoolConfigManager->create();
            $config->setName($name);
        }
        $config->setValue($value);

        $this->schoolConfigManager->update($config, true);

        $output->writeln('<info>Done.</info>');

        return 0;
    }
}
