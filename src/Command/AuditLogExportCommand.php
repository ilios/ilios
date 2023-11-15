<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuditLogRepository;
use DateTime;
use DateTimeZone;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Exports audit log entries in a given time range and, optionally, deletes them.
 *
 * Class AuditLogExportCommand
 *
 * @link http://symfony.com/doc/current/cookbook/console/logging.html
 * @link http://symfony.com/doc/current/components/console/helpers/table.html
 */
class AuditLogExportCommand extends Command
{
    public function __construct(protected LoggerInterface $logger, protected AuditLogRepository $auditLogRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ilios:export-audit-log')
            ->setAliases(['ilios:maintenance:export-audit-log'])
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');

        $from = new DateTime($from, new DateTimeZone('UTC'));
        $to = new DateTime($to, new DateTimeZone('UTC'));

        $delete = $input->getOption('delete');

        $headers = ['id', 'userId', 'action', 'createdAt', 'objectId', 'objectClass', 'valuesChanged'];

        $this->logger->info('Starting Audit Log Export.');

        $rows = array_map(function (array $arr) {
            /** @var DateTime $dt */
            $dt = $arr['createdAt'];
            return [
                $arr['id'],
                $arr['userId'],
                $arr['action'],
                $dt->format('c'),
                $arr['objectId'],
                $arr['objectClass'],
                $arr['valuesChanged']
            ];
        }, $this->auditLogRepository->findInRange($from, $to));

        $this->logger->info(
            sprintf(
                'Exporting %d audit log entries which were created between %s and %s.',
                count($rows),
                $from->format('c'),
                $to->format('c')
            )
        );

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();

        if ($delete) {
            $this->logger->info(
                sprintf(
                    'Deleting all audit log entries that were created between %s and %s.',
                    $from->format('c'),
                    $to->format('c')
                )
            );
            $this->auditLogRepository->deleteInRange($from, $to);
        }

        $this->logger->info('Finished Audit Log Export.');

        return Command::SUCCESS;
    }
}
