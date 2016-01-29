<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * MeshDescriptor controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class MeshDescriptorControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshConceptData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshTreeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshPreviousIndexingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshQualifierData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshSemanticTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshTermData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testGetMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['meshDescriptors'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($meshDescriptor),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');
    }

    /**
     * @group controllers_a
     */
    public function testGetAllMeshDescriptors()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['meshDescriptors'];
        $now = new DateTime();
        $data = [];
        foreach ($responses as $response) {
            $updatedAt = new DateTime($response['updatedAt']);
            $createdAt = new DateTime($response['createdAt']);
            unset($response['updatedAt']);
            unset($response['createdAt']);
            $diffU = $now->diff($updatedAt);
            $diffC = $now->diff($createdAt);
            $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
            $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');
            $data[] = $response;
        }
        $this->assertEquals(
            $this->mockSerialize(
                $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll()
            ),
            $data
        );
    }

    protected function queryForDescriptorsTest($q, $expectedDescriptorId)
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', array('q' => $q)),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('meshDescriptors', $result), var_export($result, true));
        $descriptors = $result['meshDescriptors'];
        $this->assertEquals(1, count($descriptors));
        $this->assertEquals(
            $expectedDescriptorId,
            $descriptors[0]['id']
        );
    }

    /**
     * @group controllers_a
     */
    public function testFindDescriptorsWithId()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $this->queryForDescriptorsTest($descriptor['id'], $descriptor['id']);
    }

    /**
     * @group controllers_a
     */
    public function testFindDescriptorsWithName()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $this->queryForDescriptorsTest($descriptor['name'], $descriptor['id']);
    }

    /**
     * @group controllers_a
     */
    public function testFindDescriptorsWithAnnotation()
    {
        $descriptors = $this->container->get('ilioscore.dataloader.meshDescriptor')->getAll();
        $descriptor = $descriptors[1];
        $this->queryForDescriptorsTest($descriptor['annotation'], $descriptor['id']);
    }

    /**
     * @group controllers
     */
    public function testFindDescriptorsByPreviousIndexing()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $previousIndexing = $this->container->get('ilioscore.dataloader.meshPreviousIndexing')->getAll();
        $previousIndexing = array_filter($previousIndexing, function ($arr) use ($descriptor) {
            return $arr['id'] == $descriptor['previousIndexing'];
        });
        $this->queryForDescriptorsTest($previousIndexing[0]['previousIndexing'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testFindDescriptorBySemanticTypeName()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $semanticTypes = $this->container->get('ilioscore.dataloader.meshSemanticType')->getAll();
        $semanticType = array_filter($semanticTypes, function ($arr) use ($concept) {
            return in_array($arr['id'], $concept['semanticTypes']);
        })[0];
        $this->queryForDescriptorsTest($semanticType['name'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testFindDescriptorByTermName()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $terms = $this->container->get('ilioscore.dataloader.meshTerm')->getAll();
        $term = array_filter($terms, function ($arr) use ($concept) {
            return in_array($arr['id'], $concept['terms']);
        })[0];
        $this->queryForDescriptorsTest($term['name'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testFindDescriptorByConceptName()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $this->queryForDescriptorsTest($concept['name'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testFindDescriptorByConceptScopeNote()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $this->queryForDescriptorsTest($concept['scopeNote'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testFindDescriptorByConceptCasn()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $this->queryForDescriptorsTest($concept['casn1Name'], $descriptor['id']);

    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptor()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')
            ->create();


        $postData = $data;
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('meshDescriptors', $result), var_export($result, true));

        $responseData = $result['meshDescriptors'][0];
        $updatedAt = new DateTime($responseData['updatedAt']);
        $createdAt = new DateTime($responseData['createdAt']);
        unset($responseData['updatedAt']);
        unset($responseData['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $responseData
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');

    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorCourse()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['courses'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_courses',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['courses'][0];
            $this->assertTrue(in_array($newId, $data['meshDescriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorSession()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['sessions'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_sessions',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['sessions'][0];
            $this->assertTrue(in_array($newId, $data['meshDescriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['objectives'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_objectives',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['objectives'][0];
            $this->assertTrue(in_array($newId, $data['meshDescriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorConcepts()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['concepts'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_meshconcepts',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['meshConcepts'][0];
            $this->assertTrue(in_array($newId, $data['descriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorQualifier()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['qualifiers'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_meshqualifiers',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['meshQualifiers'][0];
            $this->assertTrue(in_array($newId, $data['descriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorSessionLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['sessionLearningMaterials'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_sessionlearningmaterials',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['sessionLearningMaterials'][0];
            $this->assertTrue(in_array($newId, $data['meshDescriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostMeshDescriptorCourseLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.meshdescriptor')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['trees']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['meshDescriptors'][0]['id'];
        foreach ($postData['courseLearningMaterials'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_courselearningmaterials',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['courseLearningMaterials'][0];
            $this->assertTrue(in_array($newId, $data['meshDescriptors']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostBadMeshDescriptor()
    {
        $invalidMeshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $invalidMeshDescriptor]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutMeshDescriptor()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne();
        //mutate something
        $data['annotation'] = 'somethign new';

        $postData = $data;
        unset($postData['trees']);


        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshdescriptors',
                ['id' => $data['id']]
            ),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $responseData = json_decode($response->getContent(), true)['meshDescriptor'];
        $updatedAt = new DateTime($responseData['updatedAt']);
        unset($responseData['updatedAt']);
        unset($responseData['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $responseData,
            var_export($responseData, true)
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $this->assertTrue($diffU->m < 1, 'The updatedAt timestamp is within the last minute');
    }

    /**
     * @group controllers
     */
    public function testDeleteMeshDescriptor()
    {
        $meshDescriptor = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_meshdescriptors',
                ['id' => $meshDescriptor['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testMeshDescriptorNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_meshdescriptors', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $meshDescriptors = $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', ['filters[sessions][]' => 3]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            unset($arr['createdAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['meshDescriptors']);

        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $meshDescriptors = $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', ['filters[courses]' => [2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            unset($arr['createdAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['meshDescriptors']);

        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByLearningMaterial()
    {
        $meshDescriptors = $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', ['filters[learningMaterials]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            unset($arr['createdAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['meshDescriptors']);

        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByTopic()
    {
        $meshDescriptors = $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', ['filters[topics]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            unset($arr['createdAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['meshDescriptors']);

        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $meshDescriptors = $this->container->get('ilioscore.dataloader.meshdescriptor')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_meshdescriptors', ['filters[sessionTypes]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            unset($arr['createdAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['meshDescriptors']);

        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $meshDescriptors[2]
            ),
            $data[2]
        );
    }
}
