<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\InstructionHoursType;
use Ilios\CoreBundle\Entity\Manager\InstructionHoursManager;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * Class InstructionHoursHandler
 * @package Ilios\CoreBundle\Handler
 */
class InstructionHoursHandler extends InstructionHoursManager
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
     * @return InstructionHoursInterface
     */
    public function post(array $parameters)
    {
        $instructionHours = $this->createInstructionHours();

        return $this->processForm($instructionHours, $parameters, 'POST');
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param array $parameters
     *
     * @return InstructionHoursInterface
     */
    public function put(
        InstructionHoursInterface $instructionHours,
        array $parameters
    ) {
        return $this->processForm(
            $instructionHours,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param array $parameters
     *
     * @return InstructionHoursInterface
     */
    public function patch(
        InstructionHoursInterface $instructionHours,
        array $parameters
    ) {
        return $this->processForm(
            $instructionHours,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return InstructionHoursInterface
     */
    protected function processForm(
        InstructionHoursInterface $instructionHours,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new InstructionHoursType(),
            $instructionHours,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
