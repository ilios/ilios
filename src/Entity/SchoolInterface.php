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
use App\Traits\CoursesEntityInterface;
use App\Traits\ProgramsEntityInterface;

interface SchoolInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
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
    public function setTemplatePrefix(?string $templatePrefix): void;
    public function getTemplatePrefix(): ?string;

    public function setIliosAdministratorEmail(string $iliosAdministratorEmail): void;
    public function getIliosAdministratorEmail(): string;

    public function setChangeAlertRecipients(?string $changeAlertRecipients): void;
    public function getChangeAlertRecipients(): ?string;

    public function setCurriculumInventoryInstitution(
        ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ): void;
    public function getCurriculumInventoryInstitution(): ?CurriculumInventoryInstitutionInterface;

    public function setVocabularies(Collection $vocabularies): void;
    public function addVocabulary(VocabularyInterface $vocabulary): void;
    public function removeVocabulary(VocabularyInterface $vocabulary): void;
    public function getVocabularies(): Collection;

    public function addConfiguration(SchoolConfigInterface $config): void;
    public function removeConfiguration(SchoolConfigInterface $config): void;
    public function setConfigurations(Collection $configs): void;
    public function getConfigurations(): Collection;
}
