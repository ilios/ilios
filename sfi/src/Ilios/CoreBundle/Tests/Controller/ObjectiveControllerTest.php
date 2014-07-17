<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Ilios\CoreBundle\Form\ObjectiveType;

class ObjectiveControllerTest extends ApiTestCase
{

    public function testJsonGetObjectiveAction()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_objective', array('id' => 1))
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['objective']['id']), $content);
        $this->assertEquals(1, $decoded['objective']['id'], $content);
        $this->assertEquals('one', $decoded['objective']['title'], $content);
    }

    public function testJsonGetNotFoundObjectiveAction()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_objective', array('id' => 999))
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testJsonGetObjectivesAction()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_objectives')
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        
        $this->assertTrue(isset($decoded['objectives']));
        $this->assertTrue(count($decoded['objectives']) == 2);

        $this->assertEquals(1, $decoded['objectives'][0]['id']);
        $this->assertEquals('one', $decoded['objectives'][0]['title']);

        $this->assertEquals(2, $decoded['objectives'][1]['id']);
        $this->assertEquals('two', $decoded['objectives'][1]['title']);
    }

    public function testJsonPostObjectiveAction()
    {
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_objective'),
            $this->createJsonObjective('Some good data')
        );

        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testJsonPostObjectiveActionWithBadParameters()
    {
        $badTitles = array(
            '',
            'a',
            str_pad('a', 256, 'a')
        );
        foreach ($badTitles as $title) {
            $client = $this->createJsonRequest(
                'POST',
                $this->getUrl('api_1_post_objective'),
                $this->createJsonObjective($title)
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_objective'),
            'baddatanotitle'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function test404BadRoute()
    {
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_objective') . 'badroute'
        );
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testJsonPutObjectiveActionShouldModify()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_objective', array('id' => 1)),
            $this->createJsonObjective('ChangedTitle')
        );

        $this->assertJsonResponse($client->getResponse(), 202);
        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['objective']));
        $this->assertEquals('ChangedTitle', $decoded['objective']['title']);
    }

    public function testJsonPutObjectiveActionShouldCreate()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        $client = static::createClient();

        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_objective', array('id' => 0)),
            $this->createJsonObjective('newtitle')
        );

        $this->assertJsonResponse($client->getResponse(), 201, true);
        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['objective']));
        $this->assertEquals('newtitle', $decoded['objective']['title']);
    }

    public function testJsonPutExistingObjectiveBadData()
    {
        $this->loadFixtures(array('Ilios\CoreBundle\Tests\Fixtures\LoadObjectiveData'));
        $badTitles = array(
            '',
            'a',
            str_pad('a', 256, 'a')
        );
        foreach ($badTitles as $title) {
            $client = $this->createJsonRequest(
                'PUT',
                $this->getUrl('api_1_put_objective', array('id' => 1)),
                $this->createJsonObjective($title)
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_objective', array('id' => 1)),
            'baddatanotitle'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testJsonPutNewObjectiveBadData()
    {
        $badTitles = array(
            '',
            'a',
            str_pad('a', 256, 'a')
        );
        foreach ($badTitles as $title) {
            $client = $this->createJsonRequest(
                'PUT',
                $this->getUrl('api_1_put_objective', array('id' => 0)),
                $this->createJsonObjective($title)
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_objective', array('id' => 0)),
            'baddatanotitle'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }
    
    /**
     * Create a nicely formatted json objective
     * 
     * @param string $title
     * @return string
     */
    protected function createJsonObjective($title)
    {
        $json = '{"' . ObjectiveType::NAME . '": {"title":"' . $title . '"}}';
        return  $json;
    }
}
