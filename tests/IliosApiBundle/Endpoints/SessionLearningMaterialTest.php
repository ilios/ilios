<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionLearningMaterial API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class SessionLearningMaterialTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessionlearningmaterials';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'notes' => ['notes', $this->getFaker()->text],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', true],
            'session' => ['session', 3],
            'learningMaterial' => ['learningMaterial', 3],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'position' => ['position', $this->getFaker()->randomDigit],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'notes' => [[1], ['notes' => 'second slm']],
            'required' => [[0], ['required' => true]],
            'notRequired' => [[1], ['required' => false]],
            'publicNotes' => [[1], ['publicNotes' => true]],
            'notPublicNotes' => [[0], ['publicNotes' => false]],
            'session' => [[0], ['session' => 1]],
            'learningMaterial' => [[0], ['learningMaterial' => 1]],
//            'meshDescriptors' => [[1], ['meshDescriptors' => ['abc2']]],
            'position' => [[1], ['position' => 0]],
        ];
    }
}
