<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\TokenData;
use App\Classes\ServiceTokenData;
use App\Classes\UserTokenData;
use DateTimeImmutable;
use Exception;

/**
 * A factory that creates token data objects.
 */
readonly class TokenDataFactory
{
    public function __construct(protected TokenCodec $codec)
    {
    }

    /**
     * @throws Exception
     */
    public function createFromJwt(string $jwt): UserTokenData|ServiceTokenData
    {
        $data = $this->codec->decode($jwt);

        return $this->createFromArray($data);
    }

    /**
     * @throws Exception
     */
    public function createFromArray(array $data): UserTokenData|ServiceTokenData
    {
        // figure out what kind of token data it is.
        $isUserToken = array_key_exists('user_id', $data);
        $isServiceToken = array_key_exists('token_id', $data);
        if (!$isUserToken && !$isServiceToken) {
            throw new Exception('Unable to determine token data type.');
        }

        // process common token data attributes.
        $issuedAt = DateTimeImmutable::createFromFormat('U', (string)$data['iat']);
        assert($issuedAt instanceof DateTimeImmutable);
        $expiresAt = DateTimeImmutable::createFromFormat('U', (string)$data['exp']);
        assert($expiresAt instanceof DateTimeImmutable);
        $isRoot = array_key_exists('is_root', $data) && $data['is_root'];
        $performsNonLearnerFunction =
            array_key_exists('performs_non_learner_function', $data) && $data['performs_non_learner_function'];
        $canCreateOrUpdateUserInAnySchool =
            array_key_exists('can_create_or_update_user_in_any_school', $data)
            && $data['can_create_or_update_user_in_any_school'];
        $issuedWith = array_key_exists('issued_with', $data) ? (int)$data['issued_with'] : null;
        $firstCreatedAt =
            array_key_exists('firstCreatedAt', $data)
                ? DateTimeImmutable::createFromFormat('U', (string)$data['firstCreatedAt'])
                : $issuedAt;
        assert($firstCreatedAt instanceof DateTimeImmutable);
        $refreshCount = array_key_exists('refreshCount', $data) ? (int)$data['refreshCount'] : 0;
        $refreshLimit =
            array_key_exists('refreshLimit', $data)
                ? (int)$data['refreshLimit']
                : TokenData::DEFAULT_REFRESH_LIMIT;
        $audience = $this->getAudience($data);

        // process user token data.
        if ($isUserToken) {
            $userId = (int) $data['user_id'];
            return new UserTokenData(
                $issuedAt,
                $expiresAt,
                $audience,
                $userId,
                $isRoot,
                $performsNonLearnerFunction,
                $issuedWith,
                $firstCreatedAt,
                $refreshCount,
                $refreshLimit,
            );
        }

        // otherwise, process service token data.
        $serviceTokenId = (int) $data['token_id'];
        $writeableSchoolIds = $this->getWriteableSchoolIds($data);
        $canCreateUserTokensFromToken =
            array_key_exists('can_generate_user_tokens', $data) && $data['can_generate_user_tokens'];
        return new ServiceTokenData(
            $issuedAt,
            $expiresAt,
            $audience,
            $serviceTokenId,
            $writeableSchoolIds,
            $canCreateOrUpdateUserInAnySchool,
            $canCreateUserTokensFromToken,
        );
    }

    protected function getWriteableSchoolIds(array $data): array
    {
        if (!array_key_exists('writeable_schools', $data)) {
            return [];
        }
        $rhett = $data['writeable_schools'];
        assert(is_array($rhett));

        return $rhett;
    }

    protected function getAudience(array $data): array
    {
        if (!array_key_exists('aud', $data)) {
            return [];
        }
        $aud = $data['aud'];

        if (is_string($aud)) {
            $aud = [$aud];
        }

        assert(is_array($aud));

        return $aud;
    }
}
