<?php
namespace Tests\WebBundle\Controller;

use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * Class ConfigControllerTest
 * @package Tests\WebBundle\\Controller
 */
class IcsControllerTest extends WebTestCase
{
    use JsonControllerTest;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->fixtures = $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
        ])->getReferenceRepository();
    }

    public function testAbsolutePathsToLms()
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
        preg_match('/DESCRIPTION:(.+?)CATEGORIES/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
        $firstDescription = preg_replace('/\s+/', '', $matches[1]);

        $this->assertRegExp(
            '#thirdlmhttp://localhost/lm/[a-z0-9]{64}$#',
            $firstDescription,
            'LM Links are absolute paths'
        );
    }

    public function testDraftLmsNotInFeedForStudents()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $users = $container->get('ilioscore.dataloader.user')->getAll();
        $studentUser = $users[4];
        $this->assertEmpty($studentUser['roles']);

        $url = '/ics/' . $studentUser['icsFeedKey'];
        $client->request('GET', $url);
        $response = $client->getResponse();

        $content = $response->getContent();

        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertRegexp('/firstlm/', $content);
        $this->assertRegexp('/thirdlm/', $content);
        $this->assertFalse(strpos($content, 'secondlm'));
    }

    public function testSessionAttributesShowUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get('ilioscore.dataloader.session')->getOne();
        $session['attireRequired'] = true;
        $session['equipmentRequired'] = true;
        $session['attendanceRequired'] = true;
        $id = $session['id'];
        $this->makeJsonRequest(
            $client,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser(2)
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
        preg_match('/DESCRIPTION:(.+?)CATEGORIES/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
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
        $session = $container->get('ilioscore.dataloader.session')->getOne();
        $session['attireRequired'] = false;
        $session['equipmentRequired'] = false;
        $session['attendanceRequired'] = false;
        $id = $session['id'];
        $this->makeJsonRequest(
            $client,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'sessions', 'id' => $id]),
            json_encode(['session' => $session]),
            $this->getTokenForUser(2)
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
        preg_match('/DESCRIPTION:(.+?)CATEGORIES/s', $content, $matches);
        $this->assertEquals(count($matches), 2, 'Found description in response');
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
}
