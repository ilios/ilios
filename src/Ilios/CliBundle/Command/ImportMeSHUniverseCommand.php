<?php

namespace Ilios\CliBundle\Command;

use Ilios\MeSH\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imports the MeSH descriptors and co. from a given file location or URL.
 *
 * Class ImportMeSHUniverseCommand
 */
class ImportMeSHUniverseCommand extends Command {

    const URL_TEMPLATE = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc{{year}}.xml';

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
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
                InputArgument::OPTIONAL,
                'The MeSH descriptors URL.'
            )
            ->addOption(
            'path',
            InputArgument::OPTIONAL,
            'The MeSH descriptors file path.'
            )
            ->addOption(
            'year',
            InputArgument::OPTIONAL,
            'The MeSH descriptors publication year.'
    );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @todo implement [ST 2017/0]

    }
}
