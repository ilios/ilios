<?php

namespace App\Tests\Controller;

use App\Classes\SessionUserInterface;
use App\Controller\Search;
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
     * @var m\MockInterface
     */
    protected $mockSearch;

    /**
     * @var m\MockInterface
     */
    protected $mockTokenStorage;

    /**
     * @var m\MockInterface
     */
    protected $mockPermissionChecker;

    public function setUp()
    {
        parent::setUp();
        $this->mockSearch = m::mock(SearchService::class);
        $this->mockTokenStorage = m::mock(TokenStorageInterface::class);
        $this->mockPermissionChecker = m::mock(PermissionChecker::class);

        $mockSessionUser = m::mock(SessionUserInterface::class);

        $mockToken = m::mock(TokenInterface::class);
        $mockToken->shouldReceive('getUser')->andReturn($mockSessionUser);

        $this->mockTokenStorage->shouldReceive('getToken')->andReturn($mockToken);

        $this->controller = new Search(
            $this->mockSearch,
            $this->mockTokenStorage,
            $this->mockPermissionChecker
        );
    }

    public function tearDown() : void
    {
        unset($this->controller);
        unset($this->mockSearch);
        unset($this->mockTokenStorage);
        unset($this->mockPermissionChecker);


        parent::tearDown();
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

        $this->mockSearch
            ->shouldReceive('curriculumSearch')
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
            array('results' => array($result)),
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

        $this->mockSearch
            ->shouldReceive('curriculumSearch')
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
            array('results' => array($result)),
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

        $this->mockSearch
            ->shouldReceive('userSearch')
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
            array('results' => array($result)),
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

        $this->mockSearch
            ->shouldReceive('userSearch')
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
            array('results' => array($result)),
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

        $this->mockSearch
            ->shouldReceive('userSearch')
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
            array('results' => array($result)),
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
