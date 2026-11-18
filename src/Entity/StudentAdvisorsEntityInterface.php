<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

interface StudentAdvisorsEntityInterface
{
    public function setStudentAdvisors(Collection $studentAdvisors): void;

    public function addStudentAdvisor(UserInterface $studentAdvisor): void;

    public function removeStudentAdvisor(UserInterface $studentAdvisor): void;

    public function getStudentAdvisors(): Collection;
}
