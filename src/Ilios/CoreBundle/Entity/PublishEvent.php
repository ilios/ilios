<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PublishEvent
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="publish_event")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class PublishEvent implements PublishEventInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    /**
     * @var int
     *
     * @ORM\Column(name="publish_event_id", type="integer")
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
     * @ORM\Column(name="machine_ip", type="string", length=15)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 15
     * )
     */
    protected $machineIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_stamp", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected $timeStamp;

    /**
     * @var string
     * @deprecated
     * @ORM\Column(name="table_name", type="string", length=30, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     */
    protected $tableName;

    /**
     * @var int
     * @deprecated
     *
     * @ORM\Column(name="table_row_id", type="integer", nullable=true)
     *
     * @Assert\Type(type="integer")
     */
    protected $tableRowId;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="publishEvents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="administrator_id", referencedColumnName="user_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $administrator;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Program", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $programs;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\OneToMany(targetEntity="ProgramYear", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    protected $programYears;

    /**
     * Set the audit details for a publish event
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->setTimeStamp(new \DateTime());
        $this->setTableName('new');
        $this->setTableRowId(0);
    }

    /**
     * @param string $machineIp
     */
    public function setMachineIp($machineIp)
    {
        $this->machineIp = $machineIp;
    }

    /**
     * @return string
     */
    public function getMachineIp()
    {
        return $this->machineIp;
    }

    /**
     * @param \DateTime $timeStamp
     */
    public function setTimeStamp(\DateTime $timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    /**
     * @return \DateTime
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        if (!$this->tableName || $this->tableName === 'new') {
            $this->tableName = $tableName;
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId)
    {
        if (!$this->tableRowId) {
            $this->tableRowId = $tableRowId;
        }
    }

    /**
     * @return int
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * @param UserInterface $user
     */
    public function setAdministrator(UserInterface $user)
    {
        $this->administrator = $user;
    }

    /**
     * @return UserInterface
     */
    public function getAdministrator()
    {
        return $this->administrator;
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
        return $this->courses->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }

    /**
     * @param Collection $programs
     */
    public function setPrograms(Collection $programs)
    {
        $this->programs = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addProgram($program);
        }
    }

    /**
     * @param ProgramInterface $program
     */
    public function addProgram(ProgramInterface $program)
    {
        $this->programs->add($program);
    }

    /**
     * @return ArrayCollection|ProgramInterface[]
     */
    public function getPrograms()
    {
        return $this->programs->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }

    /**
     * @param Collection $programYears
     */
    public function setProgramYears(Collection $programYears)
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->add($programYear);
    }

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears()
    {
        return $this->programYears->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }
}
