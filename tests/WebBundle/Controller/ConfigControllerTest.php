<?php
namespace Tests\WebBundle\Controller;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Fixture\LoadApplicationConfigData;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 */
class ConfigControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            LoadApplicationConfigData::class,
        ]);
    }

    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/application/config');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
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

    public function testEnvOverrideForConfigItem()
    {
        $client = static::createClient();
        $_SERVER['ILIOS_ENABLE_TRACKING'] = true;
        $_SERVER['ILIOS_TRACKING_CODE'] = '123-code!';

        $client->request('GET', '/application/config');

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
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
                'trackingEnabled' => true,
                'trackingCode' => '123-code!',
            ),
            $data
        );

        unset($_SERVER['ILIOS_ENABLE_TRACKING']);
        unset($_SERVER['ILIOS_TRACKING_CODE']);
    }
}
