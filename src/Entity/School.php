<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SchoolRepository;
use Override;

#[ORM\Table(name: 'school')]
#[ORM\UniqueConstraint(name: 'template_prefix', columns: ['template_prefix'])]
#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[IA\Entity]
class School implements SchoolInterface
{
    #[ORM\Column(name: 'school_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

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
    #[ORM\ManyToMany(targetEntity: Alert::class, mappedBy: 'recipients')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $alerts;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: Competency::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: Course::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courses;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: Program::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programs;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: Vocabulary::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $vocabularies;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: InstructorGroup::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\OneToOne(mappedBy: 'school', targetEntity: CurriculumInventoryInstitution::class)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: SessionType::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionTypes;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'directedSchools')]
    #[ORM\JoinTable(name: 'school_director')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directors;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'administeredSchools')]
    #[ORM\JoinTable(name: 'school_administrator')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: SchoolConfig::class)]
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

    #[Override]
    public function setTemplatePrefix(?string $templatePrefix): void
    {
        $this->templatePrefix = $templatePrefix;
    }

    #[Override]
    public function getTemplatePrefix(): ?string
    {
        return $this->templatePrefix;
    }

    #[Override]
    public function setIliosAdministratorEmail(string $iliosAdministratorEmail): void
    {
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;
    }

    #[Override]
    public function getIliosAdministratorEmail(): string
    {
        return $this->iliosAdministratorEmail;
    }

    #[Override]
    public function setChangeAlertRecipients(?string $changeAlertRecipients): void
    {
        $this->changeAlertRecipients = $changeAlertRecipients;
    }

    #[Override]
    public function getChangeAlertRecipients(): ?string
    {
        return $this->changeAlertRecipients;
    }

    #[Override]
    public function setCurriculumInventoryInstitution(
        ?CurriculumInventoryInstitutionInterface $curriculumInventoryInstitution
    ): void {
        $this->curriculumInventoryInstitution = $curriculumInventoryInstitution;
    }

    #[Override]
    public function getCurriculumInventoryInstitution(): ?CurriculumInventoryInstitutionInterface
    {
        return $this->curriculumInventoryInstitution;
    }

    #[Override]
    public function addAlert(AlertInterface $alert): void
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addRecipient($this);
        }
    }

    #[Override]
    public function removeAlert(AlertInterface $alert): void
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeRecipient($this);
        }
    }

    #[Override]
    public function setAlerts(?Collection $alerts = null): void
    {
        $this->alerts = new ArrayCollection();
        if (is_null($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    #[Override]
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    #[Override]
    public function setVocabularies(Collection $vocabularies): void
    {
        $this->vocabularies = new ArrayCollection();

        foreach ($vocabularies as $vocabulary) {
            $this->addVocabulary($vocabulary);
        }
    }

    #[Override]
    public function addVocabulary(VocabularyInterface $vocabulary): void
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
        }
    }

    #[Override]
    public function removeVocabulary(VocabularyInterface $vocabulary): void
    {
        $this->vocabularies->removeElement($vocabulary);
    }

    #[Override]
    public function getVocabularies(): Collection
    {
        return $this->vocabularies;
    }

    #[Override]
    public function addDirector(UserInterface $director): void
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedSchool($this);
        }
    }

    #[Override]
    public function removeDirector(UserInterface $director): void
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedSchool($this);
        }
    }

    #[Override]
    public function setDirectors(Collection $directors): void
    {
        $this->directors = new ArrayCollection();

        foreach ($directors as $director) {
            $this->addDirector($director);
        }
    }

    #[Override]
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    #[Override]
    public function getAdministrators(): Collection
    {
        return $this->administrators;
    }


    #[Override]
    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSchool($this);
        }
    }

    #[Override]
    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSchool($this);
        }
    }

    #[Override]
    public function setAdministrators(Collection $administrators): void
    {
        $this->administrators = new ArrayCollection();

        foreach ($administrators as $administrator) {
            $this->addAdministrator($administrator);
        }
    }

    #[Override]
    public function addConfiguration(SchoolConfigInterface $config): void
    {
        if (!$this->configurations->contains($config)) {
            $this->configurations->add($config);
        }
    }

    #[Override]
    public function removeConfiguration(SchoolConfigInterface $config): void
    {
        $this->configurations->removeElement($config);
    }

    #[Override]
    public function setConfigurations(Collection $configs): void
    {
        $this->configurations = new ArrayCollection();

        foreach ($configs as $config) {
            $this->addConfiguration($config);
        }
    }

    #[Override]
    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    #[Override]
    public function getIndexableCourses(): array
    {
        return $this->courses->toArray();
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setCompetencies(Collection $competencies): void
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    #[Override]
    public function addCompetency(CompetencyInterface $competency): void
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
        }
    }

    #[Override]
    public function removeCompetency(CompetencyInterface $competency): void
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
        }
    }

    #[Override]
    public function getCompetencies(): Collection
    {
        return $this->competencies;
    }

    #[Override]
    public function setCourses(Collection $courses): void
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    #[Override]
    public function addCourse(CourseInterface $course): void
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
        }
    }

    #[Override]
    public function removeCourse(CourseInterface $course): void
    {
        $this->courses->removeElement($course);
    }

    #[Override]
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    #[Override]
    public function setInstructorGroups(Collection $instructorGroups): void
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    #[Override]
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
        }
    }

    #[Override]
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        $this->instructorGroups->removeElement($instructorGroup);
    }

    #[Override]
    public function getInstructorGroups(): Collection
    {
        return $this->instructorGroups;
    }

    #[Override]
    public function setPrograms(Collection $programs): void
    {
        $this->programs = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addProgram($program);
        }
    }

    #[Override]
    public function addProgram(ProgramInterface $program): void
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
        }
    }

    #[Override]
    public function removeProgram(ProgramInterface $program): void
    {
        $this->programs->removeElement($program);
    }

    #[Override]
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    #[Override]
    public function setSessionTypes(Collection $sessionTypes): void
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    #[Override]
    public function addSessionType(SessionTypeInterface $sessionType): void
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
        }
    }

    #[Override]
    public function removeSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionTypes->removeElement($sessionType);
    }

    #[Override]
    public function getSessionTypes(): Collection
    {
        return $this->sessionTypes;
    }

    #[Override]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    #[Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
