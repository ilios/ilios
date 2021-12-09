<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Ilios\MeSH\Parser;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
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
    private const YEARS = [
        2020 => 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2020.xml',
        2021 => 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2021.xml',
    ];

    public function __construct(
        protected Parser $parser,
        protected MeshDescriptorRepository $repository,
        protected Mesh $meshIndex
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:import-mesh-universe')
            ->setAliases(['ilios:maintenance:import-mesh-universe'])
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
                'The MeSH descriptors publication year. Acceptable values are '
                . implode(', ', array_keys(self::YEARS)) . '.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }
        $steps = $this->meshIndex->isEnabled() ? 5 : 4;
        $startTime = time();
        $output->writeln('Started MeSH universe import, this will take a while...');
        $uri = $this->getUri($input);
        $output->writeln("1/${steps}: Parsing MeSH XML retrieved from ${uri}.");
        $descriptorSet = $this->parser->parse($uri);
        $descriptorIds = $descriptorSet->getDescriptorUis();
        $output->writeln("2/${steps}: Clearing database of existing MeSH data.");
        $this->repository->clearExistingData();
        $existingDescriptors = $this->repository->findDTOsBy([]);
        $existingDescriptorIds = array_column($existingDescriptors, 'id');
        $updateDescriptorIds = array_intersect($existingDescriptorIds, $descriptorIds);
        $deletedDescriptorIds = array_diff($existingDescriptorIds, $descriptorIds);
        $output->writeln("3/${steps}: Importing MeSH data into database.");
        $this->repository->upsertMeshUniverse($descriptorSet, $updateDescriptorIds);
        $output->writeln("4/${steps}: Flagging orphaned MeSH descriptors as deleted.");
        $this->repository->flagDescriptorsAsDeleted($deletedDescriptorIds);

        if ($this->meshIndex->isEnabled()) {
            $output->writeln("5/${steps}: Adding MeSH data to the search index.");
            $allDescriptors = $descriptorSet->getDescriptors();
            $progressBar = new ProgressBar($output, count($allDescriptors));
            $progressBar->setMessage('Adding MeSH...');
            $progressBar->start();
            $chunks = array_chunk($allDescriptors, 500);
            foreach ($chunks as $descriptors) {
                $this->meshIndex->index($descriptors);
                $progressBar->advance(count($descriptors));
            }
            $progressBar->setMessage(count($allDescriptors) . " Descriptors Indexed for Search!");
            $progressBar->finish();
        }

        $endTime = time();
        $duration = $endTime - $startTime;
        $output->writeln('');
        $output->writeln("Finished MeSH universe import in ${duration} seconds.");
        $this->release();

        return 0;
    }

    private function getUri(InputInterface $input): string
    {
        $path = trim((string) $input->getOption('path'));
        $url = trim((string) $input->getOption('url'));
        $year = trim((string) $input->getOption('year'));

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
                $this->release();
                throw new RuntimeException('Given year must be one of: ' . implode(', ', $supportedYears));
            }

            return self::YEARS[$year];
        }

        rsort($supportedYears);

        return self::YEARS[$supportedYears[0]];
    }
}
