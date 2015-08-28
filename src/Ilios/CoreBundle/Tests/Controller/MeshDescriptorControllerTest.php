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
        $date = [];
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
    
    public function testFindDescriptorsWithId()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $this->queryForDescriptorsTest($descriptor['id'], $descriptor['id']);
    }
    
    public function testFindDescriptorsWithAnnotation()
    {
        $descriptors = $this->container->get('ilioscore.dataloader.meshDescriptor')->getAll();
        $descriptor = $descriptors[1];
        $this->queryForDescriptorsTest($descriptor['annotation'], $descriptor['id']);
    }
    
    public function testFindDescriptorsByPreviousIndexing()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $previousIndexing = $this->container->get('ilioscore.dataloader.meshPreviousIndexing')->getAll();
        $previousIndexing = array_filter($previousIndexing, function ($arr) use ($descriptor) {
            return $arr['id'] === $descriptor['previousIndexing'];
        });
        $this->queryForDescriptorsTest($previousIndexing[0]['previousIndexing'], $descriptor['id']);

    }
    
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
    
    public function testFindDescriptorByConceptName()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $this->queryForDescriptorsTest($concept['name'], $descriptor['id']);

    }
    
    public function testFindDescriptorByConceptScopeNote()
    {
        $descriptor = $this->container->get('ilioscore.dataloader.meshDescriptor')->getOne();
        $concepts = $this->container->get('ilioscore.dataloader.meshConcept')->getAll();
        $concept = array_filter($concepts, function ($arr) use ($descriptor) {
            return in_array($arr['id'], $descriptor['concepts']);
        })[0];
        $this->queryForDescriptorsTest($concept['scopeNote'], $descriptor['id']);

    }

    public function testPostMeshDescriptor()
    {
        $postData = $this->container->get('ilioscore.dataloader.meshdescriptor')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_meshdescriptors'),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        
        $data = json_decode($response->getContent(), true)['meshDescriptors'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        $createdAt = new DateTime($data['createdAt']);
        unset($data['updatedAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($postData),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $diffC = $now->diff($createdAt);
        $this->assertTrue($diffU->y < 1, 'The updatedAt timestamp is within the last year');
        $this->assertTrue($diffC->y < 1, 'The createdAt timestamp is within the last year');

    }

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

    public function testPutMeshDescriptor()
    {
        $postData = $this->container
            ->get('ilioscore.dataloader.meshdescriptor')
            ->getOne();
        $postData['annotation'] = 'somethign new';

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_meshdescriptors',
                ['id' => $postData['id']]
            ),
            json_encode(['meshDescriptor' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        
        $data = json_decode($response->getContent(), true)['meshDescriptor'];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        unset($data['createdAt']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $data
        );
        $now = new DateTime();
        $diffU = $now->diff($updatedAt);
        $this->assertTrue($diffU->m < 1, 'The updatedAt timestamp is within the last minute');
    }

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
}
