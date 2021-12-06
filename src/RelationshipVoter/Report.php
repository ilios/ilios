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
     * @return bool
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

        switch ($attribute) {
            case self::CREATE:
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $user->isTheUser($report->getUser());
                break;
        }

        return false;
    }
}
