<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterialStatusInterface;
use Exception;

class LearningMaterialData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'firstlmtitle',
            'description' => 'desc1',
            'originalAuthor' => 'author1',
            'userRole' => "1",
            'status' => LearningMaterialStatusInterface::FINALIZED,
            'owningUser' => "1",
            'copyrightRationale' => 'lorem ipsum',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [1],
            'courseLearningMaterials' => ['1', '3'],
            'citation' => 'citation1',
            'mimetype' => 'citation',
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'secondlmtitle',
            'description' => 'desc2',
            'originalAuthor' => 'some name',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::IN_DRAFT,
            'owningUser' => "1",
            'copyrightRationale' => 'lorem ipsum',
            'copyrightPermission' => false,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [2],
            'link' => 'https://example.com/example-file.txt',
            'mimetype' => 'link',
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'thirdlm',
            'description' => 'desc3',
            'originalAuthor' => 'hans dampf',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['2'],
            'courseLearningMaterials' => ['4'],
            'filename' => 'testfile.txt',
            'mimetype' => 'text/plain',
            'filesize' => 1000,
        ];

        $arr[] = [
            'id' => 4,
            'title' => 'fourthlm',
            'description' => 'desc4',
            'originalAuthor' => 'my name',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];

        $arr[] = [
            'id' => 5,
            'title' => 'fifthlm',
            'description' => 'desc5',
            'originalAuthor' => 'someone else',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['3'],
            'courseLearningMaterials' => ['5'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];

        $arr[] = [
            'id' => 6,
            'title' => 'sixthlm',
            'description' => 'desc6',
            'originalAuthor' => 'the author',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['4'],
            'courseLearningMaterials' => ['6'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];

        $arr[] = [
            'id' => 7,
            'title' => 'seventhlm',
            'description' => 'desc7',
            'originalAuthor' => 'someone else',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['5'],
            'courseLearningMaterials' => ['7'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];

        $arr[] = [
            'id' => 8,
            'title' => 'eighthlm',
            'description' => 'desc8',
            'originalAuthor' => 'the original author',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['6'],
            'courseLearningMaterials' => ['8'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];
        $arr[] = [
            'id' => 9,
            'title' => 'ninthlm',
            'description' => 'desc9',
            'originalAuthor' => 'salt',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['7'],
            'courseLearningMaterials' => ['9'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];
        $arr[] = [
            'id' => 10,
            'title' => 'tenthlm',
            'description' => 'desc10',
            'originalAuthor' => 'alpha beta',
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['8', '9'],
            'courseLearningMaterials' => ['10'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 11,
            'title' => 'new learning material',
            'description' => 'desc11',
            'originalAuthor' => 'first last',
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => 'permissions granted by original author',
            'copyrightPermission' => true,
            'citation' => 'lorem ipsum',
            'mimetype' => 'citation',
        ];
    }


    public function createCitation(): array
    {
        return [
            'id' => 11,
            'title' => 'new citation lm',
            'description' => 'lorem ipsum',
            'originalAuthor' => '',
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => 'dinner is ready',
            'mimetype' => 'citation',
        ];
    }


    public function createLink(): array
    {
        return [
            'id' => 11,
            'title' => 'new link lm',
            'description' => 'lorem ipsum',
            'originalAuthor' => 'yes no',
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => 'https://example.com',
            'mimetype' => 'link',
        ];
    }

    /**
     * @throws Exception
     */
    public function createFile(): array
    {
        return [
            'id' => 11,
            'title' => 'new file lm',
            'description' => 'lorem ipsum',
            'originalAuthor' => 'first last',
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'copyrightRationale' => 'i have permission',
            'copyrightPermission' => true,
        ];
    }


    public function createInvalid(): array
    {
        return [
            'id' => 12,
        ];
    }


    public function createInvalidCitation(): array
    {
        return [
            'id' => 11,
            'title' => 'invalid citation',
            'description' => 'lorem ipsum',
            'originalAuthor' => 'joe shmoe',
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => str_repeat('0123456789', 1000), // too long
        ];
    }


    public function createInvalidLink(): array
    {
        return [
            'id' => 11,
            'title' => 'invalid link',
            'description' => 'lorem ipsum',
            'originalAuthor' => 'first last',
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => 'https://' . str_repeat('0123456789', 1000) . '.org', // too long
        ];
    }

    public function getDtoClass(): string
    {
        return LearningMaterialDTO::class;
    }
}
