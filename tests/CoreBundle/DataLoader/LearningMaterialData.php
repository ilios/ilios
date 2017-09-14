<?php

namespace Tests\CoreBundle\DataLoader;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

class LearningMaterialData extends AbstractDataLoader
{

    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => 'firstlm' . $this->faker->text(30),
            'description' => 'desc1',
            'originalAuthor' => 'author1',
            'userRole' => "1",
            'status' => LearningMaterialStatusInterface::FINALIZED,
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [1],
            'courseLearningMaterials' => ['1', '3'],
            'citation' => 'citation1',
            'mimetype' => 'citation',
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'secondlm' . $this->faker->text(30),
            'description' => 'desc2' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::IN_DRAFT,
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => false,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [2],
            'link' => 'http://example.com/example-file.txt',
            'mimetype' => 'link',
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'thirdlm',
            'description' => 'desc3' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['2'],
            'courseLearningMaterials' => ['4'],
            'filename' => 'testfile.txt',
            'mimetype' => 'text/plain',
            'filesize' => 1000
        );

        $arr[] = array(
            'id' => 4,
            'title' => 'fourthlm',
            'description' => 'desc4' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );

        $arr[] = array(
            'id' => 5,
            'title' => 'fifthlm',
            'description' => 'desc5' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['3'],
            'courseLearningMaterials' => ['5'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );

        $arr[] = array(
            'id' => 6,
            'title' => 'sixthlm',
            'description' => 'desc6' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['4'],
            'courseLearningMaterials' => ['6'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );

        $arr[] = array(
            'id' => 7,
            'title' => 'seventhlm',
            'description' => 'desc7' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['5'],
            'courseLearningMaterials' => ['7'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );

        $arr[] = array(
            'id' => 8,
            'title' => 'eighthlm',
            'description' => 'desc8' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['6'],
            'courseLearningMaterials' => ['8'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );
        $arr[] = array(
            'id' => 9,
            'title' => 'ninthlm',
            'description' => 'desc9' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['7'],
            'courseLearningMaterials' => ['9'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );
        $arr[] = array(
            'id' => 10,
            'title' => 'tenthlm',
            'description' => 'desc10' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => LearningMaterialStatusInterface::REVISED,
            'owningUser' => "1",
            'copyrightRationale' => 'i own it',
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['8'],
            'courseLearningMaterials' => ['10'],
            'filename' => 'testfile.pdf',
            'mimetype' => 'application/pdf',
            'filesize' => 1000
        );

        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return array(
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'citation' => $this->faker->text,
            'mimetype' => 'citation',
        );
    }

    /**
     * @return array
     */
    public function createCitation()
    {
        return array(
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text,
            'mimetype' => 'citation',
        );
    }

    /**
     * @return array
     */
    public function createLink()
    {
        return array(
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => $this->faker->url,
            'mimetype' => 'link',
        );
    }

    /**
     * @throws \Exception
     */
    public function createFile()
    {
        return array(
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
        );
    }

    /**
     * @return array
     */
    public function createInvalid()
    {
        return [
            'id' => 12
        ];
    }

    /**
     * @return array
     */
    public function createInvalidCitation()
    {
        return [
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text(1000), // too long
        ];
    }

    /**
     * @return array
     */
    public function createInvalidLink()
    {
        return array(
            'id' => 11,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => $this->faker->text(600), // too long
        );
    }

    public function createInvalidFile()
    {
        throw new \Exception('Not implemented yet');
    }
}
