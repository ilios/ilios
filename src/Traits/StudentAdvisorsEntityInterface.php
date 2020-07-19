<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

interface StudentAdvisorsEntityInterface
{
    /**
     * @param Collection $studentAdvisors
     */
    public function setStudentAdvisors(Collection $studentAdvisors);

    /**
     * @param UserInterface $studentAdvisor
     */
    public function addStudentAdvisor(UserInterface $studentAdvisor);

    /**
     * @param UserInterface $studentAdvisor
     */
    public function removeStudentAdvisor(UserInterface $studentAdvisor);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getStudentAdvisors();
}
