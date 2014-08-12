<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Ilios\CoreBundle\Form\UserType;

class UserControllerTest extends ApiTestCase
{
    public function setup()
    {
        $this->loadFixtures(
            array(
                'Ilios\CoreBundle\Tests\Fixtures\LoadUserData',
                'Ilios\CoreBundle\Tests\Fixtures\LoadSchoolData'
            )
        );
    }

    public function testJsonGetUserAction()
    {
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_user', array('id' => 1))
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']['id']), $content);
        $this->assertEquals(1, $decoded['user']['id'], $content);
        $this->assertEquals('first@example.com', $decoded['user']['email'], $content);
    }

    public function testJsonGetNotFoundUserAction()
    {
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_user', array('id' => 999))
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 404);
    }

    public function testJsonGetUsersAction()
    {
        $client = $this->createJsonRequest(
            'GET',
            $this->getUrl('api_1_get_users')
        );
        
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        
        $this->assertTrue(isset($decoded['users']));
        $this->assertTrue(count($decoded['users']) == 2);

        $this->assertEquals(1, $decoded['users'][0]['id']);
        $this->assertEquals('first@example.com', $decoded['users'][0]['email']);

        $this->assertEquals(2, $decoded['users'][1]['id']);
        $this->assertEquals('second@example.com', $decoded['users'][1]['email']);
    }

    public function testJsonPostUserAction()
    {
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_user'),
            $this->createJsonUser('first', 'last', 'email@example.com', '123456789', 1)
        );

        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testJsonPostUserActionWithBadParameters()
    {
        $badInput = $this->getBadParameters();
        foreach ($badInput as $arr) {
            $client = $this->createJsonRequest(
                'POST',
                $this->getUrl('api_1_post_user'),
                $this->createJsonUser(
                    $arr['first'],
                    $arr['last'],
                    $arr['email'],
                    $arr['campusId'],
                    $arr['primarySchoolId']
                )
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_user'),
            'baddatanotitle'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function test404BadRoute()
    {
        $client = $this->createJsonRequest(
            'POST',
            $this->getUrl('api_1_post_user') . 'badroute'
        );
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testJsonPutUserActionShouldModify()
    {
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_user', array('id' => 1)),
            $this->createJsonUser('changedfirst', 'changedlast', 'changedemail@example.com', 'changedid', 2)
        );

        $this->assertJsonResponse($client->getResponse(), 202);
        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']));
        $this->assertEquals('changedfirst', $decoded['user']['firstName']);
        $this->assertEquals('changedlast', $decoded['user']['lastName']);
        $this->assertEquals('changedemail@example.com', $decoded['user']['email']);
        $this->assertEquals('changedid', $decoded['user']['campusId']);
        $this->assertEquals(2, $decoded['user']['primarySchool']);
    }

    public function testJsonPutUserActionShouldCreate()
    {
        $client = static::createClient();

        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_user', array('id' => 0)),
            $this->createJsonUser('newfirst', 'newlast', 'newemail@example.com', 'wcampusid', 1)
        );

        $this->assertJsonResponse($client->getResponse(), 201, true);
        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['user']));
        $this->assertEquals('newfirst', $decoded['user']['firstName']);
        $this->assertEquals('newlast', $decoded['user']['lastName']);
        $this->assertEquals('newemail@example.com', $decoded['user']['email']);
        $this->assertEquals('wcampusid', $decoded['user']['campusId']);
        $this->assertEquals(1, $decoded['user']['primarySchool']);
    }

    public function testJsonPutExistingUserBadData()
    {
        $badInput = $this->getBadParameters();
        foreach ($badInput as $arr) {
            $client = $this->createJsonRequest(
                'PUT',
                $this->getUrl('api_1_put_user', array('id' => 1)),
                $this->createJsonUser(
                    $arr['first'],
                    $arr['last'],
                    $arr['email'],
                    $arr['campusId'],
                    $arr['primarySchoolId']
                )
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_user', array('id' => 1)),
            'baddatanoinput'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testJsonPutNewUserBadData()
    {
        $faker = \Faker\Factory::create();
        $badInput = $this->getBadParameters();
        foreach ($badInput as $arr) {
            $client = $this->createJsonRequest(
                'PUT',
                $this->getUrl('api_1_put_user', array('id' => 0)),
                $this->createJsonUser(
                    $arr['first'],
                    $arr['last'],
                    $arr['email'],
                    $arr['campusId'],
                    $arr['primarySchoolId']
                )
            );
            $this->assertJsonResponse($client->getResponse(), 400);
        }
        
        $client = $this->createJsonRequest(
            'PUT',
            $this->getUrl('api_1_put_user', array('id' => 0)),
            'baddatanotitle'
        );
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    protected function getBadParameters()
    {
        $faker = \Faker\Factory::create();
        $clean = array(
                'first' => $faker->text,
                'last'  => $faker->text,
                'email' => $faker->email,
                'campusId' => $faker->text(9),
                'primarySchoolId' => 1
        );
        $badInput = array();
        for ($i = 0; $i <= 10; $i++) {
            $badInput[$i] = $clean;
        }
        $badInput[0]['first'] = '';
        $badInput[1]['last'] = '';
        $badInput[2]['email'] = '';
        $badInput[3]['campusId'] = '';
        $badInput[4]['primarySchoolId'] = '';
        $badInput[5]['first'] = str_pad('a', 256, 'a');
        $badInput[6]['last'] = str_pad('a', 256, 'a');
        $badInput[7]['email'] = 'invalid';
        $badInput[8]['campusId'] = '1';
        $badInput[9]['campusId'] = '1010101010';
        $badInput[10]['primarySchoolId'] = 2;


        return $badInput;
    }
    
    /**
     * Create a nicely formatted json user
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $campusId
     * @param integer $primarySchoolId
     * 
     * @return string
     */
    protected function createJsonUser($firstName, $lastName, $email, $campusId, $primarySchoolId)
    {
        $json = '{"' . UserType::NAME . '": {' .
            '"firstName":"' . $firstName . '",' .
            '"lastName":"' . $lastName . '",' .
            '"email":"' . $email . '",' .
            '"ucUid":"' . $campusId . '",' .
            '"primarySchool":"' . $primarySchoolId . '"' .
            '}}';
        return  $json;
    }
}
