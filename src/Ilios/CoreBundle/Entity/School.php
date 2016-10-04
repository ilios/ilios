<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\AlertableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;
use Ilios\CoreBundle\Traits\ProgramsEntity;
use Ilios\CoreBundle\Traits\StewardedEntity;

/**
 * Class School
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="school",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="template_prefix", columns={"template_prefix"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\SchoolRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class School implements SchoolInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use CoursesEntity;
    use ProgramsEntity;
    use StewardedEntity;
    use StringableIdEntity;
    use AlertableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="school_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
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
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="template_prefix", type="string", length=8, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 8
     * )
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("iliosAdministratorEmail")
     */
    protected $iliosAdministratorEmail;

    /**
     * @todo: Normalize later. Collection of email addresses. (Add email entity, etc)
     * @var string
     *
     * @ORM\Column(name="change_alert_recipients", type="text", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("changeAlertRecipients")
     */
    protected $changeAlertRecipients;

    /**
     * @var ArrayCollection|AlertInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Alert", mappedBy="recipients")
     *
     * Don't put alerts in the school API it takes forever to load them all
     * @JMS\Exclude
     * @JMS\Type("array<string>")
     */
    protected $alerts;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Competency", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $competencies;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramInterface[]
     *
     * @ORM\OneToMany(targetEntity="Program", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $programs;

    /**
     * @var ArrayCollection|DepartmentInterface[]
     *
     * @ORM\OneToMany(targetEntity="Department", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $departments;

    /**
     * @var ArrayCollection|VocabularyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Vocabulary", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $vocabularies;

    /**
    * @var ArrayCollection|InstructorGroupInterface[]
    *
    * @ORM\OneToMany(targetEntity="InstructorGroup", mappedBy="school")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("instructorGroups")
    */
    protected $instructorGroups;

    /**
    * @var CurriculumInventoryInstitutionInterface
    *
    * @ORM\OneToOne(targetEntity="CurriculumInventoryInstitution", mappedBy="school")
    *
    * @JMS\Expose
    * @JMS\Type("string")
    *
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\SerializedName("curriculumInventoryInstitution")
    */
    protected $curriculumInventoryInstitution;

    /**
    * @var ArrayCollection|SessionTypeInterface[]
    *
    * @ORM\OneToMany(targetEntity="SessionType", mappedBy="school")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("sessionTypes")
    */
    protected $sessionTypes;

    /**
     * @var ArrayCollection|ProgramYearStewardInterface[]
     *
     * @ORM\OneToMany(targetEntity="ProgramYearSteward", mappedBy="school")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $stewards;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->vocabularies = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->stewards = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->sessionTypes = new ArrayCollection();
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
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies)
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency)
    {
        $this->competencies->add($competency);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies()
    {
        return $this->competencies;
    }

    /**
     * @param Collection $departments
     */
    public function setDepartments(Collection $departments)
    {
        $this->departments = new ArrayCollection();

        foreach ($departments as $department) {
            $this->addDepartment($department);
        }
    }

    /**
     * @param DepartmentInterface $department
     */
    public function addDepartment(DepartmentInterface $department)
    {
        $this->departments->add($department);
    }

    /**
     * @return ArrayCollection|DepartmentInterface[]
     */
    public function getDepartments()
    {
        return $this->departments;
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
        $this->vocabularies->add($vocabulary);
    }

    /**
     * @inheritdoc
     */
    public function getVocabularies()
    {
        return $this->vocabularies;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->add($instructorGroup);
    }

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }

    /**
     * @param Collection $sessionTypes
     */
    public function setSessionTypes(Collection $sessionTypes)
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->add($sessionType);
    }

    /**
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function getSessionTypes()
    {
        return $this->sessionTypes;
    }
}
