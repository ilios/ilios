<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\DepartmentType;
use Ilios\CoreBundle\Entity\Manager\DepartmentManager;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class DepartmentHandler
 * @package Ilios\CoreBundle\Handler
 */
class DepartmentHandler extends DepartmentManager
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
     * @return DepartmentInterface
     */
    public function post(array $parameters)
    {
        $department = $this->createDepartment();

        return $this->processForm($department, $parameters, 'POST');
    }

    /**
     * @param DepartmentInterface $department
     * @param array $parameters
     *
     * @return DepartmentInterface
     */
    public function put(
        DepartmentInterface $department,
        array $parameters
    ) {
        return $this->processForm(
            $department,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param DepartmentInterface $department
     * @param array $parameters
     *
     * @return DepartmentInterface
     */
    public function patch(
        DepartmentInterface $department,
        array $parameters
    ) {
        return $this->processForm(
            $department,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param DepartmentInterface $department
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return DepartmentInterface
     */
    protected function processForm(
        DepartmentInterface $department,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            DepartmentType::class,
            $department,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
