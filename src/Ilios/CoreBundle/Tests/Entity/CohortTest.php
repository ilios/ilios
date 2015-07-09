<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Cohort;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Cohort
 */
class CohortTest extends EntityBase
{
    /**
     * @var Cohort
     */
    protected $object;

    /**
     * Instantiate a Cohort object
     */
    protected function setUp()
    {
        $this->object = new Cohort;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('up to sixty char');
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setTitle
     * @covers Ilios\CoreBundle\Entity\Cohort::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setProgramYear
     * @covers Ilios\CoreBundle\Entity\Cohort::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::addCourse
     */
    public function testAddCourse()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $this->object->addCourse($goodCourse);
        $this->object->addCourse($deletedCourse);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::getCourses
     */
    public function testGetCourses()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $collection = new ArrayCollection([$goodCourse, $deletedCourse]);
        $this->object->setCourses($collection);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }
}
