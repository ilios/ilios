<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\OfferingInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Repository\OfferingRepository;
use App\Repository\SchoolRepository;
use App\Service\Config;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

/**
 * Sends teaching reminders to educators for their upcoming session offerings.
 *
 * Class SendTeachingRemindersCommand
 */
#[AsCommand(
    name: 'ilios:send-teaching-reminders',
    description: 'Sends teaching reminders to educators.',
    aliases: ['ilios:messaging:send-teaching-reminders'],
)]
class SendTeachingRemindersCommand extends Command
{
    public const int DEFAULT_DAYS_IN_ADVANCE = 7;

    public const string DEFAULT_TEMPLATE_NAME = 'teachingreminder.text.twig';

    public const string DEFAULT_MESSAGE_SUBJECT = 'Upcoming Teaching Session';

    public function __construct(
        protected OfferingRepository $offeringRepository,
        protected SchoolRepository $schoolRepository,
        protected Environment $twig,
        protected MailerInterface $mailer,
        protected Config $config,
        protected Filesystem $fs,
        protected string $kernelProjectDir
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'Email address to send reminders from.')] string $sender,
        #[Argument(description: 'The base URL of your Ilios instance.', name: 'base_url')] string $baseUrl,
        #[Option(description: "The name of the reminder's sender.", name: 'sender_name')] ?string $senderName = null,
        #[Option(
            description: 'Only Send Reminders for this comma separated list of school ids.'
        )] ?string $schools = null,
        #[Option(
            description: 'Prints out notification instead of emailing it. Useful for testing/debugging purposes.',
            name: 'dry-run'
        )] bool $isDryRun = false,
        #[Option(
            description: 'How many days in advance of teaching events reminders should be sent.'
        )] int $days = self::DEFAULT_DAYS_IN_ADVANCE,
        #[Option(
            description: 'The subject line of the reminder emails.'
        )] string $subject = self::DEFAULT_MESSAGE_SUBJECT,
    ): int {
        // input validation
        $errors = $this->validateInput($days, $sender);
        if (! empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln("<error>{$error}</error>");
            }
            return Command::FAILURE;
        }

        $baseUrl = rtrim($baseUrl);
        $from = $sender;
        if ($senderName) {
            $from = new Address($sender, $senderName);
        }
        if ($schools) {
            $schoolIds = array_map('intval', str_getcsv($schools, escape: "\\"));
        } else {
            $schoolIds = $this->schoolRepository->getIds();
        }

        // get all applicable offerings.
        $offerings = $this->offeringRepository->getOfferingsForTeachingReminders($days, $schoolIds);

        if ($offerings === []) {
            $output->writeln('<info>No offerings with pending teaching reminders found.</info>');
            return Command::SUCCESS;
        }

        // mail out a reminder per instructor per offering.
        $templateCache = [];
        $i = 0;

        /** @var OfferingInterface $offering */
        foreach ($offerings as $offering) {
            $school = $offering->getSchool();
            if (! array_key_exists($school->getId(), $templateCache)) {
                $template = $this->getTemplatePath($school);
                $templateCache[$school->getId()] = $template;
            }
            $template = $templateCache[$school->getId()];

            $instructors = $offering->getAllInstructors()->toArray();
            $timezone = $this->config->get('timezone');


            /** @var UserInterface $instructor */
            foreach ($instructors as $instructor) {
                $i++;
                $messageBody = $this->twig->render($template, [
                    'base_url' => $baseUrl,
                    'instructor' => $instructor,
                    'offering' => $offering,
                    'timezone' => $timezone,
                ]);
                $email = $instructor->getPreferredEmail();
                if (empty($email)) {
                    $email = $instructor->getEmail();
                }
                $message = (new Email())
                    ->from($from)
                    ->to($email)
                    ->subject($subject)
                    ->text($messageBody);
                if ($isDryRun) {
                    $output->writeln($message->getHeaders()->toString());
                    $output->writeln($message->getTextBody());
                } else {
                    $this->mailer->send($message);
                }
            }
        }

        $output->writeln("<info>Sent {$i} teaching reminders.</info>");

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

    /**
     * Validates user input.
     */
    protected function validateInput(int $days, string $sender): array
    {
        $errors = [];

        if (0 > $days) {
            $errors[] = "Invalid value '{$days}' for '--days' option. Must be greater or equal to 0.";
        }
        if (! filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid value '{$sender}' for '--sender' option. Must be a valid email address.";
        }

        return $errors;
    }
}
