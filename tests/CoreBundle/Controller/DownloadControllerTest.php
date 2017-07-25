<?php
namespace Tests\CoreBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * Download controller Test.
 * @group other
 */
class DownloadControllerTest extends WebTestCase
{
    use JsonControllerTest;

    public function setUp()
    {
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadAuthenticationData'
        ]);
    }

    public function tearDown()
    {
    }

    public function testDownloadLearningMaterial()
    {
        $client = $this->createClient();
        $learningMaterials = $client->getContainer()
            ->get('Tests\CoreBundle\DataLoader\LearningMaterialData')
            ->getAll();
        $fileLearningMaterials = array_filter($learningMaterials, function ($arr) {
            return !empty($arr['filesize']);
        });
        $learningMaterial = array_values($fileLearningMaterials)[0];
        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                'ilios_api_learningmaterial_get',
                ['version' => 'v1', 'object' => 'learningmaterials', 'id' => $learningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $client->request(
            'GET',
            $data['absoluteFileUri']
        );
        
        $response = $client->getResponse();
        
        $this->assertEquals(RESPONSE::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $learningMaterialLoaderPath = realpath(__DIR__ . '/../Fixture/LoadLearningMaterialData.php');
        $this->assertEquals(file_get_contents($learningMaterialLoaderPath), $response->getContent());
    }

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
            RESPONSE::HTTP_NOT_FOUND,
            $response->getStatusCode()
        );
    }
}
