<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class LearningMaterialDTO
 * Data transfer object for a learning materials
 */
#[IA\DTO('learningMaterials')]
#[IA\ExposeGraphQL]
class LearningMaterialDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $uploadDate;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $originalAuthor;

    #[IA\Expose]
    #[IA\Related('learningMaterialUserRoles')]
    #[IA\Type('entity')]
    public int $userRole;

    #[IA\Expose]
    #[IA\Related('learningMaterialStatuses')]
    #[IA\Type('entity')]
    public int $status;

    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('entity')]
    public int $owningUser;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessionLearningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $courseLearningMaterials = [];

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $citation;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $copyrightPermission;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $copyrightRationale;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $filename;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $mimetype;

    #[IA\Expose]
    #[IA\Type('integer')]
    public ?int $filesize = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $link;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $absoluteFileUri = null;

    /**
     * Not exposed, it is used to build the URI later
     */
    #[IA\Type('string')]
    public ?string $token;

    /**
     * Not exposed, used by indexing
     */
    public ?string $relativePath;

    public function __construct(
        int $id,
        string $title,
        ?string $description,
        DateTime $uploadDate,
        ?string $originalAuthor,
        ?string $citation,
        ?bool $copyrightPermission,
        ?string $copyrightRationale,
        ?string $filename,
        ?string $mimetype,
        ?int $filesize,
        ?string $link,
        ?string $token,
        ?string $relativePath
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->uploadDate = $uploadDate;
        $this->originalAuthor = $originalAuthor;
        $this->citation = $citation;
        $this->copyrightPermission = $copyrightPermission;
        $this->copyrightRationale = $copyrightRationale;
        $this->filename = $filename;
        $this->mimetype = $mimetype;
        $this->filesize = $filesize;
        $this->link = $link;
        $this->token = $token;
        $this->relativePath = $relativePath;
    }
    /**
     * Blanks out most of the material's attributes.
     */
    public function clearMaterial(): void
    {
        $this->absoluteFileUri = null;
        $this->citation = null;
        $this->copyrightRationale = null;
        $this->description = null;
        $this->filename = null;
        $this->filesize = null;
        $this->link = null;
        $this->mimetype = null;
        $this->originalAuthor = null;
        $this->token = null;
        $this->relativePath = null;
    }
}
