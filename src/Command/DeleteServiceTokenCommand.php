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
 * Deletes a service token.
 */
#[AsCommand(
    name: 'ilios:service-token:delete',
    description: 'Deletes a given service token.'
)]
class DeleteServiceTokenCommand extends Command
{
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
        $this->tokenRepository->delete($token);
        $output->writeln("Success! Token with id #{$id} was deleted.");
        return Command::SUCCESS;
    }
}
