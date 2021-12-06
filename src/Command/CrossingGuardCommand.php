<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\CrossingGuard;

/**
 * Enable, disable, and check the status of the crossing guard service
 *
 * Class CrossingGuardCommand
 */
class CrossingGuardCommand extends Command
{
    public const ENABLED_MESSAGE = 'Crossing Guard is down - Requests will be held until further notice.';
    public const DISABLED_MESSAGE = 'Crossing Guard is up - Requests are flowing normally.';

    public function __construct(
        protected CrossingGuard $crossingGuard
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:crossing-guard')
            ->setAliases(['ilios:maintenance:crossing-guard'])
            ->setDescription('Enable, disable, and check the status of the crossing guard.')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'status|down|up'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = strtolower($input->getArgument('action'));
        if (!in_array($action, ['down', 'up', 'status'])) {
            throw new \Exception("'${action} is not a valid action (status|enable|disable)'");
        }

        if ($action === 'down') {
            $this->crossingGuard->enable();
        }
        if ($action === 'up') {
            $this->crossingGuard->disable();
        }

        $status = $this->crossingGuard->isStopped();
        $message = $status ? self::ENABLED_MESSAGE : self::DISABLED_MESSAGE;
        $output->writeln('');
        $output->writeln("<info>${message} </info>");
        $output->writeln('');

        return 0;
    }
}
