<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CourseType;
use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CourseHandler
 * @package Ilios\CoreBundle\Handler
 */
class CourseHandler extends CourseManager
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
     * @return CourseInterface
     */
    public function post(array $parameters)
    {
        $course = $this->createCourse();

        return $this->processForm($course, $parameters, 'POST');
    }

    /**
     * @param CourseInterface $course
     * @param array $parameters
     *
     * @return CourseInterface
     */
    public function put(
        CourseInterface $course,
        array $parameters
    ) {
        return $this->processForm(
            $course,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CourseInterface $course
     * @param array $parameters
     *
     * @return CourseInterface
     */
    public function patch(
        CourseInterface $course,
        array $parameters
    ) {
        return $this->processForm(
            $course,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CourseInterface $course
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CourseInterface
     */
    protected function processForm(
        CourseInterface $course,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CourseType::class,
            $course,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
