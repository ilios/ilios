<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\ObjectiveData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Objective API endpoint Test.
 * @group api_5
 */
class ObjectiveTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'objectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'position' => ['position', $this->getFaker()->randomDigit],
            'competency' => ['competency', 1],
            'courses' => ['courses', [3]],
            'programYears' => ['programYears', [2]],
            'sessions' => ['sessions', [2]],
            'parents' => ['parents', [2]],
            'children' => ['children', [4], $skipped = true],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'ancestor' => ['ancestor', 1, $skipped = true],
            'descendants' => ['descendants', [2], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'title' => [[1], ['title' => 'second objective']],
            'position' => [[0, 1, 2, 3, 4, 5, 6], ['position' => 0]],
            'competency' => [[0], ['competency' => 3]],
            'courses' => [[1, 3], ['courses' => [2]]],
            'programYears' => [[0, 1], ['programYears' => [1]]],
            'sessions' => [[1, 2], ['sessions' => [1]]],
//            'parents' => [[2, 5], ['parents' => [2]]],
//            'children' => [[1], ['children' => [3]]],
//            'meshDescriptors' => [[6], ['meshDescriptors' => ['abc3']]],
            'ancestor' => [[6], ['ancestor' => 6]],
//            'descendants' => [[1], ['descendants' => [3]]],
        ];
    }

    public function testPostCourseObjective()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'objectives', 'courses');
    }

    public function testPostProgramYearObjective()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'objectives', 'programYears');
    }

    public function testPostSessionObjective()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'objectives', 'sessions');
    }


    /**
     * Ideally, we'd be testing the "purified textarea" form type by itself.
     * However, the framework currently does not provide boilerplate to roll container-aware form test.
     * We'd need a hybrid between <code>KernelTestCase</code> and <code>TypeTestCase</code>.
     * @link  http://symfony.com/doc/current/cookbook/testing/doctrine.html
     * @link http://symfony.com/doc/current/cookbook/form/unit_testing.html
     * To keep things easy, I bolted this test on to this controller test for the time being.
     * @todo Revisit occasionally and check if future versions of Symfony have addressed this need. [ST 2015/10/19]
     *
     * @dataProvider testInputSanitationTestProvider
     *
     * @param string $input A given objective title as un-sanitized input.
     * @param string $output The expected sanitized objective title output as returned from the server.
     *
     */
    public function testInputSanitation($input, $output)
    {
        $postData = $this->container->get(ObjectiveData::class)
            ->create();
        $postData['title'] = $input;
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', [
                'version' => 'v1',
                'object' => 'objectives'
            ]),
            json_encode(['objectives' => [$postData]]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $this->assertEquals(
            json_decode($response->getContent(), true)['objectives'][0]['title'],
            $output,
            $response->getContent()
        );
    }

    /**
     * @return array
     */
    public function testInputSanitationTestProvider()
    {
        return [
            ['foo', 'foo'],
            ['<p>foo</p>', '<p>foo</p>'],
            ['<ul><li>foo</li></ul>', '<ul><li>foo</li></ul>'],
            ['<script>alert("hello");</script><p>foo</p>', '<p>foo</p>'],
            [
                '<a href="https://iliosproject.org" target="_blank">Ilios</a>',
                '<a href="https://iliosproject.org">Ilios</a>'
            ],
            ['<u>NOW I CRY</u>', '<u>NOW I CRY</u>'],
        ];
    }

    /**
     * Assert that a POST request fails if form validation fails due to input sanitation.
     */
    public function testInputSanitationFailure()
    {
        $postData = $this->container->get(ObjectiveData::class)
            ->create();
        // this markup will get stripped out, leaving a blank string as input.
        // which in turn will cause the form validation to fail.
        $postData['title'] = '<iframe></iframe>';
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', [
                'version' => 'v1',
                'object' => 'objectives'
            ]),
            json_encode(['objectives' => [$postData]]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_BAD_REQUEST);
    }
}
