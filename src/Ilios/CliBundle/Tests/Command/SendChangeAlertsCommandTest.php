<?php
namespace Ilios\CliBundle\Tests\Command;

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
use Ilios\CoreBundle\Entity\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AuditLogManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Send Change Alerts command test.
 *
 * Class SendChangeAlertsCommandTest
 * @package Ilios\CliBundle\Tests\Command
 */
class SendChangeAlertsCommandTest extends KernelTestCase
{
    const COMMAND_NAME = 'ilios:messaging:send-change-alerts';

    /**
     * @var OfferingManagerInterface
     */
    protected $fakeOfferingManager;

    /**
     * @var AlertManagerInterface
     */
    protected $fakeAlertManager;

    /**
     * @var AuditLogManagerInterface
     */
    protected $fakeAuditLogManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        $offerings = $this->getOfferings();
        $alerts = $this->getAlerts();
        $auditLogs = $this->getAuditLogs();

        $this->fakeOfferingManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\OfferingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fakeOfferingManager
            ->method('findOfferingBy')
            ->will($this->returnValueMap(
                [
                    [[ "id" => 1 ], null, $offerings[1]],
                ]
            ));
        $this->fakeAlertManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\AlertManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fakeAlertManager
            ->method('findAlertsBy')
            ->will($this->returnValue(
                $alerts
            ));

        $this->fakeAuditLogManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\AuditLogManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fakeAuditLogManager
            ->method('findAuditLogsBy')
            ->will($this->returnValueMap(
                [
                    [
                        [ 'objectId' => 1, 'objectClass' => 'alert' ],
                        [ 'createdAt' => 'asc' ],
                        null,
                        null,
                        $auditLogs[1]
                    ],
                ]
            ));

        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $command = new SendChangeAlertsCommand(
            $this->fakeAlertManager,
            $this->fakeAuditLogManager,
            $this->fakeOfferingManager,
            $kernel->getContainer()->get('templating'),
            $kernel->getContainer()->get('mailer')
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
        unset($this->fakeOfferingManager);
        unset($this->fakeAlertManager);
        unset($this->fakeAuditLogManager);
        m::close();
    }

    /**
     * @covers Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     */
    public function testExecuteDryRun()
    {
        $alert = $this->getAlerts()[1];
        $offering = $this->getOfferings()[1];
        $auditLogs = $this->getAuditLogs()[1];


        $this->commandTester->execute([
            '--dry-run' => true,
        ]);
        $output = $this->commandTester->getDisplay();

        echo $output;

        // check mail headers
        $this->assertContains(
            'From: ' . $offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail(),
            $output
        );
        $schools = $alert->getRecipients()->toArray();
        /** @var SchoolInterface $school */
        foreach ($schools as $school) {
            $recipients = explode(',', strtolower($school->getChangeAlertRecipients()));
            foreach ($recipients as $recipient) {
                $this->assertRegExp("/To:(.*){$recipient}/", $output);
            }
        }
        $expectedSubject = 'Subject: ' . $offering->getSession()->getCourse()->getExternalId() . ' - '
            . $offering->getStartDate()->format('m/d/Y');
        $this->assertContains($expectedSubject, $output);

        // check mail body
        $this->assertContains("Course:   {$offering->getSession()->getCourse()->getTitle()}", $output);
        $this->assertContains("Session:  {$offering->getSession()->getTitle()}", $output);
        $this->assertContains("Type:     {$offering->getSession()->getSessionType()->getTitle()}", $output);
        $this->assertContains("Date:     {$offering->getStartDate()->format('D M d, Y')}", $output);
        $this->assertContains(
            "Time:     {$offering->getStartDate()->format('h:i a')} - {$offering->getEndDate()->format('h:i a')}",
            $output
        );
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
            $createdAt = $log->getCreatedAt();
            $this->assertContains(
                "- Updates made {$createdAt->format('m/d/Y')} at {$createdAt->format('h:i a')}"
                . " by {$user->getFirstName()} {$user->getLastName()}",
                $output
            );
        }
    }

    /**
     * @return OfferingInterface[]
     */
    protected function getOfferings()
    {
        $rhett = [];

        $school = new School();
        $school->setId(1);
        $school->setTitle('Medicine');
        $school->setIliosAdministratorEmail('iliosadmin@som.edu');

        $course = new Course();
        $course->setId(1);
        $course->setTitle('Course A');
        $course->setExternalId('ILIOS123');
        $course->setSchool($school);

        $sessionType = new SessionType();
        $sessionType->setId(1);
        $sessionType->setTitle('Session Type A');

        $session = new Session();
        $session->setId(1);
        $session->setCourse($course);
        $session->setSessionType($sessionType);
        $session->setTitle('Session A');

        $instructor1 = new User();
        $instructor1->setId(1);
        $instructor1->setFirstName('Jane');
        $instructor1->setLastName('Doe');
        $instructor1->setEmail('jane.doe@test.com');

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
        $learnerGroup->setTitle('Learner Group A');

        $learner = new User();
        $learner->setId(2);
        $learner->setFirstName('Jimmy');
        $learner->setLastName('Dumas');

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

        $rhett[$offering->getId()] = $offering;
        return $rhett;
    }

    /**
     * @return AlertInterface[]
     */
    protected function getAlerts()
    {
        $rhett = [];

        $alert = new Alert();
        $alert->setId(1);
        $alert->setDispatched(false);
        $alert->setTableName('offering');
        $alert->setTableRowId(1);

        $schoolA = new School();
        $schoolA->setId(1);
        $schoolA->setChangeAlertRecipients('recipientA@schoolA.edu,recipientB@schoolA.edu');
        $alert->addRecipient($schoolA);

        $schoolB = new School();
        $schoolB->setId(2);
        $schoolB->setChangeAlertRecipients('recipientA@schoolB.edu');
        $alert->addRecipient($schoolB);

        $i = 0;
        foreach (['A', 'B', 'C'] as $letter) {
            $alertChangeType = new AlertChangeType();
            $alertChangeType->setId(++$i);
            $alertChangeType->setTitle("Alert Change Type {$letter}");
            $alert->addChangeType($alertChangeType);
        }

        $rhett[$alert->getId()] = $alert;
        return $rhett;
    }

    /**
     * @return AuditLogInterface[][]
     */
    protected function getAuditLogs()
    {
        $rhett = [];

        $userA = new User();
        $userA->setId(1);
        $userA->setFirstName('Jimmy');
        $userA->setLastName('Miller');

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

        $rhett[$logA->getObjectId()][] = $logA;
        $rhett[$logB->getObjectId()][] = $logB;
        return $rhett;
    }
}
