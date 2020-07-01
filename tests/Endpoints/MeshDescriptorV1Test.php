<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Mesh Descriptor API v1 endpoint Test.
 * @group api_3
 */
class MeshDescriptorV1Test extends V1ReadEndpointTest
{
    protected $testName = 'meshDescriptors';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadMeshConceptData',
            'App\Tests\Fixture\LoadMeshTreeData',
            'App\Tests\Fixture\LoadMeshPreviousIndexingData',
            'App\Tests\Fixture\LoadMeshQualifierData',
            'App\Tests\Fixture\LoadMeshTermData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $meshDescriptorData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1MeshDescriptor = $this->getOne($endpoint, $responseKey, $meshDescriptorData['id']);
        $v3MeshDescriptor = $this->getOne($endpoint, $responseKey, $meshDescriptorData['id'], 'v3');
        $this->assertEquals($v3MeshDescriptor['id'], $v1MeshDescriptor['id']);
        $this->assertEquals($v3MeshDescriptor['name'], $v1MeshDescriptor['name']);
        $this->assertEquals($v3MeshDescriptor['annotation'], $v1MeshDescriptor['annotation']);
        $this->assertEquals($v3MeshDescriptor['createdAt'], $v1MeshDescriptor['createdAt']);
        $this->assertEquals($v3MeshDescriptor['updatedAt'], $v1MeshDescriptor['updatedAt']);
        $this->assertEquals($v3MeshDescriptor['courses'], $v1MeshDescriptor['courses']);
        $this->assertEquals($v3MeshDescriptor['sessions'], $v1MeshDescriptor['sessions']);
        $this->assertEquals($v3MeshDescriptor['concepts'], $v1MeshDescriptor['concepts']);
        $this->assertEquals($v3MeshDescriptor['trees'], $v1MeshDescriptor['trees']);
        $this->assertEquals(
            $v3MeshDescriptor['sessionLearningMaterials'],
            $v1MeshDescriptor['sessionLearningMaterials']
        );
        $this->assertEquals(
            $v3MeshDescriptor['courseLearningMaterials'],
            $v1MeshDescriptor['courseLearningMaterials']
        );
        $this->assertEquals($v3MeshDescriptor['previousIndexing'], $v1MeshDescriptor['previousIndexing']);
        $this->assertEquals($v3MeshDescriptor['deleted'], $v1MeshDescriptor['deleted']);
        $this->assertCount(
            count(
                array_merge(
                    $v3MeshDescriptor['sessionObjectives'],
                    $v3MeshDescriptor['courseObjectives'],
                    $v3MeshDescriptor['programYearObjectives'],
                )
            ),
            $v1MeshDescriptor['objectives']
        );
    }
}
