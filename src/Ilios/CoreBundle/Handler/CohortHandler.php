<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CohortType;
use Ilios\CoreBundle\Entity\Manager\CohortManager;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Class CohortHandler
 * @package Ilios\CoreBundle\Handler
 */
class CohortHandler extends CohortManager
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
     * @return CohortInterface
     */
    public function post(array $parameters)
    {
        $cohort = $this->createCohort();

        return $this->processForm($cohort, $parameters, 'POST');
    }

    /**
     * @param CohortInterface $cohort
     * @param array $parameters
     *
     * @return CohortInterface
     */
    public function put(
        CohortInterface $cohort,
        array $parameters
    ) {
        return $this->processForm(
            $cohort,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CohortInterface $cohort
     * @param array $parameters
     *
     * @return CohortInterface
     */
    public function patch(
        CohortInterface $cohort,
        array $parameters
    ) {
        return $this->processForm(
            $cohort,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CohortInterface $cohort
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CohortInterface
     */
    protected function processForm(
        CohortInterface $cohort,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CohortType::class,
            $cohort,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
