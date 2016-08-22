<?php
namespace Tests\AuthenticationBundle\Voter\DTO;

use Tests\AuthenticationBundle\Voter\AbstractVoterTestCase;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\DTO\SchoolDTOVoter;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class SchoolDTOVoterTest
 * @package Tests\AuthenticationBundle\\Voter\DTO
 */
class SchoolDTOVoterTest extends AbstractVoterTestCase
{
    /**
     * @var SchoolDTOVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->voter = new SchoolDTOVoter();
    }

    /**
     * @dataProvider testVoteOnViewAccessProvider
     * @covers Ilios\AuthenticationBundle\Voter\DTO\SchoolDTOVoter::vote
     */
    public function testVoteOnViewAccess($token, SchoolDTO $object, $expectedOutcome, $message)
    {
        $this->voteOnViewAccess($token, $object, $expectedOutcome, $message);
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessProvider()
    {
        $data = [];
        $roles = ['Faculty', 'Student', 'Former Student', 'Developer', 'Course Director'];
        $school = new SchoolDTO(1, 'Test', '', '');

        $i = 0;
        foreach ($roles as $role) {
            ++$i;
            $currentUser = $this->createUserInRoles($i, [$role]);
            $token = $this->createMockTokenWithUser($currentUser);
            $data[] = [$token, $school, VoterInterface::ACCESS_GRANTED, "${role} can view school."];
        }

        $currentUser = $this->createMockUserWithUserRoles([]);
        $token = $this->createMockTokenWithUser($currentUser);
        $data[] = [$token, $school, VoterInterface::ACCESS_GRANTED, "User without roles can view school."];


        $token = $this->createMockTokenWithUser(null);
        $data[] = [$token, $school, VoterInterface::ACCESS_DENIED, "Unauthorized user cannot view school."];

        return $data;
    }

    /**
     * @param \Mockery\Mock $token A mock user token.
     * @param SchoolDTO $object A school DTO.
     * @param int $expectedOutcome The expected outcome of the vote.
     * @param string $message Additional information about the test being performed.
     */
    protected function voteOnViewAccess($token, SchoolDTO $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::VIEW];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * Creates a mock object for a user with a given user-roles and user id.
     * @param int $id The user id.
     * @param array $roles A list of user-role titles.
     * @return \Mockery\Mock The user mock object.
     */
    protected function createUserInRoles($id, array $roles)
    {
        $roles = array_map(function ($role) {
            return $this->createMockUserRole($role);
        }, $roles);
        $user = $this->createMockUserWithUserRoles($roles);
        $user->shouldReceive('getId')->withNoArgs()->andReturn($id);
        return $user;
    }
}
