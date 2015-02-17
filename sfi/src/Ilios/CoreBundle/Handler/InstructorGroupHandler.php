<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\InstructorGroupType;
use Ilios\CoreBundle\Entity\Manager\InstructorGroupManager;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

class InstructorGroupHandler extends InstructorGroupManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
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
            new InstructorGroupType(),
            $instructorGroup,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $instructorGroup = $form->getData();
            $this->updateInstructorGroup($instructorGroup, true);

            return $instructorGroup;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
