<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ApiResponseBuilder;
use App\Service\EndpointResponseNamer;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResponseBuilderTest extends TestCase
{
    /**
     * @var ApiResponseBuilder
     */
    private $obj;

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

    public function testDoubleTopLevelTree()
    {
        $input = 'cohorts.programYear.program,cohorts.programYear.programYearObjectives.objective';
        $tree = $this->obj->extractJsonApiSideLoadFields($input);
        $expected = [
            'cohorts' => [
                'programYear' => [
                    'program' => [],
                    'programYearObjectives' => [
                        'objective' => []
                    ]
                ],
            ]
        ];
        $this->assertEquals($expected, $tree);
    }

    public function testDoubleDeepLevelTree()
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
            ]
        ];
        $this->assertEquals($expected, $tree);
    }
}
