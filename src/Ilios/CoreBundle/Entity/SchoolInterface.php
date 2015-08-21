<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\ProgramsEntityInterface;

/**
 * Interface SchoolInterface
 * @package Ilios\CoreBundle\Entity
 */
interface SchoolInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    ProgramsEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $templatePrefix
     */
    public function setTemplatePrefix($templatePrefix);

    /**
     * @return string
     */
    public function getTemplatePrefix();

    /**
     * @param string $iliosAdministratorEmail
     */
    public function setIliosAdministratorEmail($iliosAdministratorEmail);

    /**
     * @return string
     */
    public function getIliosAdministratorEmail();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @param string $changeAlertRecipients
     */
    public function setChangeAlertRecipients($changeAlertRecipients);

    /**
     * @return string
     */
    public function getChangeAlertRecipients();

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts);

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();

    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies);

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency);

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies();

    /**
     * @param Collection $departments
     */
    public function setDepartments(Collection $departments);

    /**
     * @param DepartmentInterface $department
     */
    public function addDepartment(DepartmentInterface $department);

    /**
     * @return ArrayCollection|DepartmentInterface[]
     */
    public function getDepartments();

    /**
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines);

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline);

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines();
}
