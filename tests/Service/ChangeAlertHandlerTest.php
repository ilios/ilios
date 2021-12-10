<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Alert;
use App\Entity\AlertChangeType;
use App\Entity\AlertChangeTypeInterface;
use App\Entity\Course;
use App\Entity\Offering;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\AlertChangeTypeRepository;
use App\Repository\AlertRepository;
use App\Service\ChangeAlertHandler;
use DateTime;
use Mockery as m;
use App\Tests\TestCase;

/**
 * Class ChangeAlertHandlerTest
 * @package App\Tests\Service
 * @coversDefaultClass \App\Service\ChangeAlertHandler
 */
class ChangeAlertHandlerTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $mockAlertRepository;

    /**
     * @var m\MockInterface
     */
    protected $mockAlertChangeTypeRepository;
    /**
     * @var ChangeAlertHandler
     */
    protected $changeAlertHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockAlertRepository = m::mock(AlertRepository::class);
        $this->mockAlertChangeTypeRepository = m::mock(AlertChangeTypeRepository::class);
        $this->changeAlertHandler = new ChangeAlertHandler(
            $this->mockAlertRepository,
            $this->mockAlertChangeTypeRepository
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->changeAlertHandler);
        unset($this->mockAlertRepository);
        unset($this->mockAlertChangeTypeRepository);
    }

    /**
     * @covers ::createAlertForNewOffering()
     */
    public function testCreateAlertForNewOffering()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setId(2);
        $offering->setSession($session);

        $instigator = new User();
        $instigator->setId(1);

        $alertChangeType = new AlertChangeType();
        $alertChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING);

        $alert = new Alert();
        $alert->setId(10);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->andReturn($alertChangeType);

        $this->mockAlertRepository
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertRepository
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
     * @covers ::createOrUpdateAlertForUpdatedOffering()
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
        $startDate = new DateTime();
        $offering->setStartDate($startDate);
        $offering->setEndDate(new DateTime());
        $offering->setRoom('Room A');
        $offering->setSite('Site A');
        $offering->setUrl('http://example.edu');

        $instigator = new User();
        $instigator->setId(1);

        $originalProperties = [
            'learners' => [],
            'learnerGroups' => [],
            'instructors' => [],
            'instructorGroups' => [],
            'startDate' => $startDate->getTimestamp(),
            'endDate' => (new DateTime('+1 year'))->getTimestamp(),
            'site' => 'some other site',
            'room' => 'some other room',
            'url' => 'http://example.com',
        ];

        $alert = new Alert();

        $this->mockAlertRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['dispatched' => false, 'tableName' => 'offering', 'tableRowId' => $offering->getId()]])
            ->andReturn(null);

        $this->mockAlertRepository
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertRepository
            ->shouldReceive('update')
            ->withArgs([$alert, false]);

        $instructorChangeType = new AlertChangeType();
        $instructorChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR);

        $timeChangeType = new AlertChangeType();
        $timeChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_TIME);

        $locationChangeType = new AlertChangeType();
        $locationChangeType->setId(AlertChangeTypeInterface::CHANGE_TYPE_LOCATION);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION]])
            ->times(3)
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeRepository
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
     * @covers ::createOrUpdateAlertForUpdatedOffering()
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
        $offering->setStartDate(new DateTime());
        $offering->setEndDate(new DateTime());
        $offering->setRoom('Room A');
        $offering->setSite('Site A');
        $offering->setUrl('http://example.edu');

        $instigator = new User();
        $instigator->setId(1);

        $originalProperties = [
            'learners' => [1, 3, 4],
            'learnerGroups' => [1, 3],
            'instructors' => [1],
            'instructorGroups' => [2],
            'startDate' => (new DateTime('+1 year'))->getTimestamp(),
            'endDate' => (new DateTime('+1 year'))->getTimestamp(),
            'site' => 'some other site',
            'room' => 'some other room',
            'url' => null,
        ];

        $alert = new Alert();

        $this->mockAlertRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['dispatched' => false, 'tableName' => 'offering', 'tableRowId' => $offering->getId()]])
            ->andReturn(null);

        $this->mockAlertRepository
            ->shouldReceive('create')
            ->andReturn($alert);

        $this->mockAlertRepository
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

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION]])
            ->times(3)
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME]])
            ->times(2)
            ->andReturn($timeChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP ]])
            ->times(2)
            ->andReturn($learnerGroupChangeType);

        $this->mockAlertChangeTypeRepository
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
