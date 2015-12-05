<?php
namespace Ilios\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;

use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;

/**
 * Download controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class DownloadControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData'
        ]);
    }

    public function tearDown()
    {
    }

    /**
     * @group controllers_a
     */
    public function testDownloadLearningMaterial()
    {
        $client = $this->createClient();
        $learningMaterials = $client->getContainer()
            ->get('ilioscore.dataloader.learningmaterial')
            ->getAll();
        $fileLearningMaterials = array_filter($learningMaterials, function ($arr) {
            return !empty($arr['filesize']);
        });
        $learningMaterial = array_values($fileLearningMaterials)[0];
        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                'get_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $client->request(
            'GET',
            $data['absoluteFileUri']
        );
        
        $response = $client->getResponse();
        
        $this->assertEquals(CODES::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $learningMaterialLoaderPath = realpath(__DIR__ . '/../Fixture/LoadLearningMaterialData.php');
        $this->assertEquals(file_get_contents($learningMaterialLoaderPath), $response->getContent());
        
    }

    /**
     * @group controllers_a
     */
    public function testBadLearningMaterialToken()
    {
        $client = $this->createClient();
        //sending bad hash
        $client->request(
            'GET',
            '/lm/a7a8e202e9655ab81155c4c3e52b95098fcaa1c975f63f0327b467a981f6428f'
        );
        
        $response = $client->getResponse();
        $this->assertEquals(
            CODES::HTTP_NOT_FOUND,
            $response->getStatusCode()
        );
    }
}
