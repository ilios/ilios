<?php

namespace Ilios\CliBundle\Command;

use Ilios\CliBundle\Form\InstallUserZeroType;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a first ("user zero") account with Course Director privileges.
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
        $this
            ->setName('form:install_user_zero')
            ->setDescription('Creates a first user account with "Course Director" privileges.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var UserManagerInterface $userManager
         */
        $userManager = $this->getContainer()->get('ilioscore.user.manager');

        // prevent this command to run on a non-empty user store.
        $existingUser = $userManager->findUserBy([]);
        if (! empty($existingUser)) {
            throw new \Exception('Sorry, at least one user record already exists. Cannot create a "user zero".');
        }

        /**
         * @var SchoolManagerInterface $schoolManager
         */
        $schoolManager = $this->getContainer()->get('ilioscore.school.manager');

        /** @var FormHelper $formHelper */
        $formHelper = $this->getHelper('form');
        $formData = $formHelper->interactUsingForm(
            new InstallUserZeroType($schoolManager),
            $input,
            $output
        );

        $user = $userManager->createUser();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($formData['email']);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);
        /**
         * @var UserRoleManagerInterface $userRoleManager
         */
        $userRoleManager = $this->getContainer()->get('ilioscore.userrole.manager');
        $user->addRole($userRoleManager->findUserRoleBy(['title' => 'Course Director']));
        $user->setSchool($schoolManager->findSchoolBy(['id' => $formData['school']]));
        $userManager->updateUser($user);

        /**
         * @var AuthenticationManagerInterface $authenticationManager
         */
        $authenticationManager = $this->getContainer()->get('ilioscore.authentication.manager');
        $authentication = $authenticationManager->createAuthentication();

        $authentication->setUser($user);
        $user->setAuthentication($authentication); // circular reference needed here. 123 BLEAH! [ST 2015/08/31]

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, self::PASSWORD);

        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordBcrypt($encodedPassword);
        $authenticationManager->updateAuthentication($authentication);

        $output->writeln('Success!');
        $output->writeln('A user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'.", self::USERNAME, self::PASSWORD));
        $output->writeln('Please change this password as soon as possible.');
    }
}