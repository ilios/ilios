<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Alert;
use App\Entity\AlertChangeType;
use App\Entity\AlertChangeTypeInterface;
use App\Entity\Course;
use App\Entity\Offering;
use App\Entity\School;
use App\Entity\ServiceToken;
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
 */
#[CoversClass(ChangeAlertHandler::class)]
final class ChangeAlertHandlerTest extends TestCase
{
    protected m\MockInterface $mockAlertRepository;
    protected m\MockInterface $mockAlertChangeTypeRepository;
    protected ChangeAlertHandler $changeAlertHandler;

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

    public function testCreateAlertForNewOffering(): void
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

        $serviceTokenInstigator = new ServiceToken();
        $serviceTokenInstigator->setId(100);

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

        $this->changeAlertHandler->createAlertForNewOffering($offering, $instigator, $serviceTokenInstigator);

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getServiceTokenInstigators()[0], $serviceTokenInstigator);
        $this->assertEquals('offering', $alert->getTableName());
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $this->assertEquals($alert->getChangeTypes()[0], $alertChangeType);
    }

    public function testCreateOrUpdateAlertForUpdatedOfferingExistingAlert(): void
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
        $offering->setUrl('https://example.edu');

        $instigator = new User();
        $instigator->setId(1);

        $serviceTokenInstigator = new ServiceToken();
        $serviceTokenInstigator->setId(100);

        $originalProperties = [
            'learners' => [],
            'learnerGroups' => [],
            'instructors' => [],
            'instructorGroups' => [],
            'startDate' => $startDate->getTimestamp(),
            'endDate' => (new DateTime('+1 year'))->getTimestamp(),
            'site' => 'some other site',
            'room' => 'some other room',
            'url' => 'https://example.com',
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
            ->once()
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME]])
            ->once()
            ->andReturn($timeChangeType);

        $alert->addChangeType($instructorChangeType);
        $alert->addChangeType($timeChangeType);

        $this->changeAlertHandler->createOrUpdateAlertForUpdatedOffering(
            $offering,
            $originalProperties,
            $instigator,
            $serviceTokenInstigator
        );

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getServiceTokenInstigators()[0], $serviceTokenInstigator);
        $this->assertEquals('offering', $alert->getTableName());
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $changeTypes = $alert->getChangeTypes();
        $this->assertEquals(3, count($changeTypes));
        $this->assertTrue($changeTypes->contains($instructorChangeType));
        $this->assertTrue($changeTypes->contains($timeChangeType));
        $this->assertTrue($changeTypes->contains($locationChangeType));
    }

    public function testCreateOrUpdateAlertForUpdatedOfferingNewAlert(): void
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
        $offering->setUrl('https://example.edu');

        $instigator = new User();
        $instigator->setId(1);

        $serviceTokenInstigator = new ServiceToken();
        $serviceTokenInstigator->setId(100);

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
            ->once()
            ->andReturn($locationChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME]])
            ->once()
            ->andReturn($timeChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP ]])
            ->once()
            ->andReturn($learnerGroupChangeType);

        $this->mockAlertChangeTypeRepository
            ->shouldReceive('findOneBy')
            ->withArgs([['id' => AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR ]])
            ->once()
            ->andReturn($instructorChangeType);

        $this->changeAlertHandler->createOrUpdateAlertForUpdatedOffering(
            $offering,
            $originalProperties,
            $instigator,
            $serviceTokenInstigator
        );

        $this->assertEquals($alert->getInstigators()[0], $instigator);
        $this->assertEquals($alert->getServiceTokenInstigators()[0], $serviceTokenInstigator);
        $this->assertEquals('offering', $alert->getTableName());
        $this->assertEquals($alert->getTableRowId(), $offering->getId());
        $this->assertEquals($alert->getRecipients()[0], $school);
        $changeTypes = $alert->getChangeTypes();
        $this->assertEquals(4, count($changeTypes));
        $this->assertTrue($changeTypes->contains($instructorChangeType));
        $this->assertTrue($changeTypes->contains($learnerGroupChangeType));
        $this->assertTrue($changeTypes->contains($timeChangeType));
        $this->assertTrue($changeTypes->contains($locationChangeType));
    }
}
