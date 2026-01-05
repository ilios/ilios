<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Entity\MeshConcept;
use App\Entity\MeshDescriptor;
use App\Entity\MeshPreviousIndexing;
use App\Entity\MeshQualifier;
use App\Entity\MeshTerm;
use App\Entity\MeshTree;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Command\ImportMeshUniverseCommand;
use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Ilios\MeSH\Parser;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Acceptance test for the MeSH importer command.

 * @package App\Tests\Command
 */
#[Group('cli')]
#[CoversClass(ImportMeshUniverseCommand::class)]
final class ImportMeshUniverseCommandAcceptanceTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;

    protected MeshDescriptorRepository $meshDescriptorRepository;

    protected EntityManager $entityManager;
    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $this->entityManager = $em;

        // Invoke the test fixtures data loader, but without fixtures, to force schema creation in the test db.
        $databaseTool = $container->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([]);

        // Get a hold on the MeSH descriptor repo. We'll need it in teardown to clear out these tables again.
        /** @var MeshDescriptorRepository $meshDescriptorRepository */
        $meshDescriptorRepository = $this->entityManager->getRepository(MeshDescriptor::class);
        $this->meshDescriptorRepository = $meshDescriptorRepository;

        $meshParser = $container->get(Parser::class);
        $meshIndex = $container->get(Mesh::class);

        $command = new ImportMeshUniverseCommand($meshParser, $this->meshDescriptorRepository, $meshIndex);
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->commandTester);
        unset($this->entityManager);
        // Clear out the MeSH tables.
        $this->meshDescriptorRepository->clearExistingData();
        unset($this->meshDescriptorRepository);
    }

    public function testImport(): void
    {
        $meshConceptRepository = $this->entityManager->getRepository(MeshConcept::class);
        $meshPreviousIndexingRepository = $this->entityManager->getRepository(MeshPreviousIndexing::class);
        $meshQualifierRepository = $this->entityManager->getRepository(MeshQualifier::class);
        $meshTermRepository = $this->entityManager->getRepository(MeshTerm::class);
        $meshTreeRepository = $this->entityManager->getRepository(MeshTree::class);

        // Verify that the MeSH tables are empty pre-import.
        $this->assertEquals(0, $this->meshDescriptorRepository->count());
        $this->assertEquals(0, $meshConceptRepository->count());
        $this->assertEquals(0, $meshPreviousIndexingRepository->count());
        $this->assertEquals(0, $meshQualifierRepository->count());
        $this->assertEquals(0, $meshTermRepository->count());
        $this->assertEquals(0, $meshTreeRepository->count());

        $path = __DIR__ . '/TestFiles/desc.xml';
        $this->commandTester->execute(
            [
                '--path' => $path,
            ]
        );
        // Check data in MeSH tables post-import.
        $this->assertEquals(1, $this->meshDescriptorRepository->count());
        $this->assertEquals(2, $meshConceptRepository->count());
        $this->assertEquals(1, $meshPreviousIndexingRepository->count());
        $this->assertEquals(2, $meshQualifierRepository->count());
        $this->assertEquals(3, $meshTermRepository->count());
        $this->assertEquals(1, $meshTreeRepository->count());

        $descriptors = $this->meshDescriptorRepository->findAll();
        $this->assertCount(1, $descriptors);

        $this->assertEquals('D000000', $descriptors[0]->getId());
        $this->assertEquals('a descriptor', $descriptors[0]->getName());
        $this->assertEquals('an annotation', $descriptors[0]->getAnnotation());
        $this->assertFalse($descriptors[0]->isDeleted());

        $previousIndexing = $descriptors[0]->getPreviousIndexing();
        $this->assertEquals('also previously indexed as', $previousIndexing->getPreviousIndexing());

        $trees = $descriptors[0]->getTrees();
        $this->assertCount(1, $trees);

        $this->assertEquals('D00.000.000.000.001', $trees[0]->getTreeNumber());

        $qualifiers = $descriptors[0]->getQualifiers();
        $this->assertCount(2, $qualifiers);

        $this->assertEquals('Q000001', $qualifiers[0]->getId());
        $this->assertEquals('a qualifier', $qualifiers[0]->getName());
        $this->assertEquals('Q000002', $qualifiers[1]->getId());
        $this->assertEquals('another qualifier', $qualifiers[1]->getName());

        $concepts = $descriptors[0]->getConcepts();
        $this->assertCount(2, $concepts);

        $this->assertEquals('M0000001', $concepts[0]->getId());
        $this->assertEquals('a concept', $concepts[0]->getName());
        $this->assertTrue($concepts[0]->getPreferred());
        $this->assertEquals('a scope note', $concepts[0]->getScopeNote());
        $this->assertEquals('a casn1 name', $concepts[0]->getCasn1Name());

        $terms = $concepts[0]->getTerms();
        $this->assertCount(1, $terms);

        $this->assertEquals('T000001', $terms[0]->getMeshTermUid());
        $this->assertEquals('a term', $terms[0]->getName());
        $this->assertEquals('NON', $terms[0]->getLexicalTag());
        $this->assertTrue($terms[0]->isConceptPreferred());
        $this->assertTrue($terms[0]->isRecordPreferred());
        $this->assertFalse($terms[0]->isPermuted());

        $this->assertEquals('M0000002', $concepts[1]->getId());
        $this->assertEquals('another concept', $concepts[1]->getName());
        $this->assertFalse($concepts[1]->getPreferred());
        $this->assertNull($concepts[1]->getScopeNote());
        $this->assertNull($concepts[1]->getCasn1Name());

        $terms = $concepts[1]->getTerms();
        $this->assertCount(2, $terms);

        $this->assertEquals('T000002', $terms[0]->getMeshTermUid());
        $this->assertEquals('another term', $terms[0]->getName());
        $this->assertEquals('ABB', $terms[0]->getLexicalTag());
        $this->assertTrue($terms[0]->isConceptPreferred());
        $this->assertFalse($terms[0]->isRecordPreferred());
        $this->assertFalse($terms[0]->isPermuted());

        $this->assertEquals('T000003', $terms[1]->getMeshTermUid());
        $this->assertEquals('yet another term', $terms[1]->getName());
        $this->assertEquals('TRD', $terms[1]->getLexicalTag());
        $this->assertFalse($terms[1]->isConceptPreferred());
        $this->assertTrue($terms[1]->isRecordPreferred());
        $this->assertTrue($terms[1]->isPermuted());
    }
}
