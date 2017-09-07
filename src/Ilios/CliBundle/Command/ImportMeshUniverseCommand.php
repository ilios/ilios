<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imports the MeSH descriptors and co. from a given file location or URL, or for a given year.
 *
 * Class ImportMeshUniverseCommand
 */
class ImportMeshUniverseCommand extends Command {

    /**
     * @var string
     */
    const URL_TEMPLATE = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc{{year}}.xml';

    /**
     * @var array
     */
    const YEARS = [ 2016, 2017 ];

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
                'The MeSH descriptors publication year. Acceptable values are ' . implode(', ', self::YEARS)
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $this->getUri($input);
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
     * @param InputInterface $input
     * @return string
     */
    private function getUri(InputInterface $input) {
        $path = trim($input->getOption('path'));
        $url = trim($input->getOption('url'));
        $year = trim($input->getOption('year'));

        if ('' !== $path) {
            return $path;
        }

        if ('' !== $url) {
            return $url;
        }

        if ('' !== $year) {
            $year = (int) $year;
            if (!in_array($year, self::YEARS)) {
                throw new \RuntimeException('Given year must be one of: ' . implode(', ', self::YEARS));
            }
            return strtr(self::URL_TEMPLATE, [ '{{year}}' => $year ]);
        }

        $years = array_merge(self::YEARS);
        rsort($years);
        return strtr(self::URL_TEMPLATE, [ '{{year}}' => $years[0] ]);
    }

    /**
     * @param DescriptorSet $descriptors
     * @return array
     */
    private function transmogrifyMeSHDataForImport (DescriptorSet $descriptors)
    {
        $rhett = [
            'concept' => [],
            'concept_x_term' => [],
            'descriptor' => [],
            'descriptor_x_concept' => [],
            'descriptor_x_qualifier' => [],
            'qualifier' => [],
            'previous_indexing' => [],
            'term' => [],
            'tree' => [],
        ];

        foreach($descriptors->getDescriptors() as $descriptor) {
            $rhett['descriptor'][$descriptor->getUi()] = $descriptor;
            foreach($descriptor->getConcepts() as $concept) {
                $rhett['concept'][$concept->getUi()] = $concept;
                $rhett['descriptor_x_concept'][] = [ $descriptor->getUi(), $concept->getUi() ];
                foreach($concept->getTerms() as $term) {
                    // ACHTUNG MINEN!
                    // Unlike all other MeSH data points, terms do *not* possess unique UID.
                    // Generate a unique pseudo-key by hashing all relevant term properties,
                    // Use this hash instead of UID to keep track of term relationships
                    // and all relevant term permutations.
                    // [ST 2017/09/07]
                    $hash = md5(implode(',', [
                        $term->getUi(),
                        $term->getName(),
                        $term->getLexicalTag(),
                        $term->isConceptPreferred(),
                        $term->isRecordPreferred(),
                        $term->isPermuted(),
                    ]));
                    $rhett['term'][$hash] = $term;
                    $rhett['concept_x_term'][] = [ $concept->getUi(), $hash ];
                }
            }
            $rhett['tree'][$descriptor->getUi()] = $descriptor->getTreeNumbers();
            $rhett['previous_indexing'][$descriptor->getUi()] = $descriptor->getPreviousIndexing();
            foreach($descriptor->getAllowableQualifiers() as $qualifier) {
                $rhett['qualifier'][$qualifier->getQualifierReference()->getUi()] = $qualifier;
                $rhett['descriptor_x_qualifier'][] = [
                    $descriptor->getUi(),
                    $qualifier->getQualifierReference()->getUi(),
                ];
            }

        }

        return $rhett;
    }
}
