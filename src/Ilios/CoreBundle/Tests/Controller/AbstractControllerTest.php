<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tdn\PhpTypes\Type\String;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;

/**
 * Class AbstractControllerTest
 * @package Ilios\CoreBundle\Tests\Controller
 */
abstract class AbstractControllerTest extends WebTestCase
{
    use JsonControllerTest;
    /**
     * @var ContainerInterface
     */
    protected $container;

        /**
     * @var Client
     */
    protected $client;

    /**
     * @return array|FixtureInterface
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData',
        ];
    }

    /**
     * Returns array of fields that are skipped by the serialier.
     * Should use objects and serializer object moving forward, when switching to Alice.
     *
     * @return array|string
     */
    abstract protected function getPrivateFields();

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->container = $this->client->getContainer();
        $this->loadFixtures($this->getFixtures());
    }

    /**
     * Logs the 'newuser' user in and returns the user's JSON Web Token (JWT).
     * @return string the JWT
     * @todo obviously, this needs expanded in order to allow other user log-ins. [ST 2015/08/06]
     */
    protected function getAuthenticatedUserToken()
    {
        static $token;

        if (! $token) {
            $this->client->request(
                'POST',
                '/auth/login',
                array(
                    'username' => 'newuser',
                    'password' => 'newuserpass',
                )
            );
            $response = $this->client->getResponse();
            $response = json_decode($response->getContent(), true);
            $token = $response['jwt'];
        }

        return $token;
    }
    
    public function tearDown()
    {
        unset($this->client);
        unset($this->container);
    }

    /**
     * Create a JSON request
     *
     * @param string $method
     * @param string $url
     * @param string $content
     * @param string $token
     */
    public function createJsonRequest($method, $url, $content = null, $token = null)
    {
        $headers = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json'
        ];

        if (! empty($token)) {
            $headers['HTTP_X-JWT-Authorization'] = 'Token ' . $token;
        }

        $this->client->request(
            $method,
            $url,
            [],
            [],
            $headers,
            $content
        );
    }

    /**
     * Removes keys that should not be public from a data seed array.
     * Renames keys to be underscored (like serializer does),
     *
     * @param array &$array
     * @param bool  $underscore
     *
     * @return array
     */
    public function mockSerialize(array $array, $underscore = false)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                unset($array[$k]);
                $k = ($underscore) ? (string) String::create($k)->underscored() : $k;
                $array[$k] = $this->mockSerialize($v);
            }
        }

        foreach ($this->getPrivateFields() as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
