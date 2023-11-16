<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\SchoolInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class School extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            SchoolInterface::class,
            [
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof ServiceTokenUserInterface) {
            return false;
        }

        return match ($attribute) {
            VoterPermissions::VIEW => true,
            default => $this->hasWriteAccessToSchool(
                $token,
                $subject->getId()
            ),
        };
    }
}
