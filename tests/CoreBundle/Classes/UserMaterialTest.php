<?php
namespace Tests\CoreBundle\Classes;

use Ilios\CoreBundle\Classes\UserMaterial;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class UserMaterialTest
 * @package Tests\CoreBundle\Classes
 * @covers \Ilios\CoreBundle\Classes\UserMaterial
 */
class UserMaterialTest extends TestCase
{
    /**
     * @var UserMaterial
     */
    protected $userMaterial;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->userMaterial = new UserMaterial();
        $this->userMaterial->id = 1;
        $this->userMaterial->courseLearningMaterial = 1;
        $this->userMaterial->sessionLearningMaterial = 1;
        $this->userMaterial->position = 1;
        $this->userMaterial->session = 1;
        $this->userMaterial->course = 1;
        $this->userMaterial->publicNotes = 'Notes';
        $this->userMaterial->required = true;
        $this->userMaterial->title = 'My Material';
        $this->userMaterial->description = 'Lorem Ipsum';
        $this->userMaterial->originalAuthor = 'Randy Bobandy';
        $this->userMaterial->absoluteFileUri = 'http://localhost/test.txt';
        $this->userMaterial->citation = 'Citation';
        $this->userMaterial->link = 'http://127.0.0.1';
        $this->userMaterial->filename = 'test.txt';
        $this->userMaterial->filesize = 1000;
        $this->userMaterial->mimetype = 'plain/text';
        $this->userMaterial->sessionTitle = 'Session 1';
        $this->userMaterial->courseTitle = 'Course 1';
        $this->userMaterial->firstOfferingDate = time();
        $this->userMaterial->instructors = [ 1, 2, 3];
        $this->userMaterial->isBlanked = false;
        $this->userMaterial->startDate = null;
        $this->userMaterial->endDate = null;
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithNoDates()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertNotBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithStartDateAndInRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->startDate = new \DateTime('2 days ago');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertNotBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithStartDateAndOutOfRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->startDate = new \DateTime('+2 days');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithEndDateAndInRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->endDate = new \DateTime('+2 days');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertNotBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithEndDateAndOutOfRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->endDate = new \DateTime('2 days ago');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithStartEndDateAndInRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->endDate = new \DateTime('2 days ago');
        $this->userMaterial->endDate = new \DateTime('+2 days');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertNotBlanked($this->userMaterial);
    }

    /**
     * @covers UserMaterial::clearTimedMaterial
     */
    public function testClearTimedMaterialWithStartEndDateAndOutOfRange()
    {
        $this->assertNotBlanked($this->userMaterial);
        $this->userMaterial->endDate = new \DateTime('4 days ago');
        $this->userMaterial->endDate = new \DateTime('2 days ago');
        $this->userMaterial->clearTimedMaterial(new \DateTime());
        $this->assertBlanked($this->userMaterial);
    }

    /**
     * @param UserMaterial $material
     */
    protected function assertBlanked(UserMaterial $material)
    {
        $this->assertTrue($material->isBlanked);
        $this->assertNotNull($material->id);
        $this->assertNotNull($material->courseLearningMaterial);
        $this->assertNotNull($material->sessionLearningMaterial);
        $this->assertNotNull($material->position);
        $this->assertNotNull($material->title);
        $this->assertNotNull($material->session);
        $this->assertNotNull($material->sessionTitle);
        $this->assertNotNull($material->course);
        $this->assertNotNull($material->courseTitle);
        $this->assertNotNull($material->firstOfferingDate);
        $this->assertNull($material->publicNotes);
        $this->assertNull($material->required);
        $this->assertNull($material->description);
        $this->assertNull($material->originalAuthor);
        $this->assertNull($material->absoluteFileUri);
        $this->assertNull($material->citation);
        $this->assertNull($material->link);
        $this->assertNull($material->filename);
        $this->assertNull($material->filesize);
        $this->assertNull($material->mimetype);
        $this->assertEmpty($material->instructors);
    }

    /**
     * @param UserMaterial $material
     */
    protected function assertNotBlanked(UserMaterial $material)
    {
        $this->assertFalse($material->isBlanked);
        $this->assertNotNull($material->id);
        $this->assertNotNull($material->courseLearningMaterial);
        $this->assertNotNull($material->sessionLearningMaterial);
        $this->assertNotNull($material->position);
        $this->assertNotNull($material->title);
        $this->assertNotNull($material->session);
        $this->assertNotNull($material->sessionTitle);
        $this->assertNotNull($material->course);
        $this->assertNotNull($material->courseTitle);
        $this->assertNotNull($material->firstOfferingDate);
        $this->assertNotNull($material->publicNotes);
        $this->assertNotNull($material->required);
        $this->assertNotNull($material->description);
        $this->assertNotNull($material->originalAuthor);
        $this->assertNotNull($material->absoluteFileUri);
        $this->assertNotNull($material->citation);
        $this->assertNotNull($material->link);
        $this->assertNotNull($material->filename);
        $this->assertNotNull($material->filesize);
        $this->assertNotNull($material->mimetype);
        $this->assertNotEmpty($material->instructors);
    }
}
