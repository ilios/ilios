<?php
namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Revokes root-level privileges from a given user.
 *
 * Class RemoveRootUserCommand
 */
class RemoveRootUserCommand extends Command
{
    /**
     * @var string
     */
    const COMMAND_NAME = 'ilios:maintenance:remove-root-user';

    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Revokes root-level privileges from a given user.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                "The user's id."
            );
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /* @var UserInterface $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception("No user with id #{$userId} was found.");
        }
        $user->setRoot(false);
        $this->userManager->update($user, true, true);
        $output->writeln("Root-level privileges have been revoked from user with id #{$userId}.");
    }
}
