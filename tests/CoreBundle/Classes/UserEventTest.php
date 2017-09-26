<?php
namespace Tests\CoreBundle\Classes;

use Ilios\CoreBundle\Classes\CalendarEvent;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class UserEventTest
 * @package Tests\CoreBundle\Classes
 * @covers \Ilios\CoreBundle\Classes\CalendarEvent
 * @covers \Ilios\CoreBundle\Classes\UserEvent
 */
class UserEventTest extends TestCase
{
    /**
     * @var UserEvent
     */
    protected $userEvent;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->userEvent = new UserEvent();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->userEvent);
    }

    /**
     * @covers CalendarEvent::removeMaterialsInDraft
     */
    public function testRemoveMaterialsInDraft()
    {
        $draftMaterial = new UserMaterial();
        $draftMaterial->status = LearningMaterialStatusInterface::IN_DRAFT;
        $revisedMaterial = new UserMaterial();
        $revisedMaterial->status = LearningMaterialStatusInterface::REVISED;
        $finalizedMaterial = new UserMaterial();
        $finalizedMaterial->status = LearningMaterialStatusInterface::FINALIZED;

        $this->userEvent->learningMaterials = [ $draftMaterial, $revisedMaterial, $finalizedMaterial ];
        $this->userEvent->removeMaterialsInDraft();
        $this->assertEquals(2, count($this->userEvent->learningMaterials));
        $this->assertTrue(in_array($finalizedMaterial, $this->userEvent->learningMaterials));
        $this->assertTrue(in_array($revisedMaterial, $this->userEvent->learningMaterials));
    }
}
