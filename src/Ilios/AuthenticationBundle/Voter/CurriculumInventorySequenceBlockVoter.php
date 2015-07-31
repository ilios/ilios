<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceBlockVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockInterface $block
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $block, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->userHasRole($user, ['Course Director', 'Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // Sequence blocks cannot be edited or deleted
                // once the report they belong to has been exported.
                if ($block->getReport()->getExport()) {
                    return false;
                }
                return $this->userHasRole($user, ['Course Director', 'Developer']);
                break;
        }

        return false;
    }
}
