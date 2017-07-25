<?php
namespace Tests\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\UsereventVoter;
use Ilios\CoreBundle\Classes\UserEvent;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class UsereventVoterTest
 */
class UsereventVoterTest extends AbstractVoterTestCase
{
    /**
     * @var UsereventVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->voter = new UsereventVoter();
    }

    /**
     * @dataProvider testVoteOnViewAccessAsDeveloperProvider
     * @covers \Ilios\AuthenticationBundle\Voter\UsereventVoter::vote
     */
    public function testVoteOnViewAccessAsDeveloper($token, UserEvent $object, $expectedOutcome, $message)
    {
        $this->voteOnViewAccess($token, $object, $expectedOutcome, $message);
    }

    /**
     * @dataProvider testVoteOnViewAccessAsFacultyProvider
     * @covers \Ilios\AuthenticationBundle\Voter\UsereventVoter::vote
     */
    public function testVoteOnViewAccessAsFaculty($token, UserEvent $object, $expectedOutcome, $message)
    {
        $this->voteOnViewAccess($token, $object, $expectedOutcome, $message);
    }

    /**
     * @dataProvider testVoteOnViewAccessAsDirectorProvider
     * @covers \Ilios\AuthenticationBundle\Voter\UsereventVoter::vote
     */
    public function testVoteOnViewAccessAsDirector($token, UserEvent $object, $expectedOutcome, $message)
    {
        $this->voteOnViewAccess($token, $object, $expectedOutcome, $message);
    }

    /**
     * @dataProvider testVoteOnViewAccessAsStudentProvider
     * @covers \Ilios\AuthenticationBundle\Voter\UsereventVoter::vote
     */
    public function testVoteOnViewAccessAsStudent($token, UserEvent $object, $expectedOutcome, $message)
    {
        $this->voteOnViewAccess($token, $object, $expectedOutcome, $message);
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessAsDeveloperProvider()
    {
        $currentUserId = 1;
        $currentUser = $this->createUserInRoles($currentUserId, ['Developer']);
        $token = $this->createMockTokenWithSessionUser($currentUser);

        $otherUserId = $currentUserId + 1;

        return [
            [
                $token,
                $this->createUserEvent(true, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Developer can view own published event.'
            ], [
                $token,
                $this->createUserEvent(false, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Developer can view own un-published event.'
            ], [
                $token,
                $this->createUserEvent(true, $otherUserId),
                VoterInterface::ACCESS_GRANTED,
                'Developer can view other user\'s published event.'
            ], [
                $token,
                $this->createUserEvent(false, $otherUserId),
                VoterInterface::ACCESS_DENIED,
                'Developer cannot view other user\'s un-published event.'
            ]
        ];
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessAsFacultyProvider()
    {
        $currentUserId = 1;
        $currentUser = $this->createUserInRoles($currentUserId, ['Faculty']);
        $token = $this->createMockTokenWithSessionUser($currentUser);

        $otherUserId = $currentUserId + 1;

        return [
            [
                $token,
                $this->createUserEvent(true, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Faculty can view own published event.'
            ], [
                $token,
                $this->createUserEvent(false, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Faculty can view own un-published event.'
            ], [
                $token,
                $this->createUserEvent(true, $otherUserId),
                VoterInterface::ACCESS_DENIED,
                'Faculty cannot view other user\'s event.'
            ]
        ];
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessAsDirectorProvider()
    {
        $currentUserId = 1;
        $currentUser = $this->createUserInRoles($currentUserId, ['Course Director']);
        $token = $this->createMockTokenWithSessionUser($currentUser);

        $otherUserId = $currentUserId + 1;

        return [
            [
                $token,
                $this->createUserEvent(true, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Director can view own published event.'
            ], [
                $token,
                $this->createUserEvent(false, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Director can view own un-published event.'
            ], [
                $token,
                $this->createUserEvent(true, $otherUserId),
                VoterInterface::ACCESS_DENIED,
                'Director cannot view other user\'s event.'
            ]
        ];
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessAsStudentProvider()
    {
        $currentUserId = 1;
        $currentUser = $this->createUserInRoles($currentUserId, ['Student', 'Former Student']);
        $token = $this->createMockTokenWithSessionUser($currentUser);

        $otherUserId = $currentUserId + 1;
        return [
            [
                $token,
                $this->createUserEvent(true, $currentUserId),
                VoterInterface::ACCESS_GRANTED,
                'Student can view own published event.'
            ], [
                $token,
                $this->createUserEvent(false, $currentUserId),
                VoterInterface::ACCESS_DENIED,
                'Student cannot view own un-published event.'
            ], [
                $token,
                $this->createUserEvent(true, $otherUserId),
                VoterInterface::ACCESS_DENIED,
                'Student cannot view other user\'s event.'
            ]
        ];
    }

    /**
     * @param \Mockery\Mock $token A mock user token.
     * @param UserEvent $object A user event entity.
     * @param int $expectedOutcome The expected outcome of the vote.
     * @param string $message Additional information about the test being performed.
     */
    protected function voteOnViewAccess($token, UserEvent $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::VIEW];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @param boolean $isPublished
     * @param int $user
     * @return \Ilios\CoreBundle\Classes\UserEvent
     */
    protected function createUserEvent($isPublished, $user)
    {
        $event = new UserEvent();
        $event->isPublished = $isPublished;
        $event->user = $user;
        return $event;
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
