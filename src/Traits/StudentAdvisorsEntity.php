<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

trait StudentAdvisorsEntity
{
    public function setStudentAdvisors(Collection $studentAdvisors)
    {
        $this->studentAdvisors = new ArrayCollection();

        foreach ($studentAdvisors as $studentAdvisor) {
            $this->addStudentAdvisor($studentAdvisor);
        }
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getStudentAdvisors()
    {
        return $this->studentAdvisors;
    }
}
