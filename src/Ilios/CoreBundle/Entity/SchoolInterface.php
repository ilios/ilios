<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\AdministratorsEntityInterface;
use Ilios\CoreBundle\Traits\AlertableEntityInterface;
use Ilios\CoreBundle\Traits\CompetenciesEntityInterface;
use Ilios\CoreBundle\Traits\DirectorsEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\InstructorGroupsEntityInterface;
use Ilios\CoreBundle\Traits\SessionTypesEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\ProgramsEntityInterface;
use Ilios\CoreBundle\Traits\StewardedEntityInterface;

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
