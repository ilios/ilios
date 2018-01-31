<?php
namespace Tests\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Service\Config;
use Tests\AuthenticationBundle\Voter\AbstractVoterTestCase;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\DTO\ApplicationConfigDTOVoter;
use Ilios\CoreBundle\Entity\DTO\ApplicationConfigDTO;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ApplicationConfigDTOVoterTest
 */
class ApplicationConfigDTOVoterTest extends AbstractVoterTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ApplicationConfigDTOVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $config = m::mock(Config::class);
        $config->shouldReceive('useNewPermissionsSystem')->andReturn(false);
        $this->voter = new ApplicationConfigDTOVoter($config);
    }

    /**
     * @dataProvider testVoteOnViewAccessProvider
     * @covers \Ilios\AuthenticationBundle\Voter\DTO\ApplicationConfigDTOVoter::vote
     */
    public function testVoteOnViewAccess($token, ApplicationConfigDTO $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::VIEW];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @return array
     */
    public function testVoteOnViewAccessProvider()
    {
        $data = [];
        $roles = ['Faculty', 'Student', 'Former Student', 'Course Director'];
        $applicationConfig = new ApplicationConfigDTO(1, 'Test', '');

        $i = 0;
        foreach ($roles as $role) {
            ++$i;
            $currentUser = $this->createUserInRoles($i, [$role]);
            $token = $this->createMockTokenWithSessionUser($currentUser);
            $data[] = [
                $token,
                $applicationConfig,
                VoterInterface::ACCESS_DENIED,
                "${role} can not view applicationConfig."
            ];
        }

        $currentUser = $this->createMockSessionUserWithUserRoles([]);
        $token = $this->createMockTokenWithSessionUser($currentUser);
        $data[] = [
            $token,
            $applicationConfig,
            VoterInterface::ACCESS_DENIED,
            "User without roles can not view applicationConfig."
        ];

        $token = $this->createMockTokenWithSessionUser(null);
        $data[] = [
            $token,
            $applicationConfig,
            VoterInterface::ACCESS_DENIED,
            "Unauthorized user can not view applicationConfig."
        ];

        $currentUser = $this->createMockSessionUserWithUserRoles([
            $this->createMockUserRole('Developer')
        ]);
        $token = $this->createMockTokenWithSessionUser($currentUser);
        $data[] = [
            $token,
            $applicationConfig,
            VoterInterface::ACCESS_GRANTED,
            "User with Developer role can view applicationConfig."
        ];

        return $data;
    }
}
