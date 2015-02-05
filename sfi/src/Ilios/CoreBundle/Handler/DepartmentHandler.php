<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\DepartmentType;
use Ilios\CoreBundle\Entity\Manager\DepartmentManager;
use Ilios\CoreBundle\Entity\DepartmentInterface;

class DepartmentHandler extends DepartmentManager
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
    public function put(DepartmentInterface $department, array $parameters)
    {
        return $this->processForm($department, $parameters, 'PUT');
    }

    /**
     * @param DepartmentInterface $department
     * @param array $parameters
     *
     * @return DepartmentInterface
     */
    public function patch(DepartmentInterface $department, array $parameters)
    {
        return $this->processForm($department, $parameters, 'PATCH');
    }

    /**
     * @param DepartmentInterface $department
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return DepartmentInterface
     */
    protected function processForm(DepartmentInterface $department, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new DepartmentType(), $department, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $department = $form->getData();
            $this->updateDepartment($department, true);

            return $department;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
