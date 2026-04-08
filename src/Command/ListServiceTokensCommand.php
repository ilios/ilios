<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists out service tokens, with some filtering options.
 */
#[AsCommand(
    name: 'ilios:service-token:list',
    description: 'List service tokens.',
    aliases: ['ilios:maintenance:service-token:list']
)]
class ListServiceTokensCommand extends Command
{
    public function __construct(protected ServiceTokenRepository $tokenRepository)
    {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(description: 'Exclude disabled tokens.', name: 'exclude-disabled')] bool $excludeDisabled = false,
        #[Option(description: 'Exclude expired tokens.', name: 'exclude-expired')] bool $excludeExpired = false,
        #[Option(
            description: 'Only list tokes that are expiring within the given date interval from now.',
            name: 'expires-within'
        )] ?string $expiresWithin = null,
    ): int {
        $now = new DateTimeImmutable();
        $expirationDate = null;
        if ($expiresWithin) {
            try {
                $ttl = new DateInterval($expiresWithin);
                $expirationDate = $now->add($ttl);
            } catch (Exception $e) {
                $output->writeln('Unable to parse given TTL value.');
                return Command::INVALID;
            }
        }
        $criteria = [];
        if ($excludeDisabled) {
            $criteria['enabled'] = true;
        }
        $tokens = $this->tokenRepository->findBy($criteria);
        $tokens = array_values(
            array_filter(
                $tokens,
                function (ServiceTokenInterface $token) use ($excludeExpired, $now, $expirationDate) {
                    $tokenExpiresAt = $token->getExpiresAt();
                    if ($excludeExpired && $tokenExpiresAt < $now) {
                        return false;
                    }
                    if ($expirationDate && $tokenExpiresAt > $expirationDate) {
                        return false;
                    }
                    return true;
                }
            )
        );

        $output->writeln('');
        $output->writeln("<options=bold,underscore>Service Tokens</>");
        if ($excludeDisabled) {
            $output->writeln('- excludes disabled tokens.');
        }
        if ($excludeExpired) {
            $output->writeln('- excludes expired tokens.');
        }
        if ($expirationDate) {
            $output->writeln('- excludes tokens with an expiration date exceeding ' . $expirationDate->format('c'));
        }
        $output->writeln('');
        if (! count($tokens)) {
            $output->writeln('No tokens found.');
            return Command::SUCCESS;
        }
        $table = new Table($output);
        $table->setHeaders(['id', 'description', 'status', 'created at', 'expires at']);

        /** @var ServiceTokenInterface $token */
        foreach ($tokens as $token) {
            $table->addRow([
                $token->getId(),
                $token->getDescription(),
                $token->isEnabled() ? 'enabled' : 'disabled',
                $token->getCreatedAt()->format('c'),
                $token->getExpiresAt()->format('c'),
            ]);
        }
        $table->render();
        return Command::SUCCESS;
    }
}
