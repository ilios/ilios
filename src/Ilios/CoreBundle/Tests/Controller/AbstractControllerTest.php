<?php

namespace Ilios\CoreBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tdn\PhpTypes\Type\String;

/**
 * Class AbstractControllerTest
 * @package Ilios\CoreBundle\Tests\Controller
 */
abstract class AbstractControllerTest extends WebTestCase
{
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
    abstract protected function getFixtures();

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
     * Create a JSON request
     *
     * @param string $method
     * @param string $url
     * @param string $content
     */
    public function createJsonRequest($method, $url, $content = null)
    {
        $this->client->request(
            $method,
            $url,
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ],
            $content
        );
    }

    /**
     * Check if the response is valid
     * tests the status code, headers, and the content
     * @param Response $response
     * @param integer $statusCode
     * @param boolean $checkValidJson
     */
    protected function assertJsonResponse(Response $response, $statusCode, $checkValidJson = true)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());

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
