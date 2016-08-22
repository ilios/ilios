<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Objective controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ObjectiveControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData'
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
     * @group controllers_b
     */
    public function testGetObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_objectives',
                ['id' => $objective['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($objective),
            json_decode($response->getContent(), true)['objectives'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllObjectives()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_objectives'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.objective')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['objectives']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.objective')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['objectives'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostCourseObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.objective')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['objectives'][0]['id'];
        foreach ($postData['courses'] as $courseId) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_courses',
                    ['id' => $courseId]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['courses'][0];
            $this->assertTrue(in_array($newId, $data['objectives']));
        }
    }

    /**
     * @group controllers_b
     */
    public function testPostProgramYearObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.objective')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['objectives'][0]['id'];
        foreach ($postData['programYears'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_programyears',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['programYears'][0];
            $this->assertTrue(in_array($newId, $data['objectives']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostSessionObjective()
    {
        $data = $this->container->get('ilioscore.dataloader.objective')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['objectives'][0]['id'];
        foreach ($postData['sessions'] as $sessionId) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_sessions',
                    ['id' => $sessionId]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['sessions'][0];
            $this->assertTrue(in_array($newId, $data['objectives']));
        }
    }


    /**
     * Ideally, we'd be testing the "purified textarea" form type by itself.
     * However, the framework currently does not provide boilerplate to roll container-aware form test.
     * We'd need a hybrid between <code>KernelTestCase</code> and <code>TypeTestCase</code>.
     * @link  http://symfony.com/doc/current/cookbook/testing/doctrine.html
     * @link http://symfony.com/doc/current/cookbook/form/unit_testing.html
     * To keep things easy, I bolted this test on to this controller test for the time being.
     * @todo Revisit occasionally and check if future versions of Symfony have addressed this need. [ST 2015/10/19]
     *
     * @dataProvider testInputSanitationTestProvider
     *
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     *
     * @group controllers
     */
    public function testInputSanitation($input, $output)
    {
        $postData = $this->container->get('ilioscore.dataloader.objective')
            ->create();
        $postData['title'] = $input;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            json_decode($response->getContent(), true)['objectives'][0]['title'],
            $output,
            $response->getContent()
        );
    }

    /**
     * @return array
     */
    public function testInputSanitationTestProvider()
    {
        return [
            ['foo', 'foo'],
            ['<p>foo</p>', '<p>foo</p>'],
            ['<ul><li>foo</li></ul>', '<ul><li>foo</li></ul>'],
            ['<script>alert("hello");</script><p>foo</p>', '<p>foo</p>'],
            [
                '<a href="https://iliosproject.org" target="_blank">Ilios</a>',
                '<a href="https://iliosproject.org">Ilios</a>'
            ],
            ['<u>NOW I CRY</u>', '<u>NOW I CRY</u>'],
        ];
    }

    /**
     * Assert that a POST request fails if form validation fails due to input sanitation.
     *
     * @group controllers
     */
    public function testInputSanitationFailure()
    {
        $postData = $this->container->get('ilioscore.dataloader.objective')
            ->create();
        // this markup will get stripped out, leaving a blank string as input.
        // which in turn will cause the form validation to fail.
        $postData['title'] = '<iframe></iframe>';
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @group controllers
     */
    public function testPostBadObjective()
    {
        $invalidObjective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_objectives'),
            json_encode(['objective' => $invalidObjective]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutObjective()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_objectives',
                ['id' => $data['id']]
            ),
            json_encode(['objective' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['objective']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteObjective()
    {
        $objective = $this->container
            ->get('ilioscore.dataloader.objective')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_objectives',
                ['id' => $objective['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_objectives',
                ['id' => $objective['id']]
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
    public function testObjectiveNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_objectives', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
