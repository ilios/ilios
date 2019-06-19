<?php

namespace App\Command;

use App\Entity\AlertInterface;
use App\Entity\AuditLogInterface;
use App\Entity\Manager\AuditLogManager;
use App\Entity\Manager\AlertManager;
use App\Entity\Manager\OfferingManager;
use App\Entity\SchoolInterface;
use App\Service\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

/**
 * Sends change alerts emails.
 *
 * Class SendChangeAlertsCommand
 */
class SendChangeAlertsCommand extends Command
{
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_NAME = 'offeringchangealert.text.twig';

    /**
     * @var AlertManager
     */
    protected $alertManager;

    /**
     * @var AuditLogManager
     */
    protected $auditLogManager;

    /**
     * @var OfferingManager
     */
    protected $offeringManager;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $kernelProjectDir;

    /**
     * @param AlertManager $alertManager
     * @param AuditLogManager $auditLogManager
     * @param OfferingManager $offeringManager
     * @param Environment $twig
     * @param \Swift_Mailer $mailer
     * @param Config $config
     * @param Filesystem $fs
     * @param string $kernelProjectDir
     */
    public function __construct(
        AlertManager $alertManager,
        AuditLogManager $auditLogManager,
        OfferingManager $offeringManager,
        Environment $twig,
        \Swift_Mailer $mailer,
        Config $config,
        Filesystem $fs,
        string $kernelProjectDir
    ) {
        parent::__construct();
        $this->alertManager = $alertManager;
        $this->auditLogManager = $auditLogManager;
        $this->offeringManager = $offeringManager;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->config = $config;
        $this->fs = $fs;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:send-change-alerts')
            ->setAliases(['ilios:messaging:send-change-alerts'])
            ->setDescription('Sends out change alert message to configured email recipients.')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Print out alerts instead of emailing them. Useful for testing/debugging purposes.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isDryRun = $input->getOption('dry-run');

        $alerts = $this->alertManager->findBy(['dispatched' => false, 'tableName' => 'offering']);
        if (! count($alerts)) {
            $output->writeln("<info>No undispatched offering alerts found.</info>");
            return;
        }

        $templateCache = [];

        $sent = 0;
        // email out change alerts
        /* @var AlertInterface $alert */
        foreach ($alerts as $alert) {
            $output->writeln("<info>Processing offering change alert {$alert->getId()}.</info>");

            $offering = $this->offeringManager->findOneBy(['id' => $alert->getTableRowId()]);
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
            /* @var SchoolInterface $school */
            $school = $schools->first();

            $recipients = trim($school->getChangeAlertRecipients());
            if ('' === $recipients) {
                $output->writeln(
                    "<error>Recipient without email for offering change alert {$alert->getId()}.</error>"
                );
                continue;
            }
            $recipients = array_map('trim', explode(',', $recipients));

            // get change alert history from audit logs
            $history = $this->auditLogManager->findBy([
                'objectId' => $alert->getId(),
                'objectClass' => 'alert',
            ], [ 'createdAt' => 'asc' ]);
            $history = array_filter($history, function (AuditLogInterface $auditLog) {
                $user =  $auditLog->getUser();
                return isset($user);
            });

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

            $message = (new \Swift_Message($subject))
                ->setTo($recipients)
                ->setFrom($school->getIliosAdministratorEmail())
                ->setContentType('text/plain')
                ->setBody($messageBody)
                ->setMaxLineLength(998);

            if ($isDryRun) {
                $output->writeln($message->getHeaders()->toString());
                $output->writeln($message->getBody());
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
                $this->alertManager->update($alert);
            }

            $dispatched = count($alerts);
            $output->writeln("<info>Sent {$sent} offering change alert notifications.</info>");
            $output->writeln("<info>Marked {$dispatched} offering change alerts as dispatched.</info>");
        }
    }

    /**
     * Locates the applicable message template for a given school and returns its path.
     * @param SchoolInterface $school
     * @return string The template path.
     */
    protected function getTemplatePath(SchoolInterface $school)
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
