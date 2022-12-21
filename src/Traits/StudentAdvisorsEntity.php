<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

trait StudentAdvisorsEntity
{
    protected Collection $studentAdvisors;

    public function setStudentAdvisors(Collection $studentAdvisors)
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
