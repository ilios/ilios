<?php

namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\ImportMeshUniverseCommand;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class ImportMeshUniverseCommandTest
 * @package Tests\CliBundle\Command
 */
class ImportMeshUniverseCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */

    protected $meshParser;

    /**
     * @var m\MockInterface
     */
    protected $descriptorManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var string
     */
    const COMMAND_NAME = 'ilios:maintenance:import-mesh-universe';

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->meshParser = m::mock(Parser::class);
        $this->descriptorManager = m::mock(MeshDescriptorManager::class);

        $command = new ImportMeshUniverseCommand($this->meshParser, $this->descriptorManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->meshParser);
        unset($this->descriptorManager);
        unset($this->commandTester);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testNoArgs()
    {
        $this->mockHappyPath();

        $url = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc2017.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertContains("Started MeSH universe import, this will take a while...", $output);
        $this->assertContains("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
        $this->assertContains("2/4: Clearing database of existing MeSH data.", $output);
        $this->assertContains("3/4: Importing MeSH data into database.", $output);
        $this->assertContains("4/4: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertRegExp("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testGivenFilePath()
    {
        $this->mockHappyPath();

        $path = '/path/to/my/desc.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$path])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--path' => $path,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertContains("1/4: Parsing MeSH XML retrieved from ${path}.", $output);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testGivenUrl()
    {
        $this->mockHappyPath();

        $url = 'http://localhost/desc.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--url' => $url,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertContains("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testGivenYear()
    {
        $this->mockHappyPath();

        $year = '2016';
        $url = "ftp://nlmpubs.nlm.nih.gov/online/mesh/.xmlmesh/desc${year}.xml";
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertContains("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testInvalidGivenYear()
    {
        $year = '1906';
        $this->expectExceptionMessage('Given year must be one of: 2016, 2017');

        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );
        $this->descriptorManager->shouldNotHaveReceived('clearExistingData');
        $this->descriptorManager->shouldNotHaveReceived('findDTOsBy');
        $this->descriptorManager->shouldNotHaveReceived('upsertMeshUniverse');
        $this->descriptorManager->shouldNotHaveReceived('flagDescriptorsAsDeleted');
    }

    protected function mockHappyPath()
    {
        $this->descriptorManager->shouldReceive('clearExistingData')->once();
        $this->descriptorManager->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorManager->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorManager->shouldReceive('flagDescriptorsAsDeleted')->once();
    }
}
