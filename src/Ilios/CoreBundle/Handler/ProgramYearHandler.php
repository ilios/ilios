<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\ProgramYearType;
use Ilios\CoreBundle\Entity\Manager\ProgramYearManager;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearHandler
 * @package Ilios\CoreBundle\Handler
 */
class ProgramYearHandler extends ProgramYearManager
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
     * @return ProgramYearInterface
     */
    public function post(array $parameters)
    {
        $programYear = $this->createProgramYear();

        return $this->processForm($programYear, $parameters, 'POST');
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     *
     * @return ProgramYearInterface
     */
    public function put(
        ProgramYearInterface $programYear,
        array $parameters
    ) {
        return $this->processForm(
            $programYear,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     *
     * @return ProgramYearInterface
     */
    public function patch(
        ProgramYearInterface $programYear,
        array $parameters
    ) {
        return $this->processForm(
            $programYear,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return ProgramYearInterface
     */
    protected function processForm(
        ProgramYearInterface $programYear,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            ProgramYearType::class,
            $programYear,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
