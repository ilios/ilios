<?php

namespace Ilios\CoreBundle\Model;



/**
 * AssessmentOption
 */
class AssessmentOption
{
    /**
     * @var integer
     */
    private $assessmentOptionId;

    /**
     * @var string
     */
    private $name;


    /**
     * Get assessmentOptionId
     *
     * @return integer 
     */
    public function getAssessmentOptionId()
    {
        return $this->assessmentOptionId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AssessmentOption
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
