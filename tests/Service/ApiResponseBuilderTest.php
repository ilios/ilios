<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ApiResponseBuilder;
use App\Service\EndpointResponseNamer;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiResponseBuilderTest extends TestCase
{
    private ApiResponseBuilder $obj;

    public function setUp(): void
    {
        parent::setUp();
        $this->obj = new ApiResponseBuilder(
            m::mock(SerializerInterface::class),
            m::mock(EndpointResponseNamer::class),
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
    }

    public function testSimpleTree(): void
    {
        $input = 'cohorts,objective';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'cohorts' => [],
            'objective' => [],
        ];
        $this->assertEquals($expected, $tree);
    }

    public function testTwoLevelTree(): void
    {
        $input = 'cohorts.green,objective.green';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'cohorts' => [
                'green' => [],
            ],
            'objective' => [
                'green' => [],
            ],
        ];
        $this->assertEquals($expected, $tree);
    }

    public function testEmptyTree(): void
    {
        $input = '';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [];
        $this->assertEquals($expected, $tree);
    }

    public function testDoubleTopLevelTree(): void
    {
        $input = 'cohorts.programYear.program,cohorts.programYear.programYearObjectives.objective';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'cohorts' => [
                'programYear' => [
                    'program' => [],
                    'programYearObjectives' => [
                        'objective' => [],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $tree);
    }

    public function testDoubleDeepLevelTree(): void
    {
        $input = 'cohorts.programYear.program.school,cohorts.programYear.program.directors';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'cohorts' => [
                'programYear' => [
                    'program' => [
                        'school' => [],
                        'directors' => [],
                    ],
                ],
            ],
        ];
        $this->assertEquals($expected, $tree);
    }

    public function testCourseSessionTree(): void
    {
        $sessionRelationships = [
            'learningMaterials.learningMaterial.owningUser',
            'sessionObjectives.objective.parents',
            'sessionObjectives.objective.meshDescriptors',
            'sessionObjectives.terms.vocabulary',
            'offerings.learners',
            'offerings.instructors',
            'offerings.instructorGroups.users',
            'offerings.learnerGroups.users',
            'ilmSession.learners',
            'ilmSession.instructors',
            'ilmSession.instructorGroups.users',
            'ilmSession.learnerGroups.users',
            'sessionDescription',
            'terms.vocabulary',
            'meshDescriptors.trees',
        ];
        $input = array_reduce($sessionRelationships, fn($carry, $item) => "{$carry}sessions.{$item},", '');

        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'sessions' => [
                'learningMaterials' => [
                    'learningMaterial' => [
                        'owningUser' => [],
                    ],
                ],
                'sessionObjectives' => [
                    'objective' => [
                        'parents' => [],
                        'meshDescriptors' => [],
                    ],
                    'terms' => [
                        'vocabulary' => [],
                    ],
                ],
                'offerings' => [
                    'learners' => [],
                    'instructors' => [],
                    'instructorGroups' => [
                        'users' => [],
                    ],
                    'learnerGroups' => [
                        'users' => [],
                    ],
                ],
                'ilmSession' => [
                    'learners' => [],
                    'instructors' => [],
                    'instructorGroups' => [
                        'users' => [],
                    ],
                    'learnerGroups' => [
                        'users' => [],
                    ],
                ],
                'sessionDescription' => [],
                'terms' => [
                    'vocabulary' => [],
                ],
                'meshDescriptors' => [
                    'trees' => [],
                ],
            ],
        ];
        $this->assertEquals($expected, $tree);
    }
}
