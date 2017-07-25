<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Ilios\CoreBundle\Service\CrossingGuard;

/**
 * Enable, disable, and check the status of the crossing guard service
 *
 * Class CrossingGuardCommand
 */
class CrossingGuardCommand extends Command
{
    const ENABLED_MESSAGE = 'Crossing Guard is down - Requests will be held until further notice.';
    const DISABLED_MESSAGE = 'Crossing Guard is up - Requests are flowing normally.';
    /**
     * @var CrossingGuard
     */
    protected $crossingGuard;
    
    public function __construct(
        CrossingGuard $crossingGuard
    ) {
        $this->crossingGuard = $crossingGuard;
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ilios:maintenance:crossing-guard')
            ->setDescription('Enable, disable, and check the status of the crossing guard.')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'status|down|up'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
        $message = $status?self::ENABLED_MESSAGE:self::DISABLED_MESSAGE;
        $output->writeln('');
        $output->writeln("<info>${message} </info>");
        $output->writeln('');
    }
}
