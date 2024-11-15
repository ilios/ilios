<?php

declare(strict_types=1);

namespace App\Tests\Entity\DTO;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\DTO\LearningMaterialDTO;
use App\Tests\TestCase;
use DateTime;

/**
 * Class LearningMaterialDTOTest
 * @package App\Tests\Entity\DTO
 */
#[Group('model')]
#[CoversClass(LearningMaterialDTO::class)]
class LearningMaterialDTOTest extends TestCase
{
    protected LearningMaterialDTO $dto;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dto = new LearningMaterialDTO(
            1,
            'title',
            null,
            new DateTime(),
            null,
            null,
            true,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    protected function tearDown(): void
    {
        unset($this->dto);
        parent::tearDown();
    }

    public function testClearMaterial(): void
    {
        $this->dto->absoluteFileUri = 'https://ilios.demo.edu/lm/1234567890';
        $this->dto->citation = 'Lorem Ipsum';
        $this->dto->copyrightPermission = true;
        $this->dto->copyrightRationale = 'this is mine.';
        $this->dto->courseLearningMaterials = ['1', '2', '3'];
        $this->dto->description = 'LM Description';
        $this->dto->filesize = 1100000;
        $this->dto->filename = 'my.txt';
        $this->dto->id = 1234567890;
        $this->dto->link = 'https://doi.org/10.1109/5.771073';
        $this->dto->mimetype = 'text/plain';
        $this->dto->originalAuthor = 'Joe Doe';
        $this->dto->owningUser = 1;
        $this->dto->sessionLearningMaterials = ['4', '5', '6'];
        $this->dto->status = 1;
        $this->dto->title = 'My Material';
        $this->dto->token = 'aaabbbcccdddeee';
        $this->dto->uploadDate = new DateTime('12/12/2012');
        $this->dto->userRole = 1;
        $this->dto->relativePath = 'path/to/material';

        $props = array_keys(get_object_vars($this->dto));
        foreach ($props as $prop) {
            $this->assertNotNull($this->dto->$prop, "{$prop} is set");
        }

        $this->dto->clearMaterial();
        foreach ($props as $prop) {
            if (
                in_array($prop, [
                'absoluteFileUri',
                'citation',
                'copyrightRationale',
                'description',
                'filename',
                'filesize',
                'link',
                'mimetype',
                'originalAuthor',
                'token',
                'relativePath',
                ])
            ) {
                $this->assertNull($this->dto->$prop, "{$prop} is cleared");
            } else {
                $this->assertNotNull($this->dto->$prop, "{$prop} is not cleared");
            }
        }
    }
}
