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
use App\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\CoursesEntity;
use App\Traits\ProgramsEntity;
use App\Repository\SchoolRepository;

/**
 * Class School
 *
 * @ORM\Table(name="school",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="template_prefix", columns={"template_prefix"})
 *   }
 * )
 * @ORM\Entity(repositoryClass=SchoolRepository::class)
 *
 * @IS\Entity
 */
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
     *
     * @ORM\Column(name="school_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="template_prefix", type="string", length=8, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=8)
     * })
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $templatePrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="ilios_administrator_email", type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $iliosAdministratorEmail;

    /**
     * @todo: Normalize later. Collection of email addresses. (Add email entity, etc)
     * @var string
     *
     * @ORM\Column(name="change_alert_recipients", type="text", nullable=true)
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $changeAlertRecipients;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="academic_year_start_day", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 31
     * )
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected int $academicYearStartDay;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="academic_year_start_month", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 12
     * )
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected int $academicYearStartMonth;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="academic_year_end_day", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 31
     * )
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected int $academicYearEndDay;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="academic_year_end_month", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 12
     * )
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected int $academicYearEndMonth;

    /**
     * @var ArrayCollection|AlertInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Alert", mappedBy="recipients")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * Don't put alerts in the school API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    protected $alerts;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Competency", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $competencies;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramInterface[]
     *
     * @ORM\OneToMany(targetEntity="Program", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programs;

    /**
     * @var ArrayCollection|VocabularyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Vocabulary", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $vocabularies;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="InstructorGroup", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructorGroups;

    /**
    * @var CurriculumInventoryInstitutionInterface
    *
    * @ORM\OneToOne(targetEntity="CurriculumInventoryInstitution", mappedBy="school")
    *
    * @IS\Expose
    * @IS\Type("entity")
    */
    protected $curriculumInventoryInstitution;

    /**
     * @var ArrayCollection|SessionTypeInterface[]
     *
     * @ORM\OneToMany(targetEntity="SessionType", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionTypes;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="directedSchools"))
     * @ORM\JoinTable(name="school_director",
     *   joinColumns={
     *     @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directors;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="administeredSchools"))
     * @ORM\JoinTable(name="school_administrator",
     *   joinColumns={
     *     @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administrators;

    /**
     * @var ArrayCollection|SchoolConfigInterface[]
     *
     * @ORM\OneToMany(targetEntity="SchoolConfig", mappedBy="school")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $configurations;

    /**
     * Constructor
     */
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

    /**
     * @return string
     */
    public function getTemplatePrefix()
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

    /**
     * @return string
     */
    public function getIliosAdministratorEmail()
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

    /**
     * @return string
     */
    public function getChangeAlertRecipients()
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

    /**
     * @return string
     */
    public function getCurriculumInventoryInstitution()
    {
        return $this->curriculumInventoryInstitution;
    }

    /**
     * @inheritdoc
     */
    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addRecipient($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAlert(AlertInterface $alert)
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            $alert->removeRecipient($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function setVocabularies(Collection $vocabularies)
    {
        $this->vocabularies = new ArrayCollection();

        foreach ($vocabularies as $vocabulary) {
            $this->addVocabulary($vocabulary);
        }
    }

    /**
     * @inheritdoc
     */
    public function addVocabulary(VocabularyInterface $vocabulary)
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeVocabulary(VocabularyInterface $vocabulary)
    {
        $this->vocabularies->removeElement($vocabulary);
    }

    /**
     * @inheritdoc
     */
    public function getVocabularies()
    {
        return $this->vocabularies;
    }

    /**
     * @inheritdoc
     */
    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedSchool($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedSchool($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSchool($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAdministrator(UserInterface $administrator)
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSchool($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addConfiguration(SchoolConfigInterface $config)
    {
        if (!$this->configurations->contains($config)) {
            $this->configurations->add($config);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeConfiguration(SchoolConfigInterface $config)
    {
        $this->configurations->removeElement($config);
    }

    /**
     * @inheritdoc
     */
    public function setConfigurations(Collection $configs)
    {
        $this->configurations = new ArrayCollection();

        foreach ($configs as $config) {
            $this->addConfiguration($config);
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfigurations()
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

    /**
     * @return int
     */
    public function getAcademicYearStartDay(): int
    {
        return $this->academicYearStartDay;
    }

    /**
     * @param int $academicYearStartDay
     */
    public function setAcademicYearStartDay(int $academicYearStartDay): void
    {
        $this->academicYearStartDay = $academicYearStartDay;
    }

    /**
     * @return int
     */
    public function getAcademicYearStartMonth(): int
    {
        return $this->academicYearStartMonth;
    }

    /**
     * @param int $academicYearStartMonth
     */
    public function setAcademicYearStartMonth(int $academicYearStartMonth): void
    {
        $this->academicYearStartMonth = $academicYearStartMonth;
    }

    /**
     * @return int
     */
    public function getAcademicYearEndDay(): int
    {
        return $this->academicYearEndDay;
    }

    /**
     * @param int $academicYearEndDay
     */
    public function setAcademicYearEndDay(int $academicYearEndDay): void
    {
        $this->academicYearEndDay = $academicYearEndDay;
    }

    /**
     * @return int
     */
    public function getAcademicYearEndMonth(): int
    {
        return $this->academicYearEndMonth;
    }

    /**
     * @param int $academicYearEndMonth
     */
    public function setAcademicYearEndMonth(int $academicYearEndMonth): void
    {
        $this->academicYearEndMonth = $academicYearEndMonth;
    }
}
