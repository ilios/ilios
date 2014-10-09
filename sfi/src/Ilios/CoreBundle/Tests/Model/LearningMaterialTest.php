<?php
namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\LearningMaterial;
use Mockery as m;

/**
 * Tests for Model LearningMaterial
 */
class LearningMaterialTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getLearningMaterialId
     */
    public function testGetLearningMaterialId()
    {
        $this->basicGetTest('learningMaterialId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setMimeType
     */
    public function testSetMimeType()
    {
        $this->basicSetTest('mimeType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getMimeType
     */
    public function testGetMimeType()
    {
        $this->basicGetTest('mimeType', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setRelativeFileSystemLocation
     */
    public function testSetRelativeFileSystemLocation()
    {
        $this->basicSetTest('relativeFileSystemLocation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getRelativeFileSystemLocation
     */
    public function testGetRelativeFileSystemLocation()
    {
        $this->basicGetTest('relativeFileSystemLocation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setFilename
     */
    public function testSetFilename()
    {
        $this->basicSetTest('filename', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getFilename
     */
    public function testGetFilename()
    {
        $this->basicGetTest('filename', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setFilesize
     */
    public function testSetFilesize()
    {
        $this->basicSetTest('filesize', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getFilesize
     */
    public function testGetFilesize()
    {
        $this->basicGetTest('filesize', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setCopyrightOwnership
     */
    public function testSetCopyrightOwnership()
    {
        $this->basicSetTest('copyrightOwnership', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getCopyrightOwnership
     */
    public function testGetCopyrightOwnership()
    {
        $this->basicGetTest('copyrightOwnership', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setCopyrightRationale
     */
    public function testSetCopyrightRationale()
    {
        $this->basicSetTest('copyrightRationale', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getCopyrightRationale
     */
    public function testGetCopyrightRationale()
    {
        $this->basicGetTest('copyrightRationale', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setUploadDate
     */
    public function testSetUploadDate()
    {
        $this->basicSetTest('uploadDate', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getUploadDate
     */
    public function testGetUploadDate()
    {
        $this->basicGetTest('uploadDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setAssetCreator
     */
    public function testSetAssetCreator()
    {
        $this->basicSetTest('assetCreator', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getAssetCreator
     */
    public function testGetAssetCreator()
    {
        $this->basicGetTest('assetCreator', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setWebLink
     */
    public function testSetWebLink()
    {
        $this->basicSetTest('webLink', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getWebLink
     */
    public function testGetWebLink()
    {
        $this->basicGetTest('webLink', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setCitation
     */
    public function testSetCitation()
    {
        $this->basicSetTest('citation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getCitation
     */
    public function testGetCitation()
    {
        $this->basicGetTest('citation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setToken
     */
    public function testSetToken()
    {
        $this->basicSetTest('token', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getToken
     */
    public function testGetToken()
    {
        $this->basicGetTest('token', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setOwningUser
     */
    public function testSetOwningUser()
    {
        $this->modelSetTest('owningUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getOwningUser
     */
    public function testGetOwningUser()
    {
        $this->modelGetTest('owningUser', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setStatus
     */
    public function testSetStatus()
    {
        $this->modelSetTest('status', 'LearningMaterialStatus');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getStatus
     */
    public function testGetStatus()
    {
        $this->modelGetTest('status', 'LearningMaterialStatus');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::setUserRole
     */
    public function testSetUserRole()
    {
        $this->modelSetTest('userRole', 'LearningMaterialUserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Model\LearningMaterial::getUserRole
     */
    public function testGetUserRole()
    {
        $this->modelGetTest('userRole', 'LearningMaterialUserRole');
    }
}
