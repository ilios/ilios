<?php

namespace Ilios\WebBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Ilios\CoreBundle\Tests\Traits\JsonControllerTest;
use FOS\RestBundle\Util\Codes;
use Mockery as m;

class DirectoryControllerTest extends WebTestCase
{
    use JsonControllerTest;

    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures([
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAuthenticationData',
        ]);
    }

    public function tearDown()
    {
        foreach ($this->client->getContainer()->getMockedServices() as $id => $service) {
            $this->client->getContainer()->unmock($id);
        }

        m::close();

        parent::tearDown();
    }

    public function testSearch()
    {
        $container = $this->client->getContainer();

        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $container->mock('ilioscore.directory', 'Ilios\CoreBundle\Service\Directory')
            ->shouldReceive('find')
            ->with(array('a', 'b'))
            ->once()
            ->andReturn(array($fakeDirectoryUser));

        $this->makeJsonRequest(
            $this->client,
            'GET',
            $this->getUrl(
                'ilios_web_directory_search',
                ['searchTerms' => 'a b']
            ),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            array('results' => array($fakeDirectoryUser)),
            json_decode($content, true),
            var_export($content, true)
        );

    }
}
