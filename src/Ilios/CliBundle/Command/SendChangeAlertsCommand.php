<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\AuditLogInterface;
use Ilios\CoreBundle\Entity\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AuditLogManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Sends change alerts emails.
 *
 * Class SendChangeAlertsCommand
 * @package Ilios\CliBUndle\Command
 */
class SendChangeAlertsCommand extends Command
{
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_NAME = 'offeringchangealert.text.twig';

    /**
     * @var AlertManagerInterface
     */
    protected $alertManager;

    /**
     * @var AuditLogManagerInterface
     */
    protected $auditLogManager;

    /**
     * @var OfferingManagerInterface
     */
    protected $offeringManager;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param AlertManagerInterface $alertManager
     * @param AuditLogManagerInterface $auditLogManager
     * @param OfferingManagerInterface $offeringManager
     * @param EngineInterface $templatingEngine
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(
        AlertManagerInterface $alertManager,
        AuditLogManagerInterface $auditLogManager,
        OfferingManagerInterface $offeringManager,
        EngineInterface $templatingEngine,
        $mailer
    ) {
        parent::__construct();
        $this->alertManager = $alertManager;
        $this->auditLogManager = $auditLogManager;
        $this->offeringManager = $offeringManager;
        $this->templatingEngine = $templatingEngine;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:messaging:send-change-alerts')
            ->setDescription('Sends change alerts on a per-school basis to configured recipients.')
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

        $alerts = $this->alertManager->findAlertsBy(['dispatched' => false, 'tableName' => 'offering']);
        if (! count($alerts)) {
            $output->writeln("<info>No undispatched offering alerts found.</info>");
            return;
        }

        $templateCache = [];

        $sent = 0;
        // email out change alerts
        foreach ($alerts as $alert) {
            $output->writeln("<info>Processing offering change alert {$alert->getId()}.</info>");

            $offering = $this->offeringManager->findOfferingBy(['id' => $alert->getTableRowId()]);
            if (! $offering) {
                $output->writeln(
                    "<warning>No offering with id {$alert->getTableRowId()},"
                    . " unable to send change alert with id {$alert->getId()}.</warning>"
                );
                continue;
            }
            // do not send alerts for deleted stuff.
            $deleted = $offering->getSession()->isDeleted()
                || $offering->getSession()->getCourse()->isDeleted()
                || ! $offering->getSession()->getCourse()->getSchool();
            if ($deleted) {
                // @todo print another warning here? [ST 2015/09/30]
                continue;
            }

            // get change alert history from audit logs
            $history = $this->auditLogManager->findAuditLogsBy([
                'objectId' => $alert->getId(),
                'objectClass' => 'alert',
            ], [ 'createdAt' => 'asc' ]);
            $history = array_filter($history, function (AuditLogInterface $auditLog) {
                $user =  $auditLog->getUser();
                return isset($user);
            });

            // convoluted way of identifying alert recipients
            $schools = $alert->getRecipients();
            /** @var SchoolInterface $school */
            $recipients = [];
            foreach ($schools->toArray() as $school) {
                $schoolRecipients = trim($school->getChangeAlertRecipients());
                if ('' === $schoolRecipients) {
                    continue;
                }
                $recipients = array_merge($recipients, array_map('trim', explode(',', strtolower($schoolRecipients))));
            }
            array_unique($recipients);
            if (empty($recipients)) {
                $output->writeln("<error>No alert recipients for offering change alert {$alert->getId()}.</error>");
                continue;
            }

            $subject = $offering->getSession()->getCourse()->getExternalId() . ' - '
                . $offering->getStartDate()->format('m/d/Y');

            $school = $offering->getSession()->getCourse()->getSchool();
            if (! array_key_exists($school->getId(), $templateCache)) {
                $template = $this->getTemplatePath($school);
                $templateCache[$school->getId()] = $template;
            }
            $template = $templateCache[$school->getId()];
            $messageBody = $this->templatingEngine->render($template, [
                'alert' => $alert,
                'history' => $history,
                'offering' => $offering,
            ]);

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setTo($recipients)
                ->setFrom($offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail())
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
        // Mark all alerts as dispatched, regardless as to whether an actual email
        // was sent or not.
        // This is consistent with the Ilios v2 implementation of this process.
        // @todo Reassess the validity of this step. [ST 2015/10/01]
        if ($isDryRun) {
            foreach ($alerts as $alert) {
                $alert->setDispatched(true);
                $this->alertManager->updateAlert($alert);
            }
        }
        $dispatched = count($alerts);

        $output->writeln("<info>Sent {$sent} offering change alert notifications.</info>");
        $output->writeln("<info>Marked {$dispatched} offering change alerts as dispatched.</info>");
    }

    /**
     * Locates the applicable message template for a given school and returns its path.
     * @param SchoolInterface $school
     * @return string The template path.
     */
    protected function getTemplatePath(SchoolInterface $school)
    {
        $paths = [
            '@custom_email_templates/' . basename($school->getTemplatePrefix() . '_' . self::DEFAULT_TEMPLATE_NAME),
            '@custom_email_templates/' . self::DEFAULT_TEMPLATE_NAME,
        ];
        foreach ($paths as $path) {
            if ($this->templatingEngine->exists($path)) {
                return $path;
            }
        }
        return 'IliosCoreBundle:Email:' .self::DEFAULT_TEMPLATE_NAME;
    }
}
