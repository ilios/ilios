<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\InstructorGroupType;
use Ilios\CoreBundle\Entity\Manager\InstructorGroupManager;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupHandler
 * @package Ilios\CoreBundle\Handler
 */
class InstructorGroupHandler extends InstructorGroupManager
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
     * @return InstructorGroupInterface
     */
    public function post(array $parameters)
    {
        $instructorGroup = $this->createInstructorGroup();

        return $this->processForm($instructorGroup, $parameters, 'POST');
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param array $parameters
     *
     * @return InstructorGroupInterface
     */
    public function put(
        InstructorGroupInterface $instructorGroup,
        array $parameters
    ) {
        return $this->processForm(
            $instructorGroup,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param array $parameters
     *
     * @return InstructorGroupInterface
     */
    public function patch(
        InstructorGroupInterface $instructorGroup,
        array $parameters
    ) {
        return $this->processForm(
            $instructorGroup,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return InstructorGroupInterface
     */
    protected function processForm(
        InstructorGroupInterface $instructorGroup,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            InstructorGroupType::class,
            $instructorGroup,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
