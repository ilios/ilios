<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Manager\MeshDescriptorManager;
use App\Service\CsvWriter;
use Exception;
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
class ExportMeshUniverseCommand extends Command
{
    use LockableTrait;

    /**
     * MeshDescriptorManager $manager
     */
    protected $manager;

    /**
     * @var CsvWriter
     */
    protected $writer;

    /**
     * @var string
     */
    protected $kernelProjectDir;

    /**
     * @param MeshDescriptorManager $manager
     * @param CsvWriter $writer
     * @param string $kernelProjectDir
     */
    public function __construct(MeshDescriptorManager $manager, CsvWriter $writer, string $kernelProjectDir)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->writer = $writer;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:export-mesh-universe')
            ->setAliases(['ilios:development:export-mesh-universe'])
            ->setDescription('Dumps the contents of all mesh_* tables into dataimport files.');
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }

        $header = [
            'mesh_concept_uid',
            'name',
            'preferred',
            'scope_note',
            'casn_1_name',
            'registry_number',
            'created_at',
            'updated_at'
        ];
        $data = $this->manager->exportMeshConcept();
        $this->writeToFile($header, $data, 'mesh_concept.csv');

        $header = ['mesh_concept_uid', 'mesh_term_id'];
        $data = $this->manager->exportMeshConceptTerm();
        $this->writeToFile($header, $data, 'mesh_concept_x_term.csv');

        $header = ['mesh_descriptor_uid', 'name', 'annotation', 'created_at', 'updated_at', 'deleted'];
        $data = $this->manager->exportMeshDescriptor();
        $this->writeToFile($header, $data, 'mesh_descriptor.csv');

        $header = ['mesh_concept_uid', 'mesh_descriptor_uid'];
        $data = $this->manager->exportMeshDescriptorConcept();
        $this->writeToFile($header, $data, 'mesh_descriptor_x_concept.csv');

        $header = ['mesh_descriptor_uid', 'mesh_qualifier_uid'];
        $data = $this->manager->exportMeshDescriptorQualifier();
        $this->writeToFile($header, $data, 'mesh_descriptor_x_qualifier.csv');

        $header = ['mesh_descriptor_uid', 'previous_indexing', 'mesh_previous_indexing_id'];
        $data = $this->manager->exportMeshPreviousIndexing();
        $this->writeToFile($header, $data, 'mesh_previous_indexing.csv');

        $header = ['mesh_qualifier_uid', 'name', 'created_at', 'updated_at'];
        $data = $this->manager->exportMeshQualifier();
        $this->writeToFile($header, $data, 'mesh_qualifier.csv');

        $header = [
            'mesh_term_uid',
            'name',
            'lexical_tag',
            'concept_preferred',
            'record_preferred',
            'permuted',
            'created_at',
            'updated_at',
            'mesh_term_id'
        ];
        $data = $this->manager->exportMeshTerm();
        $this->writeToFile($header, $data, 'mesh_term.csv');

        $this->release();
        return 0;
    }

    /**
     * @param $header
     * @param $data
     * @param $fileName
     * @throws Exception
     */
    protected function writeToFile($header, $data, $fileName)
    {
        $this->writer->writeToFile($header, $data, $this->kernelProjectDir . '/config/dataimport/' . $fileName);
    }
}
