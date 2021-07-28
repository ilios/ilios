<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\Inflector\Inflector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

/**
 * Generates the YAML file for the swagger path docs
 *
 * Class GenerateSwaggerApiPathYamlCommand
 */
class GenerateSwaggerApiPathYamlCommand extends Command
{
    public function __construct(
        protected Environment $twig,
        protected Inflector $inflector
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:generate:swagger-path')
            ->setHidden(true)
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

        $singular = $this->inflector->singularize($endpoint);
        $plural = $this->inflector->pluralize($singular);
        $template = 'generate/path.yml.twig';

        $content = $this->twig->render($template, [
            'endpoint' => $plural,
            'object' => $singular,
        ]);

        print $content;

        return 0;
    }
}
