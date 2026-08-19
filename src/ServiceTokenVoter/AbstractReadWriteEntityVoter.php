<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\ServiceToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractReadWriteEntityVoter extends Voter
{
    public function __construct(
        protected string $supportedType,
        protected array $supportedAttributes
    ) {
    }

    public function supportsType(string $subjectType): bool
    {
        return is_a($subjectType, $this->supportedType, true);
    }

    public function supportsAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->supportedAttributes);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof $this->supportedType && $this->supportsAttribute($attribute);
    }

    protected function getWriteableSchoolIdsFromToken(TokenInterface $securityToken): array
    {
        $token = $securityToken->getAttribute('token');
        return ($token instanceof ServiceToken) ? $token->writeableSchoolIds : [];
    }

    protected function hasWriteAccessToSchool(TokenInterface $token, int $schoolId): bool
    {
        return in_array($schoolId, $this->getWriteableSchoolIdsFromToken($token));
    }
}
