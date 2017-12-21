<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventorySequenceBlock API endpoint Test.
 * @group api_4
 */
class CurriculumInventorySequenceBlockTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventorySequenceBlocks';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'required' => ['required', $this->getFaker()->randomDigit],
            'childSequenceOrder' => ['childSequenceOrder', 2],
            'orderInSequence' => ['orderInSequence', 2],
            'minimum' => ['minimum', 2],
            'maximum' => ['maximum', $this->getFaker()->randomDigit],
            'track' => ['track', false],
            'startDate' => ['startDate', $this->getFaker()->iso8601, $skipped = true],
            'endDate' => ['endDate', $this->getFaker()->iso8601, $skipped = true],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'academicLevel' => ['academicLevel', 2],
            'course' => ['course', 5],
            'parent' => ['parent', 2],
            'children' => ['children', [1], $skipped = true],
            'report' => ['report', 2],
            'sessions' => ['sessions', [1, 2]],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'Nested Sequence Block 1']],
            'description' => [[0], ['description' => 'first description']],
            'required' => [[0], ['required' => CurriculumInventorySequenceBlockInterface::REQUIRED]],
            'optional' => [[1, 2, 3, 4], ['required' => CurriculumInventorySequenceBlockInterface::OPTIONAL]],
            'childSequenceOrder' => [[0], ['childSequenceOrder' => CurriculumInventorySequenceBlockInterface::ORDERED]],
            'childSequenceOpt' => [[1, 2, 3, 4], [
                'childSequenceOrder' => CurriculumInventorySequenceBlockInterface::OPTIONAL]
            ],
            'orderInSequence' => [[1], ['orderInSequence' => 1]],
            'minimum' => [[0, 1, 2, 3, 4], ['minimum' => 1]],
            'maximum' => [[0, 1, 2, 3, 4], ['maximum' => 1]],
            'NoTrack' => [[1, 2, 3, 4], ['track' => false]],
            'track' => [[0], ['track' => true]],
            'duration' => [[1, 2, 3, 4], ['duration' => 1]],
            'academicLevel' => [[0], ['academicLevel' => 1]],
            'parent' => [[1, 2, 3, 4], ['parent' => 1]],
//            'children' => [[0], ['children' => [3]]],
            'report' => [[0, 1, 2, 3, 4], ['report' => 1]],
