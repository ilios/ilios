<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use App\Repository\LearningMaterialRepository;

/**
 * Class LearningMaterial
 * Learning materials are not serialized like other entities.  They are decorated by the controller and
 * then sent as plain php objects in order to insert the absolute path to the file
 * @IS\Entity
 */
#[ORM\Entity(repositoryClass: LearningMaterialRepository::class)]
#[ORM\Table(name: 'learning_material')]
#[ORM\UniqueConstraint(name: 'idx_learning_material_token_unique', columns: ['token'])]
class LearningMaterial implements LearningMaterialInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use DescribableEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'learning_material_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 120
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 120)]
    protected $title;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'upload_date', type: 'datetime')]
    protected $uploadDate;

    /**
     * renamed Asset Creator
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=80)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'asset_creator', type: 'string', length: 80, nullable: true)]
    protected $originalAuthor;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=64)
     * })
     */
    #[ORM\Column(name: 'token', type: 'string', length: 64, nullable: true)]
    protected $token;

    /**
     * @var LearningMaterialUserRoleInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterialUserRole', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_user_role_id',
        referencedColumnName: 'learning_material_user_role_id',
        nullable: false
    )]
    protected $userRole;

    /**
     * @var LearningMaterialStatusInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'LearningMaterialStatus', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(
        name: 'learning_material_status_id',
        referencedColumnName: 'learning_material_status_id',
        nullable: false
    )]
    protected $status;

    /**
     * @var UserInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'learningMaterials')]
    #[ORM\JoinColumn(name: 'owning_user_id', referencedColumnName: 'user_id', nullable: false)]
    protected $owningUser;

    /**
     * @var ArrayCollection|SessionLearningMaterialInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'SessionLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionLearningMaterials;

    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'learningMaterial', targetEntity: 'CourseLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courseLearningMaterials;

    /**
     * renamed from citation
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 512,
     *      groups={"citation"}
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'citation', type: 'string', length: 512, nullable: true)]
    protected $citation;

    /**
     * @var string
     * renamed from relative_file_system_location
     * @Assert\Type(type="string")
     * @Assert\NotBlank(groups={"file"})
     * @Assert\Length(
     *      min = 1,
     *      max = 128,
     *      groups={"file"}
     * )
     */
    #[ORM\Column(name: 'relative_file_system_location', type: 'string', length: 128, nullable: true)]
    protected $relativePath;

    /**
     * renamed copyrightownership
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'copyright_ownership', type: 'boolean', nullable: true)]
    protected $copyrightPermission;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'copyright_rationale', type: 'text', nullable: true)]
    protected $copyrightRationale;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     groups={"file"}
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: true)]
    protected $filename;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 96,
     *      groups={"file"}
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'mime_type', type: 'string', length: 96, nullable: true)]
    protected $mimetype;

    /**
     * @var string
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'filesize', type: 'integer', nullable: true, options: [
        'unsigned' => true,
    ])]
    protected $filesize;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 256,
     *      groups={"link"}
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'web_link', type: 'string', length: 256, nullable: true)]
    protected $link;

    public function __construct()
    {
        $this->uploadDate = new \DateTime();
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

    /**
     * @return string
     */
    public function getOriginalAuthor()
    {
        return $this->originalAuthor;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function generateToken()
    {
        $random = random_bytes(128);

        // prepend id to avoid a conflict
        // and current time to prevent a conflict with regeneration
        $key = $this->getId() . microtime() . $random;

        // hash the string to give consistent length and URL safe characters
        $this->token = hash('sha256', $key);
    }

    /**
     * @param LearningMaterialStatusInterface $status
     */
    public function setStatus(LearningMaterialStatusInterface $status)
    {
        $this->status = $status;
    }

    /**
     * @return LearningMaterialStatusInterface
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param UserInterface $user
     */
    public function setOwningUser(UserInterface $user)
    {
        $this->owningUser = $user;
    }

    /**
     * @inheritdoc
     */
    public function getOwningUser()
    {
        return $this->owningUser;
    }

    /**
     * @param LearningMaterialUserRoleInterface $userRole
     */
    public function setUserRole(LearningMaterialUserRoleInterface $userRole)
    {
        $this->userRole = $userRole;
    }

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * @return \DateTime
     */
    public function getUploadDate()
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

    /**
     * @return string
     */
    public function getCitation()
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

    /**
     * @return string
     */
    public function getRelativePath()
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

    /**
     * @return bool
     */
    public function hasCopyrightPermission()
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

    /**
     * @return string
     */
    public function getCopyrightRationale()
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

    /**
     * @return string
     */
    public function getFilename()
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

    /**
     * @return string
     */
    public function getFilesize()
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

    /**
     * @return string
     */
    public function getMimetype()
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

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param Collection $courseLearningMaterials
     */
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

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        $this->courseLearningMaterials->removeElement($courseLearningMaterial);
    }

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }

    /**
     * @param Collection $sessionLearningMaterials
     */
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

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
    }

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials;
    }

    /**
     * @inheritdoc
     */
    public function getOwningSchool()
    {
        if ($user = $this->getOwningUser()) {
            return $user->getSchool();
        }
        return null;
    }

    /**
     * @return SessionInterface[]|ArrayCollection
     */
    public function getSessions()
    {
        $sessions = [];
        foreach ($this->getSessionLearningMaterials() as $sessionLearningMaterial) {
            $sessions = array_merge($sessions, $sessionLearningMaterial->getSessions());
        }
        $sessions = array_unique($sessions);

        return $sessions;
    }

    /**
     * @inheritDoc
     */
    public function getValidationGroups()
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
            ->map(function (CourseLearningMaterialInterface $clm) {
                return $clm->getCourse();
            });

        $sessionCourses = $this->sessionLearningMaterials
            ->map(function (SessionLearningMaterialInterface $slm) {
                return $slm->getSession()->getCourse();
            });

        return array_merge(
            $directCourses->toArray(),
            $sessionCourses->toArray()
        );
    }
}
