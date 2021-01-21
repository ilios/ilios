<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class LearningMaterialDTO
 * Data transfer object for a learning materials
 *
 * @IS\DTO("learningMaterials")
 */
class LearningMaterialDTO
{

    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $uploadDate;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $originalAuthor;

    /**
     * @IS\Expose
     * @IS\Related("learningMaterialUserRoles")
     * @IS\Type("entity")
     */
    public int $userRole;

    /**
     * @IS\Expose
     * @IS\Related("learningMaterialStatuses")
     * @IS\Type("entity")
     */
    public int $status;

    /**
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("entity")
     */
    public int $owningUser;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionLearningMaterials;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courseLearningMaterials;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $citation;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $copyrightPermission;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $copyrightRationale;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $filename;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $mimetype;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public ?int $filesize;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $link;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $absoluteFileUri;

    /**
     * Not exposed, it is used to build the URI later
     * @IS\Type("string")
     */
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
        bool $copyrightPermission,
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

        $this->absoluteFileUri = null;

        $this->sessionLearningMaterials = [];
        $this->courseLearningMaterials = [];
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
