<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Controller\SearchController;
use App\Service\Index\Curriculum;
use App\Service\Index\Users;
use App\Service\SessionUserPermissionChecker;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Group('controller')]
#[CoversClass(SearchController::class)]
final class SearchControllerTest extends TestCase
{
    protected SearchController $controller;
    protected m\MockInterface $mockCurriculumSearch;
    protected m\MockInterface $mockUsersSearch;
    protected m\MockInterface $mockTokenStorage;
    protected m\MockInterface $mockPermissionChecker;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockCurriculumSearch = m::mock(Curriculum::class);
        $this->mockUsersSearch = m::mock(Users::class);
        $this->mockTokenStorage = m::mock(TokenStorageInterface::class);
        $this->mockPermissionChecker = m::mock(SessionUserPermissionChecker::class);

        $this->controller = new SearchController(
            $this->mockCurriculumSearch,
            $this->mockUsersSearch,
            $this->mockTokenStorage,
            $this->mockPermissionChecker
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->controller);
        unset($this->mockCurriculumSearch);
        unset($this->mockUsersSearch);
        unset($this->mockTokenStorage);
        unset($this->mockPermissionChecker);
    }

    public function testCurriculumSearch(): void
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'courses' => [
                [
                    'id' => 1,
                    'title' => 'This Course',
                    'year' => 2019,
                    'matchedIn' => [],
                    'sessions' => [
                        [
                            'id' => 11,
                            'title' => 'This Session',
                            'matchedIn' => [],
                        ],
                    ],
                ],
            ],
            'totalCourses' => 13,
        ];
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockCurriculumSearch
            ->shouldReceive('search')
            ->with($searchTerm, 1, 2, [3, 4], [2005, 2006])
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(true);

        $response = $this->controller->curriculumSearch($searchTerm, 1, 2, '3-4', '2005-2006');
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function test404CurriculumSearchV1API(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getServiceTokenUserBasedMockToken());

        $this->mockCurriculumSearch->shouldNotReceive('search');

        $request = new Request();
        $request->query->add(['q' => '']);

        $response = $this->controller->curriculumSearchV1($request);
        $this->assertEquals(Response::HTTP_GONE, $response->getStatusCode());
    }

    public function testCurriculumSearchFailsIfUserDoesntHaveProperPermissions(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->controller->curriculumSearch('q', 10, 0, '', '');
    }

    public function testUserSearch(): void
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [
                'one',
                'three',
            ],
            'users' => [
                [
                    'id' => 1,
                ],
            ],
        ];

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockUsersSearch
            ->shouldReceive('search')
            ->with($searchTerm, 100, false)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchUsers')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm]);

        $response = $this->controller->userSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testUserSearchWithServiceToken(): void
    {
        $searchTerm = 'janusz';
        $result = [
            'autocomplete' => [
            ],
            'users' => [
                [
                    'id' => 1,
                ],
            ],
        ];

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getServiceTokenUserBasedMockToken());

        $this->mockUsersSearch
            ->shouldReceive('search')
            ->with($searchTerm, 100, false)
            ->once()
            ->andReturn([$result]);

        $request = new Request();
        $request->query->add(['q' => $searchTerm]);

        $response = $this->controller->userSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testUserSearchSuggestionsOnly(): void
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [
                'one',
                'two',
            ],
            'courses' => [],
        ];

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockUsersSearch
            ->shouldReceive('search')
            ->with($searchTerm, 100, true)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchUsers')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm, 'onlySuggest' => true]);

        $response = $this->controller->userSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testUserSearchSize(): void
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [],
            'courses' => [],
        ];

        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockUsersSearch
            ->shouldReceive('search')
            ->with($searchTerm, 13, false)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchUsers')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm, 'size' => 13]);

        $response = $this->controller->userSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testUserSearchFailsIfUserDoesntHaveProperPermissions(): void
    {
        $this->mockTokenStorage
            ->shouldReceive('getToken')
            ->andReturn($this->getSessionUserBasedMockToken());

        $this->mockPermissionChecker
            ->shouldReceive('canSearchUsers')
            ->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->controller->userSearch(new Request());
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
