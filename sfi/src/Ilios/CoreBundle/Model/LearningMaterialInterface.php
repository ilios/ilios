<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface LearningMaterialInterface
 * @package Ilios\CoreBundle\Model
 */
interface LearningMaterialInterface 
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
     * @param string $contentAuthor
     */
    public function setContentAuthor($contentAuthor);

    /**
     * @return string
     */
    public function getContentAuthor();

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
}
