<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\SessionsEntityInterface;

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
    public function getCalendarColor(): string;

    /**
     * Set assessment
     *
     * @param bool $assessment
     */
    public function setAssessment($assessment);

    /**
     * Get assessment
     *
     * @return bool
     */
    public function isAssessment(): bool;

    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption = null);

    /**
     * @return AssessmentOptionInterface
     */
    public function getAssessmentOption(): AssessmentOptionInterface;

    public function setAamcMethods(Collection $aamcMethods);

    public function addAamcMethod(AamcMethodInterface $aamcMethod);

    public function removeAamcMethod(AamcMethodInterface $aamcMethod);

    /**
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function getAamcMethods(): Collection;
}
