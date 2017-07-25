<?php

namespace Ilios\CliBundle\Command;

use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;

/**
 * Create a new token for a user
 *
 * Class CreateUserTokenCommand
 */
class CreateUserTokenCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;
    
    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;
    
    public function __construct(
        UserManager $userManager,
        JsonWebTokenManager $jwtManager
    ) {
        $this->userManager = $userManager;
        $this->jwtManager = $jwtManager;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:create-user-token')
            ->setDescription('Create a new API token for a user.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            )
            ->addOption(
                'ttl',
                null,
                InputOption::VALUE_REQUIRED,
                'What is the interval before the token expires?',
                'PT8H'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /** @var UserInterface $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        $jwt = $this->jwtManager->createJwtFromUser($user, $input->getOption('ttl'));
        
        $output->writeln('Success!');
        $output->writeln('Token ' . $jwt);
    }
}
