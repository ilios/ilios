<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * LearningMaterial controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearningMaterialControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers_a
     */
    public function testGetLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learningMaterials'][0];
        $uploadDate = new DateTime($data['uploadDate']);
        unset($data['uploadDate']);
        $this->assertEquals(
            $this->mockSerialize($learningMaterial),
            $data
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_a
     */
    public function testGetAllLearningMaterials()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = [];
        $responses = json_decode($response->getContent(), true)['learningMaterials'];
        $now = new DateTime();
        foreach ($responses as $response) {
            $uploadDate = new DateTime($response['uploadDate']);
            unset($response['uploadDate']);
            $uri = array_key_exists('absoluteFileUri', $response)?$response['absoluteFileUri']:null;
            unset($response['absoluteFileUri']);
            $diff = $now->diff($uploadDate);
            $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
            $data[] = $response;
            if ($uri) {
                $this->client->request(
                    'GET',
                    $uri
                );

                $response = $this->client->getResponse();

                $this->assertEquals(CODES::HTTP_OK, $response->getStatusCode(), $response->getContent());
            }
        }
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learningmaterial')
                    ->getAll()
            ),
            $data
        );
    }

    /**
     * @group controllers_a
     */
    public function testFindLearningMaterialsByTitle()
    {
        $materials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => 'first')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(1, count($gotMaterials));
        $this->assertEquals(
            $materials[0]['id'],
            $gotMaterials[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => 'second')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(1, count($gotMaterials));
        $this->assertEquals(
            $materials[1]['id'],
            $gotMaterials[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => 'lm')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(3, count($gotMaterials));
        $this->assertEquals(
            $materials[0]['id'],
            $gotMaterials[0]['id']
        );
        $this->assertEquals(
            $materials[1]['id'],
            $gotMaterials[1]['id']
        );
        $this->assertEquals(
            $materials[2]['id'],
            $gotMaterials[2]['id']
        );
    }

    /**
     * @group controllers_a
     */
    public function testFindLearningMaterialsByDescription()
    {
        $materials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => '1')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(1, count($gotMaterials));
        $this->assertEquals(
            $materials[0]['id'],
            $gotMaterials[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => '2')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(1, count($gotMaterials));
        $this->assertEquals(
            $materials[1]['id'],
            $gotMaterials[0]['id']
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', array('q' => 'desc')),
            null,
            $this->getAuthenticatedUserToken()
        );

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue(array_key_exists('learningMaterials', $result));
        $gotMaterials = $result['learningMaterials'];
        $this->assertEquals(3, count($gotMaterials));
        $this->assertEquals(
            $materials[0]['id'],
            $gotMaterials[0]['id']
        );
        $this->assertEquals(
            $materials[1]['id'],
            $gotMaterials[1]['id']
        );
        $this->assertEquals(
            $materials[2]['id'],
            $gotMaterials[2]['id']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostLearningMaterial()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterial')
            ->create();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['learningMaterials'][0];
        $uploadDate = new DateTime($responseData['uploadDate']);
        unset($responseData['id']);
        unset($responseData['uploadDate']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers
     */
    public function testPostLearningMaterialCitation()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterial')
          ->createCitation();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['learningMaterials'][0];
        $uploadDate = new DateTime($responseData['uploadDate']);
        unset($responseData['id']);
        unset($responseData['uploadDate']);
        unset($responseData['copyrightPermission']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers
     */
    public function testPostLearningMaterialLink()
    {
        $data = $this->container->get('ilioscore.dataloader.learningmaterial')
          ->createLink();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['learningMaterials'][0];
        $uploadDate = new DateTime($responseData['uploadDate']);
        unset($responseData['id']);
        unset($responseData['uploadDate']);
        unset($responseData['copyrightPermission']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers
     */
    public function testPostLearningMaterialFile()
    {
        $fs = new Filesystem();
        $fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($fakeTestFileDir)) {
            $fs->mkdir($fakeTestFileDir);
        }
        $fs->copy(__FILE__, $fakeTestFileDir . '/TESTFILE.txt');
        $fakeTestFile = new UploadedFile(
            $fakeTestFileDir . '/TESTFILE.txt',
            'TESTFILE.txt',
            'text/plain',
            filesize($fakeTestFileDir . '/TESTFILE.txt')
        );
        $this->makeJsonRequest(
            $this->client,
            'POST',
            '/upload',
            null,
            $this->getAuthenticatedUserToken(),
            array('file' => $fakeTestFile)
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true);


        $data = $this->container->get('ilioscore.dataloader.learningmaterial')
          ->createFile();
        $data['fileHash'] = $responseData['fileHash'];
        $data['filename'] = $responseData['filename'];

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['learningMaterials'][0];

        $uploadDate = new DateTime($responseData['uploadDate']);
        unset($responseData['id']);
        unset($responseData['uploadDate']);
        $uri = $responseData['absoluteFileUri'];
        unset($responseData['absoluteFileUri']);
        $this->assertRegExp('/php/', $responseData['mimetype']);
        $this->assertSame(strlen(file_get_contents(__FILE__)), $responseData['filesize']);
        unset($responseData['filesize']);
        unset($responseData['mimetype']);
        unset($data['fileHash']);

        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
        $this->client->request(
            'GET',
            $uri
        );

        $response = $this->client->getResponse();

        $this->assertEquals(CODES::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(file_get_contents(__FILE__), $response->getContent());
    }

    /**
     * @group controllers
     */
    public function testPostBadLearningMaterial()
    {
        $invalidLearningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $invalidLearningMaterial]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPostBadLearningMaterialCitation()
    {
        $invalidLearningMaterial = $this->container
          ->get('ilioscore.dataloader.learningmaterial')
          ->createInvalidCitation()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $invalidLearningMaterial]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPostBadLearningMaterialLink()
    {
        $invalidLearningMaterial = $this->container
          ->get('ilioscore.dataloader.learningmaterial')
          ->createInvalidLink()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learningmaterials'),
            json_encode(['learningMaterial' => $invalidLearningMaterial]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutLearningMaterial()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterials',
                ['id' => $data['id']]
            ),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['learningMaterial'];
        $uploadDate = new DateTime($responseData['uploadDate']);
        unset($responseData['uploadDate']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($uploadDate);
        $this->assertTrue($diff->i < 10, 'The uploadDate timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers
     */
    public function testDeleteLearningMaterial()
    {
        $learningMaterial = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learningmaterials',
                ['id' => $learningMaterial['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testLearningMaterialNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learningmaterials', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByMimeType()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[mimetype]' => 'text/plain']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByIds()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[id]' => [1,3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByRole()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[userRole]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMimeTypeAndRole()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[mimetype]' => 'text/plain', 'filters[userRole]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[courses][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[sessions][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
    }



    /**
     * @group controllers
     */
    public function testFilterByInstructor()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[instructors]' => [1,2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructorGroup()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[instructorGroups][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByTopic()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[topics][]' => 3]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMeshDescriptor()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[meshDescriptors]' => ['abc1', 'abc2']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $learningMaterials = $this->container->get('ilioscore.dataloader.learningmaterial')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learningmaterials', ['filters[sessionTypes]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['uploadDate']);
            unset($arr['absoluteFileUri']);
            return $arr;
        }, json_decode($response->getContent(), true)['learningMaterials']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learningMaterials[0]
            ),
            $data[0]
        );
    }
}
