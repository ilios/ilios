<?php

namespace Ilios\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
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
                null,
                InputOption::VALUE_NONE,
                'Specify this option to delete exported entries from the database.'
            )
            ->addArgument(
                'from',
                InputArgument::OPTIONAL,
                'Expression for start-date/time of export range.',
                'midnight yesterday'
            )
            ->addArgument(
                'to',
                InputArgument::OPTIONAL,
                'Expression for end-date/time of export range.',
                'midnight today'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $from = new \DateTime($from, new \DateTimeZone('UTC'));
        $to = new \DateTime($to, new \DateTimeZone('UTC'));
        $delete = $input->getOption('delete');

        $em = $this->getContainer()->get('ilioscore.auditlog.manager');

        $headers = $em->getFieldNames();
        $rows = $em->findInRange($from, $to);

        array_walk($rows, function (&$row) {
            /** @var \DateTime $dt */
            $dt = $row['createdAt'];
            $row['createdAt'] = $dt->format('c');
        });

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();

        if ($delete) {
            $em->deleteInRange($from, $to);
        }
    }
}
