<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableNullableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use App\Repository\LearningMaterialRepository;

use function array_unique;

/**
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
    use DescribableNullableEntity;

    #[ORM\Column(name: 'learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 120)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 120)]
    protected string $title;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\Column(name: 'upload_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $uploadDate;

    #[ORM\Column(name: 'asset_creator', type: 'string', length: 80, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 80)]
    protected ?string $originalAuthor = null;

    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 64)]
    protected string $token;

    #[ORM\ManyToOne(targetEntity: 'LearningMaterialUserRole', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_user_role_id',
        referencedColumnName: 'learning_material_user_role_id',
        nullable: false
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected LearningMaterialUserRoleInterface $userRole;

    #[ORM\ManyToOne(targetEntity: 'LearningMaterialStatus', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_status_id',
        referencedColumnName: 'learning_material_status_id',
        nullable: false
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected LearningMaterialStatusInterface $status;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'owning_user_id', referencedColumnName: 'user_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected UserInterface $owningUser;

    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'SessionLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionLearningMaterials;

    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'CourseLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseLearningMaterials;

    #[ORM\Column(name: 'citation', type: 'string', length: 512, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 512, groups: ['citation'])]
    protected ?string $citation = null;

    #[ORM\Column(name: 'relative_file_system_location', type: 'string', length: 128, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank(groups: ['file'])]
    #[Assert\Length(min: 1, max: 128, groups: ['file'])]
    protected ?string $relativePath = null;

    #[ORM\Column(name: 'copyright_ownership', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $copyrightPermission = null;

    #[ORM\Column(name: 'copyright_rationale', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected ?string $copyrightRationale = null;

    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 255, groups: ['file'])]
    protected ?string $filename = null;

    #[ORM\Column(name: 'mime_type', type: 'string', length: 96, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 96, groups: ['file'])]
    protected ?string $mimetype = null;

    #[ORM\Column(name: 'filesize', type: 'integer', nullable: true, options: [
        'unsigned' => true,
    ])]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\Type(type: 'integer')]
    protected ?int $filesize = null;

    #[ORM\Column(name: 'web_link', type: 'string', length: 256, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 256, groups: ['link'])]
    protected ?string $link = null;

    public function __construct()
    {
        $this->uploadDate = new DateTime();
        $this->sessionLearningMaterials = new ArrayCollection();
        $this->courseLearningMaterials = new ArrayCollection();
    }

    public function setOriginalAuthor(?string $originalAuthor): void
    {
        $this->originalAuthor = $originalAuthor;
    }

    public function getOriginalAuthor(): ?string
    {
        return $this->originalAuthor;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function generateToken(): void
    {
        $random = random_bytes(128);

        // prepend id to avoid a conflict
        // and current time to prevent a conflict with regeneration
        $key = $this->getId() . microtime() . $random;

        // hash the string to give consistent length and URL safe characters
        $this->token = hash('sha256', $key);
    }

    public function setStatus(LearningMaterialStatusInterface $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): LearningMaterialStatusInterface
    {
        return $this->status;
    }

    public function setOwningUser(UserInterface $user): void
    {
        $this->owningUser = $user;
    }

    public function getOwningUser(): UserInterface
    {
        return $this->owningUser;
    }

    public function setUserRole(LearningMaterialUserRoleInterface $userRole): void
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

    public function setCitation(?string $citation): void
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

    public function setRelativePath(?string $path): void
    {
        $this->relativePath = $path;
    }

    public function getRelativePath(): ?string
    {
        return $this->relativePath;
    }

    public function setCopyrightPermission(?bool $copyrightPermission): void
    {
        $this->copyrightPermission = $copyrightPermission;
    }

    public function hasCopyrightPermission(): ?bool
    {
        return $this->copyrightPermission;
    }

    public function setCopyrightRationale(?string $copyrightRationale): void
    {
        $this->copyrightRationale = $copyrightRationale;
    }

    public function getCopyrightRationale(): ?string
    {
        return $this->copyrightRationale;
    }

    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilesize(?int $filesize): void
    {
        $this->filesize = $filesize;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    public function setMimetype(?string $mimetype): void
    {
        $this->mimetype = $mimetype;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setLink(?string $link): void
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

    public function setCourseLearningMaterials(?Collection $courseLearningMaterials = null): void
    {
        $this->courseLearningMaterials = new ArrayCollection();
        if (is_null($courseLearningMaterials)) {
            return;
        }

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
        }
    }

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void
    {
        $this->courseLearningMaterials->removeElement($courseLearningMaterial);
    }

    public function getCourseLearningMaterials(): Collection
    {
        return $this->courseLearningMaterials;
    }

    public function setSessionLearningMaterials(?Collection $sessionLearningMaterials = null): void
    {
        $this->sessionLearningMaterials = new ArrayCollection();
        if (is_null($sessionLearningMaterials)) {
            return;
        }

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
        }
    }

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void
    {
        $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
    }

    public function getSessionLearningMaterials(): Collection
    {
        return $this->sessionLearningMaterials;
    }

    public function getOwningSchool(): SchoolInterface
    {
        return $this->owningUser->getSchool();
    }

    public function getSessions(): Collection
    {
        $sessions = [];
        foreach ($this->getSessionLearningMaterials() as $sessionLearningMaterial) {
            $sessions = array_merge($sessions, $sessionLearningMaterial->getSessions()->toArray());
        }

        return new ArrayCollection(array_unique($sessions));
    }

    public function getValidationGroups(): array
    {
        if ($this->getCitation() !== null && strlen(trim($this->getCitation())) > 0) {
            return ['Default', 'citation'];
        } elseif ($this->getLink() !== null && strlen(trim($this->getLink())) > 0) {
            return ['Default', 'link'];
        }

        return ['Default', 'file'];
    }

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
