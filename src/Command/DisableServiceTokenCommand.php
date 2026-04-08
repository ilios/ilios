<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
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

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'The token ID.', name: 'id')] string $id,
    ): int {
        /** @var ?ServiceTokenInterface $token */
        $token = $this->tokenRepository->findOneById($id);
        if (!$token) {
            $output->writeln("No service token with id #{$id} was found.");
            return self::FAILURE;
        }
        if (!$token->isEnabled()) {
            $output->writeln("Token with id #{$id} is already disabled, no action taken.");
            return self::INVALID;
        }
        $token->setEnabled(false);
        $this->tokenRepository->update($token);
        $output->writeln("Success! Token with id #{$id} disabled.");
        return Command::SUCCESS;
    }
}
