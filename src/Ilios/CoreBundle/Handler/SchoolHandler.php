<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\SchoolType;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolHandler
 * @package Ilios\CoreBundle\Handler
 */
class SchoolHandler extends SchoolManager
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
     * @return SchoolInterface
     */
    public function post(array $parameters)
    {
        $school = $this->createSchool();

        return $this->processForm($school, $parameters, 'POST');
    }

    /**
     * @param SchoolInterface $school
     * @param array $parameters
     *
     * @return SchoolInterface
     */
    public function put(
        SchoolInterface $school,
        array $parameters
    ) {
        return $this->processForm(
            $school,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param SchoolInterface $school
     * @param array $parameters
     *
     * @return SchoolInterface
     */
    public function patch(
        SchoolInterface $school,
        array $parameters
    ) {
        return $this->processForm(
            $school,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SchoolInterface $school
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SchoolInterface
     */
    protected function processForm(
        SchoolInterface $school,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            SchoolType::class,
            $school,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
