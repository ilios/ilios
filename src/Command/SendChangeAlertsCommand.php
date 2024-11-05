<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AlertInterface;
use App\Entity\AuditLogInterface;
use App\Entity\SchoolInterface;
use App\Repository\AlertRepository;
use App\Repository\AuditLogRepository;
use App\Repository\OfferingRepository;
use App\Service\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Sends change alerts emails.
 *
 * Class SendChangeAlertsCommand
 */
#[AsCommand(
    name: 'ilios:send-change-alerts',
    description: 'Sends out change alert message to configured email recipients.',
    aliases: ['ilios:messaging:send-change-alerts'],
)]
class SendChangeAlertsCommand extends Command
{
    private const string DEFAULT_TEMPLATE_NAME = 'offeringchangealert.text.twig';

    public function __construct(
        protected AlertRepository $alertRepository,
        protected AuditLogRepository $auditLogRepository,
        protected OfferingRepository $offeringRepository,
        protected Environment $twig,
        protected MailerInterface $mailer,
        protected Config $config,
        protected Filesystem $fs,
        protected string $kernelProjectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Print out alerts instead of emailing them. Useful for testing/debugging purposes.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDryRun = $input->getOption('dry-run');

        $alerts = $this->alertRepository->findBy(['dispatched' => false, 'tableName' => 'offering']);
        if ($alerts === []) {
            $output->writeln("<info>No undispatched offering alerts found.</info>");
            return Command::SUCCESS;
        }

        $templateCache = [];

        $sent = 0;
        // email out change alerts
        /** @var AlertInterface $alert */
        foreach ($alerts as $alert) {
            $output->writeln("<info>Processing offering change alert {$alert->getId()}.</info>");

            $offering = $this->offeringRepository->findOneBy(['id' => $alert->getTableRowId()]);
            if (! $offering) {
                $output->writeln(
                    "<warning>No offering with id {$alert->getTableRowId()},"
                    . " unable to send change alert with id {$alert->getId()}.</warning>"
                );
                continue;
            }
            // do not send alerts for deleted stuff.
            $deleted = ! $offering->getSession()
                || ! $offering->getSession()->getCourse()
                || ! $offering->getSession()->getCourse()->getSchool();
            if ($deleted) {
                // @todo print another warning here? [ST 2015/09/30]
                continue;
            }

            $schools = $alert->getRecipients();
            if ($schools->isEmpty()) {
                $output->writeln("<error>No alert recipient for offering change alert {$alert->getId()}.</error>");
                continue;
            }
            // Technically, there could be multiple school as recipients to a given alert.
            // The db schema allows for it.
            // In practice, there is really only ever one school recipient.
            // So take the first one and run with it for determining recipients/rendering the email template.
            // [ST 2015/10/05]
            /** @var SchoolInterface $school */
            $school = $schools->first();

            $recipients = trim((string) $school->getChangeAlertRecipients());
            if ('' === $recipients) {
                $output->writeln(
                    "<error>Recipient without email for offering change alert {$alert->getId()}.</error>"
                );
                continue;
            }
            $recipients = array_map('trim', explode(',', $recipients));

            // get change alert history from audit logs
            $history = $this->auditLogRepository->findBy([
                'objectId' => $alert->getId(),
                'objectClass' => 'alert',
            ], [ 'createdAt' => 'asc' ]);

            $subject = $offering->getSession()->getCourse()->getExternalId() . ' - '
                . $offering->getStartDate()->format('m/d/Y');

            if (! array_key_exists($school->getId(), $templateCache)) {
                $template = $this->getTemplatePath($school);
                $templateCache[$school->getId()] = $template;
            }
            $template = $templateCache[$school->getId()];
            $timezone = $this->config->get('timezone');

            $messageBody = $this->twig->render($template, [
                'alert' => $alert,
                'history' => $history,
                'offering' => $offering,
                'timezone' => $timezone,
            ]);

            $message = (new Email())
                ->to(...$recipients)
                ->from($school->getIliosAdministratorEmail())
                ->subject($subject)
                ->text($messageBody);

            if ($isDryRun) {
                $output->writeln($message->getHeaders()->toString());
                $output->writeln($message->getTextBody());
            } else {
                $this->mailer->send($message);
            }
            $sent++;
        }
        if (! $isDryRun) {
            // Mark all alerts as dispatched, regardless as to whether an actual email
            // was sent or not.
            // This is consistent with the Ilios v2 implementation of this process.
            // @todo Reassess the validity of this step. [ST 2015/10/01]
            foreach ($alerts as $alert) {
                $alert->setDispatched(true);
                $this->alertRepository->update($alert);
            }

            $dispatched = count($alerts);
            $output->writeln("<info>Sent {$sent} offering change alert notifications.</info>");
            $output->writeln("<info>Marked {$dispatched} offering change alerts as dispatched.</info>");
        }

        return Command::SUCCESS;
    }

    /**
     * Locates the applicable message template for a given school and returns its path.
     */
    protected function getTemplatePath(SchoolInterface $school): string
    {
        $prefix = $school->getTemplatePrefix();
        if ($prefix) {
            $path = 'email/' . basename($prefix . '_' . self::DEFAULT_TEMPLATE_NAME);
            if ($this->fs->exists($this->kernelProjectDir . '/custom/templates/' . $path)) {
                return $path;
            }
        }

        return 'email/' . self::DEFAULT_TEMPLATE_NAME;
    }
}
