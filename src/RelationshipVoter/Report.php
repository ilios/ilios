<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\ReportDTO;
use App\Entity\ReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Report
 */
class Report extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof ReportInterface && in_array(
            $attribute,
            [
                        self::VIEW,
                        self::CREATE,
                        self::EDIT,
                        self::DELETE,
            ]
        );
    }

    /**
     * @param string $attribute
     * @param ReportInterface $report
     * @param TokenInterface $token
     */
    protected function voteOnAttribute($attribute, $report, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            self::CREATE, self::VIEW, self::EDIT, self::DELETE => $user->isTheUser($report->getUser()),
            default => false,
        };
    }
}
