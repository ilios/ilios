<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;
use Ilios\CoreBundle\Traits\ProgramsEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\OfferingsEntity;

/**
 * Class PublishEvent
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="publish_event")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 *
 * @deprecated
 */
class PublishEvent implements PublishEventInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use CoursesEntity;
    use SessionsEntity;
    use ProgramYearsEntity;
    use ProgramsEntity;
    use OfferingsEntity;

    /**
     *
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
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\OneToMany(targetEntity="Offering", mappedBy="publishEvent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $offerings;

    /**
     * Set the audit details for a publish event
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->offerings = new ArrayCollection();
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
     * {@inheritdoc}
     */
    public function setTableName($tableName)
    {
        if (!$this->tableName || $this->tableName === 'new') {
            $this->tableName = $tableName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function setTableRowId($tableRowId)
    {
        if (!$this->tableRowId) {
            $this->tableRowId = $tableRowId;
        }
    }

    /**
     * {@inheritdoc}
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
}
