<?php

declare(strict_types=1);

namespace App\ServiceTokenVoter;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\CohortInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Cohort extends AbstractReadWriteEntityVoter
{
    public function __construct()
    {
        parent::__construct(
            CohortInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
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
                $subject->getProgramYear()->getProgram()->getSchool()->getId()
            ),
        };
    }
}
