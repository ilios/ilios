<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\DTO\ReportDTO;
use AppBundle\Entity\ReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Report
 */
class Report extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ReportInterface && in_array(
            $attribute,
            array(
                        self::VIEW,
                        self::CREATE,
                        self::EDIT,
                        self::DELETE,
                    )
        );
    }

    /**
     * @param string $attribute
     * @param ReportInterface $report
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
