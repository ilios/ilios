<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\ApplicationConfigDTO;
use Ilios\AuthenticationBundle\Voter\Entity\ApplicationConfigEntityVoter;

/**
 * Class ApplicationConfigDTOVoter
 */
class ApplicationConfigDTOVoter extends ApplicationConfigEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ApplicationConfigDTO && in_array($attribute, array(self::VIEW));
    }
}
