<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;

/**
 * Class AssessmentOption
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="assesment_option")
 */
class AssessmentOption implements AssessmentOptionInterface
{
    use IdentifiableEntity;
    use NameableEntity;
}
