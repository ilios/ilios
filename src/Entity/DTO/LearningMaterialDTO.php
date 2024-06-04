<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * Class LearningMaterialDTO
 * Data transfer object for a learning materials
 */
#[IA\DTO('learningMaterials')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "LearningMaterial",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "uploadDate",
            description:"Upload date",
            type:"string",
            format: "date-time"
        ),
        new OA\Property(
            "originalAuthor",
            description: "Original author",
            type: "string"
        ),
        new OA\Property(
            "userRole",
            description: "Learning material user role",
            type: "integer"
        ),
        new OA\Property(
            "status",
            description: "Learning material status",
            type: "integer"
        ),
        new OA\Property(
            "owningUser",
            description: "The user who owns this learning material",
            type: "integer"
        ),
        new OA\Property(
            "copyrightPermission",
            description: "Has copyright permissions",
            type: "boolean"
        ),
        new OA\Property(
            "copyrightRationale",
            description: "Copyright rationale",
            type: "string"
        ),
        new OA\Property(
            "filename",
            description: "Filename",
            type: "string"
        ),
        new OA\Property(
            "mimetype",
            description: "File mimetype",
            type: "string"
        ),
        new OA\Property(
            "filesize",
            description: "Filesize in bytes",
            type: "integer"
        ),
        new OA\Property(
            "absoluteFileUrl",
            description: "File URL",
            type: "string"
        ),
        new OA\Property(
            "link",
            description: "Link",
            type: "string"
        ),
        new OA\Property(
            "citation",
            description: "Citation",
            type: "string"
        ),
        new OA\Property(
            "sessionLearningMaterials",
            description: "Session learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courseLearningMaterials",
            description: "Course learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructors', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructorGroups', IA\Type::INTEGERS)]
#[IA\FilterableBy('terms', IA\Type::INTEGERS)]
#[IA\FilterableBy('fullCourses', IA\Type::INTEGERS)]
#[IA\FilterableBy('meshDescriptors', IA\Type::STRINGS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
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
    public ?string $absoluteFileUri = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $link;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $citation;

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionLearningMaterials = [];

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $courseLearningMaterials = [];

    /**
     * Not exposed, it is used to build the URI later
     */
    #[IA\Type('string')]
    #[Ignore]
    public ?string $token;

    /**
     * Not exposed, used by indexing
     */
    #[Ignore]
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
