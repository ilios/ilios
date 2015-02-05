<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\CourseClerkshipTypeType;
use Ilios\CoreBundle\Entity\Manager\CourseClerkshipTypeManager;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

class CourseClerkshipTypeHandler extends CourseClerkshipTypeManager
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
    public function put(CourseClerkshipTypeInterface $courseClerkshipType, array $parameters)
    {
        return $this->processForm($courseClerkshipType, $parameters, 'PUT');
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param array $parameters
     *
     * @return CourseClerkshipTypeInterface
     */
    public function patch(CourseClerkshipTypeInterface $courseClerkshipType, array $parameters)
    {
        return $this->processForm($courseClerkshipType, $parameters, 'PATCH');
    }

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CourseClerkshipTypeInterface
     */
    protected function processForm(CourseClerkshipTypeInterface $courseClerkshipType, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new CourseClerkshipTypeType(), $courseClerkshipType, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $courseClerkshipType = $form->getData();
            $this->updateCourseClerkshipType($courseClerkshipType, true);

            return $courseClerkshipType;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
