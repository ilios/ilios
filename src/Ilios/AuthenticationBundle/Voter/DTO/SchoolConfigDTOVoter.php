<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\SchoolConfigDTO;
use Ilios\AuthenticationBundle\Voter\Entity\SchoolConfigEntityVoter;

/**
 * Class SchoolConfigDTOVoter
 */
class SchoolConfigDTOVoter extends SchoolConfigEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolConfigDTO && in_array($attribute, array(self::VIEW));
    }
}
