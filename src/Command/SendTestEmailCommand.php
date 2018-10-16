<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends a test email
 *
 * Class SendTestEmailCommand
 */
class SendTestEmailCommand extends Command
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(
        \Swift_Mailer $mailer
    ) {
        parent::__construct();
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:send-test-email')
            ->setDescription('Sends out a test email, useful for ensuring email is working.')
            ->addArgument(
                'from',
                InputArgument::REQUIRED,
                'The email address to send from'
            )
            ->addArgument(
                'to',
                InputArgument::REQUIRED,
                'The email address to send to'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $to = $input->getArgument('to');
        $from = $input->getArgument('from');

        $message = (new \Swift_Message('Ilios Test Email'))
            ->setTo($to)
            ->setFrom($from)
            ->setContentType('text/plain')
            ->setBody('This is a test email from your ilios system.');
        $this->mailer->send($message);
    }
}
