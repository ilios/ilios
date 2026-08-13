<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\AbstractTokenData;
use App\Classes\TokenData;
use DateTimeImmutable;

/**
 * A factory that creates token data objects.
 */
readonly class TokenDataFactory
{
    public function __construct(protected TokenCodec $codec)
    {
    }

    public function createFromJwt(string $jwt): TokenData
    {
        $data = $this->codec->decode($jwt);

        return $this->createFromArray($data);
    }

    public function createFromArray(array $data): TokenData
    {
        $userId = (int)$data['user_id'];
        $serviceTokenId = (int)$data['token_id'];
        $isUserToken = array_key_exists('user_id', $data);
        $isServiceToken = array_key_exists('token_id', $data);
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
                : AbstractTokenData::DEFAULT_REFRESH_LIMIT;
        $permissions =
            array_key_exists('permissions', $data)
                ? (string)$data['permissions']
                : AbstractTokenData::DEFAULT_PERMISSIONS;
        $writeableSchoolIds = $isServiceToken ? $this->getWriteableSchoolIds($data) : [];
        $canCreateUserTokensFromToken =
            $isServiceToken
            && array_key_exists('can_generate_user_tokens', $data)
            && $data['can_generate_user_tokens'];
        $audience = $isServiceToken ? $this->getAudience($data) : [];

        return new TokenData(
            $issuedAt,
            $expiresAt,
            $isRoot,
            $performsNonLearnerFunction,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
            $refreshLimit,
            $permissions,
            $audience,
            $userId,
            $serviceTokenId,
            $isUserToken,
            $isServiceToken,
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
