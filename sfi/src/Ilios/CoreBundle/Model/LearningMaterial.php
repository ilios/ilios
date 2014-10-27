<?php
namespace Ilios\CoreBundle\Model;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;
use Ilios\CoreBundle\Traits\TitleTrait;

/**
 * Abstract Class LearningMaterial
 * @package Ilios\CoreBundle\Model
 */
abstract class LearningMaterial implements LearningMaterialInterface
{
    use IdentifiableTrait;
    use TitleTrait;
    use NameableTrait;
    use DescribableTrait;
    use TimestampableEntity;
    use BlameableEntity;

    /**
     * @var string
     */
    protected $type;

    /**
     * renamed Asset Creator
     * @var string
     */
    protected $contentAuthor;

    /**
     * @var string
     */
    private $token;

    /**
     * @var LearningMaterialUserRoleInterface
     */
    private $userRole;
    
    /**
     * @var LearningMaterialStatusInterface
     */
    private $status;

    /**
     * Set token
     *
     * @param string $token
     * @return LearningMaterial
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param \Ilios\CoreBundle\Model\LearningMaterialStatus $status
     */
    public function setStatus(\Ilios\CoreBundle\Model\LearningMaterialStatus $status = null)
    {
        $this->status = $status;
    }

    /**
     * @return \Ilios\CoreBundle\Model\LearningMaterialStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param \Ilios\CoreBundle\Model\User $user
     */
    public function setOwningUser(\Ilios\CoreBundle\Model\User $user = null)
    {
        $this->owningUser = $user;
    }

    /**
     * Get owningUser
     *
     * @return \Ilios\CoreBundle\Model\User 
     */
    public function getOwningUser()
    {
        return $this->owningUser;
    }

    /**
     * Set userRole
     *
     * @param \Ilios\CoreBundle\Model\LearningMaterialUserRole $userRole
     * @return LearningMaterial
     */
    public function setUserRole(\Ilios\CoreBundle\Model\LearningMaterialUserRole $userRole)
    {
        $this->userRole = $userRole;
    }

    /**
     * Get userRole
     *
     * @return \Ilios\CoreBundle\Model\LearningMaterialUserRole 
     */
    public function getUserRole()
    {
        return $this->userRole;
    }
}
