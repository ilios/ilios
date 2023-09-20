<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use App\Service\ServiceTokenUserProvider;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\JsonWebTokenManager;

/**
 * Create a new service account token.
 */
class CreateServiceTokenCommand extends Command
{
    public const TTL_KEY = 'ttl';
    public const TTL_MAX_VALUE = 'P180D'; // roughly six months
    public const WRITEABLE_SCHOOLS_KEY = 'writeable-schools';
    public const DESCRIPTION_KEY = 'description';

    public function __construct(
        protected ServiceTokenRepository $tokenRepository,
        protected ServiceTokenUserProvider $userProvider,
        protected JsonWebTokenManager $jwtManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ilios:service-token:create')
            ->setAliases(['ilios:maintenance:service-token:create'])
            ->setDescription('Creates a new service token for the API with given capabilities.')
            ->addOption(
                self::WRITEABLE_SCHOOLS_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                'Schools that the token has write access to, provided as a comma-separated list of ids.',
                ''
            )
            ->addOption(
                self::TTL_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                "The token's time-to-live.",
                self::TTL_MAX_VALUE,
            )
            ->addArgument(
                self::DESCRIPTION_KEY,
                InputArgument::REQUIRED,
                "The token's description.",
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $ttl = new DateInterval($input->getOption(self::TTL_KEY));
        } catch (Exception $e) {
            $output->writeln('Unable to parse given TTL value.');
            return Command::INVALID;
        }
        if ($this->ttlExceedsMaximumTtl($ttl)) {
            $output->writeln('The given time-to-live exceeds the maximum allowed value (' . self::TTL_MAX_VALUE . ').');
            return Command::INVALID;
        }

        $issuedAt = new DateTime();
        $expiresAt = (clone $issuedAt)->add($ttl);

        /** @var ServiceTokenInterface $token */
        $token = $this->tokenRepository->create();
        $token->setDescription($input->getArgument(self::DESCRIPTION_KEY));
        $token->setCreatedAt($issuedAt);
        $token->setExpiresAt($expiresAt);
        $this->tokenRepository->update($token);

        $schoolIds = $this->getSchoolIdsFromInput($input->getOption(self::WRITEABLE_SCHOOLS_KEY));

        $serviceTokenUser = $this->userProvider->loadUserByIdentifier((string) $token->getId());

        $jwt = $this->jwtManager->createJwtFromServiceTokenUser(
            $serviceTokenUser,
            $schoolIds,
        );
        $output->writeln('Success!');
        $output->writeln('Token ' . $jwt);

        return Command::SUCCESS;
    }

    protected function ttlExceedsMaximumTtl(DateInterval $ttl): bool
    {
        $maxTtl = new DateInterval(self::TTL_MAX_VALUE);
        $now = new DateTimeImmutable();
        $ttlDate = $now->add($ttl);
        $maxTtlDate = $now->add($maxTtl);
        return $ttlDate > $maxTtlDate;
    }

    protected function getSchoolIdsFromInput(string $input): array
    {
        return array_values(
            array_unique(
                array_filter(
                    array_map(fn(string $id): int => (int) trim($id), explode(',', $input))
                )
            )
        );
    }
}
