<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceBlockDTOVoter
 */
class CurriculumInventorySequenceBlockDTOVoter extends CurriculumInventoryReportDTOVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceBlockDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockDTO $sequenceBlockDTO
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $sequenceBlockDTO, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return $this->isViewGranted($sequenceBlockDTO->school, $user);
    }
}
