<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AcademicYear controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AcademicYearControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_a
     */
    public function testGetAcademicYear()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_academicyears',
                ['id' => $course['year']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->assertEquals(
            [
                'id' => $course['year'],
                'title' => $course['year']
            ],
            json_decode($response->getContent(), true)['academicYears'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAcademicYears()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();
        $academicYears = array_map(function ($arr) {
            return $arr['year'];
        }, $courses);
        $academicYears = array_unique($academicYears);
        sort($academicYears);
        $academicYears = array_map(function ($year) {
            return [
                'id' => $year,
                'title' => $year
            ];
        }, $academicYears);
        $this->createJsonRequest('GET', $this->getUrl('cget_academicyears'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();
    
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $academicYears,
            json_decode($response->getContent(), true)['academicYears']
        );
    }
}
