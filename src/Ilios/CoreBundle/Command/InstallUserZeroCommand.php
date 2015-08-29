<?php

namespace Ilios\CoreBundle\Command;

use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Creates a first user account with admin-level privileges.
 *
 * Class InstallUserZeroCommand
 * @package Ilios\CoreBundle\Command
 */
class InstallUserZeroCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    const USERNAME = 'zero_user';

    /**
     * @var string
     */
    const PASSWORD = 'Ch4nge_m3';

    /**
     * @var string
     */
    const FIRST_NAME = 'User';

    /**
     * @var string
     */
    const LAST_NAME = 'Zero';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $description =<<< EOL
This command will create a user account with the given email address
and linked to the given school in the `user` table.
The generated user account will be assigned to the "Course Director" user role
and thus grant full privileges to all Ilios features to its user.
EOL;

        $this
            ->setName('ilios:setup:install_user_zero')
            ->setDescription($description)
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'A valid email address.'
            )
            ->addArgument(
                'school id',
                InputArgument::REQUIRED,
                'A valid school id.'
            );
    }

    /**
     * {@inheritdoc}
     *
     * @todo add proper input validation [ST 2015/08/28]
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $schoolId = $input->getArgument('school id');

        /**
         * @var SchoolManagerInterface $schoolManager
         */
        $schoolManager = $this->getContainer()->get('ilios.school.manager');
        $school = $schoolManager->findSchoolBy(['id' => $schoolId]);
        if (empty($school)) {
            throw new \Exception('School not found.');
        }

        /**
         * @var UserManagerInterface $userManager
         */
        $userManager = $this->getContainer()->get('ilios.user.manager');
        $user = $userManager->createUser();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($email);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);
        /**
         * @var UserRoleManagerInterface $userRoleManager
         */
        $userRoleManager = $this->getContainer()->get('ilios.userrole.manager');
        $user->addRole($userRoleManager->findUserRoleBy(['title' => 'Course Director']));
        $user->setSchool($school);
        $userManager->updateUser($user);

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, self::PASSWORD);

        /**
         * @var AuthenticationManagerInterface $authenticationManager
         */
        $authenticationManager = $this->getContainer()->get('ilios.authentication.manager');
        $authentication = $authenticationManager->createAuthentication();
        $authentication->setUser($user);
        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordBcrypt($encodedPassword);
        $authenticationManager->updateAuthentication($authentication);

        $output->writeln('The user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'", self::USERNAME, self::PASSWORD));
    }
}