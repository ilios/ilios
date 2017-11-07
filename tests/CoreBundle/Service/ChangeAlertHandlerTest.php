<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Alert;
use Ilios\CoreBundle\Entity\AlertChangeType;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\AlertInterface;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\InstructorGroup;
use Ilios\CoreBundle\Entity\LearnerGroup;
use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManager;
use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Service\ChangeAlertHandler;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * Class ChangeAlertHandlerTest
 * @package Tests\CoreBundle\Service
 */
class ChangeAlertHandlerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */
    protected $mockUserManager;

    /**
     * @var m\MockInterface
     */
    protected $mockAlertManager;

    /**
     * @var m\MockInterface
     */
    protected $mockAlertChangeTypeManager;
    /**
     * @var ChangeAlertHandler
     */
    protected $changeAlertHandler;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->mockAlertManager = m::mock(AlertManager::class);
        $this->mockAlertChangeTypeManager = m::mock(AlertChangeTypeManager::class);
        $this->mockUserManager = m::mock(UserManager::class);
        $this->changeAlertHandler = new ChangeAlertHandler(
            $this->mockAlertManager,
            $this->mockAlertChangeTypeManager,
            $this->mockUserManager
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->changeAlertHandler);
        unset($this->mockAlertManager);
        unset($this->mockAlertChangeTypeManager);
        unset($this->mockUserManager);
    }

    /**
     * @covers ChangeAlertHandler::createAlertForNewOffering()
     */
    public function testCreateAlertForNewOffering()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setSession($session);

        $instigator = new User();
        $instigator->setId(1);

        $alertChangeType = new AlertChangeType();
        $alertChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING);

        $alert = new Alert();
        $alert->setId(10);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->andReturn($alertChangeType);

        $this->mockAlertManager
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertManager
            ->shouldReceive('update')
            ->withArgs([$alert, false]);

        $this->changeAlertHandler->createAlertForNewOffering($offering, $instigator);

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getTableName(), 'offering');
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $this->assertEquals($alert->getChangeTypes()[0], $alertChangeType);
    }

    /**
     * @covers ChangeAlertHandler::createOrUpdateAlertForUpdatedOffering()
     */
    public function testCreateOrUpdateAlertForUpdatedOfferingExistingAlert()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setId(1);
        $offering->setSession($session);
        $startDate = new \DateTime();
        $offering->setStartDate($startDate);
        $offering->setEndDate(new \DateTime());
        $offering->setRoom('Room A');
        $offering->setSite('Site A');

        $instigator = new User();
        $instigator->setId(1);

        $originalProperties = [
            'learners' => [],
            'learnerGroups' => [],
            'instructors' => [],
            'instructorGroups' => [],
            'startDate' => $startDate->getTimestamp(),
            'endDate' => (new \DateTime('+1 year'))->getTimestamp(),
            'site' => 'some other site',
            'room' => 'some other room',
        ];

        $alert = new Alert();

        $this->mockAlertManager
            ->shouldReceive('findOneBy')
            ->withArgs([['dispatched' => false, 'tableName' => 'offering', 'tableRowId' => $offering->getId()]])
            ->andReturn(null);

        $this->mockAlertManager
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertManager
            ->shouldReceive('update')
            ->withArgs([$alert, false]);

        $instructorChangeType = new AlertChangeType();
        $instructorChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR);

        $timeChangeType = new AlertChangeType();
        $timeChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_TIME);

        $locationChangeType = new AlertChangeType();
        $locationChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_LOCATION);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION]])
            ->times(2)
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME]])
            ->once()
            ->andReturn($timeChangeType);

        $alert->addChangeType($instructorChangeType);
        $alert->addChangeType($timeChangeType);

        $this->changeAlertHandler->createOrUpdateAlertForUpdatedOffering($offering, $instigator, $originalProperties);

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getTableName(), 'offering');
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $changeTypes = $alert->getChangeTypes();
        $this->assertEquals(count($changeTypes), 3);
        $this->assertTrue($changeTypes->contains($instructorChangeType));
        $this->assertTrue($changeTypes->contains($timeChangeType));
        $this->assertTrue($changeTypes->contains($locationChangeType));
    }

    /**
     * @covers ChangeAlertHandler::createOrUpdateAlertForUpdatedOffering()
     */
    public function testCreateOrUpdateAlertForUpdatedOfferingNewAlert()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setId(1);
        $offering->setSession($session);
        $offering->setStartDate(new \DateTime());
        $offering->setEndDate(new \DateTime());
        $offering->setRoom('Room A');
        $offering->setSite('Site A');

        $instigator = new User();
        $instigator->setId(1);

        $originalProperties = [
            'learners' => [1, 3, 4],
            'learnerGroups' => [1, 3],
            'instructors' => [1],
            'instructorGroups' => [2],
            'startDate' => (new \DateTime('+1 year'))->getTimestamp(),
            'endDate' => (new \DateTime('+1 year'))->getTimestamp(),
            'site' => 'some other site',
            'room' => 'some other room',
        ];

        $alert = new Alert();

        $this->mockAlertManager
            ->shouldReceive('findOneBy')
            ->withArgs([['dispatched' => false, 'tableName' => 'offering', 'tableRowId' => $offering->getId()]])
            ->andReturn(null);

        $this->mockAlertManager
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertManager
            ->shouldReceive('update')
            ->withArgs([$alert, false]);

        $instructorChangeType = new AlertChangeType();
        $instructorChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR);

        $learnerGroupChangeType = new AlertChangeType();
        $learnerGroupChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP);

        $timeChangeType = new AlertChangeType();
        $timeChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_TIME);

        $locationChangeType = new AlertChangeType();
        $locationChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_LOCATION);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION]])
            ->times(2)
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME]])
            ->times(2)
            ->andReturn($timeChangeType);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP ]])
            ->times(2)
            ->andReturn($learnerGroupChangeType);

        $this->mockAlertChangeTypeManager
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR ]])
            ->times(2)
            ->andReturn($instructorChangeType);

        $this->changeAlertHandler->createOrUpdateAlertForUpdatedOffering($offering, $instigator, $originalProperties);

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getTableName(), 'offering');
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $changeTypes = $alert->getChangeTypes();
        $this->assertEquals(count($changeTypes), 4);
        $this->assertTrue($changeTypes->contains($instructorChangeType));
        $this->assertTrue($changeTypes->contains($learnerGroupChangeType));
        $this->assertTrue($changeTypes->contains($timeChangeType));
        $this->assertTrue($changeTypes->contains($locationChangeType));
    }
}