//            'sessions' => [[0], ['sessions' => [1]]],
        ];
    }

    public function testDeleteBlockFromStartOfOrderedSequence()
    {
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);

        $firstBlockInSequence = $childrenBeforeDeletion[0];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->deleteOne('curriculuminventorysequenceblocks', $firstBlockInSequence['id']);

        $childrenAfterDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenAfterDeletion = $this->sortOrderedSequence($childrenAfterDeletion);

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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);

        $lastBlockInSequence = $childrenBeforeDeletion[count($childrenBeforeDeletion) - 1];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->deleteOne('curriculuminventorysequenceblocks', $lastBlockInSequence['id']);

        $childrenAfterDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenAfterDeletion = $this->sortOrderedSequence($childrenAfterDeletion);

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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeDeletion = $this->sortOrderedSequence($childrenBeforeDeletion);
        $blockInSequence = $childrenBeforeDeletion[2];

        $blockMap = [];
        foreach ($childrenBeforeDeletion as $block) {
            $blockMap[$block['id']] = $block;
        }

        $this->deleteOne('curriculuminventorysequenceblocks', $blockInSequence['id']);

        $childrenAfterDeletion = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenAfterDeletion = $this->sortOrderedSequence($childrenAfterDeletion);

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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        $postData['orderInSequence']  = 1;
        $newBlock = $this->postOne(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlock',
            'curriculumInventorySequenceBlocks',
            $postData
        );

        $childrenAfterAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        $postData['orderInSequence']  = count($childrenBeforeAddition) + 1;
        $newBlock = $this->postOne(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlock',
            'curriculumInventorySequenceBlocks',
            $postData
        );

        $childrenAfterAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );

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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeAddition = $this->sortOrderedSequence($childrenBeforeAddition);

        $blockMap = [];
        foreach ($childrenBeforeAddition as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        $postData['orderInSequence']  = 2;
        $newBlock = $this->postOne(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlock',
            'curriculumInventorySequenceBlocks',
            $postData
        );

        $childrenAfterAddition = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );

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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeMove = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeMove = $this->sortOrderedSequence($childrenBeforeMove);

        $blockMap = [];
        foreach ($childrenBeforeMove as $block) {
            $blockMap[$block['id']] = $block;
        }

        $postData = $dataLoader->create();
        $postData['orderInSequence']  = 2;

        $postData = $childrenBeforeMove[1];
        $oldPosition = $postData['orderInSequence'];
        $newPosition = $childrenBeforeMove[count($childrenBeforeMove) - 2]['orderInSequence'];
        $blockId = $postData['id'];
        $postData['orderInSequence'] = $newPosition;

        $updatedBlock = $this->putOne(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlock',
            $blockId,
            $postData
        );

        $this->assertEquals(
            $postData['orderInSequence'],
            $updatedBlock['orderInSequence'],
            'Block has been moved into the proper position.'
        );

        $childrenAfterMove = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );

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

        $updatedBlock = $this->putOne(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlock',
            $blockId,
            $postData
        );
        $this->assertEquals(
            $postData['orderInSequence'],
            $updatedBlock['orderInSequence'],
            'Block has been moved back into its original position.'
        );

        $childrenAfterMove = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
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
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeMove = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeMove = $this->sortOrderedSequence($childrenBeforeMove);

        $blockMap = [];
        foreach ($childrenBeforeMove as $block) {
            $blockMap[$block['id']] = $block;
        }

        $block = $dataLoader->create();
        $block['parent'] = $parent['id'];
        $block['orderInSequence'] = 0; // out of bounds on lower boundary

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'curriculuminventorysequenceblocks']),
            json_encode(['curriculumInventorySequenceBlocks' => [$block]]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        //Fails on lower boundary
        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        $block['orderInSequence'] = count($parent['children']) + 2; // out of bounds on upper boundary

        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'curriculuminventorysequenceblocks']),
            json_encode(['curriculumInventorySequenceBlocks' => [$block]]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        //Fails on upper boundary
        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        $block['orderInSequence'] = count($parent['children']) + 1; // ok
        $this->postTest($block, $block);
    }

    public function testPutBlockWithInvalidOrderInSequence()
    {
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();

        $childrenBeforeMove = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        $childrenBeforeMove = $this->sortOrderedSequence($childrenBeforeMove);

        $blockMap = [];
        foreach ($childrenBeforeMove as $block) {
            $blockMap[$block['id']] = $block;
        }

        $block = $dataLoader->create();
        $blockId = $block['id'];
        $block['parent'] = $parent['id'];
        $block['orderInSequence'] = 0; // out of bounds on lower boundary

        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', [
                'version' => 'v1',
                'object' => 'curriculuminventorysequenceblocks',
                'id' => $blockId
            ]),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        //Fails on lower boundary
        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        $block['orderInSequence'] = count($parent['children']) + 2; // out of bounds on upper boundary

        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', [
                'version' => 'v1',
                'object' => 'curriculuminventorysequenceblocks',
                'id' => $blockId
            ]),
            json_encode(['curriculumInventorySequenceBlock' => $block]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        //Fails on upper boundary
        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        $block['orderInSequence'] = count($parent['children']) + 1; // ok
        $this->putTest($block, $block, $blockId, $new = true);
    }

    public function testChangeChildSequenceOrder()
    {
        $dataLoader = $this->getDataLoader();
        $parent = $dataLoader->getOne();
        $this->assertEquals($parent['childSequenceOrder'], CurriculumInventorySequenceBlockInterface::ORDERED);

        $parentId = $parent['id'];
        $parent['childSequenceOrder'] = CurriculumInventorySequenceBlockInterface::UNORDERED;
        $this->putOne('curriculuminventorysequenceblocks', 'curriculumInventorySequenceBlock', $parentId, $parent);

        $unorderedSequence = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
        foreach ($unorderedSequence as $block) {
            $this->assertEquals(0, $block['orderInSequence'], 'Blocks in an unordered sequence hold a 0 position.');
        }

        $parent['childSequenceOrder'] = CurriculumInventorySequenceBlockInterface::ORDERED;
        $this->putOne('curriculuminventorysequenceblocks', 'curriculumInventorySequenceBlock', $parentId, $parent);
        $orderedSequence = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );

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
        $this->putOne('curriculuminventorysequenceblocks', 'curriculumInventorySequenceBlock', $parentId, $parent);
        $unorderedSequence = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[parent]' => $parent['id']]
        );
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
