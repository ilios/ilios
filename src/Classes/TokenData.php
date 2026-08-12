<?php

declare(strict_types=1);

namespace App\Classes;

use App\Service\TokenCodec;
use DateTimeImmutable;

/**
 * Immutable representation of a decoded JWT.
 */
readonly class TokenData
{
    public const int DEFAULT_REFRESH_LIMIT = 12;
    public const string DEFAULT_PERMISSIONS = 'user';

    public int $userId;

    public int $serviceTokenId;
    public bool $isUserToken;
    public bool $isServiceToken;

    public DateTimeImmutable $issuedAt;
    public DateTimeImmutable $expiresAt;

    public bool $isRoot;
    public bool $performsNonLearnerFunction;
    public bool $canCreateOrUpdateUserInAnySchool;

    public ?int $issuedWith;
    public DateTimeImmutable $firstCreatedAt;
    public int $refreshCount;
    public int $refreshLimit;
    public string $permissions;
    public array $writeableSchoolIds;
    public bool $canCreateUserTokensFromToken;
    public array $audience;

    public function __construct(protected TokenCodec $codec, protected string $jwt)
    {
        $arr = $this->codec->decode($this->jwt);
        $this->userId = (int)$arr['user_id'];
        $this->serviceTokenId = (int)$arr['token_id'];
        $this->isUserToken = array_key_exists('user_id', $arr);
        $this->isServiceToken = array_key_exists('token_id', $arr);
        $issuedAt = DateTimeImmutable::createFromFormat('U', (string)$arr['iat']);
        assert($issuedAt instanceof DateTimeImmutable);
        $this->issuedAt = $issuedAt;
        $expiresAt = DateTimeImmutable::createFromFormat('U', (string)$arr['exp']);
        assert($expiresAt instanceof DateTimeImmutable);
        $this->expiresAt = $expiresAt;
        $this->isRoot = array_key_exists('is_root', $arr) && $arr['is_root'];
        $this->performsNonLearnerFunction =
            array_key_exists('performs_non_learner_function', $arr) && $arr['performs_non_learner_function'];
        $this->canCreateOrUpdateUserInAnySchool =
            array_key_exists('can_create_or_update_user_in_any_school', $arr)
            && $arr['can_create_or_update_user_in_any_school'];
        $this->issuedWith = array_key_exists('issued_with', $arr) ? (int) $arr['issued_with'] : null;
        $firstCreatedAt =
            array_key_exists('firstCreatedAt', $arr)
                ? DateTimeImmutable::createFromFormat('U', (string)$arr['firstCreatedAt'])
                : $this->issuedAt;
        assert($firstCreatedAt instanceof DateTimeImmutable);
        $this->firstCreatedAt = $firstCreatedAt;
        $this->refreshCount = array_key_exists('refreshCount', $arr) ? (int)$arr['refreshCount'] : 0;
        $this->refreshLimit =
            array_key_exists('refreshLimit', $arr)
                ? (int)$arr['refreshLimit']
                : self::DEFAULT_REFRESH_LIMIT;
        $this->permissions =
            array_key_exists('permissions', $arr)
                ? (string) $arr['permissions']
                : self::DEFAULT_PERMISSIONS;
        $this->writeableSchoolIds = $this->isServiceToken ? $this->getWriteableSchoolIds($arr) : [];
        $this->canCreateUserTokensFromToken =
            $this->isServiceToken
            && array_key_exists('can_generate_user_tokens', $arr)
            && $arr['can_generate_user_tokens'];
        $this->audience = $this->isServiceToken ? $this->getAudience($arr) : [];
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

    public function toArray(): array
    {
        $rhett = [
            'user_id' => $this->userId,
            'token_id' => $this->serviceTokenId,
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
            'is_root' => $this->isRoot,
            'performs_non_learner_function' => $this->performsNonLearnerFunction,
            'can_create_or_update_user_in_any_school' => $this->canCreateOrUpdateUserInAnySchool,
            'firstCreatedAt' => $this->firstCreatedAt->format('U'),
            'refreshCount' => $this->refreshCount,
            'refreshLimit' => $this->refreshLimit,
            'permissions' => $this->permissions,
            'writeable_schools' => $this->writeableSchoolIds,
            'can_generate_user_tokens' => $this->canCreateUserTokensFromToken,
            'aud' => $this->audience,
        ];
        if (!is_null($this->issuedWith)) {
            $rhett['issuedWith'] = $this->issuedWith;
        }
        return $rhett;
    }
}
