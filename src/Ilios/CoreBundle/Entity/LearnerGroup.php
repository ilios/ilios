<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class LearnerGroup
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class LearnerGroup implements LearnerGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
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
     * @ORM\Column(name="location", type="string", length=100, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $location;

    /**
     * @var CohortInterface
     *
     * @ORM\ManyToOne(targetEntity="Cohort", inversedBy="learnerGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cohort_id", referencedColumnName="cohort_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $cohort;

    /**
     * @var GroupInterface
     *
     * @ORM\ManyToOne(targetEntity="LearnerGroup", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_group_id", referencedColumnName="group_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $parent;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearnerGroup", mappedBy="parent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $children;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="learnerGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("ilmSessions")
     */
    protected $ilmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="learnerGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $offerings;

    /**
     * @var string
     *
     * @ORM\Column(name="instructors", type="string", length=120, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $instructors;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="learnerGroups")
     * @ORM\JoinTable(name="group_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorGroups")
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="learnerGroups", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="group_x_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $users;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructorUserGroups", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="group_x_instructor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorUsers")
     */
    protected $instructorUsers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->ilmSessions = new ArrayCollection();
        $this->offerings = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->instructorUsers = new ArrayCollection();
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return CohortInterface
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @param string $instructors
     */
    public function setInstructors($instructors)
    {
        $this->instructors = $instructors;
    }

    /**
     * @return string
     */
    public function getInstructors()
    {
        return $this->instructors;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users = null)
    {
        $this->users = new ArrayCollection();
        if (is_null($users)) {
            return;
        }

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        $this->users->add($user);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions = null)
    {
        $this->ilmSessions = new ArrayCollection();
        if (is_null($ilmSessions)) {
            return;
        }
        foreach ($ilmSessions as $ilmSession) {
            $this->addIlmSession($ilmSession);
        }
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        $this->ilmSessions->add($ilmSession);
    }

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getIlmSessions()
    {
        return $this->ilmSessions;
    }

    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings = null)
    {
        $this->offerings = new ArrayCollection();
        if (is_null($offerings)) {
            return;
        }

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering)
    {
        $this->offerings->add($offering);
    }

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings()
    {
        return $this->offerings->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }

    /**
     * @param GroupInterface $parent
     */
    public function setParent(LearnerGroupInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return GroupInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children = null)
    {
        $this->children = new ArrayCollection();
        if (is_null($children)) {
            return;
        }

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param LearnerGroupInterface $child
     */
    public function addChild(LearnerGroupInterface $child)
    {
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups = null)
    {
        $this->instructorGroups = new ArrayCollection();

        if (is_null($instructorGroups)) {
            return;
        }

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
     * @param Collection $instructorUsers
     */
    public function setInstructorUsers(Collection $instructorUsers = null)
    {
        $this->instructorUsers = new ArrayCollection();
        if (is_null($instructorUsers)) {
            return;
        }

        foreach ($instructorUsers as $instructorUser) {
            $this->addInstructorGroup($instructorUser);
        }
    }

    /**
     * @param UserInterface $instructorUser
     */
    public function addInstructorUser(UserInterface $instructorUser)
    {
        $this->instructorUsers->add($instructorUser);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructorUsers()
    {
        return $this->instructorUsers;
    }
}
