<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Mockery as m;

/**
 * Tests for Entity LearningMaterial
 */
class LearningMaterialTest extends EntityBase
{
    /**
     * @var LearningMaterial
     */
    protected $object;

    /**
     * Instantiate a LearningMaterial object
     */
    protected function setUp()
    {
        $this->object = new LearningMaterial;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getLearningMaterialId
     */
    public function testGetLearningMaterialId()
    {
        $this->basicGetTest('learningMaterialId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setMimeType
     */
    public function testSetMimeType()
    {
        $this->basicSetTest('mimeType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getMimeType
     */
    public function testGetMimeType()
    {
        $this->basicGetTest('mimeType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setRelativeFileSystemLocation
     */
    public function testSetRelativeFileSystemLocation()
    {
        $this->basicSetTest('relativeFileSystemLocation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getRelativeFileSystemLocation
     */
    public function testGetRelativeFileSystemLocation()
    {
        $this->basicGetTest('relativeFileSystemLocation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setFilename
     */
    public function testSetFilename()
    {
        $this->basicSetTest('filename', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getFilename
     */
    public function testGetFilename()
    {
        $this->basicGetTest('filename', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setFilesize
     */
    public function testSetFilesize()
    {
        $this->basicSetTest('filesize', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getFilesize
     */
    public function testGetFilesize()
    {
        $this->basicGetTest('filesize', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setCopyrightOwnership
     */
    public function testSetCopyrightOwnership()
    {
        $this->basicSetTest('copyrightOwnership', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getCopyrightOwnership
     */
    public function testGetCopyrightOwnership()
    {
        $this->basicGetTest('copyrightOwnership', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setCopyrightRationale
     */
    public function testSetCopyrightRationale()
    {
        $this->basicSetTest('copyrightRationale', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getCopyrightRationale
     */
    public function testGetCopyrightRationale()
    {
        $this->basicGetTest('copyrightRationale', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setUploadDate
     */
    public function testSetUploadDate()
    {
        $this->basicSetTest('uploadDate', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getUploadDate
     */
    public function testGetUploadDate()
    {
        $this->basicGetTest('uploadDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setAssetCreator
     */
    public function testSetAssetCreator()
    {
        $this->basicSetTest('assetCreator', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getAssetCreator
     */
    public function testGetAssetCreator()
    {
        $this->basicGetTest('assetCreator', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setWebLink
     */
    public function testSetWebLink()
    {
        $this->basicSetTest('webLink', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getWebLink
     */
    public function testGetWebLink()
    {
        $this->basicGetTest('webLink', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setCitation
     */
    public function testSetCitation()
    {
        $this->basicSetTest('citation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getCitation
     */
    public function testGetCitation()
    {
        $this->basicGetTest('citation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setToken
     */
    public function testSetToken()
    {
        $this->basicSetTest('token', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getToken
     */
    public function testGetToken()
    {
        $this->basicGetTest('token', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setOwningUser
     */
    public function testSetOwningUser()
    {
        $this->entitySetTest('owningUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getOwningUser
     */
    public function testGetOwningUser()
    {
        $this->entityGetTest('owningUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setStatus
     */
    public function testSetStatus()
    {
        $this->entitySetTest('status', 'LearningMaterialStatus');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getStatus
     */
    public function testGetStatus()
    {
        $this->entityGetTest('status', 'LearningMaterialStatus');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::setUserRole
     */
    public function testSetUserRole()
    {
        $this->entitySetTest('userRole', 'LearningMaterialUserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearningMaterial::getUserRole
     */
    public function testGetUserRole()
    {
        $this->entityGetTest('userRole', 'LearningMaterialUserRole');
    }
}
