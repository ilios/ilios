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
    /** @var array */
    protected $dtos;

    /** @var array */
    protected $entities;

    /** @var array */
    protected $names = [
        'MeshConcept',
        'MeshDescriptor',
        'MeshPreviousIndexing',
        'MeshQualifier',
        'MeshTerm',
        'MeshTree'
    ];

    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);

        $this->dtos['MeshConcept'] = MeshConceptDTO::class;
        $this->dtos['MeshDescriptor'] = MeshDescriptorDTO::class;
        $this->dtos['MeshPreviousIndexing'] = MeshPreviousIndexingDTO::class;
        $this->dtos['MeshQualifier'] = MeshQualifierDTO::class;
        $this->dtos['MeshTerm'] = MeshTermDTO::class;
        $this->dtos['MeshTree'] = MeshTreeDTO::class;

        $this->entities['MeshConcept'] = MeshConcept::class;
        $this->entities['MeshDescriptor'] = MeshDescriptor::class;
        $this->entities['MeshPreviousIndexing'] = MeshPreviousIndexing::class;
        $this->entities['MeshQualifier'] = MeshQualifier::class;
        $this->entities['MeshTerm'] = MeshTerm::class;
        $this->entities['MeshTree'] = MeshTree::class;
    }

    public function tearDown()
    {
        unset($this->dtos);
        unset($this->entities);
    }

    public function testAllowsRootFullAccess()
    {
        foreach ($this->names as $name) {
            $this->checkRootAccess($this->entities[$name], $this->dtos[$name]);
        }
    }

    public function testCanViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        foreach ($this->names as $name) {
            $dto = m::mock($this->dtos[$name]);
            $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${name}DTO View allowed");
        }
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        foreach ($this->names as $name) {
            $entity = m::mock($this->entities[$name]);
            $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${name} View allowed");
        }
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        foreach ($this->names as $name) {
            $entity = m::mock($this->entities[$name]);
            $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${name} Edit denied");
        }
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        foreach ($this->names as $name) {
            $entity = m::mock($this->entities[$name]);
            $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${name} Delete denied");
        }
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        foreach ($this->names as $name) {
            $entity = m::mock($this->entities[$name]);
            $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
            $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "${name} Create denied");
        }
    }
}
