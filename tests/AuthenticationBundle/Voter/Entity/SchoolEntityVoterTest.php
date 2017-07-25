<?php
namespace Tests\AuthenticationBundle\Voter\Entity;

use Tests\AuthenticationBundle\Voter\AbstractVoterTestCase;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\Entity\SchoolEntityVoter;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Mockery as m;

/**
 * Class SchoolEntityVoterTest
 * @todo Test voting on create, edit and delete operations. [ST 2016/05/17]
 */
class SchoolEntityVoterTest extends AbstractVoterTestCase
{
    /**
     * @var SchoolEntityVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->voter = new SchoolEntityVoter(
            m::mock('Ilios\CoreBundle\Entity\Manager\PermissionManager')
        );
    }

    /**
     * @dataProvider testVoteOnViewAccessProvider
     * @covers \Ilios\AuthenticationBundle\Voter\Entity\SchoolEntityVoter::vote
     */
    public function testVoteOnViewAccess($token, SchoolInterface $object, $expectedOutcome, $message)
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
        $school = new School();

        $i = 0;
        foreach ($roles as $role) {
            ++$i;
            $currentUser = $this->createUserInRoles($i, [$role]);
            $token = $this->createMockTokenWithSessionUser($currentUser);
            $data[] = [$token, $school, VoterInterface::ACCESS_GRANTED, "${role} can view school."];
        }
        
        $currentUser = $this->createMockSessionUserWithUserRoles([]);
        $token = $this->createMockTokenWithSessionUser($currentUser);
        $data[] = [$token, $school, VoterInterface::ACCESS_GRANTED, "User without roles can view school."];

        $token = $this->createMockTokenWithSessionUser(null);
        $data[] = [$token, $school, VoterInterface::ACCESS_DENIED, "Unauthorized user cannot view school."];

        return $data;
    }

    /**
     * @param \Mockery\Mock $token A mock user token.
     * @param SchoolInterface $object A school entity.
     * @param int $expectedOutcome The expected outcome of the vote.
     * @param string $message Additional information about the test being performed.
     */
    protected function voteOnViewAccess($token, SchoolInterface $object, $expectedOutcome, $message)
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
        $sessionUser = $this->createMockSessionUserWithUserRoles($roles);
        $sessionUser->shouldReceive('getId')->withNoArgs()->andReturn($id);

        return $sessionUser;
    }
}
