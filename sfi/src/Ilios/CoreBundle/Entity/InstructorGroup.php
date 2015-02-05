<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class InstructorGroup
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="instructor_group")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class InstructorGroup implements InstructorGroupInterface
{
//    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Column(name="instructor_group_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
    * @ORM\Column(type="string", length=60)
    * @todo should be on the TitledEntity Trait
    * @var string
    */
    protected $title;

    /**
     * original annotation: ORM\Column(name="school_id", type="integer")
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="instructorGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $school;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|IlmSessionFacet[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSessionFacet", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("ilmSessions")
     */
    protected $ilmSessionFacets;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructorGroups")
     * @ORM\JoinTable(name="instructor_group_x_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id")
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
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="instructorGroups")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->ilmSessionFacets = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->offerings = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->instructorGroupId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->instructorGroupId : $this->id;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param Collection $learnerGroups
     */
    public function setLearnerGroups(Collection $learnerGroups)
    {
        $this->learnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $group) {
            $this->addLearnerGroup($group);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        $this->learnerGroups->add($learnerGroup);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
    }

    /**
     * @param Collection $ilmSessionFacets
     */
    public function setIlmSessionFacets(Collection $ilmSessionFacets)
    {
        $this->ilmSessionFacets = new ArrayCollection();

        foreach ($ilmSessionFacets as $ilmSessionFacet) {
            $this->addIlmSessionFacet($ilmSessionFacet);
        }
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     */
    public function addIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet)
    {
        $this->ilmSessionFacets->add($ilmSessionFacet);
    }

    /**
     * @return ArrayCollection|IlmSessionFacetInterface[]
     */
    public function getIlmSessionFacets()
    {
        return $this->ilmSessionFacets;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users)
    {
        $this->users = new ArrayCollection();

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
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

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
        return $this->offerings;
    }
}
