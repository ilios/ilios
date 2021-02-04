<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\AdministratorsEntityInterface;
use App\Traits\AlertableEntityInterface;
use App\Traits\CompetenciesEntityInterface;
use App\Traits\DirectorsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\SessionTypesEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\ProgramsEntityInterface;

/**
 * Interface SchoolInterface
 */
interface SchoolInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    IndexableCoursesEntityInterface,
    ProgramsEntityInterface,
    LoggableEntityInterface,
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

    /**
     * @return int
     */
    public function getAcademicYearStartDay(): int;

    /**
     * @param int $academicYearStartDay
     */
    public function setAcademicYearStartDay(int $academicYearStartDay): void;

    /**
     * @return int
     */
    public function getAcademicYearStartMonth(): int;

    /**
     * @param int $academicYearStartMonth
     */
    public function setAcademicYearStartMonth(int $academicYearStartMonth): void;

    /**
     * @return int
     */
    public function getAcademicYearEndDay(): int;

    /**
     * @param int $academicYearEndDay
     */
    public function setAcademicYearEndDay(int $academicYearEndDay): void;

    /**
     * @return int
     */
    public function getAcademicYearEndMonth(): int;

    /**
     * @param int $academicYearEndMonth
     */
    public function setAcademicYearEndMonth(int $academicYearEndMonth): void;
}
