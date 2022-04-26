<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Attribute as IA;
use App\Repository\LearningMaterialRepository;

use function array_unique;

/**
 * Class LearningMaterial
 * Learning materials are not serialized like other entities.  They are decorated by the controller and
 * then sent as plain php objects in order to insert the absolute path to the file
 */
#[ORM\Entity(repositoryClass: LearningMaterialRepository::class)]
#[ORM\Table(name: 'learning_material')]
#[ORM\UniqueConstraint(name: 'idx_learning_material_token_unique', columns: ['token'])]
#[IA\Entity]
class LearningMaterial implements LearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use DescribableEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 120)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 120)]
    protected $title;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 65000),
    ])]
    protected $description;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'upload_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected $uploadDate;

    /**
     * renamed Asset Creator
     * @var string
     */
    #[ORM\Column(name: 'asset_creator', type: 'string', length: 80, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 80),
    ])]
    protected $originalAuthor;

    /**
     * @var string
     */
    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 64),
    ])]
    protected $token;

    /**
     * @var LearningMaterialUserRoleInterface
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterialUserRole', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_user_role_id',
        referencedColumnName: 'learning_material_user_role_id',
        nullable: false
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $userRole;

    /**
     * @var LearningMaterialStatusInterface
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterialStatus', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_status_id',
        referencedColumnName: 'learning_material_status_id',
        nullable: false
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $status;

    /**
     * @var UserInterface
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'owning_user_id', referencedColumnName: 'user_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $owningUser;

    /**
     * @var ArrayCollection|SessionLearningMaterialInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'SessionLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionLearningMaterials;

    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'CourseLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courseLearningMaterials;

    /**
     * renamed from citation
     * @var string
     */
    #[ORM\Column(name: 'citation', type: 'string', length: 512, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 512, groups: ['citation'])]
    protected $citation;

    /**
     * @var string
     * renamed from relative_file_system_location
     */
    #[ORM\Column(name: 'relative_file_system_location', type: 'string', length: 128, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank(groups: ['file'])]
    #[Assert\Length(min: 1, max: 128, groups: ['file'])]
    protected $relativePath;

    /**
     * renamed copyrightownership
     * @var bool
     */
    #[ORM\Column(name: 'copyright_ownership', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $copyrightPermission;

    /**
     * @var string
     */
    #[ORM\Column(name: 'copyright_rationale', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected $copyrightRationale;

    /**
     * @var string
     */
    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255, groups: ['file'])]
    protected $filename;

    /**
     * @var string
     */
    #[ORM\Column(name: 'mime_type', type: 'string', length: 96, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 96, groups: ['file'])]
    protected $mimetype;

    /**
     * @var string
     */
    #[ORM\Column(name: 'filesize', type: 'integer', nullable: true, options: [
        'unsigned' => true,
    ])]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\Type(type: 'integer')]
    protected $filesize;

    /**
     * @var string
     */
    #[ORM\Column(name: 'web_link', type: 'string', length: 256, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 256, groups: ['link'])]
    protected $link;

    public function __construct()
    {
        $this->uploadDate = new DateTime();
        $this->sessionLearningMaterials = new ArrayCollection();
        $this->courseLearningMaterials = new ArrayCollection();
    }

    /**
     * @param string $originalAuthor
     */
    public function setOriginalAuthor($originalAuthor)
    {
        $this->originalAuthor = $originalAuthor;
    }

    public function getOriginalAuthor(): string
    {
        return $this->originalAuthor;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function generateToken()
    {
        $random = random_bytes(128);

        // prepend id to avoid a conflict
        // and current time to prevent a conflict with regeneration
        $key = $this->getId() . microtime() . $random;

        // hash the string to give consistent length and URL safe characters
        $this->token = hash('sha256', $key);
    }

    public function setStatus(LearningMaterialStatusInterface $status)
    {
        $this->status = $status;
    }

    public function getStatus(): LearningMaterialStatusInterface
    {
        return $this->status;
    }

    public function setOwningUser(UserInterface $user)
    {
        $this->owningUser = $user;
    }

    public function getOwningUser(): ?UserInterface
    {
        return $this->owningUser;
    }

    public function setUserRole(LearningMaterialUserRoleInterface $userRole)
    {
        $this->userRole = $userRole;
    }

    public function getUserRole(): LearningMaterialUserRoleInterface
    {
        return $this->userRole;
    }

    public function getUploadDate(): DateTime
    {
        return $this->uploadDate;
    }

    /**
     * @param string $citation
     */
    public function setCitation($citation)
    {
        if (!is_null($citation)) {
            $this->mimetype = 'citation';
        }
        $this->citation = $citation;
    }

    public function getCitation(): ?string
    {
        return $this->citation;
    }

    /**
     * @param string $path
     */
    public function setRelativePath($path)
    {
        $this->relativePath = $path;
    }

    public function getRelativePath(): ?string
    {
        return $this->relativePath;
    }

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission)
    {
        $this->copyrightPermission = $copyrightPermission;
    }

    public function hasCopyrightPermission(): ?bool
    {
        return $this->copyrightPermission;
    }

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale)
    {
        $this->copyrightRationale = $copyrightRationale;
    }

    public function getCopyrightRationale(): ?string
    {
        return $this->copyrightRationale;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filesize
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        if (!is_null($link)) {
            $this->mimetype = 'link';
        }
        $this->link = $link;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setCourseLearningMaterials(Collection $courseLearningMaterials = null)
    {
        $this->courseLearningMaterials = new ArrayCollection();
        if (is_null($courseLearningMaterials)) {
            return;
        }

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
        }
    }

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        $this->courseLearningMaterials->removeElement($courseLearningMaterial);
    }

    public function getCourseLearningMaterials(): Collection
    {
        return $this->courseLearningMaterials;
    }

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null)
    {
        $this->sessionLearningMaterials = new ArrayCollection();
        if (is_null($sessionLearningMaterials)) {
            return;
        }

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
        }
    }

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
    }

    public function getSessionLearningMaterials(): Collection
    {
        return $this->sessionLearningMaterials;
    }

    public function getOwningSchool(): ?SchoolInterface
    {
        if ($user = $this->getOwningUser()) {
            return $user->getSchool();
        }
        return null;
    }

    public function getSessions(): Collection
    {
        $sessions = [];
        foreach ($this->getSessionLearningMaterials() as $sessionLearningMaterial) {
            $sessions = array_merge($sessions, $sessionLearningMaterial->getSessions()->toArray());
        }

        return new ArrayCollection(array_unique($sessions));
    }

    /**
     * @inheritDoc
     */
    public function getValidationGroups(): array
    {
        if ($this->getCitation() !== null && strlen(trim($this->getCitation())) > 0) {
            return ['Default', 'citation'];
        } elseif ($this->getLink() !== null && strlen(trim($this->getLink())) > 0) {
            return ['Default', 'link'];
        }

        return ['Default', 'file'];
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        $directCourses = $this->courseLearningMaterials
            ->map(fn(CourseLearningMaterialInterface $clm) => $clm->getCourse());

        $sessionCourses = $this->sessionLearningMaterials
            ->map(fn(SessionLearningMaterialInterface $slm) => $slm->getSession()->getCourse());

        return array_merge(
            $directCourses->toArray(),
            $sessionCourses->toArray()
        );
    }
}
