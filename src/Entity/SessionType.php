<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\SessionsEntity;
use App\Traits\SchoolEntity;
use App\Repository\SessionTypeRepository;

/**
 * SessionType
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'session_type')]
#[ORM\Index(name: 'school_id', columns: ['school_id'])]
#[ORM\Index(name: 'assessment_option_fkey', columns: ['assessment_option_id'])]
#[ORM\Entity(repositoryClass: SessionTypeRepository::class)]
class SessionType implements SessionTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use SessionsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'session_type_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

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
    #[ORM\Column(type: 'string', length: 100)]
    protected $title;

    /**
     * @var string
     * @Assert\Type(type="string")
     * Validate that this is a valid hex color #000 or #faFAfa
     * @Assert\Regex(
     *     pattern = "/^#[0-9a-fA-F]{6}$/",
     *     message = "This not a valid HTML hex color code. Eg #aaa of #a1B2C3"
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'calendar_color', type: 'string', length: 7, nullable: false)]
    protected $calendarColor;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $active;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'assessment', type: 'boolean')]
    protected $assessment;

    /**
     * @var AssessmentOptionInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'AssessmentOption', inversedBy: 'sessionTypes')]
    #[ORM\JoinColumn(name: 'assessment_option_id', referencedColumnName: 'assessment_option_id')]
    protected $assessmentOption;

    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'sessionTypes')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    protected $school;

    /**
     * @var ArrayCollection|AamcMethodInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'AamcMethod', inversedBy: 'sessionTypes')]
    #[ORM\JoinTable(name: 'session_type_x_aamc_method')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id')]
    #[ORM\InverseJoinColumn(name: 'method_id', referencedColumnName: 'method_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $aamcMethods;

    /**
     * @var ArrayCollection|SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Session', mappedBy: 'sessionType')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessions;

    public function __construct()
    {
        $this->aamcMethods = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->assessment = false;
        $this->active = true;
    }

    /**
     * @inheritdoc
     */
    public function setCalendarColor($color)
    {
        $this->calendarColor = $color;
    }

    /**
     * @return string
     */
    public function getCalendarColor()
    {
        return $this->calendarColor;
    }

    /**
     * Set assessment
     *
     * @param bool $assessment
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get assessment
     *
     * @return bool
     */
    public function isAssessment()
    {
        return $this->assessment;
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     */
    public function setAssessmentOption(AssessmentOptionInterface $assessmentOption = null)
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

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setSessionType($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        $sessionId = $session->getId();
        throw new \Exception(
            'Sessions can not be removed from sessionTypes.' .
            "You must modify session #{$sessionId} directly."
        );
    }
}
