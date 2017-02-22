<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * SessionType
 *
 * @ORM\Table(name="session_type",
 *   indexes={
 *     @ORM\Index(name="school_id", columns={"school_id"}),
 *     @ORM\Index(name="assessment_option_fkey", columns={"assessment_option_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\SessionTypeRepository")
 *
 * @IS\Entity
 */
class SessionType implements SessionTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use SessionsEntity;
    use SchoolEntity;
    use StringableIdEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="session_type_id", type="integer")
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
     * @ORM\Column(type="string", length=100)
     * @var string
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
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="session_type_css_class", type="string", length=64, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 64
     * )
     */
    protected $sessionTypeCssClass;

    /**
     * @var boolean
     *
     * @ORM\Column(name="assessment", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    protected $assessment;

    /**
     * @var AssessmentOptionInterface
     *
     * @ORM\ManyToOne(targetEntity="AssessmentOption", inversedBy="sessionTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assessment_option_id", referencedColumnName="assessment_option_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $assessmentOption;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="sessionTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
     * @var ArrayCollection|AamcMethodInterface[]
     *
     * @ORM\ManyToMany(targetEntity="AamcMethod", inversedBy="sessionTypes")
     * @ORM\JoinTable(name="session_type_x_aamc_method",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_type_id", referencedColumnName="session_type_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="method_id", referencedColumnName="method_id")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $aamcMethods;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="sessionType")
     *
     * Don't put sessions in the sessionType API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    protected $sessions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aamcMethods = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->assessment = false;
    }

    /**
     * @param string $sessionTypeCssClass
     */
    public function setSessionTypeCssClass($sessionTypeCssClass)
    {
        $this->sessionTypeCssClass = $sessionTypeCssClass;
    }

    /**
     * @return string
     */
    public function getSessionTypeCssClass()
    {
        return $this->sessionTypeCssClass;
    }

    /**
     * Set assessment
     *
     * @param boolean $assessment
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get assessment
     *
     * @return boolean
     */
    public function isAssessment()
    {
        return $this->assessment;
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     */
    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption)
    {
        $this->assessmentOption = $assessmentOption;
    }

    /**
     * @return AssessmentOptionInterface
     */
    public function getAssessmentOption()
    {
        return $this->assessmentOption;
    }

    /**
     * @param Collection $aamcMethods
     */
    public function setAamcMethods(Collection $aamcMethods)
    {
        $this->aamcMethods = new ArrayCollection();

        foreach ($aamcMethods as $aamcMethod) {
            $this->addAamcMethod($aamcMethod);
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function addAamcMethod(AamcMethodInterface $aamcMethod)
    {
        if (!$this->aamcMethods->contains($aamcMethod)) {
            $this->aamcMethods->add($aamcMethod);
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function removeAamcMethod(AamcMethodInterface $aamcMethod)
    {
        $this->aamcMethods->removeElement($aamcMethod);
    }

    /**
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function getAamcMethods()
    {
        return $this->aamcMethods;
    }
}
