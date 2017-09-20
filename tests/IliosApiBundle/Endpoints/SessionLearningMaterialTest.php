<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionLearningMaterial API endpoint Test.
 * @group api_1
 */
class SessionLearningMaterialTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName = 'sessionLearningMaterials';

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
    public function readOnlyPropertiesToTest()
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
            'notRequired' => [[1, 2, 3, 4, 5, 6, 7], ['required' => false]],
            'publicNotes' => [[1, 2, 3, 4, 5, 6, 7], ['publicNotes' => true]],
            'notPublicNotes' => [[0], ['publicNotes' => false]],
            'session' => [[0], ['session' => 1]],
            'learningMaterial' => [[0], ['learningMaterial' => 1]],
            'meshDescriptors' => [[1, 2, 3, 4, 5, 6, 7], ['meshDescriptors' => ['abc2']]],
            'position' => [[1, 2, 3, 4, 5, 6, 7], ['position' => 0]],
        ];
    }

    protected function compareData(array $expected, array $result)
    {
        // TOTAL GROSSNESS!
        // get the expected fixture from the repo, then correct
        // the expected start- and end-dates by overriding them.
        // @todo load fixtures upstream without regenerating them [ST 2017/09/14].
        $ref = 'sessionLearningMaterials'.$expected['id'];
        if ($this->fixtures->hasReference($ref)) {
            $fixture = $this->fixtures->getReference($ref);
            $startDate = $fixture->getStartDate();
            $endDate = $fixture->getEndDate();
            $expected['startDate'] = is_null($startDate) ? null : date_format($startDate, 'c');
            $expected['endDate'] = is_null($endDate) ? null : date_format($endDate, 'c');
        }

        if (is_null($expected['startDate'])) {
            $this->assertFalse(array_key_exists('startDate', $result));
            unset($expected['startDate']);
        }

        if (is_null($expected['endDate'])) {
            $this->assertFalse(array_key_exists('endDate', $result));
            unset($expected['endDate']);
        }

        parent::compareData($expected, $result);
    }
}
