<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CourseLearningMaterial controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CourseLearningMaterialControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData'
        ];
    }

    public function testGetCourseLearningMaterial()
    {
        $courseLearningMaterial = $this->container
            ->get('ilioscore.dataloader.courselearningmaterial')
            ->getOne()['courseLearningMaterial']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courselearningmaterials',
                ['id' => $courseLearningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $courseLearningMaterial,
            json_decode($response->getContent(), true)['courseLearningMaterial']
        );
    }

    public function testGetAllCourseLearningMaterials()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_courselearningmaterials'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.courselearningmaterial')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCourseLearningMaterial()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courselearningmaterials'),
            json_encode(
                $this->container->get('ilioscore.dataloader.courselearningmaterial')
                    ->create()['courseLearningMaterial']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCourseLearningMaterial()
    {
        $invalidCourseLearningMaterial = array_shift(
            $this->container->get('ilioscore.dataloader.courselearningmaterial')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courselearningmaterials'),
            $invalidCourseLearningMaterial
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCourseLearningMaterial()
    {
        $courseLearningMaterial = $this->container
            ->get('ilioscore.dataloader.courselearningmaterial')
            ->createWithId()['courseLearningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courselearningmaterials',
                ['id' => $courseLearningMaterial['id']]
            ),
            json_encode($courseLearningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.courselearningmaterial')
                ->getLastCreated()['courseLearningMaterial'],
            json_decode($response->getContent(), true)['courseLearningMaterial']
        );
    }

    public function testDeleteCourseLearningMaterial()
    {
        $courseLearningMaterial = $this->container
            ->get('ilioscore.dataloader.courselearningmaterial')
            ->createWithId()['courseLearningMaterial']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courselearningmaterials',
                ['id' => $courseLearningMaterial['id']]
            ),
            json_encode($courseLearningMaterial)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_courselearningmaterials',
                ['id' => $courseLearningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_courselearningmaterials',
                ['id' => $courseLearningMaterial['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCourseLearningMaterialNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_courselearningmaterials', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
