<?php

namespace Ilios\CliBundle\Command;

use Ilios\CliBundle\Form\InstallFirstUserType;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Matthias\SymfonyConsoleForm\Console\Helper\FormHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a first user account with Course Director privileges.
 *
 * Class InstallFirstUserCommand
 * @package Ilios\CoreBundle\Command
 */
class InstallFirstUserCommand extends ContainerAwareCommand
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
        /**
         * @var UserManager $userManager
         */
        $userManager = $this->getContainer()->get('ilioscore.user.manager');

        // prevent this command to run on a non-empty user store.
        $existingUser = $userManager->findOneBy([]);
        if (! empty($existingUser)) {
            throw new \Exception(
                'Sorry, at least one user record already exists. Cannot create a "first" user account.'
            );
        }

        /**
         * @var SchoolManager $schoolManager
         */
        $schoolManager = $this->getContainer()->get('ilioscore.school.manager');
        $schoolEntities = $schoolManager->findBy([]);

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

        $user = $userManager->create();
        $user->setFirstName(self::FIRST_NAME);
        $user->setMiddleName(date('Y-m-d_h.i.s'));
        $user->setLastName(self::LAST_NAME);
        $user->setEmail($formData['email']);
        $user->setAddedViaIlios(true);
        $user->setEnabled(true);
        $user->setUserSyncIgnore(false);

        $userRoleManager = $this->getContainer()->get('ilioscore.userrole.manager');
        $user->addRole($userRoleManager->findOneBy(['title' => 'Course Director']));
        $user->setSchool($schoolManager->findOneBy(['id' => $formData['school']]));
        $userManager->update($user);

        /**
         * @var AuthenticationManager $authenticationManager
         */
        $authenticationManager = $this->getContainer()->get('ilioscore.authentication.manager');
        $authentication = $authenticationManager->create();

        $authentication->setUser($user);
        $user->setAuthentication($authentication); // circular reference needed here. 123 BLEAH! [ST 2015/08/31]

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encodedPassword = $encoder->encodePassword($user, self::PASSWORD);

        $authentication->setUsername(self::USERNAME);
        $authentication->setPasswordBcrypt($encodedPassword);
        $authenticationManager->update($authentication);

        $output->writeln('Success!');
        $output->writeln('A user account has been created.');
        $output->writeln(sprintf("You may now log in as '%s' with the password '%s'.", self::USERNAME, self::PASSWORD));
        $output->writeln('Please change this password as soon as possible.');
    }
}
