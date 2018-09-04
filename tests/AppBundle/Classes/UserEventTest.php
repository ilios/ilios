<?php
namespace Tests\AppBundle\Classes;

use AppBundle\Classes\CalendarEvent;
use AppBundle\Classes\UserEvent;
use AppBundle\Classes\UserMaterial;
use AppBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class UserEventTest
 * @package Tests\AppBundle\Classes
 * @covers \AppBundle\Classes\CalendarEvent
 * @covers \AppBundle\Classes\UserEvent
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
