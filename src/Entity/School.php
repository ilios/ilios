<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\AdministratorsEntity;
use App\Traits\AlertableEntity;
use App\Traits\CompetenciesEntity;
use App\Traits\DirectorsEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\SessionTypesEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\CoursesEntity;
use App\Traits\ProgramsEntity;
use App\Repository\SchoolRepository;

#[ORM\Table(name: 'school')]
#[ORM\UniqueConstraint(name: 'template_prefix', columns: ['template_prefix'])]
#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[IA\Entity]
class School implements SchoolInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use CoursesEntity;
    use ProgramsEntity;
    use StringableIdEntity;
    use AlertableEntity;
    use SessionTypesEntity;
    use InstructorGroupsEntity;
    use CompetenciesEntity;
    use DirectorsEntity;
    use AdministratorsEntity;

    #[ORM\Column(name: 'school_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 60, unique: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\Column(name: 'template_prefix', type: 'string', length: 8, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 8)]
    protected ?string $templatePrefix = null;

    #[ORM\Column(name: 'ilios_administrator_email', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected string $iliosAdministratorEmail;

    /**
     * @todo: Normalize later. Collection of email addresses. (Add email entity, etc)
     */
    #[ORM\Column(name: 'change_alert_recipients', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected ?string $changeAlertRecipients = null;

    /**
     * Don't put alerts in the school API it takes forever to load them all
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'recipients')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $alerts;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Competency')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courses;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programs;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Vocabulary')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $vocabularies;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'InstructorGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\OneToOne(mappedBy: 'school', targetEntity: 'CurriculumInventoryInstitution')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'SessionType')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionTypes;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedSchools')]
    #[ORM\JoinTable(name: 'school_director')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directors;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSchools')]
    #[ORM\JoinTable(name: 'school_administrator')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'SchoolConfig')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $configurations;

    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->vocabularies = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->sessionTypes = new ArrayCollection();
        $this->directors = new ArrayCollection();
        $this->administrators = new ArrayCollection();
        $this->configurations = new ArrayCollection();
    }

    public function setTemplatePrefix(?string $templatePrefix): void
    {
        $this->templatePrefix = $templatePrefix;
    }

    public function getTemplatePrefix(): ?string
    {
        return $this->templatePrefix;
    }

    public function setIliosAdministratorEmail(string $iliosAdministratorEmail): void
    {
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;
    }

    public function getIliosAdministratorEmail(): string
    {
        return $this->iliosAdministratorEmail;
    }

    public function setChangeAlertRecipients(?string $changeAlertRecipients): void
    {
        $this->changeAlertRecipients = $changeAlertRecipients;
    }

    public function getChangeAlertRecipients(): ?string
    {
        return $this->changeAlertRecipients;
    }

    public function setCurriculumInventoryInstitution(
        ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ): void {
        $this->curriculumInventoryInstitution = $curriculumInventoryInstitution;
    }

    public function getCurriculumInventoryInstitution(): ?CurriculumInventoryInstitutionInterface
    {
        return $this->curriculumInventoryInstitution;
    }

    public function addAlert(AlertInterface $alert): void
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addRecipient($this);
        }
    }

    public function removeAlert(AlertInterface $alert): void
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeRecipient($this);
        }
    }

    public function setVocabularies(Collection $vocabularies): void
    {
        $this->vocabularies = new ArrayCollection();

        foreach ($vocabularies as $vocabulary) {
            $this->addVocabulary($vocabulary);
        }
    }

    public function addVocabulary(VocabularyInterface $vocabulary): void
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
        }
    }

    public function removeVocabulary(VocabularyInterface $vocabulary): void
    {
        $this->vocabularies->removeElement($vocabulary);
    }

    public function getVocabularies(): Collection
    {
        return $this->vocabularies;
    }

    public function addDirector(UserInterface $director): void
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedSchool($this);
        }
    }

    public function removeDirector(UserInterface $director): void
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedSchool($this);
        }
    }

    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSchool($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSchool($this);
        }
    }

    public function addConfiguration(SchoolConfigInterface $config): void
    {
        if (!$this->configurations->contains($config)) {
            $this->configurations->add($config);
        }
    }

    public function removeConfiguration(SchoolConfigInterface $config): void
    {
        $this->configurations->removeElement($config);
    }

    public function setConfigurations(Collection $configs): void
    {
        $this->configurations = new ArrayCollection();

        foreach ($configs as $config) {
            $this->addConfiguration($config);
        }
    }

    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    public function getIndexableCourses(): array
    {
        return $this->courses->toArray();
    }
}
