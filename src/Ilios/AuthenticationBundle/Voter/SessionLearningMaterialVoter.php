<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionLearningMaterialVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\SessionLearningMaterialInterface');
    }

    /**
     * @param string $attribute
     * @param SessionLearningMaterialInterface $material
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $material, $user = null)
    {
        // grant perms based on the owning session
        return parent::isGranted($attribute, $material->getSession(), $user);
    }
}