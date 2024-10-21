<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MeshDescriptorRepository;
use App\Service\CsvWriter;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dumps the contents of the various mesh_* tables into corresponding CSV files in the config/dataimport directory.
 * This is a dev tool.
 *
 * Class ExportMeshUniverseCommand
 */
#[AsCommand(
    name: 'ilios:export-mesh-universe',
    description: 'Dumps the contents of all mesh_* tables into dataimport files.'
)]
class ExportMeshUniverseCommand extends Command
{
    use LockableTrait;

    public function __construct(
        protected MeshDescriptorRepository $repository,
        protected CsvWriter $writer,
        protected string $kernelProjectDir
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return Command::SUCCESS;
        }

        $header = [
            'mesh_concept_uid',
            'name',
            'preferred',
            'scope_note',
            'casn_1_name',
            'registry_number',
        ];
        $data = $this->repository->exportMeshConcepts();
        $this->writeToFile($header, $data, 'mesh_concept.csv');

        $header = ['mesh_concept_uid', 'mesh_term_id'];
        $data = $this->repository->exportMeshConceptTerms();
        $this->writeToFile($header, $data, 'mesh_concept_x_term.csv');

        $header = ['mesh_descriptor_uid', 'name', 'annotation', 'deleted'];
        $data = $this->repository->exportMeshDescriptors();
        $this->writeToFile($header, $data, 'mesh_descriptor.csv');

        $header = ['mesh_concept_uid', 'mesh_descriptor_uid'];
        $data = $this->repository->exportMeshDescriptorConcepts();
        $this->writeToFile($header, $data, 'mesh_descriptor_x_concept.csv');

        $header = ['mesh_descriptor_uid', 'mesh_qualifier_uid'];
        $data = $this->repository->exportMeshDescriptorQualifiers();
        $this->writeToFile($header, $data, 'mesh_descriptor_x_qualifier.csv');

        $header = ['mesh_descriptor_uid', 'previous_indexing', 'mesh_previous_indexing_id'];
        $data = $this->repository->exportMeshPreviousIndexings();
        $this->writeToFile($header, $data, 'mesh_previous_indexing.csv');

        $header = ['mesh_qualifier_uid', 'name'];
        $data = $this->repository->exportMeshQualifiers();
        $this->writeToFile($header, $data, 'mesh_qualifier.csv');

        $header = [
            'mesh_term_uid',
            'name',
            'lexical_tag',
            'concept_preferred',
            'record_preferred',
            'permuted',
            'mesh_term_id',
        ];
        $data = $this->repository->exportMeshTerms();
        $this->writeToFile($header, $data, 'mesh_term.csv');

        $header = ['tree_number', 'mesh_descriptor_uid', 'mesh_tree_id'];
        $data = $this->repository->exportMeshTrees();
        $this->writeToFile($header, $data, 'mesh_tree.csv');

        $this->release();
        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    protected function writeToFile(array $header, array $data, string $fileName): void
    {
        $this->writer->writeToFile($header, $data, $this->kernelProjectDir . '/config/dataimport/' . $fileName);
    }
}
