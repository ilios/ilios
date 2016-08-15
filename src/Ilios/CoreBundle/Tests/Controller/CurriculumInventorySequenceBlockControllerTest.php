<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

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
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
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

    public function testDeleteBlockFromStartOfOrderedSequence()
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
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);
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

    public function testDeleteBlockFromEndOfOrderedSequence()
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
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);
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

    public function testDeleteBlockFromMiddleOfOrderedSequence()
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
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);
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

    public function testAddBlockToStartOfOrderedSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);
        $postData['orderInSequence']  = 1;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $newBlock = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0];

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];

        $this->assertEquals(
            count($childrenBeforeAddition) + 1,
            count($childrenAfterAddition),
            'Sequence contains one more block after addition.'
        );

        foreach ($childrenAfterAddition as $block) {
            if ($newBlock['id'] === $block['id']) {
                $this->assertEquals(
                    $block['orderInSequence'],
                    1,
                    'New block holds first position in sequence.'
                );
            } else {
                $oldOrderInSequence = $blockMap[$block['id']]['orderInSequence'];
                $this->assertEquals(
                    $block['orderInSequence'],
                    $oldOrderInSequence + 1,
                    'Pre-existing blocks have shifted up one position in the sequence.'
                );
            }
        }
    }

    public function testAddBlockToEndOfOrderedSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);
        $postData['orderInSequence']  = count($childrenBeforeAddition) + 1;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $newBlock = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0];

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeAddition) + 1,
            count($childrenAfterAddition),
            'Sequence contains one more block after addition.'
        );
        foreach ($childrenAfterAddition as $block) {
            if ($newBlock['id'] === $block['id']) {
                $this->assertEquals(
                    $block['orderInSequence'],
                    count($childrenAfterAddition),
                    'New block holds last position in sequence.'
                );
            } else {
                $oldOrderInSequence = $blockMap[$block['id']]['orderInSequence'];
                $this->assertEquals(
                    $block['orderInSequence'],
                    $oldOrderInSequence,
                    'Pre-existing blocks maintained their position in the sequence.'
                );
            }
        }
    }

    public function testAddBlockToMiddleOfOrderedSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);
        $postData['orderInSequence']  = 2;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $newBlock = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'][0];

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterAddition = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeAddition) + 1,
            count($childrenAfterAddition),
            'Sequence contains one more block after addition.'
        );
        foreach ($childrenAfterAddition as $block) {
            if ($newBlock['id'] === $block['id']) {
                $this->assertEquals(
                    $block['orderInSequence'],
                    $newBlock['orderInSequence'],
                    'New block holds given position in sequence.'
                );
            } else {
                $oldOrderInSequence = $blockMap[$block['id']]['orderInSequence'];
                if ($oldOrderInSequence < $newBlock['orderInSequence']) {
                    $this->assertEquals(
                        $block['orderInSequence'],
                        $oldOrderInSequence,
                        'Pre-existing blocks maintained their position in the sequence.'
                    );
                } else {
                    $this->assertEquals(
                        $block['orderInSequence'],
                        $oldOrderInSequence + 1,
                        'Pre-existing blocks shifted up one position in the sequence.'
                    );
                }
            }
        }
    }

    public function testMoveBlockInOrderedSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenBeforeMove = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $childrenBeforeMove = $this->sortOrderedSequence($childrenBeforeMove);

        $blockMap = [];
        foreach ($childrenBeforeMove as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $childrenBeforeMove[1];
        $oldPosition = $postData['orderInSequence'];
        $newPosition = $childrenBeforeMove[count($childrenBeforeMove) - 2]['orderInSequence'];
        $blockId = $postData['id'];

        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['children']);
        $postData['orderInSequence'] = $newPosition;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $blockId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $updatedBlock = json_decode($response->getContent(), true)['curriculumInventorySequenceBlock'];

        $this->assertEquals(
            $postData['orderInSequence'],
            $updatedBlock['orderInSequence'],
            'Block has been moved into the proper position.'
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterMove = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $this->assertEquals(
            count($childrenBeforeMove),
            count($childrenAfterMove),
            'Sequence contains the same number of blocks as before.'
        );

        $childrenAfterMove = $this->sortOrderedSequence($childrenAfterMove);

        for ($i = 0, $n = count($childrenAfterMove); $i < $n; $i++) {
            $this->assertEquals(
                $i + 1,
                $childrenAfterMove[$i]['orderInSequence'],
                'Sequence is sorted incrementally, without gaps.'
            );
        }

        $filteredOldSequence = array_values(array_filter($childrenBeforeMove, function ($block) use ($blockId) {
            return $block['id'] !== $blockId;
        }));
        $filteredNewSequence = array_values(array_filter($childrenAfterMove, function ($block) use ($blockId) {
            return $block['id'] !== $blockId;
        }));

        for ($i = 0, $n = count($filteredNewSequence); $i < $n; $i++) {
            $this->assertEquals(
                $filteredOldSequence[$i]['id'],
                $filteredNewSequence[$i]['id'],
                'All blocks except the moved one remain their order in sequence.'
            );
        }

        // move block back into its original position
        $postData['orderInSequence'] = $oldPosition;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' => $blockId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $updatedBlock = json_decode($response->getContent(), true)['curriculumInventorySequenceBlock'];

        $this->assertEquals(
            $postData['orderInSequence'],
            $updatedBlock['orderInSequence'],
            'Block has been moved back into its original position.'
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parent['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $childrenAfterMove = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];

        $childrenAfterMove = $this->sortOrderedSequence($childrenAfterMove);

        for ($i = 0, $n = count($childrenAfterMove); $i < $n; $i++) {
            $this->assertEquals(
                $i + 1,
                $childrenAfterMove[$i]['orderInSequence'],
                'Sequence is sorted incrementally, without gaps.'
            );
        }

        for ($i = 0, $n = count($childrenAfterMove); $i < $n; $i++) {
            $this->assertEquals(
                $childrenBeforeMove[$i]['id'],
                $childrenAfterMove[$i]['id'],
                'All blocks are back in their original positions.'
            );
        }
    }

    public function testPostBlockWithInvalidOrderInSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $block = $dataLoader->create();
        $block['parent'] = $parent['id'];
        unset($block['id']);
        unset($block['sessions']);
        unset($block['children']);

        $block['orderInSequence'] = 0; // out of bounds on lower boundary
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), 'Fails on lower boundary.');

        $block['orderInSequence'] = count($parent['children']) + 2; // out of bounds on upper boundary
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), 'Fails on upper boundary.');

        $block['orderInSequence'] = count($parent['children']) + 1; // ok
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventorysequenceblocks'),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
    }

    public function testPutBlockWithInvalidOrderInSequence()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();

        $block = $dataLoader->create();
        $block['parent'] = $parent['id'];
        $blockId = $block['id'];
        unset($block['id']);
        unset($block['sessions']);
        unset($block['children']);

        $block['orderInSequence'] = 0; // out of bounds on lower boundary
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$blockId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), 'Fails on lower boundary.');

        $block['orderInSequence'] = count($parent['children']) + 2; // out of bounds on upper boundary
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$blockId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode(), 'Fails on upper boundary.');

        $block['orderInSequence'] = count($parent['children']) + 1; // ok
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$blockId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
    }

    public function testChangeChildSequenceOrder()
    {
        $dataLoader = $this->container->get('ilioscore.dataloader.curriculuminventorysequenceblock');
        $parent = $dataLoader->getOne();
        $this->assertEquals($parent['childSequenceOrder'], CurriculumInventorySequenceBlockInterface::ORDERED);

        $parentId = $parent['id'];
        unset($parent['id']);
        unset($parent['sessions']);
        unset($parent['children']);

        $parent['childSequenceOrder'] = CurriculumInventorySequenceBlockInterface::UNORDERED;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$parentId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $parent]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parentId]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $unorderedSequence = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        foreach ($unorderedSequence as $block) {
            $this->assertEquals(0, $block['orderInSequence'], 'Blocks in an unordered sequence hold a 0 position.');
        }

        $parent['childSequenceOrder'] = CurriculumInventorySequenceBlockInterface::ORDERED;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$parentId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $parent]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parentId]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $orderedSequence = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        $orderedSequence = $this->sortOrderedSequence($orderedSequence);
        for ($i = 0, $n = count($orderedSequence); $i < $n; $i++) {
            $block = $orderedSequence[$i];
            $this->assertEquals(
                $i + 1,
                $block['orderInSequence'],
                'Blocks in an ordered sequence are sorted sequentially.'
            );
        }

        $parent['childSequenceOrder'] = CurriculumInventorySequenceBlockInterface::PARALLEL;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventorysequenceblocks',
                ['id' =>$parentId]
            ),
            json_encode(['curriculumInventorySequenceBlock' => $parent]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventorysequenceblocks', ['filters[parent]' => $parentId]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $unorderedSequence = json_decode($response->getContent(), true)['curriculumInventorySequenceBlocks'];
        foreach ($unorderedSequence as $block) {
            $this->assertEquals(0, $block['orderInSequence'], 'Blocks in a parallel sequence hold a 0 position.');
        }
    }

    /**
     * Sorts the blocks in a given sequence by their 'order in sequence' values.
     * @param CurriculumInventorySequenceBlockInterface[] $sequence The unsorted sequence.
     * @return CurriculumInventorySequenceBlockInterface[] The sorted sequence.
     */
    protected function sortOrderedSequence(array $sequence)
    {
        $sortedSequence = array_values($sequence); // cheap-o way of copying the array
        usort($sortedSequence, function ($a, $b) {
            return ((int) $a['orderInSequence'] > (int) $b['orderInSequence']) ? 1 : -1;
        });
        return $sortedSequence;
    }
}
