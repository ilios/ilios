<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\Type\AssessmentOptionType;
use Ilios\CoreBundle\Entity\Manager\AssessmentOptionManager;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionHandler
 * @package Ilios\CoreBundle\Handler
 */
class AssessmentOptionHandler extends AssessmentOptionManager
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
     * @return AssessmentOptionInterface
     */
    public function post(array $parameters)
    {
        $assessmentOption = $this->createAssessmentOption();

        return $this->processForm($assessmentOption, $parameters, 'POST');
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param array $parameters
     *
     * @return AssessmentOptionInterface
     */
    public function put(
        AssessmentOptionInterface $assessmentOption,
        array $parameters
    ) {
        return $this->processForm(
            $assessmentOption,
            $parameters,
            'PUT'
        );
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param array $parameters
     *
     * @return AssessmentOptionInterface
     */
    public function patch(
        AssessmentOptionInterface $assessmentOption,
        array $parameters
    ) {
        return $this->processForm(
            $assessmentOption,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return AssessmentOptionInterface
     */
    protected function processForm(
        AssessmentOptionInterface $assessmentOption,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            AssessmentOptionType::class,
            $assessmentOption,
            array('method' => $method)
        );

        $form->submit($parameters, 'PATCH' !== $method);

        if (! $form->isValid()) {
            throw new InvalidFormException('Invalid submitted data', $form);
        }

        return $form->getData();
    }
}
