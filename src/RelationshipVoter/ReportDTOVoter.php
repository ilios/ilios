<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\ReportDTO;
use App\Entity\ReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Report
 */
class ReportDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ReportDTO && self::VIEW === $attribute;
    }

    /**
     * @param string $attribute
     * @param ReportDTO $report
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $report, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        return $user->getId() === $report->user;
    }
}
