<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Command\SendChangeAlertsCommand as Command;
use App\Entity\Alert;
use App\Entity\AlertChangeType;
use App\Entity\AlertChangeTypeInterface;
use App\Entity\AlertInterface;
use App\Entity\AuditLog;
use App\Entity\AuditLogInterface;
use App\Entity\Course;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\LearnerGroupInterface;
use App\Entity\Offering;
use App\Entity\OfferingInterface;
use App\Entity\School;
use App\Entity\SchoolInterface;
use App\Entity\Session;
use App\Entity\SessionType;
use App\Entity\User;
use App\Entity\UserInterface;
use App\Repository\AlertRepository;
use App\Repository\AuditLogRepository;
use App\Repository\OfferingRepository;
use App\Service\Config;
use DateTime;
use DateTimeZone;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

/**
 * Send Change Alerts command test.
 *
 * Class SendChangeAlertsCommandTest
 */
#[Group('cli')]
#[CoversClass(Command::class)]
final class SendChangeAlertsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $offeringRepository;
    protected m\MockInterface $alertRepository;
    protected m\MockInterface $auditLogRepository;
    protected m\MockInterface $twig;
    protected m\MockInterface $mailer;
    protected CommandTester $commandTester;
    protected string $timezone;

    public function setUp(): void
    {
        parent::setUp();
        $this->offeringRepository = m::mock(OfferingRepository::class);
        $this->alertRepository = m::mock(AlertRepository::class);
        $this->auditLogRepository = m::mock(AuditLogRepository::class);
        $this->twig = m::mock(Environment::class);
        $this->mailer = m::mock(MailerInterface::class);
        $fs = m::mock(Filesystem::class);
        $fs->shouldReceive('exists')->with('fish');

        $this->timezone = 'UTC';
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('timezone')->andReturn($this->timezone);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = new Command(
            $this->alertRepository,
            $this->auditLogRepository,
            $this->offeringRepository,
            static::getContainer()->get('twig'),
            $this->mailer,
            $config,
            $fs,
            sys_get_temp_dir()
        );
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->offeringRepository);
        unset($this->alertRepository);
        unset($this->auditLogRepository);
        unset($this->mailer);
        unset($this->commandTester);
        unset($this->timezone);
    }

    /**
     *
     * @param AuditLogInterface[] $auditLogs
     */
    #[DataProvider('executeProvider')]
    public function testExecuteDryRun(AlertInterface $alert, OfferingInterface $offering, array $auditLogs): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->offeringRepository
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);
        $this->auditLogRepository
            ->shouldReceive('findBy')
            ->with([ 'objectId' => $alert->getId(), 'objectClass' => 'alert' ], [ 'createdAt' => 'asc' ])
            ->andReturn($auditLogs);

        $this->commandTester->execute([
            '--dry-run' => true,
        ]);
        $output = $this->commandTester->getDisplay();

        // check mail headers
        $this->assertStringContainsString(
            'From: ' . $offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail(),
            $output
        );
        $schools = $alert->getRecipients()->toArray();
        /** @var SchoolInterface $school */
        foreach ($schools as $school) {
            $recipients = explode(',', $school->getChangeAlertRecipients());
            foreach ($recipients as $recipient) {
                $this->assertMatchesRegularExpression("/To:(.*){$recipient}/", $output);
            }
        }
        $expectedSubject = 'Subject: ' . $offering->getSession()->getCourse()->getExternalId() . ' - '
            . $offering->getStartDate()->format('m/d/Y');
        $this->assertStringContainsString($expectedSubject, $output);

        // check mail body
        $timezone = new DateTimeZone($this->timezone);
        $startDate = $offering->getStartDate()->setTimezone($timezone);
        $endDate = $offering->getEndDate()->setTimezone($timezone);
        $courseTitle = trim(strip_tags($offering->getSession()->getCourse()->getTitle()));
        $this->assertStringContainsString("Course:   {$courseTitle}", $output);
        $sessionTitle = trim(strip_tags($offering->getSession()->getTitle()));
        $this->assertStringContainsString("Session:  {$sessionTitle}", $output);
        $this->assertStringContainsString("Type:     {$offering->getSession()->getSessionType()->getTitle()}", $output);
        $this->assertStringContainsString("Date:     {$startDate->format('D M d, Y')}", $output);
        $this->assertStringContainsString(
            "Time:     {$startDate->format('h:i a')} - {$endDate->format('h:i a')}",
            $output
        );
        $this->assertStringContainsString("Location: {$offering->getRoom()}", $output);
        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $this->assertStringContainsString("- {$instructor->getFirstName()} {$instructor->getLastName()}", $output);
        }
        /** @var LearnerGroupInterface $learnerGroup */
        foreach ($offering->getLearnerGroups()->toArray() as $learnerGroup) {
            $this->assertStringContainsString("- {$learnerGroup->getTitle()}", $output);
        }
        /** @var UserInterface $learner */
        foreach ($offering->getLearners()->toArray() as $learner) {
            $this->assertStringContainsString("- {$learner->getFirstName()} {$learner->getLastName()}", $output);
        }
        /** @var AlertChangeTypeInterface $changeType */
        foreach ($alert->getChangeTypes()->toArray() as $changeType) {
            $this->assertStringContainsString("- {$changeType->getTitle()}", $output);
        }
        /** @var AuditLogInterface $log */
        foreach ($auditLogs as $log) {
            $user = $log->getUser();
            $createdAt = $log->getCreatedAt()->setTimezone($timezone);
            $this->assertStringContainsString(
                "- Updates made {$createdAt->format('m/d/Y')} at {$createdAt->format('h:i a')}"
                . " by {$user->getFirstName()} {$user->getLastName()}",
                $output
            );
        }
    }

    /**
     *
     * @param AuditLogInterface[] $auditLogs
     */
    #[DataProvider('executeProvider')]
    public function testExecute(AlertInterface $alert, OfferingInterface $offering, array $auditLogs): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->alertRepository->shouldReceive('update');
        $this->offeringRepository
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);
        $this->auditLogRepository
            ->shouldReceive('findBy')
            ->with([ 'objectId' => $alert->getId(), 'objectClass' => 'alert' ], [ 'createdAt' => 'asc' ])
            ->andReturn($auditLogs);
        $this->mailer->shouldReceive('send')->once();
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Sent 1 offering change alert notifications.", $output);
        $this->assertStringContainsString("Marked 1 offering change alerts as dispatched.", $output);
    }

    public function testExecuteNoPendingAlerts(): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([]);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertEquals('No undispatched offering alerts found.', trim($output));
    }

    #[DataProvider('executeNoRecipientsConfiguredProvider')]
    public function testExecuteNoRecipientsConfigured(AlertInterface $alert, OfferingInterface $offering): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->alertRepository->shouldReceive('update');
        $this->offeringRepository
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString("No alert recipient for offering change alert {$alert->getId()}.", $output);
        $this->assertStringContainsString("Sent 0 offering change alert notifications.", $output);
        $this->assertStringContainsString("Marked 1 offering change alerts as dispatched.", $output);
    }


    #[DataProvider('executeRecipientWithoutEmailProvider')]
    public function testExecuteRecipientWithoutEmail(AlertInterface $alert, OfferingInterface $offering): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->alertRepository->shouldReceive('update');
        $this->offeringRepository
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString(
            "Recipient without email for offering change alert {$alert->getId()}.",
            $output
        );
        $this->assertStringContainsString("Sent 0 offering change alert notifications.", $output);
        $this->assertStringContainsString("Marked 1 offering change alerts as dispatched.", $output);
    }


    #[DataProvider('executeDeletedOfferingProvider')]
    public function testExecuteDeletedOffering(AlertInterface $alert, OfferingInterface $offering): void
    {
        $this->alertRepository->shouldReceive('findBy')->andReturn([ $alert ]);
        $this->alertRepository->shouldReceive('update');
        $this->offeringRepository
            ->shouldReceive('findOneBy')
            ->with([ "id" => $offering->getId() ])
            ->andReturn($offering);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString("Sent 0 offering change alert notifications.", $output);
        $this->assertStringContainsString("Marked 1 offering change alerts as dispatched.", $output);
    }

    public static function executeProvider(): array
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
        $offering->setStartDate(new DateTime('2015-10-01 15:15:00', new DateTimeZone('UTC')));
        $offering->setEndDate(new DateTime('2015-10-01 18:30:00', new DateTimeZone('UTC')));
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
        $logA->setCreatedAt(new DateTime('2015-09-20 11:12:22', new DateTimeZone('UTC')));

        $logB = new AuditLog();
        $logB->setObjectClass('alert');
        $logB->setObjectId(1);
        $logB->setUser($userB);
        $logB->setCreatedAt(new DateTime('2015-09-20 15:20:15', new DateTimeZone('UTC')));

        return [
            [ $alert , $offering, [ $logA, $logB ]],
        ];
    }

    public static function executeRecipientWithoutEmailProvider(): array
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

    public static function executeNoRecipientsConfiguredProvider(): array
    {
        $school = new School();
        $school->setId(1);
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

    public static function executeDeletedOfferingProvider(): array
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offeringA = new Offering();
        $offeringA->setId(1);
        $offeringA->setSession($session);
        $alertA = new Alert();
        $alertA->setId(1);
        $alertA->setTableName('offering');
        $alertA->setTableRowId($offeringA->getId());

        $schoolB = new School();
        $courseB = new Course();
        $courseB->setSchool($schoolB);
        $sessionB = new Session();
        $sessionB->setCourse($courseB);
        $offeringB = new Offering();
        $offeringB->setId(1);
        $offeringB->setSession($sessionB);
        $alertB = new Alert();
        $alertB->setId(2);
        $alertB->setTableName('offering');
        $alertB->setTableRowId($offeringB->getId());

        $schoolC = new School();
        $courseC = new Course();
        $courseC->setSchool($schoolC);
        $sessionC = new Session();
        $sessionC->setCourse($courseC);
        $offeringC = new Offering();
        $offeringC->setId(1);
        $offeringC->setSession($sessionC);
        $alertC = new Alert();
        $alertC->setId(3);
        $alertC->setTableName('offering');
        $alertC->setTableRowId($offeringC->getId());

        return [
            [ $alertA, $offeringA ],
            [ $alertB, $offeringB ],
            [ $alertC, $offeringC ],
        ];
    }
}
