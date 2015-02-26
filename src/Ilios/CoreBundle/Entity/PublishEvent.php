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
     *
     * @ORM\Column(name="table_name", type="string", length=30)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     */
    protected $tableName;

    /**
     * @var int
     *
     * @ORM\Column(name="table_row_id", type="integer")
     *
     * @Assert\NotBlank()
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
     * @todo: Implement as one to one later on.
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

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
        $this->tableName = $tableName;
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
        $this->tableRowId = $tableRowId;
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
        return $this->courses;
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
        return $this->sessions;
    }
}
