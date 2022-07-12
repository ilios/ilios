<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CleanupStringsCommand;
use App\Entity\CourseObjectiveInterface;
use App\Entity\LearningMaterialInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Entity\SessionInterface;
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
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:cleanup-strings';

    protected $purifier;
    protected $em;
    protected $sessionObjectiveRepository;
    protected $courseObjectiveRepository;
    protected $programYearObjectiveRepository;
    protected $learningMaterialRepository;
    protected $courseLearningMaterialRepository;
    protected $sessionLearningMaterialRepository;
    protected $sessionRepository;
    protected CommandTester $commandTester;
    protected HttpClientInterface $httpClient;

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
        $commandInApp = $application->find(self::COMMAND_NAME);
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

    public function testPurifyObjectiveTitle()
    {
        $cleanSessionObjective = m::mock(SessionObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtySessionObjective = m::mock(SessionObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setTitle')->with('html title')
            ->mock();
        $this->sessionObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSessionObjective, $dirtySessionObjective]);
        $this->sessionObjectiveRepository->shouldReceive('update')->with($dirtySessionObjective, false);
        $this->sessionObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanCourseObjective = m::mock(CourseObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtyCourseObjective = m::mock(CourseObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setTitle')->with('html title')
            ->mock();
        $this->courseObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourseObjective, $dirtyCourseObjective]);
        $this->courseObjectiveRepository->shouldReceive('update')->with($dirtyCourseObjective, false);
        $this->courseObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanPyObjective = m::mock(ProgramYearObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtyProgramYearObjective = m::mock(ProgramYearObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setTitle')->with('html title')
            ->mock();
        $this->programYearObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanPyObjective, $dirtyProgramYearObjective]);
        $this->programYearObjectiveRepository->shouldReceive('update')->with($dirtyProgramYearObjective, false);
        $this->programYearObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $this->em->shouldReceive('flush')->times(3);
        $this->em->shouldReceive('clear')->times(3);
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--objective-title' => true
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/3 Objective Titles updated/',
            $output
        );
    }

    public function testTrimObjectiveTitle()
    {
        $cleanSessionObjective = m::mock(SessionObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtySessionObjective = m::mock(SessionObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t")
            ->shouldReceive('setTitle')->with('lorem ipsum')
            ->mock();
        $this->sessionObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSessionObjective, $dirtySessionObjective]);
        $this->sessionObjectiveRepository->shouldReceive('update')->with($dirtySessionObjective, false);
        $this->sessionObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanCourseObjective = m::mock(CourseObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtyCourseObjective = m::mock(CourseObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t")
            ->shouldReceive('setTitle')->with('lorem ipsum')
            ->mock();
        $this->courseObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourseObjective, $dirtyCourseObjective]);
        $this->courseObjectiveRepository->shouldReceive('update')->with($dirtyCourseObjective, false);
        $this->courseObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $cleanPyObjective = m::mock(ProgramYearObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtyProgramYearObjective = m::mock(ProgramYearObjectiveInterface::class)
            ->shouldReceive('getTitle')->andReturn("\r\n\t    lorem ipsum  \t \r\n\n\r  \t")
            ->shouldReceive('setTitle')->with('lorem ipsum')
            ->mock();
        $this->programYearObjectiveRepository->shouldReceive('findBy')->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanPyObjective, $dirtyProgramYearObjective]);
        $this->programYearObjectiveRepository->shouldReceive('update')->with($dirtyProgramYearObjective, false);
        $this->programYearObjectiveRepository->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $this->em->shouldReceive('flush')->times(3);
        $this->em->shouldReceive('clear')->times(3);
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--objective-title-blankspace' => true
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/3 Objective Titles updated/',
            $output
        );
    }

    public function testLearningMaterialDescription()
    {
        $clean = m::mock('App\Entity\LearningMaterialInterface')
            ->shouldReceive('getDescription')->andReturn('clean title')
            ->mock();
        $dirty = m::mock('App\Entity\LearningMaterialInterface')
            ->shouldReceive('getDescription')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setDescription')->with('html title')
            ->mock();
        $this->learningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$clean, $dirty]);
        $this->learningMaterialRepository->shouldReceive('update')->with($dirty, false);
        $this->learningMaterialRepository->shouldReceive('getTotalLearningMaterialCount')->andReturn(2);

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--learningmaterial-description' => true
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Learning Material Descriptions updated/',
            $output
        );
    }

    public function testLearningMaterialNotes()
    {
        $cleanCourse = m::mock('App\Entity\CourseLearningMaterialInterface')
            ->shouldReceive('getNotes')->andReturn('clean course note')
            ->mock();
        $dirtyCourse = m::mock('App\Entity\CourseLearningMaterialInterface')
            ->shouldReceive('getNotes')->andReturn('<script>alert();</script><h1>html course note</h1>')
            ->shouldReceive('setNotes')->with('html course note')
            ->mock();
        $this->courseLearningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanCourse, $dirtyCourse]);
        $this->courseLearningMaterialRepository->shouldReceive('update')->with($dirtyCourse, false);
        $this->courseLearningMaterialRepository->shouldReceive('getTotalCourseLearningMaterialCount')->andReturn(2);

        $cleanSession = m::mock('App\Entity\SessionLearningMaterialInterface')
            ->shouldReceive('getNotes')->andReturn('clean session note')
            ->mock();
        $dirtySession = m::mock('App\Entity\SessionLearningMaterialInterface')
            ->shouldReceive('getNotes')->andReturn('<script>alert();</script><h1>html session note</h1>')
            ->shouldReceive('setNotes')->with('html session note')
            ->mock();
        $this->sessionLearningMaterialRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$cleanSession, $dirtySession]);
        $this->sessionLearningMaterialRepository->shouldReceive('update')
            ->with($dirtySession, false);
        $this->sessionLearningMaterialRepository->shouldReceive('getTotalSessionLearningMaterialCount')->andReturn(2);

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->twice();
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--learningmaterial-note' => true
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

    public function testSessionDescription()
    {
        $clean = m::mock(SessionInterface::class)
            ->shouldReceive('getDescription')->andReturn('clean title')
            ->mock();
        $dirty = m::mock(SessionInterface::class)
            ->shouldReceive('getDescription')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setDescription')->with('html title')
            ->mock();
        $this->sessionRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$clean, $dirty]);
        $this->sessionRepository->shouldReceive('update')->with($dirty, false);
        $this->sessionRepository->shouldReceive('getTotalSessionCount')->andReturn(2);

        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--session-description' => true
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 Session Descriptions updated/',
            $output
        );
    }

    public function testSessionDescriptionBrInP()
    {
        $emptyPTag = m::mock(SessionInterface::class)
            ->shouldReceive('getDescription')->andReturn('<p>Empty P<br></p><p></p>')
            ->shouldReceive('setDescription')->with('<p>Empty P</p>')
            ->mock();
        $emptyPTagWithLineBreak = m::mock(SessionInterface::class)
            ->shouldReceive('getDescription')->andReturn('<p>Empty With Br<br></p><p><br /></p>')
            ->shouldReceive('setDescription')->with('<p>Empty With Br</p>')
            ->mock();
        $this->sessionRepository->shouldReceive('findBy')
            ->with([], ['id' => 'ASC'], 500, 0)
            ->andReturn([$emptyPTag, $emptyPTagWithLineBreak]);
        $this->sessionRepository->shouldReceive('update')->with($emptyPTag, false);
        $this->sessionRepository->shouldReceive('update')->with($emptyPTagWithLineBreak, false);
        $this->sessionRepository->shouldReceive('getTotalSessionCount')->andReturn(1);
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'           => self::COMMAND_NAME,
            '--session-description' => true
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/2 Session Descriptions updated/',
            $output
        );
    }

    public function correctLearningMaterialLinksProvider(): array
    {
        return [
            ['iliosproject.org', 'https://iliosproject.org'],
            ['http//iliosproject.org', 'https://http//iliosproject.org'],
        ];
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     * @dataProvider correctLearningMaterialLinksProvider
     * @param string $link
     * @param string $fixedLink
     */
    public function testCorrectLearningMaterialLinks($link, $fixedLink)
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    public function correctLearningMaterialLinksWhithoutFetchingProvider(): array
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
     * @param string $link
     * @param string $fixedLink
     */
    public function testCorrectLearningMaterialLinksWithoutFetching($link, $fixedLink)
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    public function correctLearningMaterialLinksNoChangesProvider(): array
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
    public function testCorrectLearningMaterialLinksNoChanges($link)
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksInBulk()
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("{$total} learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksFails()
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 1 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksFailsOnHttps()
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

        $this->commandTester->execute([ 'command' => self::COMMAND_NAME, '--learningmaterial-links' => true ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1 learning material links updated, 0 failures.", $output);
    }

    /**
     * @covers \App\Command\CleanupStringsCommand::correctLearningMaterialLinks
     */
    public function testCorrectLearningMaterialLinksVerboseFailureOutput()
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
            [ 'command' => self::COMMAND_NAME,'--learningmaterial-links' => true ],
            [ 'verbosity' => OutputInterface::VERBOSITY_VERBOSE],
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("0 learning material links updated, 1 failures.", $output);
        $this->assertMatchesRegularExpression('/\| Learning Material ID\s+\| Link\s+\| Error Message\s+\|/', $output);
        $this->assertMatchesRegularExpression('/\| 1\s+\| iliosproject.org\s+\| FAIL!\s+\|/', $output);
    }
}
