<?php

namespace App\Command;

use Doctrine\Common\Inflector\Inflector;
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
    /**
     * @var Environment
     */
    protected $twig;

    public function __construct(
        Environment $twig
    ) {
        parent::__construct();
        $this->twig = $twig;
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

        $singular = Inflector::singularize($endpoint);
        $plural = Inflector::pluralize($singular);
        $template = 'generate/path.yml.twig';

        $content = $this->twig->render($template, [
            'endpoint' => $plural,
            'object' => $singular,
        ]);

        print $content;

        exit();
    }
}
