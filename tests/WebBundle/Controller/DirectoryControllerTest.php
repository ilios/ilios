<?php

namespace Tests\WebBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Service\Directory;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Client;

class DirectoryControllerTest extends WebTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    use JsonControllerTest;

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures([
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
        ]);
    }

    public function tearDown()
    {
        foreach ($this->client->getContainer()->getMockedServices() as $id => $service) {
            $this->client->getContainer()->unmock($id);
        }
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

        $container->mock(Directory::class, Directory::class)
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
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));
        $fakeDirectoryUser['user'] = null;

        $this->assertEquals(
            array('results' => array($fakeDirectoryUser)),
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testSearchReturnsCurrentUserId()
    {
        $container = $this->client->getContainer();

        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'alast',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $fakeDirectoryUser2 = [
            'firstName' => 'first',
            'lastName' => 'xlast',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => '1111@school.edu',
        ];

        $container->mock(Directory::class, Directory::class)
            ->shouldReceive('find')
            ->with(array('a', 'b'))
            ->once()
            ->andReturn(array($fakeDirectoryUser1, $fakeDirectoryUser2));

        $this->makeJsonRequest(
            $this->client,
            'GET',
            $this->getUrl(
                'ilios_web_directory_search',
                ['searchTerms' => 'a b']
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $fakeDirectoryUser1['user'] = null;
        $fakeDirectoryUser2['user'] = 1;

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));
        $results = json_decode($content, true)['results'];

        $this->assertEquals(
            $fakeDirectoryUser1,
            $results[0],
            var_export($results, true)
        );

        $this->assertEquals(
            $fakeDirectoryUser2,
            $results[1],
            var_export($results, true)
        );
    }

    public function testFind()
    {
        $container = $this->client->getContainer();

        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $mockSchool = m::mock('Ilios\CoreBundle\Entity\School')
            ->shouldReceive('getId')->once()->andReturn('1')
            ->mock();

        $mockAuthentication = m::mock('Ilios\CoreBundle\Entity\Authentication')
            ->shouldReceive('getInvalidateTokenIssuedBefore')->once()->andReturn(null)
            ->shouldReceive('getPassword')->once()->andReturn('hash')
            ->shouldReceive('isLegacyAccount')->once()->andReturn(false)
            ->mock();

        $mockUser = m::mock('Ilios\CoreBundle\Entity\User')
            ->shouldReceive('getId')->twice()->andReturn('2')
            ->shouldReceive('isRoot')->once()->andReturn(false)
            ->shouldReceive('isEnabled')->once()->andReturn(true)
            ->shouldReceive('getCampusId')->once()->andReturn('abc')
            ->shouldReceive('getSchool')->once()->andReturn($mockSchool)
            ->shouldReceive('getAuthentication')->once()->andReturn($mockAuthentication)
            ->shouldReceive('getPermissions')->once()->andReturn(new ArrayCollection([]))
            ->mock();

        $container->mock(Directory::class, Directory::class)
            ->shouldReceive('findByCampusId')
            ->with('abc')
            ->once()
            ->andReturn($fakeDirectoryUser);

        $relationships = [
            'roleTitles' => ['Developer'],
            'schoolIds' => [],
            'directedCourseIds' => [],
            'administeredCourseIds' => [],
            'directedSchoolIds' => [],
            'administeredSchoolIds' => [],
            'directedCourseSchoolIds' => [],
            'administeredCourseSchoolIds' => [],
            'administeredSessionSchoolIds' => [],
            'administeredSessionCourseIds' => [],
            'taughtCourseIds' => [],
            'taughtCourseSchoolIds' => [],
            'administeredSessionIds' => [],
            'instructedSessionIds' => [],
            'directedProgramIds' => [],
            'directedProgramYearIds' => [],
            'directedProgramYearProgramIds' => [],
            'directedCohortIds' => [],
        ];
        $container->mock(UserManager::class, UserManager::class)
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])
            ->twice()
            ->andReturn($mockUser)
            ->shouldReceive('buildSessionRelationships')
            ->with(2)
            ->once()
            ->andReturn($relationships);

        $this->makeJsonRequest(
            $this->client,
            'GET',
            $this->getUrl(
                'ilios_web_directory_find',
                ['id' => '1']
            ),
            null,
            $this->getTokenForUser(1)
        );

        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            array('result' => $fakeDirectoryUser),
            json_decode($content, true),
            var_export($content, true)
        );
    }
}
