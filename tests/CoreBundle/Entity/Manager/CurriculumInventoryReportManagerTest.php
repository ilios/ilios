<?php

namespace Tests\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Entity\Repository\CurriculumInventoryReportRepository;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * Class CurriculumInventoryReportManagerTest
 * @package Tests\CoreBundle\Entity\Manager
 */
class CurriculumInventoryReportManagerTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $repository;

    /**
     * @var m\MockInterface
     */
    protected $registry;

    /**
     * @var CurriculumInventoryReportManager
     */
    protected $manager;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $class = CurriculumInventoryReport::class;
        $em = m::mock(EntityManager::class);
        $this->repository = m::mock(CurriculumInventoryReportRepository::class);
        $this->registry = m::mock(\Doctrine\Bundle\DoctrineBundle\Registry::class)
            ->shouldReceive('getManagerForClass')
            ->andReturn($em)
            ->shouldReceive('getRepository')
            ->andReturn($this->repository)
            ->mock();
        $this->manager = new CurriculumInventoryReportManager($this->registry, $class);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->registry);
        unset($this->manager);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getEventReferencesForSequenceBlocks
     */
    public function testGetEventReferencesForSequenceBlocks()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getCompetencyObjectReferencesForSequenceBlocks
     */
    public function testGetCompetencyObjectReferencesForSequenceBlocks()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getCompetencyObjectReferencesForEvents
     */
    public function testGetCompetencyObjectReferencesForEvents()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getSessionObjectivesToCourseObjectivesRelations
     */
    public function testGetSessionObjectivesToCourseObjectivesRelations()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getCourseObjectivesToProgramObjectivesRelations
     */
    public function testGetCourseObjectivesToProgramObjectivesRelations()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getProgramObjectivesToPcrsRelations
     */
    public function testGetProgramObjectivesToPcrsRelations()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getEventsFromIlmOnlySessions
     */
    public function testGetEventsFromIlmOnlySessions()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getEventsFromOfferingsOnlySessions
     */
    public function testGetEventsFromOfferingsOnlySessions()
    {
        $this->markTestIncomplete('to be implemented');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager::getEventsFromIlmSessionsWithOfferings
     */
    public function testGetEventsFromIlmSessionsWithOfferings()
    {
        $this->markTestIncomplete('to be implemented');
    }
}
