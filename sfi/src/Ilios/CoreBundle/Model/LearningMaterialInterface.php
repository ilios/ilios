<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface LearningMaterialInterface
 */
interface LearningMaterialInterface 
{
    public function getLearningMaterialId();

    public function setTitle($title);

    public function getTitle();

    public function setMimeType($mimeType);

    public function getMimeType();

    public function setRelativeFileSystemLocation($relativeFileSystemLocation);

    public function getRelativeFileSystemLocation();

    public function setFilename($filename);

    public function getFilename();

    public function setFilesize($filesize);

    public function getFilesize();

    public function setDescription($description);

    public function getDescription();

    public function setCopyrightOwnership($copyrightOwnership);

    public function getCopyrightOwnership();

    public function setCopyrightRationale($copyrightRationale);

    public function getCopyrightRationale();

    public function setUploadDate($uploadDate);

    public function getUploadDate();

    public function setAssetCreator($assetCreator);

    public function getAssetCreator();

    public function setWebLink($webLink);

    public function getWebLink();

    public function setCitation($citation);

    public function getCitation();

    public function setToken($token);

    public function getToken();

    public function setStatus(\Ilios\CoreBundle\Model\LearningMaterialStatus $status = null);

    public function getStatus();

    public function setOwningUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getOwningUser();

    public function setUserRole(\Ilios\CoreBundle\Model\LearningMaterialUserRole $userRole = null);

    public function getUserRole();
}
