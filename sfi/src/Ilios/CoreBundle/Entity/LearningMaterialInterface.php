<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\BlameableEntityInterface;
use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialInterface
 * @package Ilios\CoreBundle\Entity
 */
interface LearningMaterialInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    DescribableEntityInterface,
    TimestampableEntityInterface
//    BlameableEntityInterface
{
    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';
    const TYPE_CITATION = 'citation';

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $orignalAuthor
     */
    public function setOriginalAuthor($orignalAuthor);

    /**
     * @return string
     */
    public function getOriginalAuthor();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param LearningMaterialStatusInterface $status
     */
    public function setStatus(LearningMaterialStatusInterface $status);

    /**
     * @return LearningMaterialStatusInterface
     */
    public function getStatus();

    /**
     * @param LearningMaterialUserRoleInterface $userRole
     */
    public function setUserRole(LearningMaterialUserRoleInterface $userRole);

    /**
     * @return LearningMaterialUserRoleInterface
     */
    public function getUserRole();

    /**
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy);

    /**
     * @return UserInterface
     */
    public function getCreatedBy();

    /**
     * @param UserInterface $updatedBy
     */
    public function setUpdatedBy(UserInterface $updatedBy);

    /**
     * @return UserInterface
     */
    public function getUpdatedBy();
}
