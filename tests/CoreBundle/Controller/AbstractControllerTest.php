<?php

namespace Tests\CoreBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stringy\Stringy as S;
use Tests\CoreBundle\Traits\JsonControllerTest;

/**
 * Class AbstractControllerTest
 * @package Tests\CoreBundle\\Controller
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
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
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
    public function createJsonRequest($method, $url, $content = null, $token = null, $files = array())
    {
        $this->makeJsonRequest($this->client, $method, $url, $content, $token, $files);
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
                $k = ($underscore) ? (string) S::create($k)->underscored() : $k;
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
