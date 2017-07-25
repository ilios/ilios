<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceDTOVoter
 */
class CurriculumInventorySequenceDTOVoter extends CurriculumInventoryReportDTOVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceDTO $sequenceDTO
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $sequenceDTO, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return $this->isViewGranted($sequenceDTO->school, $user);
    }
}
