<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\UserMadeReminderType;
use Ilios\CoreBundle\Entity\Manager\UserMadeReminderManager;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderHandler
 * @package Ilios\CoreBundle\Handler
 */
class UserMadeReminderHandler extends UserMadeReminderManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param Registry $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Registry $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return UserMadeReminderInterface
     */
    public function post(array $parameters)
    {
        $userMadeReminder = $this->createUserMadeReminder();

        return $this->processForm($userMadeReminder, $parameters, 'POST');
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param array $parameters
     *
     * @return UserMadeReminderInterface
     */
    public function put(
        UserMadeReminderInterface $userMadeReminder,
        array $parameters
    ) {
        return $this->processForm(
            $userMadeReminder,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param array $parameters
     *
     * @return UserMadeReminderInterface
     */
    public function patch(
        UserMadeReminderInterface $userMadeReminder,
        array $parameters
    ) {
        return $this->processForm(
            $userMadeReminder,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return UserMadeReminderInterface
     */
    protected function processForm(
        UserMadeReminderInterface $userMadeReminder,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            UserMadeReminderType::class,
            $userMadeReminder,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
