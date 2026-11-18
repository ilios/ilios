<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\AdministratorsEntityInterface;
use App\Entity\AlertableEntityInterface;
use App\Entity\CompetenciesEntityInterface;
use App\Entity\CoursesEntityInterface;
use App\Entity\DirectorsEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\IndexableCoursesEntityInterface;
use App\Entity\InstructorGroupsEntityInterface;
use App\Entity\ProgramsEntityInterface;
use App\Entity\SessionTypesEntityInterface;
use App\Entity\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

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
