<?php

namespace App\Tests\Command;

use App\Command\ImportMeshUniverseCommand;
use App\Entity\Manager\MeshDescriptorManager;
use App\Service\Index;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ImportMeshUniverseCommandTest
 * @package App\Tests\Command
 */
class ImportMeshUniverseCommandTest extends KernelTestCase
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
     * @var m\MockInterface
     */
    protected $index;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var string
     */
    const COMMAND_NAME = 'ilios:import-mesh-universe';

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->meshParser = m::mock(Parser::class);
        $this->descriptorManager = m::mock(MeshDescriptorManager::class);
        $this->index = m::mock(Index::class);

        $command = new ImportMeshUniverseCommand($this->meshParser, $this->descriptorManager, $this->index);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown() : void
    {
        unset($this->meshParser);
        unset($this->descriptorManager);
        unset($this->index);
        unset($this->commandTester);
    }

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testNoArgs()
    {
        $this->mockHappyPath();

        $url = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2019.xml';
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
    public function testYear2018()
    {
        $this->mockHappyPath();

        $year = '2018';
        $url = "ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2018.xml";
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
    public function testYear2019()
    {
        $this->mockHappyPath();

        $year = '2019';
        $url = "ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2019.xml";
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
        $this->expectExceptionMessage('Given year must be one of: 2018, 2019');
        $this->index->shouldReceive('isEnabled');

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

    /**
     * @covers ImportMeshUniverseCommand::execute
     */
    public function testIndexesResults()
    {
        $this->descriptorManager->shouldReceive('clearExistingData')->once();
        $this->descriptorManager->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorManager->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorManager->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->index->shouldReceive('isEnabled')->andReturn(true);

        $descriptor = m::mock(Descriptor::class);
        $descriptorSet = m::mock(DescriptorSet::class);

        $descriptorSet
            ->shouldReceive('getDescriptors')->once()->andReturn([$descriptor])
            ->shouldReceive('getDescriptorUis')->andReturn(['id']);
        $this->index
            ->shouldReceive('indexMeshDescriptors')->with([$descriptor])
            ->once()->andReturn(true);

        $url = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2019.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn($descriptorSet);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertContains("Started MeSH universe import, this will take a while...", $output);
        $this->assertContains("1/5: Parsing MeSH XML retrieved from ${url}.", $output);
        $this->assertContains("2/5: Clearing database of existing MeSH data.", $output);
        $this->assertContains("3/5: Importing MeSH data into database.", $output);
        $this->assertContains("4/5: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertContains("5/5: Adding MeSH data to the search index.", $output);
        $this->assertRegExp("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    protected function mockHappyPath()
    {
        $this->descriptorManager->shouldReceive('clearExistingData')->once();
        $this->descriptorManager->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorManager->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorManager->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->index->shouldReceive('isEnabled')->andReturn(false);
    }
}
