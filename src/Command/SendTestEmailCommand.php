<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Sends a test email
 *
 * Class SendTestEmailCommand
 */
#[AsCommand(
    name: 'ilios:send-test-email',
    description: 'Sends out a test email, useful for ensuring email is working.',
)]
class SendTestEmailCommand extends Command
{
    public function __construct(protected MailerInterface $mailer)
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'The email address to send from')] string $from,
        #[Argument(description: 'The email address to send to')] string $to,
    ): int {
        $message = (new Email())
            ->to($to)
            ->from($from)
            ->subject('Ilios Test Email')
            ->text('This is a test email from your ilios system.');
        $this->mailer->send($message);

        return Command::SUCCESS;
    }
}
