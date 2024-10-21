<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    public const EXCLUDE_DISABLED_KEY = 'exclude-disabled';
    public const EXCLUDE_EXPIRED_KEY = 'exclude-expired';
    public const EXPIRES_WITHIN_KEY = 'expires-within';


    public function __construct(protected ServiceTokenRepository $tokenRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                self::EXCLUDE_DISABLED_KEY,
                null,
                InputOption::VALUE_NONE,
                'Exclude disabled tokens.',
            )
            ->addOption(
                self::EXCLUDE_EXPIRED_KEY,
                null,
                InputOption::VALUE_NONE,
                'Exclude expired tokens.',
            )
            ->addOption(
                self::EXPIRES_WITHIN_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                'Only list tokes that are expiring within the given date interval from now.',
                null,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $excludeDisabledTokens = $input->getOption(self::EXCLUDE_DISABLED_KEY);
        $excludeExpiredTokens = $input->getOption(self::EXCLUDE_EXPIRED_KEY);
        $now = new DateTimeImmutable();
        $expirationDate = null;
        if ($input->getOption(self::EXPIRES_WITHIN_KEY)) {
            try {
                $ttl = new DateInterval($input->getOption(self::EXPIRES_WITHIN_KEY));
                $expirationDate = $now->add($ttl);
            } catch (Exception $e) {
                $output->writeln('Unable to parse given TTL value.');
                return Command::INVALID;
            }
        }
        $criteria = [];
        if ($excludeDisabledTokens) {
            $criteria['enabled'] = true;
        }
        $tokens = $this->tokenRepository->findBy($criteria);
        $tokens = array_values(
            array_filter(
                $tokens,
                function (ServiceTokenInterface $token) use ($excludeExpiredTokens, $now, $expirationDate) {
                    $tokenExpiresAt = $token->getExpiresAt();
                    if ($excludeExpiredTokens && $tokenExpiresAt < $now) {
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
        if ($excludeDisabledTokens) {
            $output->writeln('- excludes disabled tokens.');
        }
        if ($excludeExpiredTokens) {
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
