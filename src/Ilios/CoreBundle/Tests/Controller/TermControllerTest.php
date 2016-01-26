<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Term controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class TermControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadVocabularyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadTermData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'courses',
            'sessions',
            'programYears'
        ];
    }

    /**
     * @group controllers_b
     */
    public function testGetTerm()
    {
        $term = $this->container
            ->get('ilioscore.dataloader.term')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_terms',
                ['id' => $term['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($term),
            json_decode($response->getContent(), true)['terms'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllTerms()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_terms'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.term')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['terms']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostTerm()
    {
        $data = $this->container->get('ilioscore.dataloader.term')
            ->create();
        unset($data['courses']);
        unset($data['sessions']);
        unset($data['programYears']);
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_terms'),
            json_encode(['term' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['terms'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadTerm()
    {
        $invalidTerm = $this->container
            ->get('ilioscore.dataloader.term')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_terms'),
            json_encode(['term' => $invalidTerm]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutTerm()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.term')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courses']);
        unset($postData['sessions']);
        unset($postData['programYears']);
        unset($postData['children']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_terms',
                ['id' => $data['id']]
            ),
            json_encode(['term' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['term']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteTerm()
    {
        $term = $this->container
            ->get('ilioscore.dataloader.term')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_terms',
                ['id' => $term['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_terms',
                ['id' => $term['id']]
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
    public function testTermNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_terms', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[courses][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(4, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[3]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[sessions]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(4, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[3]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[sessionTypes]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(6, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[2]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[3]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[4]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[5]
            ),
            $data[5]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructor()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[instructors][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(4, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[3]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructorGroup()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[instructorGroups]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(4, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[3]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByLearningMaterial()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[learningMaterials]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(4, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[2]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[5]
            ),
            $data[3]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCompetency()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[competencies]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(6, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[2]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[3]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[4]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[5]
            ),
            $data[5]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMeshDescriptor()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[meshDescriptors]' => ['abc1', 'abc2', 'abc3']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(6, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[2]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[3]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[4]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[5]
            ),
            $data[5]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByProgram()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[programs][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySchool()
    {
        $terms = $this->container->get('ilioscore.dataloader.term')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_terms', ['filters[schools][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['terms'];
        $this->assertEquals(6, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $terms[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[2]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[3]
            ),
            $data[3]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[4]
            ),
            $data[4]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $terms[5]
            ),
            $data[5]
        );
    }
}
