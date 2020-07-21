<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Classes\SessionUserInterface;
use App\Controller\Search;
use App\Service\Index\Curriculum;
use App\Service\Index\Users;
use App\Service\PermissionChecker;
use App\Service\Search as SearchService;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SearchControllerTest extends TestCase
{
    /**
     * @var Search
     */
    protected $controller;

    /**
     * @var Curriculum|m\MockInterface
     */
    protected $mockCurriculumSearch;

    /**
     * @var Users|m\MockInterface
     */
    protected $mockUsersSearch;

    /**
     * @var TokenStorageInterface|m\MockInterface
     */
    protected $mockTokenStorage;

    /**
     * @var PermissionChecker|m\MockInterface
     */
    protected $mockPermissionChecker;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockCurriculumSearch = m::mock(Curriculum::class);
        $this->mockUsersSearch = m::mock(Users::class);
        $this->mockTokenStorage = m::mock(TokenStorageInterface::class);
        $this->mockPermissionChecker = m::mock(PermissionChecker::class);

        $mockSessionUser = m::mock(SessionUserInterface::class);

        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockSessionUser);

        $this->mockTokenStorage->shouldReceive('getToken')->andReturn($mockToken);

        $this->controller = new Search(
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

    public function testCurriculumSearch()
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [
              'one',
              'two',
            ],
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
                            'matchedIn' => []
                        ]
                    ]
                ]
            ]
        ];

        $this->mockCurriculumSearch
            ->shouldReceive('search')
            ->with($searchTerm, false)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm]);

        $response = $this->controller->curriculumSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testCurriculumSearchSuggestionsOnly()
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [
              'one',
              'two',
            ],
            'courses' => []
        ];

        $this->mockCurriculumSearch
            ->shouldReceive('search')
            ->with($searchTerm, true)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm, 'onlySuggest' => true]);

        $response = $this->controller->curriculumSearch($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            ['results' => [$result]],
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testCurriculumSearchFailsIfUserDoesntHaveProperPermissions()
    {
        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->controller->curriculumSearch(new Request());
    }

    public function testUserSearch()
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
                ]
            ]
        ];

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

    public function testUserSearchSuggestionsOnly()
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [
                'one',
                'two',
            ],
            'courses' => []
        ];

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

    public function testUserSearchSize()
    {
        $searchTerm = 'jasper & jackson';
        $result = [
            'autocomplete' => [],
            'courses' => []
        ];

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

    public function testUserSearchFailsIfUserDoesntHaveProperPermissions()
    {
        $this->mockPermissionChecker
            ->shouldReceive('canSearchUsers')
            ->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->controller->userSearch(new Request());
    }
}
