<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\SessionsEntityInterface;

interface SessionTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    SessionsEntityInterface,
    SchoolEntityInterface,
    ActivatableEntityInterface,
    LoggableEntityInterface
{
    public function setCalendarColor(string $color);
    public function getCalendarColor(): string;

    public function setAssessment(bool $assessment);
    public function isAssessment(): bool;

    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption = null);
    public function getAssessmentOption(): ?AssessmentOptionInterface;

    public function setAamcMethods(Collection $aamcMethods);
    public function addAamcMethod(AamcMethodInterface $aamcMethod);
    public function removeAamcMethod(AamcMethodInterface $aamcMethod);
    public function getAamcMethods(): Collection;
}
