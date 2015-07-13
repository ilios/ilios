<?php

namespace Ilios\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface LearningMaterialInterface
 * @package Ilios\CoreBundle\Entity
 */
interface LearningMaterialInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    DescribableEntityInterface
{

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
     * @param UserInterface $user
     */
    public function setOwningUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getOwningUser();
    
    /**
     * @param string $text
     */
    public function setCitation($citation);

    /**
     * @return string
     */
    public function getCitation();
    
    /**
     * @param string $link
     */
    public function setLink($link);

    /**
     * @return string
     */
    public function getLink();
    
    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param bool $copyrightPermission
     */
    public function setCopyrightPermission($copyrightPermission);

    /**
     * @return bool
     */
    public function hasCopyrightPermission();

    /**
     * @param string $copyrightRationale
     */
    public function setCopyrightRationale($copyrightRationale);

    /**
     * @return string
     */
    public function getCopyrightRationale();


    /**
     * @return string
     */
    public function getAbsolutePath();

    /**
     * @return string
     */
    public function getWebPath();

    /**
     * @param UploadedFile $resource
     */
    public function setResource(UploadedFile $resource);

    /**
     * @return UploadedFile|\SplFileInfo
     */
    public function getResource();

    /**
     * @return void
     */
    public function upload();
}
