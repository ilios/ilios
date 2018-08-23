<?php

namespace AppBundle\Command;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\MeSH\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imports the MeSH descriptors and co. from a given file location or URL, or for a given year.
 *
 * Class ImportMeshUniverseCommand
 */
class ImportMeshUniverseCommand extends Command
{
    use LockableTrait;

    /**
     * @var array
     */
    const YEARS = [
        2017 => 'ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc2017.xml',
        2018 => 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2018.xml',
    ];

    /**
     * @var MeshDescriptorManager
     */
    protected $manager;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @param Parser $parser
     * @param MeshDescriptorManager $manager
     */
    public function __construct(Parser $parser, MeshDescriptorManager $manager)
    {
        $this->parser = $parser;
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:import-mesh-universe')
            ->setDescription('Imports the MeSH universe into Ilios.')
            ->addOption(
                'url',
                'u',
                InputOption::VALUE_REQUIRED,
                'The MeSH descriptors URL.'
            )
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'The MeSH descriptors file path.'
            )
            ->addOption(
                'year',
                'y',
                InputOption::VALUE_REQUIRED,
                'The MeSH descriptors publication year. Acceptable values are '.implode(', ', self::YEARS)
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }
        $startTime = time();
        $output->writeln('Started MeSH universe import, this will take a while...');
        $uri = $this->getUri($input);
        $output->writeln("1/4: Parsing MeSH XML retrieved from ${uri}.");
        $descriptorSet = $this->parser->parse($uri);
        $descriptorIds = $descriptorSet->getDescriptorUis();
        $output->writeln("2/4: Clearing database of existing MeSH data.");
        $this->manager->clearExistingData();
        $existingDescriptors = $this->manager->findDTOsBy(array());
        $existingDescriptorIds = array_column($existingDescriptors, 'id');
        $updateDescriptorIds = array_intersect($existingDescriptorIds, $descriptorIds);
        $deletedDescriptorIds = array_diff($existingDescriptorIds, $descriptorIds);
        $output->writeln("3/4: Importing MeSH data into database.");
        $this->manager->upsertMeshUniverse($descriptorSet, $updateDescriptorIds);
        $output->writeln("4/4: Flagging orphaned MeSH descriptors as deleted.");
        $this->manager->flagDescriptorsAsDeleted($deletedDescriptorIds);
        $endTime = time();
        $duration = $endTime - $startTime;
        $output->writeln("Finished MeSH universe import in ${duration} seconds.");
        $this->release();
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getUri(InputInterface $input)
    {
        $path = trim($input->getOption('path'));
        $url = trim($input->getOption('url'));
        $year = trim($input->getOption('year'));

        if ('' !== $path) {
            return $path;
        }

        if ('' !== $url) {
            return $url;
        }

        $supportedYears = array_keys(self::YEARS);

        if ('' !== $year) {
            $year = (int)$year;
            if (!in_array($year, $supportedYears)) {
                throw new \RuntimeException('Given year must be one of: '.implode(', ', $supportedYears));
            }

            return self::YEARS[$year];
        }

        rsort($supportedYears);

        return self::YEARS[$supportedYears[0]];
    }
}
