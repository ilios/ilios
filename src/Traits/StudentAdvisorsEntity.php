<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait StudentAdvisorsEntity
{
    protected Collection $studentAdvisors;

    public function setStudentAdvisors(Collection $studentAdvisors): void
    {
        $this->studentAdvisors = new ArrayCollection();

        foreach ($studentAdvisors as $studentAdvisor) {
            $this->addStudentAdvisor($studentAdvisor);
        }
    }

    public function getStudentAdvisors(): Collection
    {
        return $this->studentAdvisors;
    }
}
