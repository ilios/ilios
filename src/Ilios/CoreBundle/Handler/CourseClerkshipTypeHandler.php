<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CourseClerkshipTypeType;
use Ilios\CoreBundle\Entity\Manager\CourseClerkshipTypeManager;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class CourseClerkshipTypeHandler
 * @package Ilios\CoreBundle\Handler
 */
class CourseClerkshipTypeHandler extends CourseClerkshipTypeManager
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
     * @return CourseClerkshipTypeInterface
     */
    public function post(array $parameters)
    {
        $courseClerkshipType = $this->createCourseClerkshipType();

        return $this->processForm($courseClerkshipType, $parameters, 'POST');
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param array $parameters
     *
     * @return CourseClerkshipTypeInterface
     */
    public function put(
        CourseClerkshipTypeInterface $courseClerkshipType,
        array $parameters
    ) {
        return $this->processForm(
            $courseClerkshipType,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param array $parameters
     *
     * @return CourseClerkshipTypeInterface
     */
    public function patch(
        CourseClerkshipTypeInterface $courseClerkshipType,
        array $parameters
    ) {
        return $this->processForm(
            $courseClerkshipType,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CourseClerkshipTypeInterface
     */
    protected function processForm(
        CourseClerkshipTypeInterface $courseClerkshipType,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CourseClerkshipTypeType::class,
            $courseClerkshipType,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
