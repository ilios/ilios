<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\CompetencyType;
use Ilios\CoreBundle\Entity\Manager\CompetencyManager;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetencyHandler
 * @package Ilios\CoreBundle\Handler
 */
class CompetencyHandler extends CompetencyManager
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
     * @return CompetencyInterface
     */
    public function post(array $parameters)
    {
        $competency = $this->createCompetency();

        return $this->processForm($competency, $parameters, 'POST');
    }

    /**
     * @param CompetencyInterface $competency
     * @param array $parameters
     *
     * @return CompetencyInterface
     */
    public function put(
        CompetencyInterface $competency,
        array $parameters
    ) {
        return $this->processForm(
            $competency,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param CompetencyInterface $competency
     * @param array $parameters
     *
     * @return CompetencyInterface
     */
    public function patch(
        CompetencyInterface $competency,
        array $parameters
    ) {
        return $this->processForm(
            $competency,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param CompetencyInterface $competency
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return CompetencyInterface
     */
    protected function processForm(
        CompetencyInterface $competency,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            CompetencyType::class,
            $competency,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
