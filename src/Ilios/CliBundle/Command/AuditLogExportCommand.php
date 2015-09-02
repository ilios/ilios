<?php

namespace Ilios\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Exports audit log entries in a given time range and, optionally, deletes them.
 *
 * Class AuditLogExportCommand
 * @package Ilios\CoreBundle\Command
 */
class AuditLogExportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:export-audit-log')
            ->setDescription('Exports audit log entries in a given time range and, optionally, deletes them.')
            ->addOption(
                'delete',
                'null',
                InputOption::VALUE_OPTIONAL,
                'Set to TRUE to delete exported entries from the database.',
                false
            )
            ->addArgument(
                'from',
                InputArgument::REQUIRED,
                'Expression for start-date/time of export range.',
                'midnight yesterday'
            )
            ->addArgument(
                'to',
                InputArgument::OPTIONAL,
                'Expression for end-date/time of export range.',
                'midnight today'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $delete = $input->getOption('delete');

        var_dump($delete);
    }
}
