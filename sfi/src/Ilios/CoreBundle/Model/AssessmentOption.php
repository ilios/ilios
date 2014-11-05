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
 * @ORM\Table(name="assessment_option")
 */
class AssessmentOption implements AssessmentOptionInterface
{
//    use IdentifiableEntity; //Implement in 3.1
    use NameableEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="assessment_option_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $assessmentOptionId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->assessmentOptionId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->assessmentOptionId : $this->id;
    }
}
