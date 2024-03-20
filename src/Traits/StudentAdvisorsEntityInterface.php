<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

interface StudentAdvisorsEntityInterface
{
    public function setStudentAdvisors(Collection $studentAdvisors): void;

    public function addStudentAdvisor(UserInterface $studentAdvisor): void;

    public function removeStudentAdvisor(UserInterface $studentAdvisor): void;

    public function getStudentAdvisors(): Collection;
}
