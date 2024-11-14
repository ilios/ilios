<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DataLoader\IlmSessionData;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadSessionObjectiveData;
use App\Tests\GetUrlTrait;
use DateTime;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\SessionData;
use App\Tests\Traits\TestableJsonController;

#[\PHPUnit\Framework\Attributes\Group('controller')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Controller\IcsController::class)]
class IcsControllerTest extends WebTestCase
{
    use TestableJsonController;
    use GetUrlTrait;

    protected string $apiVersion = 'v3';
    protected KernelBrowser $kernelBrowser;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $databaseTool = $this->kernelBrowser->getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([
            LoadAuthenticationData::class,
            LoadSessionData::class,
            LoadSessionObjectiveData::class,
            LoadOfferingData::class,
            LoadCourseLearningMaterialData::class,
            LoadSessionLearningMaterialData::class,
            LoadAuthenticationData::class,
            LoadAuthenticationData::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
    }

    public function testSessionAttributesShowUp(): void
    {
        $container = $this->kernelBrowser->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = true;
        $session['equipmentRequired'] = true;
        $session['attendanceRequired'] = true;
        $session['prerequisites'] = ['3'];
        $session['learningMaterials'] = ['2'];
        $id = $session['id'];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_sessions_put',
                ['version' => $this->apiVersion, 'id' => $id]
            ),
            json_encode(['session' => $session]),
            $this->createJwtFromUserId($this->kernelBrowser, 2)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $this->kernelBrowser->request('GET', $url);
        $response = $this->kernelBrowser->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTART/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertMatchesRegularExpression(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required shows up'
        );

        $this->assertMatchesRegularExpression(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required shows up'
        );

        $this->assertMatchesRegularExpression(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required shows up'
        );

        $this->assertMatchesRegularExpression(
            '#SessionhasPre-work#',
            $firstDescription,
            'Has Pre-work shows up'
        );

        $this->assertMatchesRegularExpression(
            '#SessionhasLearningMaterials#',
            $firstDescription,
            'Has Learning Materials shows up'
        );
    }

    public function testSessionAttributesAreHidden(): void
    {
        $container = $this->kernelBrowser->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = false;
        $session['equipmentRequired'] = false;
        $session['attendanceRequired'] = false;
        $session['prerequisites'] = [];
        $session['learningMaterials'] = [];
        $id = $session['id'];
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_sessions_put',
                ['version' => $this->apiVersion, 'id' => $id]
            ),
            json_encode(['session' => $session]),
            $this->createJwtFromUserId($this->kernelBrowser, 2)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $this->kernelBrowser->request('GET', $url);
        $response = $this->kernelBrowser->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTART/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertDoesNotMatchRegularExpression(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required is hidden'
        );

        $this->assertDoesNotMatchRegularExpression(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required is hidden'
        );

        $this->assertDoesNotMatchRegularExpression(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required is hidden'
        );

        $this->assertDoesNotMatchRegularExpression(
            '#SessionhasPre-work#',
            $firstDescription,
            'Has Pre-work is hidden'
        );

        $this->assertMatchesRegularExpression(
            '#SessionhasLearningMaterials#',
            $firstDescription,
            'Has Learning Materials shows up b/c the owning course has learning materials.'
        );
    }

    public function testAbsolutePathsToEvents(): void
    {
        $url = '/ics/' . hash('sha256', '1');
        $this->kernelBrowser->request('GET', $url);
        $response = $this->kernelBrowser->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $matches = [];
        preg_match('/DESCRIPTION:(.+?)DTSTART/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $today = new DateTime();
        $format = $today->format('Ymd');

        $this->assertMatchesRegularExpression(
            "#http://localhost/events/U{$format}O2#",
            $firstDescription,
            'Event Links are absolute paths'
        );
    }

    protected function postOne(string $key, array $postData): array
    {
        $endpoint = strtolower($key);
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode([$key => [$postData]]),
            $this->createJwtFromUserId($this->kernelBrowser, 2)
        );

        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$key][0];
    }

    public function testLinkedSessionWithDueDatesNotShown2635(): void
    {
        $container = $this->kernelBrowser->getContainer();
        /** @var array $sessionData */
        $sessionData = $container->get(SessionData::class)->create();
        $skipTitle = 'SKIP THIS SESSION';
        $sessionData['title'] = $skipTitle;
        $sessionData['postrequisite'] = '1';
        $sessionData['publishedAsTbd'] = false;

        $session = $this->postOne('sessions', $sessionData);
        /** @var array $ilmSessionData */
        $ilmSessionData = $container->get(IlmSessionData::class)->create();
        $ilmSessionData['session'] = $session['id'];
        $dt = new DateTime('tomorrow');
        $dt->setTime(0, 0, 0);
        $ilmSessionData['dueDate'] = $dt->format('c');
        $this->postOne('ilmSessions', $ilmSessionData);
        $url = '/ics/' . hash('sha256', '1');
        $this->kernelBrowser->request('GET', $url);
        $response = $this->kernelBrowser->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertTrue(
            $response->headers->contains(
                'Content-Type',
                'text/calendar; charset=utf-8'
            ),
            var_export($response->headers, true)
        );

        $this->assertTrue(
            (!empty($content)),
            $content
        );
        $this->assertFalse(strpos($content, $skipTitle), 'Prerequisite Session Not Included');
    }

    public function testPublishedTTL(): void
    {
        $container = $this->kernelBrowser->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $this->makeJsonRequest(
            $this->kernelBrowser,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_sessions_put',
                ['version' => $this->apiVersion, 'id' => $session['id']]
            ),
            json_encode(['session' => $session]),
            $this->createJwtFromUserId($this->kernelBrowser, 2)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $this->kernelBrowser->request('GET', $url);
        $response = $this->kernelBrowser->getResponse();
        $content = $response->getContent();
        $this->assertStringContainsString('X-PUBLISHED-TTL:PT1H', $content);
    }
}
