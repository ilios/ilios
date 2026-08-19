<?php

declare(strict_types=1);

namespace App\Command;

use App\Classes\ServiceToken;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use App\Service\ServiceTokenUserProvider;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a new service account token.
 */
#[AsCommand(
    name: 'ilios:service-token:create',
    description: 'Creates a new service token for the API with given capabilities.'
)]
class CreateServiceTokenCommand extends Command
{
    public function __construct(
        protected ServiceTokenRepository $tokenRepository,
        protected ServiceTokenUserProvider $userProvider,
        protected TokenFactory $tokenFactory,
        protected TokenCodec $tokenCodec,
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: "The token's time-to-live in ISO-8601 duration format, up to 180 days.")] string $ttl,
        #[Argument(description: "The token's description.")] string $description,
        #[Option(description: "Can this service token be used to generate user tokens?")]
        bool $allowUserTokenGeneration = false,
        #[Option(description: 'Grant this token the LTI-dashboard audience claim?')]
        bool $grantLtiDashboardAudienceClaim = false,
        #[Option(
            description: 'Schools that the token has write access to, provided as a comma-separated list of ids.',
            name: 'writeable-schools'
        )] string $writeableSchools = '',
    ): int {
        try {
            $ttl = new DateInterval($ttl);
        } catch (Exception) {
            $output->writeln('Unable to parse given TTL value.');
            return Command::INVALID;
        }
        if ($this->ttlExceedsMaximumTtl($ttl)) {
            $output->writeln(
                sprintf(
                    'The given time-to-live exceeds the maximum allowed value (%s).',
                    TokenManager::SERVICE_TOKEN_MAX_TTL
                )
            );
            return Command::INVALID;
        }

        $issuedAt = new DateTime();
        $expiresAt = (clone $issuedAt)->add($ttl);

        /** @var ServiceTokenInterface $token */
        $token = $this->tokenRepository->create();
        $token->setDescription($description);
        $token->setCreatedAt($issuedAt);
        $token->setExpiresAt($expiresAt);
        $this->tokenRepository->update($token);

        $schoolIds = $this->getSchoolIdsFromInput($writeableSchools);

        $data = [
            'iat' => $token->getCreatedAt()->format('U'),
            'exp' => $token->getExpiresAt()->format('U'),
            'aud' => $grantLtiDashboardAudienceClaim
                ? TokenManager::TOKEN_LTI_DASHBOARD_AUDIENCE
                : TokenManager::TOKEN_AUDIENCE,
            'iss' => TokenManager::TOKEN_ISSUER,
            'token_id' => $token->getId(),
            'writeable_schools' => $schoolIds,
            'can_generate_user_tokens' => $allowUserTokenGeneration,
        ];

        $serviceToken = $this->tokenFactory->create($data);
        assert($serviceToken instanceof ServiceToken);

        $jwt = $this->tokenCodec->encode($serviceToken);
        $output->writeln('Success!');
        $output->writeln('Token ' . $jwt);

        return Command::SUCCESS;
    }

    protected function ttlExceedsMaximumTtl(DateInterval $ttl): bool
    {
        $maxTtl = new DateInterval(TokenManager::SERVICE_TOKEN_MAX_TTL);
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
