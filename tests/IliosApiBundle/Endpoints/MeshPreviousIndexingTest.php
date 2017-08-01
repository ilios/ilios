<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\MeshDescriptorData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshPreviousIndexing API endpoint Test.
 * @group api_4
 */
class MeshPreviousIndexingTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshPreviousIndexings';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshPreviousIndexingData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
            'Tests\CoreBundle\Fixture\LoadMeshQualifierData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'previousIndexing' => ['previousIndexing', $this->getFaker()->text],
            'descriptor' => ['descriptor', 'abc3'],
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
            'descriptor' => [[1], ['descriptor' => 'abc2']],
            'previousIndexing' => [[1], ['previousIndexing' => 'second previous indexing']],
        ];
    }

    /**
     * We need to create additional descriptors to
     * go with each new PreviousIndex
     * @inheritdoc
     */
    public function testPostMany()
    {
        $count = 51;
        $descriptorDataLoader = $this->container->get(MeshDescriptorData::class);
        $descriptors = $descriptorDataLoader->createMany($count);
        $savedDescriptors = $this->postMany('meshdescriptors', 'meshDescriptors', $descriptors);

        $dataLoader = $this->getDataLoader();
        $data = [];

        foreach ($savedDescriptors as $i => $descriptor) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['descriptor'] = $descriptor['id'];

            $data[] = $arr;
        }

        $this->postManyTest($data);
    }
}
