<?php

namespace App\Tests\Entity\DTO;

use App\Entity\DTO\LearningMaterialDTO;
use App\Tests\TestCase;

class LearningMaterialDTOTest extends TestCase
{
    /**
     * @var LearningMaterialDTO $dto
     */
    protected $dto;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dto = new LearningMaterialDTO(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->dto);
        parent::tearDown();
    }


    /**
     * @covers \App\Entity\DTO\LearningMaterialDTO::clearMaterial
     */
    public function testClearMaterial()
    {
        $this->dto->absoluteFileUri = 'https://ilios.demo.edu/lm/1234567890';
        $this->dto->citation = 'Lorem Ipsum';
        $this->dto->copyrightPermission = true;
        $this->dto->copyrightRationale = 'this is mine.';
        $this->dto->courseLearningMaterials = [1, 2, 3];
        $this->dto->description = 'LM Description';
        $this->dto->filesize = 1100000;
        $this->dto->filename = 'my.txt';
        $this->dto->id = 1234567890;
        $this->dto->link = 'https://doi.org/10.1109/5.771073';
        $this->dto->mimetype = 'text/plain';
        $this->dto->originalAuthor = 'Joe Doe';
        $this->dto->owningUser = 1;
        $this->dto->sessionLearningMaterials = [4,5,6];
        $this->dto->status = 1;
        $this->dto->title = 'My Material';
        $this->dto->token = 'aaabbbcccdddeee';
        $this->dto->uploadDate = '12/12/2012';
        $this->dto->userRole = 1;

        $props = array_keys(get_object_vars($this->dto));
        foreach ($props as $prop) {
            $this->assertNotNull($this->dto->$prop);
        }

        $this->dto->clearMaterial();
        foreach ($props as $prop) {
            if (in_array($prop, [
                'absoluteFileUri',
                'citation',
                'copyrightRationale',
                'description',
                'filename',
                'filesize',
                'link',
                'mimetype',
                'originalAuthor',
                'token'
            ])) {
                $this->assertNull($this->dto->$prop);
            } else {
                $this->assertNotNull($this->dto->$prop);
            }
        }
    }
}
