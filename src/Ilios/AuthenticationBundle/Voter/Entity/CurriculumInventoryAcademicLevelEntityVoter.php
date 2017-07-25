<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryAcademicLevelEntityVoter
 */
class CurriculumInventoryAcademicLevelEntityVoter extends CurriculumInventoryReportEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryAcademicLevelInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelInterface $level
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $level, TokenInterface $token)
    {
        return parent::voteOnAttribute($attribute, $level->getReport(), $token);
    }
}
