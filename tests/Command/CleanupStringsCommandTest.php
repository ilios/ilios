<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CleanupStringsCommand;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjectiveInterface;
use App\Entity\LearningMaterialInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use App\Entity\SessionObjectiveInterface;
use App\Repository\CourseLearningMaterialRepository;
use App\Repository\CourseObjectiveRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\ProgramYearObjectiveRepository;
use App\Repository\SessionLearningMaterialRepository;
use App\Repository\SessionObjectiveRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use HTMLPurifier;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class CleanupStringsCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class CleanupStringsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected HTMLPurifier $purifier;
    protected m\MockInterface $em;
    protected m\MockInterface $sessionObjectiveRepository;
    protected m\MockInterface $courseObjectiveRepository;
    protected m\MockInterface $programYearObjectiveRepository;
    protected m\MockInterface $learningMaterialRepository;
    protected m\MockInterface $courseLearningMaterialRepository;
    protected m\MockInterface $sessionLearningMaterialRepository;
    protected m\MockInterface $sessionRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $httpClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->purifier = static::getContainer()->get(HTMLPurifier::class);
        $this->sessionObjectiveRepository = m::mock(SessionObjectiveRepository::class);
        $this->courseObjectiveRepository = m::mock(CourseObjectiveRepository::class);
        $this->programYearObjectiveRepository = m::mock(ProgramYearObjectiveRepository::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->courseLearningMaterialRepository = m::mock(CourseLearningMaterialRepository::class);
        $this->sessionLearningMaterialRepository = m::mock(SessionLearningMaterialRepository::class);
        $this->sessionRepository = m::mock(SessionRepository::class);
        $this->em = m::mock(EntityManagerInterface::class);
        $this->httpClient = m::mock(HttpClientInterface::class);

        $command = new CleanupStringsCommand(
            $this->purifier,
            $this->em,
            $this->learningMaterialRepository,
            $this->courseLearningMaterialRepository,
            $this->sessionLearningMaterialRepository,
            $this->sessionRepository,
            $this->sessionObjectiveRepository,
            $this->courseObjectiveRepository,
            $this->programYearObjectiveRepository,
            $this->httpClient
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->purifier);
        unset($this->em);
        unset($this->sessionObjectiveRepository);
        unset($this->courseObjectiveRepository);
        unset($this->programYearObjectiveRepository);
        unset($this->learningMaterialRepository);
        unset($this->courseLearningMaterialRepository);
        unset($this->sessionLearningMaterialRepository);
        unset($this->sessionRepository);
        unset($this->commandTester);
        unset($this->httpClient);
    }

    public function testPurifyObjectiveTitle(): void
    {
        $cleanSessionObjective = m::mock(SessionObjectiveInterface::class);
        $cleanSessionObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtySessionObjective = m::mock(SessionObjectiveInterface::class);
        $dirtySessionObjective->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>');
        $dirtySessionObjective->shouldReceive('setTitle')->with('html title');

        $this->sessionObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSessionObjective, $dirtySessionObjective]);
        $this->sessionObjectiveRepository->shouldReceive('update')->with($dirtySessionObjective, false);
        $this->sessionObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanCourseObjective = m::mock(CourseObjectiveInterface::class);
        $cleanCourseObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtyCourseObjective = m::mock(CourseObjectiveInterface::class);
        $dirtyCourseObjective->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>');
        $dirtyCourseObjective->shouldReceive('setTitle')->with('html title');

        $this->courseObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourseObjective, $dirtyCourseObjective]);
        $this->courseObjectiveRepository->shouldReceive('update')->with($dirtyCourseObjective, false);
        $this->courseObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanPyObjective = m::mock(ProgramYearObjectiveInterface::class);
        $cleanPyObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtyProgramYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $dirtyProgramYearObjective
            ->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>');
        $dirtyProgramYearObjective->shouldReceive('setTitle')->with('html title');

        $this->programYearObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanPyObjective, $dirtyProgramYearObjective]);
        $this->programYearObjectiveRepository->shouldReceive('update')->with($dirtyProgramYearObjective, false);
        $this->programYearObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $this->em->shouldReceive('flush')->times(3);
        $this->em->shouldReceive('clear')->times(3);
        $this->commandTester->execute([
            '--objective-title' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/3 Objective Titles updated/',
            $output
        );
    }

    public function testTrimObjectiveTitle(): void
    {
        $cleanSessionObjective = m::mock(SessionObjectiveInterface::class);
        $cleanSessionObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtySessionObjective = m::mock(SessionObjectiveInterface::class);
        $dirtySessionObjective->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t");
        $dirtySessionObjective->shouldReceive('setTitle')->with('lorem ipsum');

        $this->sessionObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSessionObjective, $dirtySessionObjective]);
        $this->sessionObjectiveRepository->shouldReceive('update')->with($dirtySessionObjective, false);
        $this->sessionObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanCourseObjective = m::mock(CourseObjectiveInterface::class);
        $cleanCourseObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtyCourseObjective = m::mock(CourseObjectiveInterface::class);
        $dirtyCourseObjective->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t");
        $dirtyCourseObjective->shouldReceive('setTitle')->with('lorem ipsum');

        $this->courseObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourseObjective, $dirtyCourseObjective]);
        $this->courseObjectiveRepository->shouldReceive('update')->with($dirtyCourseObjective, false);
        $this->courseObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanPyObjective = m::mock(ProgramYearObjectiveInterface::class);
        $cleanPyObjective->shouldReceive('getTitle')->andReturn('clean title');

        $dirtyProgramYearObjective = m::mock(ProgramYearObjectiveInterface::class);
        $dirtyProgramYearObjective->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t");
        $dirtyProgramYearObjective->shouldReceive('setTitle')->with('lorem ipsum');

        $this->programYearObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanPyObjective, $dirtyProgramYearObjective]);
        $this->programYearObjectiveRepository->shouldReceive('update')->with($dirtyProgramYearObjective, false);
        $this->programYearObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $this->em->shouldReceive('flush')->times(3);
        $this->em->shouldReceive('clear')->times(3);
        $this->commandTester->execute([
            '--objective-title-blankspace' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/3 Objective Titles updated/',
            $output
        );
    }

    public function testLearningMaterialDescription(): void
    {
        $clean = m::mock(LearningMaterialInterface::class);
        $clean->shouldReceive('getDescription')->andReturn('clean title');

        $dirty = m::mock(LearningMaterialInterface::class);
        $dirty->shouldReceive('getDescription')->andReturn('<script>alert();</script><h1>html title</h1>');
        $dirty->shouldReceive('setDescription')->with('html title');

        $this->learningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$clean, $dirty]);
        $this->learningMaterialRepository->shouldReceive('update')->with($dirty, false);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->andReturn(2);

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            '--learningmaterial-description' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Learning Material Descriptions updated/',
            $output
        );
    }

    public function testLearningMaterialNotes(): void
    {
        $cleanCourse = m::mock(CourseLearningMaterialInterface::class);
        $cleanCourse->shouldReceive('getNotes')->andReturn('clean course note');

        $dirtyCourse = m::mock(CourseLearningMaterialInterface::class);
        $dirtyCourse->shouldReceive('getNotes')->andReturn('<script>alert();</script><h1>html course note</h1>');
        $dirtyCourse->shouldReceive('setNotes')->with('html course note');

        $this->courseLearningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourse, $dirtyCourse]);
        $this->courseLearningMaterialRepository->shouldReceive('update')->with($dirtyCourse, false);
        $this->courseLearningMaterialRepository->shouldReceive('getTotalCourseLearningMaterialCount')->andReturn(2);

        $cleanSession = m::mock(SessionLearningMaterialInterface::class);
        $cleanSession->shouldReceive('getNotes')->andReturn('clean session note');

        $dirtySession = m::mock(SessionLearningMaterialInterface::class);
        $dirtySession->shouldReceive('getNotes')->andReturn('<script>alert();</script><h1>html session note</h1>');
        $dirtySession->shouldReceive('setNotes')->with('html session note');

        $this->sessionLearningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSession, $dirtySession]);
        $this->sessionLearningMaterialRepository->shouldReceive('update')
            ->with($dirtySession, false);
        $this->sessionLearningMaterialRepository->shouldReceive('getTotalSessionLearningMaterialCount')->andReturn(2);

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->twice();
        $this->commandTester->execute([
            '--learningmaterial-note' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Course Learning Material Notes updated/',
            $output
        );


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Session Learning Material Notes updated/',
            $output
        );
    }

    public function testSessionDescription(): void
    {
        $clean = m::mock(SessionInterface::class);
        $clean->shouldReceive('getDescription')->andReturn('clean title');
        $dirty = m::mock(SessionInterface::class);
        $dirty->shouldReceive('getDescription')->andReturn('<script>alert();</script><h1>html title</h1>');
        $dirty->shouldReceive('setDescription')->with('html title');

        $this->sessionRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$clean, $dirty]);
        $this->sessionRepository->shouldReceive('update')->with($dirty, false);
        $this->sessionRepository->shouldReceive('getTotalSessionCount')->andReturn(2);

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            '--session-description' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Session Descriptions updated/',
            $output
        );
    }

    public function testSessionDescriptionBrInP(): void
    {
        $emptyPTag = m::mock(SessionInterface::class);
        $emptyPTag->shouldReceive('getDescription')->andReturn('<p>Empty P<br></p><p></p>');
        $emptyPTag->shouldReceive('setDescription')->with('<p>Empty P</p>');

        $emptyPTagWithLineBreak = m::mock(SessionInterface::class);
        $emptyPTagWithLineBreak->shouldReceive('getDescription')->andReturn('<p>Empty With Br<br></p><p><br /></p>');
        $emptyPTagWithLineBreak->shouldReceive('setDescription')->with('<p>Empty With Br</p>');

        $this->sessionRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$emptyPTag, $emptyPTagWithLineBreak]);
        $this->sessionRepository->shouldReceive('update')->with($emptyPTag, false);
        $this->sessionRepository->shouldReceive('update')->with($emptyPTagWithLineBreak, false);
        $this->sessionRepository->shouldReceive('getTotalSessionCount')->andReturn(1);
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            '--session-description' => true,
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/2 Session Descriptions updated/',
            $output
        );
    }

    public static function correctLearningMaterialLinksProvider(): array
    {
        return [
            ['iliosproject.org', 'https://iliosproject.org'],
            ['http//iliosproject.org', 'https://http//iliosproject.org'],
        ];
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     * @dataProvider correctLearningMaterialLinksProvider
     */
    public function testCorrectLearningMaterialLinks(string $link, string $fixedLink): void
    {
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldReceive('request')->once()->with('HEAD', $fixedLink);
        $lm->shouldReceive('setLink')->once()->with($fixedLink);
        $this->learningMaterialRepository->shouldReceive('update')->once()->with($lm, false);
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    public static function correctLearningMaterialLinksWhithoutFetchingProvider(): array
    {
        return [
            [' http://iliosproject.org', 'http://iliosproject.org'],
            ['https://iliosproject.org    ', 'https://iliosproject.org'],
            [' ftps://iliosproject.org ', 'ftps://iliosproject.org'],
            ['  ftp://iliosproject.org ', 'ftp://iliosproject.org'],
            ['http://https://iliosproject.org', 'https://iliosproject.org'],
            ['http://http://iliosproject.org', 'http://iliosproject.org'],
            ['http://ftp://iliosproject.org', 'ftp://iliosproject.org'],
            ['http://ftps://iliosproject.org', 'ftps://iliosproject.org'],
        ];
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     * @dataProvider correctLearningMaterialLinksWhithoutFetchingProvider
     */
    public function testCorrectLearningMaterialLinksWithoutFetching(string $link, string $fixedLink): void
    {
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldNotReceive('request');
        $lm->shouldReceive('setLink')->once()->with($fixedLink);
        $this->learningMaterialRepository->shouldReceive('update')->once()->with($lm, false);
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    public static function correctLearningMaterialLinksNoChangesProvider(): array
    {
        return [
            [null],
            [''],
            ['    '],
            ['http://iliosproject.org/'],
            ['https://iliosproject.org/'],
            ['ftp://iliosproject.org/'],
            ['ftps://iliosproject.org/'],
            ['HttPs://iliosproject.org/'],
        ];
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     * @dataProvider correctLearningMaterialLinksNoChangesProvider
     */
    public function testCorrectLearningMaterialLinksNoChanges(?string $link): void
    {
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldNotReceive('request');
        $lm->shouldNotReceive('setLink');
        $this->learningMaterialRepository->shouldNotReceive('update');
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksInBulk(): void
    {
        $total = 1001;
        $lms = [];
        for ($i = 0; $i < $total; $i++) {
            $url = "iliosproject{$i}.org";
            $fixedUrl = 'https://' . $url;
            $lm = m::mock(LearningMaterialInterface::class);
            $lm->shouldReceive('getLink')->once()->andReturn($url);
            $lm->shouldReceive('setLink')->once()->with($fixedUrl);
            $lms[] = $lm;
        }
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn($total);
        $this->learningMaterialRepository
            ->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->once()
            ->andReturn(array_slice($lms, 0, 500));
        $this->learningMaterialRepository
            ->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 500)
            ->once()
            ->andReturn(array_slice($lms, 500, 500));
        $this->learningMaterialRepository
            ->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 1000)
            ->once()
            ->andReturn(array_slice($lms, 1000));

        $this->httpClient->shouldReceive('request')->times($total);
        $this->learningMaterialRepository->shouldReceive('update')->times($total);
        $this->em->shouldReceive('flush')->times(3);
        $this->em->shouldReceive('clear')->times(3);

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("{$total} learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksFails(): void
    {
        $link = 'iliosproject.org';
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $lm->shouldReceive('getId')->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('HEAD', 'https://' . $link)
            ->andThrow(new Exception());
        $this->httpClient->shouldReceive('request')->once()->with('HEAD', 'http://' . $link)->andThrow(new Exception());
        $lm->shouldNotReceive('setLink');
        $this->learningMaterialRepository->shouldNotReceive('update');
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 1 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksFailsOnHttps(): void
    {
        $link = 'iliosproject.org';
        $fixedUrl = 'http://iliosproject.org';
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('HEAD', 'https://' . $link)
            ->andThrow(new Exception());
        $this->httpClient->shouldReceive('request')->once()->with('HEAD', 'http://' . $link);
        $lm->shouldReceive('setLink')->once()->with($fixedUrl);
        $this->learningMaterialRepository->shouldReceive('update')->once()->with($lm, false);
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(['--learningmaterial-links' => true]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksVerboseFailureOutput(): void
    {
        $link = 'iliosproject.org';
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getLink')->andReturn($link);
        $lm->shouldReceive('getId')->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->once()->andReturn(1);
        $this->learningMaterialRepository->shouldReceive('findBy')->andReturn([ $lm ]);
        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('HEAD', 'https://' . $link)
            ->andThrow(new Exception());
        $this->httpClient->shouldReceive('request')
            ->once()
            ->with('HEAD', 'http://' . $link)
            ->andThrow(new Exception('FAIL!'));
        $lm->shouldNotReceive('setLink');
        $this->learningMaterialRepository->shouldNotReceive('update');
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();

        $this->commandTester->execute(
            [ '--learningmaterial-links' => true],
            [ 'verbosity' => OutputInterface::VERBOSITY_VERBOSE],
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 1 failures.", $output);
        $this->assertMatchesRegularExpression('/\| Learning Material ID\s+\| Link\s+\| Error Message\s+\|/', $output);
        $this->assertMatchesRegularExpression('/\| 1\s+\| iliosproject.org\s+\| FAIL!\s+\|/', $output);
    }
}
