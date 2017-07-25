<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\ActivatableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;

/**
 * Interface SessionTypeInterface
 */
interface SessionTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    SessionsEntityInterface,
    SchoolEntityInterface,
    ActivatableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $color
     */
    public function setCalendarColor($color);

    /**
     * @return string
     */
    public function getCalendarColor();

    /**
     * Set assessment
     *
     * @param boolean $assessment
     */
    public function setAssessment($assessment);

    /**
     * Get assessment
     *
     * @return boolean
     */
    public function isAssessment();

    /**
     * @param AssessmentOptionInterface $assessmentOption
     */
    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption = null);

    /**
     * @return AssessmentOptionInterface
     */
    public function getAssessmentOption();

    /**
     * @param Collection $aamcMethods
     */
    public function setAamcMethods(Collection $aamcMethods);

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function addAamcMethod(AamcMethodInterface $aamcMethod);

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function removeAamcMethod(AamcMethodInterface $aamcMethod);

    /**
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function getAamcMethods();
}
