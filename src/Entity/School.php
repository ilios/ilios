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
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\CoursesEntity;
use App\Traits\ProgramsEntity;
use App\Repository\SchoolRepository;

/**
 * Class School
 */
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

    /**
     * @var int
     */
    #[ORM\Column(name: 'school_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 60, unique: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected $title;

    /**
     * @var string
     */
    #[ORM\Column(name: 'template_prefix', type: 'string', length: 8, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 8)]
    protected $templatePrefix;

    /**
     * @var string
     */
    #[ORM\Column(name: 'ilios_administrator_email', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected $iliosAdministratorEmail;

    /**
     * @todo: Normalize later. Collection of email addresses. (Add email entity, etc)
     * @var string
     */
    #[ORM\Column(name: 'change_alert_recipients', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $changeAlertRecipients;

    /**
     * @var ArrayCollection|AlertInterface[]
     * Don't put alerts in the school API it takes forever to load them all
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'recipients')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type('entityCollection')]
    protected $alerts;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Competency')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $competencies;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'Course', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courses;

    /**
     * @var ArrayCollection|ProgramInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Program')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programs;

    /**
     * @var ArrayCollection|VocabularyInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'Vocabulary')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $vocabularies;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'InstructorGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructorGroups;

    /**
     * @var CurriculumInventoryInstitutionInterface
     */
    #[ORM\OneToOne(mappedBy: 'school', targetEntity: 'CurriculumInventoryInstitution')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $curriculumInventoryInstitution;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'SessionType')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionTypes;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedSchools')]
    #[ORM\JoinTable(name: 'school_director')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directors;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSchools')]
    #[ORM\JoinTable(name: 'school_administrator')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administrators;

    /**
     * @var ArrayCollection|SchoolConfigInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'SchoolConfig', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $configurations;

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

    /**
     * @param string $templatePrefix
     */
    public function setTemplatePrefix($templatePrefix)
    {
        $this->templatePrefix = $templatePrefix;
    }

    public function getTemplatePrefix(): ?string
    {
        return $this->templatePrefix;
    }

    /**
     * @param string $iliosAdministratorEmail
     */
    public function setIliosAdministratorEmail($iliosAdministratorEmail)
    {
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;
    }

    public function getIliosAdministratorEmail(): string
    {
        return $this->iliosAdministratorEmail;
    }

    /**
     * @param string $changeAlertRecipients
     */
    public function setChangeAlertRecipients($changeAlertRecipients)
    {
        $this->changeAlertRecipients = $changeAlertRecipients;
    }

    public function getChangeAlertRecipients(): ?string
    {
        return $this->changeAlertRecipients;
    }

    /**
     * @param string $curriculumInventoryInstitution
     */
    public function setCurriculumInventoryInstitution($curriculumInventoryInstitution)
    {
        $this->curriculumInventoryInstitution = $curriculumInventoryInstitution;
    }

    public function getCurriculumInventoryInstitution(): ?CurriculumInventoryInstitutionInterface
    {
        return $this->curriculumInventoryInstitution;
    }

    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addRecipient($this);
        }
    }

    public function removeAlert(AlertInterface $alert)
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeRecipient($this);
        }
    }

    public function setVocabularies(Collection $vocabularies)
    {
        $this->vocabularies = new ArrayCollection();

        foreach ($vocabularies as $vocabulary) {
            $this->addVocabulary($vocabulary);
        }
    }

    public function addVocabulary(VocabularyInterface $vocabulary)
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
        }
    }

    public function removeVocabulary(VocabularyInterface $vocabulary)
    {
        $this->vocabularies->removeElement($vocabulary);
    }

    public function getVocabularies(): Collection
    {
        return $this->vocabularies;
    }

    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedSchool($this);
        }
    }

    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedSchool($this);
        }
    }

    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSchool($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator)
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSchool($this);
        }
    }

    public function addConfiguration(SchoolConfigInterface $config)
    {
        if (!$this->configurations->contains($config)) {
            $this->configurations->add($config);
        }
    }

    public function removeConfiguration(SchoolConfigInterface $config)
    {
        $this->configurations->removeElement($config);
    }

    public function setConfigurations(Collection $configs)
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

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return $this->courses->toArray();
    }
}
