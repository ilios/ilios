<?php

namespace Ilios\CoreBundle\Model;

use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\TitleTrait;

/**
 * Class LearningMaterial
 * @package Ilios\CoreBundle\Model
 */
class LearningMaterial
{
    use IdentifiableTrait;
    use TitleTrait;
    use DescribableTrait;
    use TimestampableEntity;
    use BlameableEntity;

    const UPLOAD_DIR = 'uploads/learning_materials';

    /**
     * renamed: relativeFileSystemLocation
     * @var string
     */
    protected $file;

    /**
     * @var boolean
     */
    private $copyrightOwnership;

    /**
     * @var string
     */
    private $copyrightRationale;

    /**
     * @var string
     */
    private $assetCreator;

    /**
     * @var string
     */
    private $webLink;

    /**
     * @var string
     */
    private $citation;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Ilios\CoreBundle\Model\LearningMaterialUserRole
     */
    private $userRole;
    
    /**
     * @var \Ilios\CoreBundle\Model\LearningMaterialStatus
     */
    private $status;


    /**
     * Get learningMaterialId
     *
     * @return integer 
     */
    public function getLearningMaterialId()
    {
        return $this->learningMaterialId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return LearningMaterial
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return LearningMaterial
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set relativeFileSystemLocation
     *
     * @param string $relativeFileSystemLocation
     * @return LearningMaterial
     */
    public function setRelativeFileSystemLocation($relativeFileSystemLocation)
    {
        $this->relativeFileSystemLocation = $relativeFileSystemLocation;

        return $this;
    }

    /**
     * Get relativeFileSystemLocation
     *
     * @return string 
     */
    public function getRelativeFileSystemLocation()
    {
        return $this->relativeFileSystemLocation;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return LearningMaterial
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filesize
     *
     * @param integer $filesize
     * @return LearningMaterial
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize
     *
     * @return integer 
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return LearningMaterial
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set copyrightOwnership
     *
     * @param boolean $copyrightOwnership
     * @return LearningMaterial
     */
    public function setCopyrightOwnership($copyrightOwnership)
    {
        $this->copyrightOwnership = $copyrightOwnership;

        return $this;
    }

    /**
     * Get copyrightOwnership
     *
     * @return boolean 
     */
    public function getCopyrightOwnership()
    {
        return $this->copyrightOwnership;
    }

    /**
     * Set copyrightRationale
     *
     * @param string $copyrightRationale
     * @return LearningMaterial
     */
    public function setCopyrightRationale($copyrightRationale)
    {
        $this->copyrightRationale = $copyrightRationale;

        return $this;
    }

    /**
     * Get copyrightRationale
     *
     * @return string 
     */
    public function getCopyrightRationale()
    {
        return $this->copyrightRationale;
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     * @return LearningMaterial
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return \DateTime 
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set assetCreator
     *
     * @param string $assetCreator
     * @return LearningMaterial
     */
    public function setAssetCreator($assetCreator)
    {
        $this->assetCreator = $assetCreator;

        return $this;
    }

    /**
     * Get assetCreator
     *
     * @return string 
     */
    public function getAssetCreator()
    {
        return $this->assetCreator;
    }

    /**
     * Set webLink
     *
     * @param string $webLink
     * @return LearningMaterial
     */
    public function setWebLink($webLink)
    {
        $this->webLink = $webLink;

        return $this;
    }

    /**
     * Get webLink
     *
     * @return string 
     */
    public function getWebLink()
    {
        return $this->webLink;
    }

    /**
     * Set citation
     *
     * @param string $citation
     * @return LearningMaterial
     */
    public function setCitation($citation)
    {
        $this->citation = $citation;

        return $this;
    }

    /**
     * Get citation
     *
     * @return string 
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return LearningMaterial
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
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
     * Set status
     *
     * @param \Ilios\CoreBundle\Model\LearningMaterialStatus $status
     * @return LearningMaterial
     */
    public function setStatus(\Ilios\CoreBundle\Model\LearningMaterialStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Ilios\CoreBundle\Model\LearningMaterialStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set owningUser
     *
     * @param \Ilios\CoreBundle\Model\User $user
     * @return LearningMaterial
     */
    public function setOwningUser(\Ilios\CoreBundle\Model\User $user = null)
    {
        $this->owningUser = $user;

        return $this;
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
    public function setUserRole(\Ilios\CoreBundle\Model\LearningMaterialUserRole $userRole = null)
    {
        $this->userRole = $userRole;

        return $this;
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
