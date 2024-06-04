<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\SessionsEntity;
use App\Traits\SchoolEntity;
use App\Repository\SessionTypeRepository;

#[ORM\Table(name: 'session_type')]
#[ORM\Index(columns: ['school_id'], name: 'school_id')]
#[ORM\Index(columns: ['assessment_option_id'], name: 'assessment_option_fkey')]
#[ORM\Entity(repositoryClass: SessionTypeRepository::class)]
#[IA\Entity]
class SessionType implements SessionTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use SessionsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;

    #[ORM\Column(name: 'session_type_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected string $title;

    #[ORM\Column(name: 'calendar_color', type: 'string', length: 7, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    // Validate that this is a valid hex color #000 or #faFAfa
    #[Assert\Regex(pattern: '/^#[0-9a-fA-F]{6}$/', message: 'This not a valid HTML hex color code. Eg #aaa of #a1B2C3')]
    protected string $calendarColor;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    #[ORM\Column(name: 'assessment', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $assessment;

    #[ORM\ManyToOne(targetEntity: 'AssessmentOption', inversedBy: 'sessionTypes')]
    #[ORM\JoinColumn(name: 'assessment_option_id', referencedColumnName: 'assessment_option_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?AssessmentOptionInterface $assessmentOption = null;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'sessionTypes')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToMany(targetEntity: 'AamcMethod', inversedBy: 'sessionTypes')]
    #[ORM\JoinTable(name: 'session_type_x_aamc_method')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id')]
    #[ORM\InverseJoinColumn(name: 'method_id', referencedColumnName: 'method_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $aamcMethods;

    #[ORM\OneToMany(mappedBy: 'sessionType', targetEntity: 'Session')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessions;

    public function __construct()
    {
        $this->aamcMethods = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->assessment = false;
        $this->active = true;
    }

    public function setCalendarColor(string $color): void
    {
        $this->calendarColor = $color;
    }

    public function getCalendarColor(): string
    {
        return $this->calendarColor;
    }

    public function setAssessment(bool $assessment): void
    {
        $this->assessment = $assessment;
    }

    public function isAssessment(): bool
    {
        return $this->assessment;
    }

    public function setAssessmentOption(?AssessmentOptionInterface $assessmentOption = null): void
    {
        $this->assessmentOption = $assessmentOption;
    }

    public function getAssessmentOption(): ?AssessmentOptionInterface
    {
        return $this->assessmentOption;
    }

    public function setAamcMethods(Collection $aamcMethods): void
    {
        $this->aamcMethods = new ArrayCollection();

        foreach ($aamcMethods as $aamcMethod) {
            $this->addAamcMethod($aamcMethod);
        }
    }

    public function addAamcMethod(AamcMethodInterface $aamcMethod): void
    {
        if (!$this->aamcMethods->contains($aamcMethod)) {
            $this->aamcMethods->add($aamcMethod);
        }
    }

    public function removeAamcMethod(AamcMethodInterface $aamcMethod): void
    {
        $this->aamcMethods->removeElement($aamcMethod);
    }

    public function getAamcMethods(): Collection
    {
        return $this->aamcMethods;
    }

    public function addSession(SessionInterface $session): void
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setSessionType($this);
        }
    }

    public function removeSession(SessionInterface $session): void
    {
        $sessionId = $session->getId();
        throw new Exception(
            'Sessions can not be removed from sessionTypes.' .
            "You must modify session #{$sessionId} directly."
        );
    }
}
