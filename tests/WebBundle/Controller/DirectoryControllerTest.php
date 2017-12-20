<?php

namespace Tests\WebBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Directory;
use Ilios\WebBundle\Controller\DirectoryController;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DirectoryControllerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var DirectoryController
     */
    protected $directoryController;

    /**
     * @var m\MockInterface
     */
    protected $tokenStorageMock;

    /**
     * @var m\MockInterface
     */
    protected $userManagerMock;

    /**
     * @var m\MockInterface
     */
    protected $directoryMock;

    public function setUp()
    {
        parent::setUp();
        $this->tokenStorageMock = m::mock(TokenStorageInterface::class);
        $this->userManagerMock = m::mock(UserManager::class);
        $this->directoryMock = m::mock(Directory::class);

        $mockSessionUser = m::mock(SessionUserInterface::class);
        $mockSessionUser->shouldReceive('hasRole')->with(['Developer'])->andReturn(true);

        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockSessionUser);

        $this->tokenStorageMock->shouldReceive('getToken')->andReturn($mockToken);

        $this->directoryController = new DirectoryController(
            $this->tokenStorageMock,
            $this->userManagerMock,
            $this->directoryMock
        );
    }

    public function tearDown()
    {
        unset($this->directoryController);
        unset($this->tokenStorageMock);
        unset($this->userManagerMock);
        unset($this->directoryMock);

        parent::tearDown();
    }

    public function testSearchOne()
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->directoryMock
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser]);

        $user = m::mock(UserDTO::class);

        $this->userManagerMock
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc'])->andReturn([$user]);


        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->searchAction($request);
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

        $this->directoryMock
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);

        $user = m::mock(UserDTO::class);
        $user->id = 1;
        $user->campusId = '1111@school.edu';

        $this->userManagerMock
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc', '1111@school.edu'])->andReturn([$user]);

        $fakeDirectoryUser1['user'] = null;
        $fakeDirectoryUser2['user'] = 1;

        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->searchAction($request);
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
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->directoryMock
            ->shouldReceive('findByCampusId')
            ->with('abc')
            ->once()
            ->andReturn($fakeDirectoryUser);

        $userMock = m::mock(UserInterface::class)
            ->shouldReceive('getCampusId')
            ->andReturn('abc')
            ->mock();

        $this->userManagerMock
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])->andReturn($userMock);

        $response = $this->directoryController->findAction(1);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            array('result' => $fakeDirectoryUser),
            json_decode($content, true),
            var_export($content, true)
        );
    }
}
