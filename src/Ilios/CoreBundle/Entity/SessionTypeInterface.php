<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface SessionTypeInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SessionTypeInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param string $sessionTypeCssClass
     */
    public function setSessionTypeCssClass($sessionTypeCssClass);

    /**
     * @return string
     */
    public function getSessionTypeCssClass();

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
    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption);

    /**
     * @return AssessmentOptionInterface
     */
    public function getAssessmentOption();

    /**
     * @param SchoolInterface $owningSchool
     */
    public function setOwningSchool(SchoolInterface $owningSchool);

    /**
     * @return SchoolInterface
     */
    public function getOwningSchool();

    /**
     * @param Collection $aamcMethods
     */
    public function setAamcMethods(Collection $aamcMethods);

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function addAamcMethod(AamcMethodInterface $aamcMethod);

    /**
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function getAamcMethods();

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions);

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
