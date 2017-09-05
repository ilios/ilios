<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imports the MeSH descriptors and co. from a given file location or URL, or for a given year.
 *
 * Class ImportMeSHUniverseCommand
 */
class ImportMeSHUniverseCommand extends Command {

    /**
     * @var string
     */
    const URL_TEMPLATE = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc{{year}}.xml';

    /**
     * @var array
     */
    protected $years = [ 2016, 2017 ];

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
                'The MeSH descriptors URL.'
            )
            ->addOption(
            'path',
            'The MeSH descriptors file path.'
            )
            ->addOption(
            'year',
            'The MeSH descriptors publication year. Acceptable values are ' . implode(', ', $this->years)
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $this->getUri();
        $descriptorSet = $this->parser->parse($uri);
        $descriptorIds = $descriptorSet->getDescriptorUis();
        $this->manager->clearExistingData();
        $existingDescriptors = $this->manager->findDTOsBy(array());
        $existingDescriptorIds = array_column($existingDescriptors, 'id');
        $updateDescriptorIds = array_intersect($existingDescriptorIds, $descriptorIds);
        $deletedDescriptorIds = array_diff($existingDescriptorIds, $descriptorIds);
        $data = $this->transmogrifyMeSHDataForImport($descriptorSet);
        $this->manager->upsertMeshUniverse($data, $updateDescriptorIds);
        $this->manager->flagDescriptorsAsDeleted($deletedDescriptorIds);
    }

    /**
     * @return string
     */
    private function getUri() {
        rsort($this->years);
        return strtr(self::URL_TEMPLATE, [ '{{year}}' => $this->years[0] ]);
    }

    /**
     * @param DescriptorSet $descriptors
     * @return array
     */
    private function transmogrifyMeSHDataForImport (DescriptorSet $descriptors)
    {
        $rhett = [
            'term' => [],
            'previous_indexing' => [],
            'descriptor' => [],
            'tree' => [],
            'concept' => [],
            'qualifier' => [],
            'semantic_type' => [],
            'concept_x_semantic_type' => [],
            'concept_x_term' => [],
            'descriptor_x_concept' => [],
            'descriptor_x_semantic_type' => [],
        ];

        // @todo implement [ST 2017/09/05]

        return $rhett;
    }
}
