<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    public function __construct(
        protected SessionUserPermissionChecker $permissionChecker,
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
}
