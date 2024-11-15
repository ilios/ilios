<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Controller\DirectoryController;
use App\Entity\DTO\UserDTO;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Service\Directory;
use App\Service\SessionUserPermissionChecker;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Group('controller')]
#[CoversClass(DirectoryController::class)]
class DirectoryControllerTest extends TestCase
{
    protected DirectoryController $directoryController;
    protected m\MockInterface $mockTokenStorage;
    protected m\MockInterface $mockUserRepository;
    protected m\MockInterface $mockDirectory;
    protected m\MockInterface $mockPermissionChecker;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockTokenStorage = m::mock(TokenStorageInterface::class);
        $this->mockUserRepository = m::mock(UserRepository::class);
        $this->mockDirectory = m::mock(Directory::class);
        $this->mockPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->directoryController = new DirectoryController(
            $this->mockTokenStorage,
            $this->mockUserRepository,
            $this->mockDirectory,
            $this->mockPermissionChecker
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->directoryController);
        unset($this->mockTokenStorage);
        unset($this->mockUserRepository);
        unset($this->mockDirectory);
        unset($this->mockPermissionChecker);
    }

    public function testSearchOne(): void
    {
        $fakeDirectoryUser = [
            'user' => 1,
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->mockDirectory
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser]);

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockPermissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);

        $user = m::mock(UserDTO::class);
        $user->id = 1;
        $user->campusId = 'abc';


        $this->mockUserRepository
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc'])->andReturn([$user]);


        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->search($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$fakeDirectoryUser]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testSearchReturnsCurrentUserId(): void
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

        $this->mockDirectory
            ->shouldReceive('find')
            ->with(['a', 'b'])
            ->once()
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockPermissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);

        $user = m::mock(UserDTO::class);
        $user->id = 1;
        $user->campusId = '1111@school.edu';

        $this->mockUserRepository
            ->shouldReceive('findAllMatchingDTOsByCampusIds')
            ->with(['abc', '1111@school.edu'])->andReturn([$user]);

        $fakeDirectoryUser1['user'] = null;
        $fakeDirectoryUser2['user'] = 1;

        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);

        $response = $this->directoryController->search($request);
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

    public function testFind(): void
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockPermissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(true);

        $this->mockDirectory
            ->shouldReceive('findByCampusId')
            ->with('abc')
            ->once()
            ->andReturn($fakeDirectoryUser);

        $userMock = m::mock(UserInterface::class);
        $userMock->shouldReceive('getCampusId')->andReturn('abc');

        $this->mockUserRepository
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])->andReturn($userMock);

        $response = $this->directoryController->find(1);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['result' => $fakeDirectoryUser],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testFindFailsIfUserDoesntHaveProperPermissions(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());
        $this->mockPermissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->directoryController->find(1);
    }

    public function testSearchFailsIfUserDoesntHaveProperPermissions(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());
        $this->mockPermissionChecker->shouldReceive('canCreateUsersInAnySchool')->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);
        $this->directoryController->search($request);
    }

    public function testFindIsForbiddenWithServiceToken(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getServiceTokenUserBasedMockToken());
        $this->expectException(AccessDeniedException::class);
        $this->directoryController->find(1);
    }

    public function testSearchIsForbiddenWithServiceToken(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getServiceTokenUserBasedMockToken());
        $this->expectException(AccessDeniedException::class);
        $request = new Request();
        $request->query->add(['searchTerms' => 'a b']);
        $this->directoryController->search($request);
    }


    protected function getSessionUserBasedMockToken(): m\MockInterface
    {
        $mockUser = m::mock(SessionUserInterface::class);
        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockUser);
        return $mockToken;
    }

    protected function getServiceTokenUserBasedMockToken(): m\MockInterface
    {
        $mockUser = m::mock(ServiceTokenUserInterface::class);
        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockUser);
        return $mockToken;
    }
}
