<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTrait;

/**
 * AssessmentOption
 */
class AssessmentOption implements AssessmentOptionInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
