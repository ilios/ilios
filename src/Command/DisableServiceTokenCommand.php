<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Disables a service token.
 */
#[AsCommand(
    name: 'ilios:service-token:disable',
    description: 'Disable a given service token.'
)]
class DisableServiceTokenCommand extends Command
{
    public const string ID_KEY = 'id';

    public function __construct(protected ServiceTokenRepository $tokenRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(self::ID_KEY, InputArgument::REQUIRED, "The token ID.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tokenId = $input->getArgument(self::ID_KEY);
        /** @var ?ServiceTokenInterface $token */
        $token = $this->tokenRepository->findOneById($input->getArgument(self::ID_KEY));
        if (!$token) {
            $output->writeln("No service token with id #{$tokenId} was found.");
            return self::FAILURE;
        }
        if (!$token->isEnabled()) {
            $output->writeln("Token with id #{$tokenId} is already disabled, no action taken.");
            return self::INVALID;
        }
        $token->setEnabled(false);
        $this->tokenRepository->update($token);
        $output->writeln("Success! Token with id #{$tokenId} disabled.");
        return Command::SUCCESS;
    }
}
