<?php

namespace Ilios\CliBundle\Command;

use Ilios\CliBundle\Form\InstallFirstUserType;
use Ilios\CoreBundle\Entity\Manager\ManagerInterface;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Creates a first user account with Course Director privileges.
 *
 * Class InstallFirstUserCommand
 * @package Ilios\CoreBundle\Command
 */
class InstallFirstUserCommand extends Command
{
    /**
     * @var string
     */
    const USERNAME = 'first_user';

    /**
     * @var string
     */
    const PASSWORD = 'Ch4nge_m3';

    /**
     * @var string
     */
    const FIRST_NAME = 'First';

    /**
     * @var string
     */
    const LAST_NAME = 'User';

    /**
     * @var ManagerInterface
     */
    protected $userManager;

    /**
     * @var ManagerInterface
     */
    protected $schoolManager;

    /**
     * @var ManagerInterface
     */
    protected $userRoleManager;

    /**
     * @var  ManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * Constructor.
     * @param ManagerInterface $userManager
     * @param ManagerInterface $schoolManager
     * @param ManagerInterface $userRoleManager
     * @param ManagerInterface $authenticationManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        ManagerInterface $userManager,
        ManagerInterface $schoolManager,
        ManagerInterface $userRoleManager,
        ManagerInterface $authenticationManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userManager = $userManager;
        $this->schoolManager = $schoolManager;
        $this->userRoleManager = $userRoleManager;
        $this->authenticationManager = $authenticationManager;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:setup:first-user')
            ->setDescription('Creates a first user account with "Course Director" privileges.')
            ->addOption(
                'school',
                null,
                InputOption::VALUE_REQUIRED,
                'A valid school id.'
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_REQUIRED,
                'A valid email address.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // prevent this command to run on a non-empty user store.
        $existingUser = $this->userManager->findOneBy([]);
        if (! empty($existingUser)) {
            throw new \Exception(
                'Sorry, at least one user record already exists. Cannot create a "first" user account.'
            );
        }

        $schoolEntities = $this->schoolManager->findBy([]);

        // check if any school data is present before invoking the form helper
        // to prevent the form from breaking on missing school data further downstream.
        if (empty($schoolEntities)) {
            throw new \Exception('No schools found. Please load schools into this Ilios instance first.');
        }

        // transform school data into a format that can be processed by the form.
        $schools = [];
        /* @var SchoolInterface $entity */
        foreach ($schoolEntities as $entity) {
            $schools[$entity->getId()] = $entity->getTitle();
        }

        /** @var FormHelper $formHelper */
        $formHelper = $this->getHelper('form');
        $formData = $formHelper->interactUsingForm(
            new InstallFirstUserType($schools),
            $input,
            $output
        );

        $user = $this->userManager->create();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($formData['email']);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);

        $user->addRole($this->userRoleManager->findOneBy(['title' => 'Developer']));
        $user->setSchool($this->schoolManager->findOneBy(['id' => $formData['school']]));
        $this->userManager->update($user);

        $authentication = $this->authenticationManager->create();

        $authentication->setUser($user);
        $user->setAuthentication($authentication); // circular reference needed here. 123 BLEAH! [ST 2015/08/31]

        $encodedPassword = $this->passwordEncoder->encodePassword($user, self::PASSWORD);

        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordBcrypt($encodedPassword);
        $this->authenticationManager->update($authentication);

        $output->writeln('Success!');
        $output->writeln('A user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'.", self::USERNAME, self::PASSWORD));
        $output->writeln('Please change this password as soon as possible.');
    }
}
