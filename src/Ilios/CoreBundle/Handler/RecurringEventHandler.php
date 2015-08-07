<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\RecurringEventType;
use Ilios\CoreBundle\Entity\Manager\RecurringEventManager;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * Class RecurringEventHandler
 * @package Ilios\CoreBundle\Handler
 */
class RecurringEventHandler extends RecurringEventManager
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
     * @return RecurringEventInterface
     */
    public function post(array $parameters)
    {
        $recurringEvent = $this->createRecurringEvent();

        return $this->processForm($recurringEvent, $parameters, 'POST');
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param array $parameters
     *
     * @return RecurringEventInterface
     */
    public function put(
        RecurringEventInterface $recurringEvent,
        array $parameters
    ) {
        return $this->processForm(
            $recurringEvent,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param array $parameters
     *
     * @return RecurringEventInterface
     */
    public function patch(
        RecurringEventInterface $recurringEvent,
        array $parameters
    ) {
        return $this->processForm(
            $recurringEvent,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param RecurringEventInterface $recurringEvent
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return RecurringEventInterface
     */
    protected function processForm(
        RecurringEventInterface $recurringEvent,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new RecurringEventType(),
            $recurringEvent,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
