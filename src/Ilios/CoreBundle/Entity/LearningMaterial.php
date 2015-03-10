<?php
namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

use Ilios\CoreBundle\Entity\LearningMaterials\Citation;
use Ilios\CoreBundle\Entity\LearningMaterials\File;
use Ilios\CoreBundle\Entity\LearningMaterials\link;

/**
 * Abstract Class LearningMaterial
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *  name="learning_material",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="idx_learning_material_token_unique", columns={"token"})}
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *   "file" = "Ilios\CoreBundle\Entity\LearningMaterials\File",
 *   "link" = "Ilios\CoreBundle\Entity\LearningMaterials\Link",
 *   "citation" = "Ilios\CoreBundle\Entity\LearningMaterials\Citation"
 * })
 *
 * @JMS\ExclusionPolicy("all")
 */
abstract class LearningMaterial implements LearningMaterialInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use DescribableEntity;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="learning_material_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @deprecated Replace with TimestampableEntity in 3.1
     * @var \DateTime
     *
     * @ORM\Column(name="upload_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("uploadDate")
     */
    protected $createdAt;

    /**
     * renamed Asset Creator
     * @var string
     *
     * @ORM\Column(name="asset_creator", type="string", length=80, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("originalAuthor")
     */
    protected $originalAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 64
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $token;

    /**
     * @var LearningMaterialUserRoleInterface
     *
     * @ORM\ManyToOne(targetEntity="LearningMaterialUserRole", inversedBy="learningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="learning_material_user_role_id", referencedColumnName="learning_material_user_role_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("userRole")
     */
    protected $userRole;

    /**
     * @var LearningMaterialStatusInterface
     *
     * @ORM\ManyToOne(targetEntity="LearningMaterialStatus", inversedBy="learningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="learning_material_status_id", referencedColumnName="learning_material_status_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $status;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="learningMaterials")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owning_user_id", referencedColumnName="user_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("owningUser")
     */
    protected $owningUser;

    /**
     * @var UserInterface
     */
    protected $createdBy;

    /**
     * @todo: Not yet implemented
     * @var UserInterface
     */
    protected $updatedBy;

    /**
     * @var ArrayCollection|SessioinLearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="SessionLearningMaterial", mappedBy="learningMaterial")
     *
     * JMS\Expose
     * JMS\Type("array<string>")
     * JMS\SerializedName("sessionLearningMaterials")
     */
    protected $sessionLearningMaterials;

    /**
    * @var ArrayCollection|CourseLearningMaterialInterface[]
    *
    * @ORM\OneToMany(targetEntity="CourseLearningMaterial",mappedBy="learningMaterial")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("courseLearningMaterials")
    */
    protected $courseLearningMaterials;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_CITATION,
            self::TYPE_FILE,
            self::TYPE_LINK
        ];
    }

    /**
     *
     */
    public function stampUpdate()
    {
        throw new \BadFunctionCallException('Not yet implamented');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        throw new \BadFunctionCallException('Not yet implamented');
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
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->owningUser = $createdBy;
        $this->createdBy = $createdBy;
    }

    /**
     * @return UserInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param UserInterface $updatedBy
     */
    public function setUpdatedBy(UserInterface $updatedBy)
    {
        throw new \BadFunctionCallException('Method not yet implemented.');
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return UserInterface
     */
    public function getUpdatedBy()
    {
        throw new \BadFunctionCallException('Method not yet implemented.');

        return $this->updatedBy;
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
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, static::getTypes())) {
            throw new \InvalidArgumentException('Type ' . $type . ' is not a valid type.');
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
