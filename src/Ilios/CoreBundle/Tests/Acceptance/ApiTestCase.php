<?php

namespace Ilios\CoreBundle\Tests\Acceptance;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Client;

class ApiTestCase extends WebTestCase
{
    /**
     * symfony container
     * @var Symfony\Component\DependencyInjection\ContainerInterface;
     */
    protected $container;

    /**
     * symfony client
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;


    public function setup()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
    }

    /**
     * Create a JSON request
     *
     * @param string $method
     * @param string $url
     * @param string $content
     * @param integer $userId
     */
    public function createJsonRequest($method, $url, $content = null, $userId = null)
    {
        if ($userId) {
            $this->login($userId, $client);
        }

        $this->client->request(
            $method,
            $url,
            array(),
            array(),
            array(
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $content
        );
    }

    /**
     * Check if the response is valid
     * tests the status code, headers, and the content
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param integer $statusCode
     * @param boolean $checkValidJson
     */
    protected function assertJsonResponse(
        \Symfony\Component\HttpFoundation\Response $response,
        $statusCode,
        $checkValidJson = true
    ) {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode(),
            $response->getContent()
        );

        if ($checkValidJson) {
            $this->assertTrue(
                $response->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                $response->headers
            );
            $decode = json_decode($response->getContent());
            $this->assertTrue(
                ($decode != null && $decode != false),
                'Invalid JSON: [' . $response->getContent() . ']'
            );
        }
    }

    // /**
    //  * Login as a user
    //  * @param integer $userId
    //  * @param Client $client
    //  */
    // protected function login($userId, Client $client)
    // {
    //     $this->loadFixtures(
    //         array(
    //             'Ilios\CoreBundle\Tests\Fixtures\LoadUserData'
    //         )
    //     );
    //     $doctrine = self::$kernel->getContainer()->get('doctrine');
    //     $repo = $doctrine->getManager()->getRepository('IliosCoreBundle:User');
    //     $user =  $repo->find($userId);
    //
    //     $session = $client->getContainer()->get('session');
    //
    //     $firewall = 'test';
    //     $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_ADMIN'));
    //
    //     $session->set('_security_'.$firewall, serialize($token));
    //     $session->save();
    //
    //     $cookie = new Cookie($session->getName(), $session->getId());
    //     $client->getCookieJar()->set($cookie);
    // }
}
