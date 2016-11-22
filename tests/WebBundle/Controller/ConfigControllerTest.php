<?php
namespace Tests\WebBundle\Controller;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 * @package Tests\WebBundle\\Controller
 */
class ConfigControllerTest extends WebTestCase
{
    use JsonControllerTest;
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/application/config');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('config', $content);
        $data = $content['config'];
        $this->assertArrayHasKey('maxUploadSize', $data);
        $this->assertGreaterThan(0, $data['maxUploadSize']);
        unset($data['maxUploadSize']);

        $this->assertEquals(
            array(
                'type' => 'form',
                'locale' => 'en',
                'userSearchType' => 'local',
                'apiVersion' => WebIndexFromJson::API_VERSION,
                'trackingEnabled' => false,
            ),
            $data
        );
    }
}
