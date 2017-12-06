<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Mesh as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\DTO\MeshConceptDTO;
use Ilios\CoreBundle\Entity\DTO\MeshPreviousIndexingDTO;
use Ilios\CoreBundle\Entity\DTO\MeshQualifierDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTreeDTO;
use Ilios\CoreBundle\Entity\MeshConcept;
use Ilios\CoreBundle\Entity\MeshDescriptor;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\MeshPreviousIndexing;
use Ilios\CoreBundle\Entity\MeshQualifier;
use Ilios\CoreBundle\Entity\MeshTerm;
use Ilios\CoreBundle\Entity\MeshTree;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class MeshTest extends AbstractBase
{
    /**
     * @inheritdoc
     */
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    /**
     * @return array
     */
    public function dtoProvider()
    {
        return [
            [MeshConceptDTO::class],
            [MeshDescriptorDTO::class],
            [MeshPreviousIndexingDTO::class],
            [MeshQualifierDTO::class],
            [MeshTermDTO::class],
            [MeshTreeDTO::class],
        ];
    }

    /**
     * @return array
     */
    public function entityProvider()
    {
        return [
            [MeshConcept::class],
            [MeshDescriptor::class],
            [MeshPreviousIndexing::class],
            [MeshQualifier::class],
            [MeshTerm::class],
            [MeshTree::class],
        ];
    }

    /**
     * @return array
     */
    public function pairProvider()
    {
        return [
            [MeshConcept::class, MeshConceptDTO::class],
            [MeshDescriptor::class, MeshDescriptorDTO::class],
            [MeshPreviousIndexing::class, MeshPreviousIndexingDTO::class],
            [MeshQualifier::class, MeshQualifierDTO::class],
            [MeshTerm::class, MeshTermDTO::class],
            [MeshTree::class, MeshTreeDTO::class]
        ];
    }

    /**
     * @dataProvider pairProvider
     * @param string $entityClass
     * @param string $dtoClass
     */
    public function testAllowsRootFullAccess($entityClass, $dtoClass)
    {
        $this->checkRootAccess($entityClass, $dtoClass);
    }

    /**
     * @dataProvider dtoProvider
     * @param string $dtoClass
     */
    public function testCanViewDTO($dtoClass)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $dto = m::mock($dtoClass);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${dtoClass} View allowed");
    }

    /**
     * @dataProvider entityProvider
     * @param string $entityClass
     */
    public function testCanView($entityClass)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock($entityClass);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${entityClass} View allowed");
    }

    /**
     * @dataProvider entityProvider
     * @param string $entityClass
     */
    public function testCanNotEdit($entityClass)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock($entityClass);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${entityClass} Edit denied");
    }

    /**
     * @dataProvider entityProvider
     * @param string $entityClass
     */
    public function testCanNotDelete($entityClass)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock($entityClass);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${entityClass} Delete denied");
    }

    /**
     * @dataProvider entityProvider
     * @param string $entityClass
     */
    public function testCanNotCreate($entityClass)
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock($entityClass);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${entityClass} Create denied");
    }
}
