<?php

namespace Tests\CoreBundle\Service\CurriculumInventory\Export;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Service\Config;
use Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

/**
 * Class AggregatorTest
 * @package Tests\CoreBundle\Service\CurriculumInventory\Export
 */
class AggregatorTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */
    protected $reportManager;

    /**
     * @var m\MockInterface
     */
    protected $institutionManager;

    /**
     * @var m\MockInterface
     */
    protected $config;

    /**
     * @var Aggregator
     */
    protected $aggregator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->reportManager = m::mock(CurriculumInventoryReportManager::class);
        $this->institutionManager = m::mock(CurriculumInventoryInstitutionManager::class);
        $this->config = m::mock(Config::class);
        $this->aggregator = new Aggregator($this->reportManager, $this->institutionManager, $this->config);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->aggregator);
        unset($this->reportManager);
        unset($this->institutionManager);
        unset($this->config);
        parent::tearDown();
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::addKeywordsToEvents
     */
    public function testAddKeywordsToEvents()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::addResourceTypesToEvents
     */
    public function testAddResourceTypesToEvents()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::addCompetencyObjectReferencesToEvents
     */
    public function testAddCompetencyObjectReferencesToEvents()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::getConsolidatedObjectivesMap
     */
    public function testGetConsolidatedObjectivesMap()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::getData
     */
    public function testGetFailsIfReportHasNoProgram()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::getData
     */
    public function testGetFailsIfProgramHasNoSchool()
    {
        $this->markTestIncomplete('to be implemented.');
    }

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator::getData
     */
    public function testGetData()
    {
        $this->markTestIncomplete('to be implemented.');
    }
}
