<?php

namespace Ilios\CliBundle\Command;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Generates the YAML file for the swagger path docs
 *
 * Class GenerateSwaggerApiPathYamlCommand
 */
class GenerateSwaggerApiPathYamlCommand extends Command
{
    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @param \Symfony\Component\Templating\EngineInterface
     */
    public function __construct(
        EngineInterface $templatingEngine
    ) {
        parent::__construct();
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:generate:swagger-path')
            ->setDescription('Creates standard swagger path yaml file for an endpoint.')
            ->addArgument(
                'endpointName',
                InputArgument::REQUIRED,
                'The basename of the end point e.g. session.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endpoint = $input->getArgument('endpointName');

        $singular = Inflector::singularize($endpoint);
        $plural = Inflector::pluralize($singular);
        $template = 'IliosCliBundle:Template:path.yml.twig';

        $content = $this->templatingEngine->render($template, [
            'endpoint' => $plural,
            'object' => $singular,
        ]);

        print $content;

        exit();
    }
}
