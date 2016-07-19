<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventorySequenceBlock controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventorySequenceBlockControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockSessionData',
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
    public function testGetCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventorySequenceBlock),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCurriculumInventorySequenceBlocks()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCurriculumInventorySequenceBlock()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCurriculumInventorySequenceBlock()
    {
        $invalidCurriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $invalidCurriculumInventorySequenceBlock]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutCurriculumInventorySequenceBlock()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventorySequenceBlock']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventorySequenceBlock()
    {
        $curriculumInventorySequenceBlock = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventorysequenceblocks',
                ['id' => $curriculumInventorySequenceBlock['id']]
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
    public function testCurriculumInventorySequenceBlockNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequenceblocks', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    public function testDeleteBlockFromOrderedSequenceFromStartOfSequence()
    {

        $parent = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        usort($childrenBeforeDeletion, function ($a, $b) {
            return ((int) $a > (int) $b) ? 1 : -1;
        });
        $firstBlockInSequence = $childrenBeforeDeletion[0];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocks',
                ['id' => $firstBlockInSequence['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeDeletion) - 1,
            count($childrenAfterDeletion),
            'Sequence contains one less block after deletion.'
        );
        foreach ($childrenAfterDeletion as $block) {
            $this->assertEquals(
                $block['orderInSequence'],
                $blockMap[$block['id']]['orderInSequence'] - 1,
                'Remaining blocks have shifted one position down in sequence.'
            );
        }
    }

    public function testDeleteBlockFromOrderedSequenceFromEndOfSequence()
    {

        $parent = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        usort($childrenBeforeDeletion, function ($a, $b) {
            return ((int) $a > (int) $b) ? 1 : -1;
        });
        $lastBlockInSequence = $childrenBeforeDeletion[count($childrenBeforeDeletion) - 1];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocks',
                ['id' => $lastBlockInSequence['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeDeletion) - 1,
            count($childrenAfterDeletion),
            'Sequence contains one less block after deletion.'
        );
        foreach ($childrenAfterDeletion as $block) {
            $this->assertEquals(
                $block['orderInSequence'],
                $blockMap[$block['id']]['orderInSequence'],
                'Remaining blocks maintained their position in sequence.'
            );
        }
    }

    public function testDeleteBlockFromOrderedSequenceFromMiddleOfSequence()
    {
        $parent = $this->container
            ->get('ilioscore.dataloader.curriculuminventorysequenceblock')
            ->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        usort($childrenBeforeDeletion, function ($a, $b) {
            return ((int) $a > (int) $b) ? 1 : -1;
        });
        $blockInSequence = $childrenBeforeDeletion[2];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventorysequenceblocks',
                ['id' => $blockInSequence['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterDeletion = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeDeletion) - 1,
            count($childrenAfterDeletion),
            'Sequence contains one less block after deletion.'
        );
        foreach ($childrenAfterDeletion as $block) {
            $oldOrderInSequence = $blockMap[$block['id']]['orderInSequence'];
            if ($oldOrderInSequence < $blockInSequence['orderInSequence']) {
                $this->assertEquals(
                    $block['orderInSequence'],
                    $oldOrderInSequence,
                    'Blocks with a lower sort order than deleted block maintained their position in sequence.'
                );
            } else {
                $this->assertEquals(
                    $block['orderInSequence'],
                    $oldOrderInSequence - 1,
                    'Blocks with a higher sort order than deleted block have shifted one position down in sequence.'
                );
            }
        }
    }

    public function testAddBlockToOrderedSequence()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testMoveBlockInOrderedSequence()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPostBlockWithInvalidOrderInSequence()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPutBlockWithInvalidOrderInSequence()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testChangeChildSequenceOrder()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
