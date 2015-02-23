<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class School
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="school",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="template_prefix", columns={"template_prefix"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class School implements SchoolInterface
{
    use TitledEntity;

    /**
     * @deprecated Replace with Trait in 3.xf
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
     * @ORM\Column(type="string", length=60)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="template_prefix", type="string", length=8, nullable=true)
     */
    protected $templatePrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="ilios_administrator_email", type="string", length=100)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("iliosAdministratorEmail")
     */
    protected $iliosAdministratorEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $deleted;

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
     * @JMS\Expose
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
     * @ORM\OneToMany(targetEntity="Course", mappedBy="owningSchool")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramInterface[]
     *
     * @ORM\OneToMany(targetEntity="Program", mappedBy="owningSchool")
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
     * @var ArrayCollection|DisciplineInterface[]
     *
     * @ORM\OneToMany(targetEntity="Discipline", mappedBy="owningSchool")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $disciplines;

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
    * @JMS\SerializedName("curriculumInventoryInsitution")
    */
    protected $curriculumInventoryInsitution;

    /**
    * @var ArrayCollection|SessionTypeInterface[]
    *
    * @ORM\OneToMany(targetEntity="SessionType", mappedBy="owningSchool")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("sessionTypes")
    */
    protected $sessionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->competencies = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->disciplines = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->deleted = false;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->schoolId = $id;
        $this->id = $id;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->schoolId : $this->id;
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
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
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts)
    {
        $this->alerts = new ArrayCollection();

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert)
    {
        $this->alerts->add($alert);
    }

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts()
    {
        return $this->alerts;
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
        $this->addCompetency($competency);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies()
    {
        return $this->competencies;
    }

    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses)
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course)
    {
        $this->courses->add($course);
    }

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses()
    {
        return $this->courses;
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
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines)
    {
        $this->disciplines = new ArrayCollection();

        foreach ($disciplines as $discipline) {
            $this->addDiscipline($discipline);
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline)
    {
        $this->disciplines->add($discipline);
    }

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines()
    {
        return $this->disciplines;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
