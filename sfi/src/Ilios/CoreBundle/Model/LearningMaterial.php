<?php
namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

use Ilios\CoreBundle\Model\LearningMaterials\Citation;
use Ilios\CoreBundle\Model\LearningMaterials\File;
use Ilios\CoreBundle\Model\LearningMaterials\link;

/**
 * Abstract Class LearningMaterial
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"file" = "File", "link" = "Link", "citation" = "Citation"})
 */
class LearningMaterial implements LearningMaterialInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use NameableEntity;
    use DescribableEntity;
    use TimestampableEntity;
    use BlameableEntity; //Replace owningUser

    protected $id;

    /**
     * renamed Asset Creator
     * @var string
     */
    protected $contentAuthor;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var LearningMaterialUserRoleInterface
     */
    protected $userRole;
    
    /**
     * @var LearningMaterialStatusInterface
     */
    protected $status;

    /**
     * @var string
     */
    private $type;

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

    /**
     * @param string $contentAuthor
     */
    public function setContentAuthor($contentAuthor)
    {
        $this->contentAuthor = $contentAuthor;
    }

    /**
     * @return string
     */
    public function getContentAuthor()
    {
        return $this->contentAuthor;
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
}
