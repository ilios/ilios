<?php
namespace Tests\App\Classes;

use App\Classes\CalendarEvent;
use App\Classes\UserEvent;
use App\Classes\UserMaterial;
use App\Entity\LearningMaterialStatusInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class UserEventTest
 * @package Tests\App\Classes
 * @covers \App\Classes\CalendarEvent
 * @covers \App\Classes\UserEvent
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
        $this->userEvent->isPublished = true;
        $this->userEvent->clearDataForUnprivilegedUsers(new \DateTime());
        $this->assertEquals(2, count($this->userEvent->learningMaterials));
        $this->assertTrue(in_array($finalizedMaterial, $this->userEvent->learningMaterials));
        $this->assertTrue(in_array($revisedMaterial, $this->userEvent->learningMaterials));
    }
}
