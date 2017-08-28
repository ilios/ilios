<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\SendChangeAlertsCommand;
use Ilios\CoreBundle\Entity\Alert;
use Ilios\CoreBundle\Entity\AlertChangeType;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\AlertInterface;
use Ilios\CoreBundle\Entity\AuditLog;
use Ilios\CoreBundle\Entity\AuditLogInterface;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\InstructorGroup;
use Ilios\CoreBundle\Entity\LearnerGroup;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\Manager\AuditLogManager;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Send Change Alerts command test.
 *
 * Class SendChangeAlertsCommandTest
 */
class SendChangeAlertsCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:messaging:send-change-alerts';

    /**
     * @var m\MockInterface
     */
    protected $offeringManager;

    /**
     * @var m\MockInterface
     */
    protected $alertManager;

    /**
     * @var m\MockInterface
     */
    protected $auditLogManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var string
     */
    protected $timezone;

    public function setUp()
    {
        $this->offeringManager = m::mock(OfferingManager::class);
        $this->alertManager = m::mock(AlertManager::class);
        $this->auditLogManager = m::mock(AuditLogManager::class);
        $this->timezone = 'UTC';
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('timezone')->andReturn($this->timezone);

        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $command = new SendChangeAlertsCommand(
            $this->alertManager,
            $this->auditLogManager,
            $this->offeringManager,
            $kernel->getContainer()->get('templating'),
            $kernel->getContainer()->get('mailer'),
            $config
        );
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->offeringManager);
        unset($this->alertManager);
        unset($this->auditLogManager);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     * @dataProvider executeProvider
     *
     * @param AlertInterface $alert
     * @param OfferingInterface $offering
     * @param AuditLogInterface[] $auditLogs
     */
    public function testExecuteDryRun(AlertInterface $alert, OfferingInterface $offering, array $auditLogs)
    {
        $this->alertManager->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->offeringManager
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);
        $this->auditLogManager
            ->shouldReceive('findBy')
            ->with([ 'objectId' => $alert->getId(), 'objectClass' => 'alert' ], [ 'createdAt' => 'asc' ])
            ->andReturn($auditLogs);

        $this->commandTester->execute([
            '--dry-run' => true,
        ]);
        $output = $this->commandTester->getDisplay();

        // check mail headers
        $this->assertContains(
            'From: ' . $offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail(),
            $output
        );
        $schools = $alert->getRecipients()->toArray();
        /** @var SchoolInterface $school */
        foreach ($schools as $school) {
            $recipients = explode(',', $school->getChangeAlertRecipients());
            foreach ($recipients as $recipient) {
                $this->assertRegExp("/To:(.*){$recipient}/", $output);
            }
        }
        $expectedSubject = 'Subject: ' . $offering->getSession()->getCourse()->getExternalId() . ' - '
            . $offering->getStartDate()->format('m/d/Y');
        $this->assertContains($expectedSubject, $output);

        // check mail body
        $timezone = new \DateTimeZone($this->timezone);
        $startDate = $offering->getStartDate()->setTimezone($timezone);
        $endDate = $offering->getEndDate()->setTimezone($timezone);
        $courseTitle = trim(strip_tags($offering->getSession()->getCourse()->getTitle()));
        $this->assertContains("Course:   {$courseTitle}", $output);
        $sessionTitle = trim(strip_tags($offering->getSession()->getTitle()));
        $this->assertContains("Session:  {$sessionTitle}", $output);
        $this->assertContains("Type:     {$offering->getSession()->getSessionType()->getTitle()}", $output);
        $this->assertContains("Date:     {$startDate->format('D M d, Y')}", $output);
        $this->assertContains("Time:     {$startDate->format('h:i a')} - {$endDate->format('h:i a')}", $output);
        $this->assertContains("Location: {$offering->getRoom()}", $output);
        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $this->assertContains("- {$instructor->getFirstName()} {$instructor->getLastName()}", $output);
        }
        /** @var LearnerGroupInterface $learnerGroup */
        foreach ($offering->getLearnerGroups()->toArray() as $learnerGroup) {
            $this->assertContains("- {$learnerGroup->getTitle()}", $output);
        }
        /** @var UserInterface $learner */
        foreach ($offering->getLearners()->toArray() as $learner) {
            $this->assertContains("- {$learner->getFirstName()} {$learner->getLastName()}", $output);
        }
        /** @var AlertChangeTypeInterface $changeType */
        foreach ($alert->getChangeTypes()->toArray() as $changeType) {
            $this->assertContains("- {$changeType->getTitle()}", $output);
        }
        /** @var AuditLogInterface $log */
        foreach ($auditLogs as $log) {
            $user = $log->getUser();
            $createdAt = $log->getCreatedAt()->setTimezone($timezone);
            $this->assertContains(
                "- Updates made {$createdAt->format('m/d/Y')} at {$createdAt->format('h:i a')}"
                . " by {$user->getFirstName()} {$user->getLastName()}",
                $output
            );
        }
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     * @dataProvider executeProvider
     *
     * @param AlertInterface $alert
     * @param OfferingInterface $offering
     * @param AuditLogInterface[] $auditLogs
     */
    public function testExecute(AlertInterface $alert, OfferingInterface $offering, array $auditLogs)
    {
        $this->alertManager->shouldReceive('findBy')->andReturn([ $alert ])->shouldReceive('update');
        $this->offeringManager
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);
        $this->auditLogManager
            ->shouldReceive('findBy')
            ->with([ 'objectId' => $alert->getId(), 'objectClass' => 'alert' ], [ 'createdAt' => 'asc' ])
            ->andReturn($auditLogs);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertContains("Sent 1 offering change alert notifications.", $output);
        $this->assertContains("Marked 1 offering change alerts as dispatched.", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     */
    public function testExecuteNoPendingAlerts()
    {
        $this->alertManager->shouldReceive('findBy')->andReturn([]);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertEquals('No undispatched offering alerts found.', trim($output));
    }
    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     * @dataProvider executeNoRecipientsConfiguredProvider
     *
     * @param AlertInterface $alert
     * @param OfferingInterface $offering
     */
    public function testExecuteNoRecipientsConfigured(AlertInterface $alert, OfferingInterface $offering)
    {
        $this->alertManager->shouldReceive('findBy')->andReturn([ $alert ])->shouldReceive('update');
        $this->offeringManager
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertContains("No alert recipient for offering change alert {$alert->getId()}.", $output);
        $this->assertContains("Sent 0 offering change alert notifications.", $output);
        $this->assertContains("Marked 1 offering change alerts as dispatched.", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     * @dataProvider executeRecipientWithoutEmailProvider
     *
     * @param AlertInterface $alert
     * @param OfferingInterface $offering
     */
    public function testExecuteRecipientWithoutEmail(AlertInterface $alert, OfferingInterface $offering)
    {
        $this->alertManager->shouldReceive('findBy')->andReturn([ $alert ])->shouldReceive('update');
        $this->offeringManager
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertContains("Recipient without email for offering change alert {$alert->getId()}.", $output);
        $this->assertContains("Sent 0 offering change alert notifications.", $output);
        $this->assertContains("Marked 1 offering change alerts as dispatched.", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     * @dataProvider executeDeletedOfferingProvider
     *
     * @param AlertInterface $alert
     * @param OfferingInterface $offering
     */
    public function testExecuteDeletedOffering(AlertInterface $alert, OfferingInterface $offering)
    {
        $this->alertManager
            ->shouldReceive('findBy')->andReturn([ $alert ])->shouldReceive('update');
        $this->offeringManager
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertContains("Sent 0 offering change alert notifications.", $output);
        $this->assertContains("Marked 1 offering change alerts as dispatched.", $output);
    }


    /**
     * @return array
     */
    public function executeProvider()
    {
        $schoolA = new School();
        $schoolA->setId(1);
        $schoolA->setTitle('Medicine');
        $schoolA->setIliosAdministratorEmail('iliosadmin@som.edu');
        $schoolA->setChangeAlertRecipients('recipientA@som.edu,recipientB@som.edu');

        $schoolB = new School();
        $schoolB->setTitle('Dentistry');
        $schoolB->setId(2);
        $schoolA->setIliosAdministratorEmail('iliosadmin@sod.edu');
        $schoolB->setChangeAlertRecipients('recipientA@sod.edu');

        $course = new Course();
        $course->setId(1);
        $course->setTitle('Course <strong>A</strong>');
        $course->setExternalId('ILIOS123');
        $course->setSchool($schoolA);

        $sessionType = new SessionType();
        $sessionType->setId(1);
        $sessionType->setTitle('Session Type A');

        $session = new Session();
        $session->setId(1);
        $session->setCourse($course);
        $session->setSessionType($sessionType);
        $session->setTitle('<i>Session A</i>');

        $instructor1 = new User();
        $instructor1->setId(1);
        $instructor1->setFirstName("D'jango");
        $instructor1->setLastName("D'avila");
        $instructor1->setEmail('django.davila@test.com');

        $instructor2 = new User();
        $instructor2->setId(2);
        $instructor2->setFirstName('Mike');
        $instructor2->setLastName('Smith');
        $instructor2->setEmail('mike.smith@test.com');

        $instructorGroup = new InstructorGroup();
        $instructorGroup->setId(1);
        $instructorGroup->addUser($instructor2);

        $learnerGroup = new LearnerGroup();
        $learnerGroup->setId(1);
        $learnerGroup->setTitle('Learner <em>Group</em> A');

        $learner = new User();
        $learner->setId(2);
        $learner->setFirstName('Jimmy');
        $learner->setLastName("O'Mulligan");

        $offering = new Offering();
        $offering->setId(1);
        $offering->setSession($session);
        $offering->setStartDate(new \DateTime('2015-10-01 15:15:00', new \DateTimeZone('UTC')));
        $offering->setEndDate(new \DateTime('2015-10-01 18:30:00', new \DateTimeZone('UTC')));
        $offering->setRoom('Library - Room 119');
        $offering->addInstructorGroup($instructorGroup);
        $offering->addInstructor($instructor1);
        $offering->addLearnerGroup($learnerGroup);
        $offering->addLearner($learner);

        $alert = new Alert();
        $alert->setId(1);
        $alert->setDispatched(false);
        $alert->setTableName('offering');
        $alert->setTableRowId(1);
        $alert->addRecipient($schoolA);

        $i = 0;
        foreach (['A', 'B', 'C'] as $letter) {
            $alertChangeType = new AlertChangeType();
            $alertChangeType->setId(++$i);
            $alertChangeType->setTitle("Alert Change Type {$letter}");
            $alert->addChangeType($alertChangeType);
        }

        $userA = new User();
        $userA->setId(1);
        $userA->setFirstName("K'aren");
        $userA->setLastName("D'lunchtime");

        $userB = new User();
        $userB->setId(2);
        $userB->setFirstName('Billy');
        $userB->setLastName('Brown');

        $logA = new AuditLog();
        $logA->setObjectClass('alert');
        $logA->setObjectId(1);
        $logA->setUser($userA);
        $logA->setCreatedAt(new \DateTime('2015-09-20 11:12:22', new \DateTimeZone('UTC')));

        $logB = new AuditLog();
        $logB->setObjectClass('alert');
        $logB->setObjectId(1);
        $logB->setUser($userB);
        $logB->setCreatedAt(new \DateTime('2015-09-20 15:20:15', new \DateTimeZone('UTC')));

        return [
            [ $alert , $offering, [ $logA, $logB ]]
        ];
    }

    /**
     * @return array
     */
    public function executeRecipientWithoutEmailProvider()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setId(1);
        $offering->setSession($session);

        $alert = new Alert();
        $alert->setId(1);
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());
        $alert->addRecipient($school);

        return [[ $alert, $offering ]];
    }

    /**
     * @return array
     */
    public function executeNoRecipientsConfiguredProvider()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setId(1);
        $offering->setSession($session);

        $alert = new Alert();
        $alert->setId(1);
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());

        return [[ $alert, $offering ]];
    }

    /**
     * @return array
     */
    public function executeDeletedOfferingProvider()
    {
        $course = new Course();
        $session = new Session();
        $session->setCourse($course);
        $offeringA = new Offering();
        $offeringA->setId(1);
        $offeringA->setSession($session);
        $alertA = new Alert();
        $alertA->setTableName('offering');
        $alertA->setTableRowId($offeringA->getId());

        $session = new Session();
        $offeringB = new Offering();
        $offeringB->setId(1);
        $offeringB->setSession($session);
        $alertB = new Alert();
        $alertB->setTableName('offering');
        $alertB->setTableRowId($offeringB->getId());

        $offeringC = new Offering();
        $offeringC->setId(1);
        $alertC = new Alert();
        $alertC->setTableName('offering');
        $alertC->setTableRowId($offeringC->getId());

        return [
            [ $alertA, $offeringA ],
            [ $alertB, $offeringB ],
            [ $alertC, $offeringC ],
        ];
    }
}
