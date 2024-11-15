<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controller\DownloadController;
use App\Entity\LearningMaterial;
use App\Tests\DataLoader\ApplicationConfigData;
use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\GetUrlTrait;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\TestableJsonController;

#[Group('controller')]
#[CoversClass(DownloadController::class)]
class DownloadControllerTest extends WebTestCase
{
    use TestableJsonController;
    use GetUrlTrait;

    protected ReferenceRepository $fixtures;
    protected KernelBrowser $kernelBrowser;
    protected string $apiVersion = 'v3';

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $databaseTool->loadFixtures([
            LoadApplicationConfigData::class,
            LoadAuthenticationData::class,
            LoadCourseLearningMaterialData::class,
            LoadLearningMaterialData::class,
            LoadOfferingData::class,
            LoadServiceTokenData::class,
            LoadSessionLearningMaterialData::class,
        ]);
        $this->fixtures = $executor->getReferenceRepository();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->fixtures);
    }

    public function testDownloadLearningMaterial(): void
    {
        $fileLms = $this->getFileLms();
        $lm = $fileLms[0];

        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_getone',
                ['version' => $this->apiVersion, 'id' => $lm->getId()]
            ),
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $this->kernelBrowser->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(
            $response->headers->get('Content-Disposition'),
            'attachment; filename="' . $data['filename'] . '"'
        );
        $this->assertEquals(RESPONSE::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $learningMaterialLoaderPath = realpath(__DIR__ . '/../Fixture/LoadLearningMaterialData.php');
        $this->assertEquals(file_get_contents($learningMaterialLoaderPath), $response->getContent());
    }

    public function testPdfInlineDownload(): void
    {
        $learningMaterial = $this->fixtures->getReference('learningMaterials4', LearningMaterial::class);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_getone',
                ['version' => $this->apiVersion, 'id' => $learningMaterial->getId()]
            ),
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $this->kernelBrowser->request(
            'GET',
            $data['absoluteFileUri'] . '?inline=true'
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(
            $response->headers->get('Content-Disposition'),
            'inline'
        );
    }

    public function testBadLearningMaterialToken(): void
    {
        //sending bad hash
        $this->kernelBrowser->request(
            'GET',
            '/lm/a7a8e202e9655ab81155c4c3e52b95098fcaa1c975f63f0327b467a981f6428f'
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(
            RESPONSE::HTTP_NOT_FOUND,
            $response->getStatusCode()
        );
    }

    protected function setLearningMaterialsDisabled(string $setTo): void
    {
        $container = $this->kernelBrowser->getContainer();
        $config = $container->get(ApplicationConfigData::class)->getOne();
        $config['name'] = 'learningMaterialsDisabled';
        $config['value'] = $setTo;
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_applicationconfigs_post',
                ['version' => $this->apiVersion]
            ),
            json_encode(['applicationConfig' => $config]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $this->assertJsonResponse($this->kernelBrowser->getResponse(), Response::HTTP_CREATED);
    }

    public function testDisabledMaterialsWithLM(): void
    {
        $this->setLearningMaterialsDisabled('true');

        $learningMaterial = $this->fixtures->getReference('learningMaterials4', LearningMaterial::class);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_getone',
                ['version' => $this->apiVersion, 'id' => $learningMaterial->getId()]
            ),
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $this->kernelBrowser->request(
            'GET',
            $data['absoluteFileUri'] . '?inline=true'
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(
            'Learning Materials are disabled on this instance.',
            $response->getContent()
        );
    }

    public function testDisabledMaterialsWithMissingLm(): void
    {
        $this->setLearningMaterialsDisabled('true');
        $this->kernelBrowser->request(
            'GET',
            '/lm/a7a8e202e9655ab81155c4c3e52b95098fcaa1c975f63f0327b467a981f6428f'
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertEquals(
            'Learning Materials are disabled on this instance.',
            $response->getContent()
        );
    }

    public function testBadLearningMaterialTokenWithLmEnabled(): void
    {
        $this->setLearningMaterialsDisabled('false');
        //sending bad hash
        $this->kernelBrowser->request(
            'GET',
            '/lm/a7a8e202e9655ab81155c4c3e52b95098fcaa1c975f63f0327b467a981f6428f'
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertEquals(
            RESPONSE::HTTP_NOT_FOUND,
            $response->getStatusCode()
        );
    }

    public function testDownloadLearningMaterialWithServiceToken(): void
    {
        $fileLms = $this->getFileLms();
        $lm = $fileLms[0];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_learningmaterials_getone',
                ['version' => $this->apiVersion, 'id' => $lm->getId()]
            ),
            null,
            $this->createJwtForEnabledServiceToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];

        $this->kernelBrowser->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertEquals(
            $response->headers->get('Content-Disposition'),
            'attachment; filename="' . $data['filename'] . '"'
        );
        $this->assertEquals(RESPONSE::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $learningMaterialLoaderPath = realpath(__DIR__ . '/../Fixture/LoadLearningMaterialData.php');
        $this->assertEquals(file_get_contents($learningMaterialLoaderPath), $response->getContent());
    }

    protected function getFileLms(): array
    {
        $learningMaterials = $this->fixtures->getReferencesByClass()[LearningMaterial::class];
        return array_values(array_filter($learningMaterials, fn(LearningMaterial $lm) => !!$lm->getFilename()));
    }
}
