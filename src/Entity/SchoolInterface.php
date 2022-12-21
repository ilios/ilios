<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
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
    public function setTemplatePrefix(?string $templatePrefix);
    public function getTemplatePrefix(): ?string;

    public function setIliosAdministratorEmail(string $iliosAdministratorEmail);
    public function getIliosAdministratorEmail(): string;

    public function setChangeAlertRecipients(?string $changeAlertRecipients);
    public function getChangeAlertRecipients(): ?string;

    public function setCurriculumInventoryInstitution(
        ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    );
    public function getCurriculumInventoryInstitution(): ?CurriculumInventoryInstitutionInterface;

    public function setVocabularies(Collection $vocabularies);
    public function addVocabulary(VocabularyInterface $vocabulary);
    public function removeVocabulary(VocabularyInterface $vocabulary);
    public function getVocabularies(): Collection;

    public function addConfiguration(SchoolConfigInterface $config);
    public function removeConfiguration(SchoolConfigInterface $config);
    public function setConfigurations(Collection $configs);
    public function getConfigurations(): Collection;
}
