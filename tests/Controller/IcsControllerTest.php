<?php
namespace App\Tests\Controller;

use App\Tests\DataLoader\IlmSessionData;
use App\Tests\GetUrlTrait;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\SessionData;
use App\Tests\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 */
class IcsControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;
    use GetUrlTrait;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures([
            'App\Tests\Fixture\LoadAuthenticationData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadSessionDescriptionData',
        ])->getReferenceRepository();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        unset($this->fixtures);
    }

    public function testSessionAttributesShowUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = true;
        $session['equipmentRequired'] = true;
        $session['attendanceRequired'] = true;
        $id = $session['id'];
        $this->makeJsonRequest(
            $client,
            'PUT',
            $this->getUrl($client, 'ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser($client, 2)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $client->request('GET', $url);
        $response = $client->getResponse();

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
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertRegExp(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required shows up'
        );

        $this->assertRegExp(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required shows up'
        );

        $this->assertRegExp(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required shows up'
        );
    }

    public function testSessionAttributesAreHidden()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get(SessionData::class)->getOne();
        $session['attireRequired'] = false;
        $session['equipmentRequired'] = false;
        $session['attendanceRequired'] = false;
        $id = $session['id'];
        $this->makeJsonRequest(
            $client,
            'PUT',
            $this->getUrl($client, 'ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser($client, 2)
        );
        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        $url = '/ics/' . hash('sha256', '1');
        $client->request('GET', $url);
        $response = $client->getResponse();

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
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertNotRegExp(
            '#Youwillneedspecialattire#',
            $firstDescription,
            'Special attire required is hidden'
        );

        $this->assertNotRegExp(
            '#Youwillneedspecialequipment#',
            $firstDescription,
            'Special equiptment required is hidden'
        );

        $this->assertNotRegExp(
            '#AttendanceisRequired#',
            $firstDescription,
            'Attendance required is hidden'
        );
    }

    public function testAbsolutePathsToEvents()
    {
        $client = static::createClient();
        $url = '/ics/' . hash('sha256', '1');
        $client->request('GET', $url);
        $response = $client->getResponse();

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
        preg_match('/DESCRIPTION:(.+?)DTSTAMP/s', $content, $matches);
        $this->assertEquals(2, count($matches), 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $today = new \DateTime();
        $format = $today->format('Ymd');

        $this->assertRegExp(
            "#http://localhost/events/U${format}O2#",
            $firstDescription,
            'Event Links are absolute paths'
        );
    }

    protected function postOne(
        KernelBrowser $client,
        string $key,
        array $postData
    ) {
        $this->makeJsonRequest(
            $client,
            'POST',
            $this->getUrl($client, 'ilios_api_post', ['version' => 'v1', 'object' => strtolower($key)]),
            json_encode([$key => [$postData]]),
            $this->getTokenForUser($client, 2)
        );

        $response = $client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$key][0];
    }

    public function testLinkedSessionWithDueDatesNotShown2635()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $sessionData = $container->get(SessionData::class)->create();
        $skipTitle = 'SKIP THIS SESSION';
        $sessionData['title'] = $skipTitle;
        $sessionData['postrequisite'] = '1';
        $sessionData['publishedAsTbd'] = false;

        $session = $this->postOne($client, 'sessions', $sessionData);
        $ilmSessionData = $container->get(IlmSessionData::class)->create();
        $ilmSessionData['session'] = $session['id'];
        $dt = new \DateTime('tomorrow');
        $dt->setTime(0, 0, 0);
        $ilmSessionData['dueDate'] = $dt->format('c');
        $this->postOne($client, 'ilmSessions', $ilmSessionData);
        $url = '/ics/' . hash('sha256', '1');
        $client->request('GET', $url);
        $response = $client->getResponse();

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
}
