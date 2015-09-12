<?php

namespace Ilios\CoreBundle\Tests\Classes\CurriculumInventory;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Classes\CurriculumInventory\SequenceBlockHierarchySorter;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class SequenceBlockHierarchySorterTest
 * @package Ilios\CoreBundle\Tests\Classes
 */
class SequenceBlockHierarchySorterTest extends TestCase
{
    /**
     * @var array
     */
    protected $fixtures;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $fixtures = null;
    }

    /**
     * @covers SequenceBlockHierarchySorter::compareSequenceBlocksWithDefaultStrategy
     * @dataProvider testCompareSequenceBlocksWithDefaultStrategyProvider
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface $a
     * @param \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface $b
     * @param $expected
     */
    public function testCompareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b,
        $expected
    ) {
        $this->assertEquals($expected, SequenceBlockHierarchySorter::compareSequenceBlocksWithDefaultStrategy($a, $b));
    }

    /**
     * @return array
     */
    public function testCompareSequenceBlocksWithDefaultStrategyProvider()
    {
        $fixtures = $this->getFixtures();

        return [
            [
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                0,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['smetana']),
                $this->createSequenceBlock($fixtures['composers']['smetana2']),
                -1,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['bach']),
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                1,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['wagner']),
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                1,
            ],
        ];
    }

    /**
     * @covers SequenceBlockHierarchySorter::compareSequenceBlocksWithOrderedStrategy
     * @dataProvider testCompareSequenceBlocksWithOrderedStrategyProvider
     *
     * @param \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface $a
     * @param \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface $b
     * @param $expected
     */
    public function testCompareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b,
        $expected
    ) {
        $this->assertEquals($expected, SequenceBlockHierarchySorter::compareSequenceBlocksWithOrderedStrategy($a, $b));
    }

    /**
     * @return array
     */
    public function testCompareSequenceBlocksWithOrderedStrategyProvider()
    {
        $fixtures = $this->getFixtures();

        return [
            [
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                0,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['bruckner']),
                $this->createSequenceBlock($fixtures['composers']['wagner']),
                0,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['smetana']),
                $this->createSequenceBlock($fixtures['composers']['wagner']),
                1,
            ],
            [
                $this->createSequenceBlock($fixtures['composers']['wagner']),
                $this->createSequenceBlock($fixtures['composers']['smetana']),
                -1,
            ],
        ];
    }

    /**
     * @covers SequenceBlockHierarchySorter::sort
     * @dataProvider testSortProvider
     *
     * @param ArrayCollection $input
     * @param ArrayCollection $expected
     */
    public function testSort(ArrayCollection $input, ArrayCollection $expected)
    {
        $sorter = new SequenceBlockHierarchySorter();
        $actual = $sorter->sort($input);
        $this->assertSequenceBlockHierarchyEquals($actual, $expected);
    }

    /**
     * @return array
     */
    public function testSortProvider()
    {
        $fixtures = $this->getFixtures();
        $input = new ArrayCollection(
            [
                $this->createSequenceBlock(
                    $fixtures['eras']['baroque'],
                    new ArrayCollection(
                        [
                            $this->createSequenceBlock($fixtures['composers']['handel']),
                            $this->createSequenceBlock($fixtures['composers']['bach']),
                        ]
                    )
                ),
                $this->createSequenceBlock(
                    $fixtures['eras']['romantic'],
                    new ArrayCollection(
                        [
                            $this->createSequenceBlock($fixtures['composers']['bruckner']),
                            $this->createSequenceBlock($fixtures['composers']['smetana']),
                            $this->createSequenceBlock($fixtures['composers']['wagner']),
                        ]
                    )
                ),
            ]
        );

        $expected = new ArrayCollection(
            [
                $this->createSequenceBlock(
                    $fixtures['eras']['romantic'],
                    new ArrayCollection(
                        [
                            $this->createSequenceBlock($fixtures['composers']['bruckner']),
                            $this->createSequenceBlock($fixtures['composers']['wagner']),
                            $this->createSequenceBlock($fixtures['composers']['smetana']),
                        ]
                    )
                ),
                $this->createSequenceBlock(
                    $fixtures['eras']['baroque'],
                    new ArrayCollection(
                        [
                            $this->createSequenceBlock($fixtures['composers']['bach']),
                            $this->createSequenceBlock($fixtures['composers']['handel']),
                        ]
                    )
                ),
            ]
        );

        return [
            [$input, $expected],
        ];
    }

    /**
     * @param array $data
     * @param ArrayCollection $children
     * @return CurriculumInventorySequenceBlockInterface
     */
    protected function createSequenceBlock(array $data, ArrayCollection $children = null)
    {
        $block = new CurriculumInventorySequenceBlock();
        $block->setId($data['id']);
        $block->setTitle($data['title']);
        $block->setAcademicLevel($data['level']);
        $block->setOrderInSequence(array_key_exists('order_in_sequence', $data) ? $data['order_in_sequence'] : 0);
        $block->setStartDate(array_key_exists('start_date', $data) ? $data['start_date'] : null);
        $block->setEndDate(array_key_exists('end_date', $data) ? $data['end_date'] : null);
        $block->setChildSequenceOrder(
            array_key_exists('child_sequence_order', $data) ?
            $data['child_sequence_order'] : CurriculumInventorySequenceBlockInterface::UNORDERED
        );
        $block->setChildren(isset($children) ? $children : new ArrayCollection());

        return $block;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $actual
     * @param \Doctrine\Common\Collections\ArrayCollection $expected
     */
    protected function assertSequenceBlockHierarchyEquals(ArrayCollection $actual, ArrayCollection $expected)
    {
        $multipleIterator = new \MultipleIterator(\MultipleIterator::MIT_NEED_ALL | \MultipleIterator::MIT_KEYS_ASSOC);
        $multipleIterator->attachIterator($actual->getIterator(), 'actual');
        $multipleIterator->attachIterator($expected->getIterator(), 'expected');

        /** @var CurriculumInventorySequenceBlockInterface[] $elements */
        foreach ($multipleIterator as $elements) {
            $this->assertEquals($elements['actual']->getId(), $elements['expected']->getId());

            if (!$elements['expected']->getChildren()->isEmpty()) {
                $this->assertSequenceBlockHierarchyEquals(
                    $elements['actual']->getChildren(),
                    $elements['expected']->getChildren()
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function getFixtures()
    {
        $fixtures['levels'] = [];
        for ($i = 1; $i <= 10; $i++) {
            $level = new CurriculumInventoryAcademicLevel();
            $level->setId($i);
            $level->setLevel($i);
            $fixtures['levels'][$i] = $level;
        }

        $fixtures['eras'] = [
            'baroque' => [
                'id' => 1,
                'title' => 'Baroque',
                'level' => $fixtures['levels'][3],
                'child_sort_order' => CurriculumInventorySequenceBlockInterface::UNORDERED,
            ],
            'romantic' => [
                'id' => 2,
                'title' => 'Romantic',
                'level' => $fixtures['levels'][1],
                'child_sort_order' => CurriculumInventorySequenceBlockInterface::ORDERED,
            ],
        ];

        $fixtures['composers'] = [
            'bach' => [
                'id' => 3,
                'title' => 'Johann Sebastian Bach', // that's the dude from SKID ROW!
                'level' => $fixtures['levels'][1],
                'order_in_sequence' => 10,
            ],
            'handel' => [
                'id' => 4,
                'title' => 'Georg Friedrich Handel',
                'level' => $fixtures['levels'][4],
                'order_in_sequence' => 10,
            ],
            'bruckner' => [
                'id' => 5,
                'title' => 'Anton Bruckner',
                'level' => $fixtures['levels'][1],
                'order_in_sequence' => 2,
            ],
            'wagner' => [
                'id' => 6,
                'title' => 'Richard Wagner',
                'level' => $fixtures['levels'][6],
                'order_in_sequence' => 2,
            ],
            'smetana' => [
                'id' => 7,
                'title' => 'Bedrich Smetana',
                'level' => $fixtures['levels'][10],
                'order_in_sequence' => 7,
            ],
            'smetana2' => [
                'id' => 8,
                'title' => 'Bedrich Smetana',
                'level' => $fixtures['levels'][10],
                'order_in_sequence' => 7,
            ],
        ];

        return $fixtures;
    }
}
