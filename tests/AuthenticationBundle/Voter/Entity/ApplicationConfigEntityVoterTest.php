<?php
namespace Tests\AuthenticationBundle\Voter\Entity;

use Tests\AuthenticationBundle\Voter\AbstractVoterTestCase;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\Entity\ApplicationConfigEntityVoter;
use Ilios\CoreBundle\Entity\ApplicationConfig;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ApplicationConfigEntityVoterTest
 */
class ApplicationConfigEntityVoterTest extends AbstractVoterTestCase
{
    /**
     * @var ApplicationConfigEntityVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->voter = new ApplicationConfigEntityVoter();
    }

    /**
     * @dataProvider voteProvider
     * @covers \Ilios\AuthenticationBundle\Voter\ApplicationConfigEntityVoter::vote
     */
    public function testVoteOnView($token, ApplicationConfigInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::VIEW];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider voteProvider
     * @covers \Ilios\AuthenticationBundle\Voter\ApplicationConfigEntityVoter::vote
     * @no
     */
    public function testVoteOnCreate($token, ApplicationConfigInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::CREATE];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider voteProvider
     * @covers \Ilios\AuthenticationBundle\Voter\ApplicationConfigEntityVoter::vote
     */
    public function testVoteOnEdit($token, ApplicationConfigInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::EDIT];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider voteProvider
     * @covers \Ilios\AuthenticationBundle\Voter\ApplicationConfigEntityVoter::vote
     */
    public function testVoteOnDelete($token, ApplicationConfigInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::DELETE];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @return array
     */
    public function voteProvider()
    {
        $data = [];
        $currentUser = $this->createMockSessionUserWithUserRoles([
            $this->createMockUserRole('Developer')
        ]);
        $token = $this->createMockTokenWithSessionUser($currentUser);
        $data[] = [
            $token,
            new ApplicationConfig(),
            VoterInterface::ACCESS_GRANTED,
            'Developer can take action.',
        ];
        foreach (['Faculty', 'Course Director', 'Student', 'Former Student'] as $role) {
            $currentUser = $this->createMockSessionUserWithUserRoles([
                $this->createMockUserRole($role)
            ]);
            $token = $this->createMockTokenWithSessionUser($currentUser);
            $data[] = [
                $token,
                new ApplicationConfig(),
                VoterInterface::ACCESS_DENIED,
                "{$role} cannot take action.",
            ];
        }
        return $data;
    }
}
