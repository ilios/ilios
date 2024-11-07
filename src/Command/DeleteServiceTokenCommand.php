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
 * Deletes a service token.
 */
#[AsCommand(
    name: 'ilios:service-token:delete',
    description: 'Deletes a given service token.'
)]
class DeleteServiceTokenCommand extends Command
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
        $this->tokenRepository->delete($token);
        $output->writeln("Success! Token with id #{$tokenId} was deleted.");
        return Command::SUCCESS;
    }
}
