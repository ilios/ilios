<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\DepartmentDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class DepartmentDTOVoter
 */
class DepartmentDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof DepartmentDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param DepartmentDTO $departmentDTO
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $departmentDTO, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
        }
        return false;
    }
}
