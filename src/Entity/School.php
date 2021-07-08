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
 *   uniqueConstraints={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'school')]
#[ORM\UniqueConstraint(name: 'template_prefix', columns: ['template_prefix'])]
#[ORM\Entity(repositoryClass: SchoolRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'school_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 60, unique: true)]
    protected $title;
    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=8)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'template_prefix', type: 'string', length: 8, nullable: true)]
    protected $templatePrefix;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'ilios_administrator_email', type: 'string', length: 100)]
    protected $iliosAdministratorEmail;
    /**
     * @todo: Normalize later. Collection of email addresses. (Add email entity, etc)
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'change_alert_recipients', type: 'text', nullable: true)]
    protected $changeAlertRecipients;
    /**
     * @var ArrayCollection|AlertInterface[]
     * Don't put alerts in the school API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'recipients')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $alerts;
    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Competency', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $competencies;
    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Course', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courses;
    /**
     * @var ArrayCollection|ProgramInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Program', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $programs;
    /**
     * @var ArrayCollection|VocabularyInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Vocabulary', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $vocabularies;
    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'InstructorGroup', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructorGroups;
    /**
     * @var CurriculumInventoryInstitutionInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'CurriculumInventoryInstitution', mappedBy: 'school')]
    protected $curriculumInventoryInstitution;
    /**
     * @var ArrayCollection|SessionTypeInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'SessionType', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionTypes;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'directedSchools')]
    #[ORM\JoinTable(name: 'school_director')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directors;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSchools')]
    #[ORM\JoinTable(name: 'school_administrator')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administrators;
    /**
     * @var ArrayCollection|SchoolConfigInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'SchoolConfig', mappedBy: 'school')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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
}
