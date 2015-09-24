<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\InstructorGroupInterface;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends teaching reminders to educators for their upcoming session offerings.
 *
 * Class SendTeachingRemindersCommand
 * @package Ilios\CliBUndle\Command
 */
class SendTeachingRemindersCommand extends Command
{

    /**
     * @var OfferingManagerInterface
     */
    protected $offeringManager;

    public function __construct(OfferingManagerInterface $offeringManager)
    {
        $this->offeringManager = $offeringManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:messaging:send-teaching-reminders')
            ->setDescription('Sends teaching reminders to educators.')
            ->addOption(
                'sender',
                null,
                InputOption::VALUE_REQUIRED,
                'Email address to send reminders from.'
            )
            ->addOption(
                'days',
                null,
                InputOption::VALUE_OPTIONAL,
                'How many days in advance of teaching events reminders should be sent.',
                7
            )
            ->addOption(
                'subject',
                null,
                InputOption::VALUE_OPTIONAL,
                'The subject line of reminder emails.',
                'Upcoming Teaching Session'
            );
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // input validation
        $errors = $this->validateInput($input);
        if (! empty($errors)) {
            foreach ($errors as $error) {
                $output->writeln("<error>{$error}</error>");
            }
            return;
        }

        $daysInAdvance = $input->getOption('days');
        $sender = $input->getOption('sender');

        // get all applicable offerings.
        $offerings = $this->offeringManager->getOfferingsForTeachingReminders($daysInAdvance);

        if ($offerings->isEmpty()) {
            $output->writeln('<info>No offerings with pending teaching reminders found.</info>');
            return;
        }

        $iterator = $offerings->getIterator();
        /** @var OfferingInterface $offering */
        foreach ($iterator as $offering) {
            $instructors = $this->getAllInstructorsForOffering($offering);

            // @todo Implement the rest of it. Render mail body, send out notifications etc [ST 2015/09/22]
        }
    }


    /**
     * @param \Ilios\CoreBundle\Entity\OfferingInterface $offering
     * @return UserInterface[]
     */
    protected function getAllInstructorsForOffering(OfferingInterface $offering)
    {
        $rhett = [];
        $instructors = $offering->getInstructors();
        $iterator = $instructors->getIterator();
        /** @var UserInterface $instructor */
        foreach ($iterator as $instructor) {
            $rhett[$instructor->getId()] = $instructor;
        }

        $instructorGroups = $offering->getInstructorGroups();
        $iterator = $instructorGroups->getIterator();

        /** @var InstructorGroupInterface $instructorGroup */
        foreach ($iterator as $instructorGroup) {
            $instructors = $instructorGroup->getUsers();
            $iterator2 = $instructors->getIterator();
            /** @var UserInterface $instructor */
            foreach ($iterator2 as $instructor) {
                if (! array_key_exists($instructor->getId(), $rhett)) {
                    $rhett[$instructor->getId()] = $instructor;
                }
            }
        }
        return array_values($rhett);
    }

    /**
     * Validates user input.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array A list of validation error message. Empty if no validation errors occurred.
     */
    protected function validateInput(InputInterface $input)
    {
        $errors = [];

        $daysInAdvance = intval($input->getOption('days', 10));
        if (0 > $daysInAdvance) {
            $errors[] = "Invalid value '{$daysInAdvance}' for '--days' option. Must be greater or equal to 0.";
        }
        $sender = $input->getOption('sender');
        if (! filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid value '{$sender}' for '--sender' option. Must be a valid email address.";
        }

        return $errors;
    }
}
