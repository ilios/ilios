<?php
namespace Tests\WebBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use FOS\RestBundle\Util\Codes;

/**
 * Class ConfigControllerTest
 * @package Tests\WebBundle\\Controller
 */
class IcsControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
        ]);
    }

    public function testAbsolutePathsToLms()
    {
        $client = static::createClient();
        $url = '/ics/' . hash('sha256', '1');
        $client->request('GET', $url);
        $response = $client->getResponse();

        $content = $response->getContent();


        $this->assertEquals(
            Codes::HTTP_OK,
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
        $this->assertEquals(count($matches), 2);
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
            Codes::HTTP_OK,
            $response->getStatusCode(),
            "Status Code: {$response->getStatusCode()} is not OK"
        );
        $this->assertRegexp('/firstlm/', $content);
        $this->assertRegexp('/thirdlm/', $content);
        $this->assertFalse(strpos($content, 'secondlm'));
    }
}
