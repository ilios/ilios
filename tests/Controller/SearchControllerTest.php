<?php

namespace App\Tests\Controller;

use App\Classes\SessionUserInterface;
use App\Controller\Search;
use App\Service\PermissionChecker;
use App\Service\Search as SearchService;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SearchControllerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

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

    public function testSearch()
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
                            'matchedIn' => []
                        ]
                    ]
                ]
            ]
        ];

        $this->mockSearch
            ->shouldReceive('curriculumSearch')
            ->with($searchTerm)
            ->once()
            ->andReturn([$result]);

        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(true);

        $request = new Request();
        $request->query->add(['q' => $searchTerm]);

        $response = $this->controller->search($request);
        $content = $response->getContent();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), var_export($content, true));

        $this->assertEquals(
            array('results' => array($result)),
            json_decode($content, true),
            var_export($content, true)
        );
    }

    public function testSearchFailsIfUserDoesntHaveProperPermissions()
    {
        $this->mockPermissionChecker
            ->shouldReceive('canSearchCurriculum')
            ->andReturn(false);
        $this->expectException(AccessDeniedException::class);
        $this->controller->search(new Request());
    }
}
