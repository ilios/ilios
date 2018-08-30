<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\AdministratorsEntityInterface;
use AppBundle\Traits\AlertableEntityInterface;
use AppBundle\Traits\CompetenciesEntityInterface;
use AppBundle\Traits\DirectorsEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\InstructorGroupsEntityInterface;
use AppBundle\Traits\SessionTypesEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\CoursesEntityInterface;
use AppBundle\Traits\ProgramsEntityInterface;
use AppBundle\Traits\StewardedEntityInterface;

/**
 * Interface SchoolInterface
 */
interface SchoolInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    ProgramsEntityInterface,
    LoggableEntityInterface,
    StewardedEntityInterface,
    AlertableEntityInterface,
    SessionTypesEntityInterface,
    InstructorGroupsEntityInterface,
    CompetenciesEntityInterface,
    DirectorsEntityInterface,
    AdministratorsEntityInterface
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
     * @param string $changeAlertRecipients
     */
    public function setChangeAlertRecipients($changeAlertRecipients);

    /**
     * @return string
     */
    public function getChangeAlertRecipients();
    /**
     * @param Collection $departments
     */
    public function setDepartments(Collection $departments);

    /**
     * @param DepartmentInterface $department
     */
    public function addDepartment(DepartmentInterface $department);

    /**
     * @param DepartmentInterface $department
     */
    public function removeDepartment(DepartmentInterface $department);

    /**
     * @return ArrayCollection|DepartmentInterface[]
     */
    public function getDepartments();

    /**
     * @param Collection $vocabularies
     */
    public function setVocabularies(Collection $vocabularies);

    /**
     * @param VocabularyInterface $vocabulary
     */
    public function addVocabulary(VocabularyInterface $vocabulary);

    /**
     * @param VocabularyInterface $vocabulary
     */
    public function removeVocabulary(VocabularyInterface $vocabulary);

    /**
     * @return ArrayCollection|VocabularyInterface[]
     */
    public function getVocabularies();

    /**
     * @param SchoolConfigInterface $config
     */
    public function addConfiguration(SchoolConfigInterface $config);

    /**
     * @param SchoolConfigInterface $config
     */
    public function removeConfiguration(SchoolConfigInterface $config);

    /**
     * @param Collection $configs
     */
    public function setConfigurations(Collection $configs);

    /**
     * @return ArrayCollection|SchoolConfigInterface[]
     */
    public function getConfigurations();
}
